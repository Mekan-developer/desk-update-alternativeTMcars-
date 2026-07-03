<script setup>
import { ref, computed, watch, onBeforeUnmount } from 'vue'

/**
 * Загрузка изображения с выбором области кропа перетаскиванием.
 * Файл не режется на клиенте — на сервер уходят crop_x/crop_y (проценты,
 * семантика background-position), финальный кроп делает ImageConversionService.
 * Переиспользуемый: подходит для обложек новостей, фото объявлений и т.д.
 */
const props = defineProps({
    modelValue:  File,                              // выбранный файл
    cropX:       { type: Number, default: 50 },
    cropY:       { type: Number, default: 50 },
    existingUrl: String,                            // уже сохранённое изображение (режим редактирования)
    aspect:      { type: Number, default: 16 / 9 },
    maxBytes:    { type: Number, default: 5 * 1024 * 1024 },
    minWidth:    { type: Number, default: 1200 },
    minHeight:   { type: Number, default: 675 },
})
const emit = defineEmits(['update:modelValue', 'update:cropX', 'update:cropY', 'removeExisting'])

const fileInput  = ref(null)
const frame      = ref(null)
const objectUrl  = ref(null)
const natural    = ref(null)   // { w, h } исходника — для драга и предупреждения
const error      = ref('')
const warning    = ref('')
const hideExisting = ref(false)

const ACCEPTED = ['image/jpeg', 'image/png', 'image/webp']

const previewUrl = computed(() => objectUrl.value || (!hideExisting.value && props.existingUrl) || null)

// По какой оси изображение выступает за 16:9-кадр (только по ней есть смысл драгать)
const dragAxis = computed(() => {
    if (!natural.value) return null
    const imgAspect = natural.value.w / natural.value.h
    if (Math.abs(imgAspect - props.aspect) < 0.005) return null
    return imgAspect > props.aspect ? 'x' : 'y'
})

watch(() => props.modelValue, file => {
    if (objectUrl.value) URL.revokeObjectURL(objectUrl.value)
    objectUrl.value = file ? URL.createObjectURL(file) : null
})
watch(previewUrl, url => {
    natural.value = null
    warning.value = ''
    if (!url) return
    const img = new Image()
    img.onload = () => {
        natural.value = { w: img.naturalWidth, h: img.naturalHeight }
        // Предупреждаем о малом исходнике, но не блокируем (только для нового файла)
        if (objectUrl.value && (img.naturalWidth < props.minWidth || img.naturalHeight < props.minHeight)) {
            warning.value = `Изображение меньше ${props.minWidth}×${props.minHeight}px — качество обложки может пострадать`
        }
    }
    img.src = url
}, { immediate: true })
onBeforeUnmount(() => { if (objectUrl.value) URL.revokeObjectURL(objectUrl.value) })

function pick() { fileInput.value?.click() }

function onSelect(e) {
    const file = e.target.files?.[0]
    e.target.value = ''
    if (!file) return
    error.value = ''
    if (!ACCEPTED.includes(file.type)) {
        error.value = 'Допустимые форматы: JPG, PNG, WebP'
        return
    }
    if (file.size > props.maxBytes) {
        error.value = `Файл больше ${Math.round(props.maxBytes / 1024 / 1024)} МБ`
        return
    }
    emit('update:cropX', 50)
    emit('update:cropY', 50)
    emit('update:modelValue', file)
}

function remove() {
    error.value = ''
    warning.value = ''
    emit('update:cropX', 50)
    emit('update:cropY', 50)
    if (props.modelValue) {
        emit('update:modelValue', null)
    } else if (props.existingUrl) {
        hideExisting.value = true
        emit('removeExisting')
    }
}

// Драг кадра: смещение мыши → проценты background-position
let drag = null
function onPointerDown(e) {
    if (!dragAxis.value || !natural.value) return
    const rect  = frame.value.getBoundingClientRect()
    const scale = Math.max(rect.width / natural.value.w, rect.height / natural.value.h)
    const hidden = dragAxis.value === 'x'
        ? natural.value.w * scale - rect.width
        : natural.value.h * scale - rect.height
    if (hidden < 1) return
    drag = { startX: e.clientX, startY: e.clientY, baseX: props.cropX, baseY: props.cropY, hidden }
    frame.value.setPointerCapture(e.pointerId)
}
function onPointerMove(e) {
    if (!drag) return
    const clamp = v => Math.max(0, Math.min(100, v))
    if (dragAxis.value === 'x') {
        emit('update:cropX', clamp(drag.baseX - (e.clientX - drag.startX) / drag.hidden * 100))
    } else {
        emit('update:cropY', clamp(drag.baseY - (e.clientY - drag.startY) / drag.hidden * 100))
    }
}
function onPointerUp(e) {
    drag = null
    frame.value?.releasePointerCapture?.(e.pointerId)
}
</script>

<template>
  <div>
    <input ref="fileInput" type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="onSelect" />

    <!-- Пустое состояние -->
    <button
      v-if="!previewUrl"
      type="button"
      @click="pick"
      class="flex w-full flex-col items-center justify-center gap-2 rounded-[11px] border-2 border-dashed border-[var(--field-border)] bg-[var(--field-bg)] py-8 text-[var(--text-secondary)] transition-colors hover:border-[var(--accent)] hover:text-[var(--accent)]"
    >
      <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[var(--accent-tint)] text-xl font-bold text-[var(--accent)]">+</span>
      <span class="text-[13px] font-semibold">Загрузить изображение</span>
      <span class="text-[11px] text-[var(--text-muted)]">JPG, PNG, WebP · до 5 МБ · от {{ minWidth }}×{{ minHeight }}px</span>
    </button>

    <!-- Превью с драгом кадра -->
    <template v-else>
      <div
        ref="frame"
        class="w-full select-none rounded-[11px] border border-[var(--field-border)] bg-no-repeat touch-none"
        :class="dragAxis ? 'cursor-move' : ''"
        :style="{
          aspectRatio: aspect,
          backgroundImage: `url(${previewUrl})`,
          backgroundSize: 'cover',
          backgroundPosition: `${cropX}% ${cropY}%`,
        }"
        @pointerdown="onPointerDown"
        @pointermove="onPointerMove"
        @pointerup="onPointerUp"
        @pointercancel="onPointerUp"
      ></div>
      <p v-if="dragAxis" class="mt-1.5 text-[11px] text-[var(--text-muted)]">Перетащите изображение, чтобы выбрать видимую область</p>

      <div class="mt-2 flex gap-2">
        <button
          type="button" @click="pick"
          class="rounded-[10px] border border-[var(--field-border)] px-3.5 py-[7px] text-[12px] font-semibold text-[var(--text-secondary)] transition-colors hover:bg-[var(--nav-hover)]"
        >Заменить</button>
        <button
          type="button" @click="remove"
          class="rounded-[10px] px-3.5 py-[7px] text-[12px] font-semibold text-red transition-colors hover:bg-red/10"
        >Удалить</button>
      </div>
    </template>

    <p v-if="error" class="mt-1.5 text-[11px] font-semibold text-red">{{ error }}</p>
    <p v-else-if="warning" class="mt-1.5 text-[11px] font-semibold text-orange">{{ warning }}</p>
  </div>
</template>
