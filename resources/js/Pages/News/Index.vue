<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AppDrawer from '@/Components/AppDrawer.vue'
import DrawerField from '@/Components/DrawerField.vue'
import Pagination from '@/Components/Pagination.vue'
import Icon from '@/Components/Icon.vue'
import ToggleSwitch from '@/Components/ToggleSwitch.vue'
import ImageCropUpload from '@/Components/ImageCropUpload.vue'
import RichTextEditor from '@/Components/RichTextEditor.vue'
import DataTable from '@/Components/DataTable.vue'

const props = defineProps({
    news:    Object,
    filters: Object,
})

const typeMeta = {
    regular: { label: 'Обычная',  cls: 'bg-blue/10 text-blue' },
    ad:      { label: 'Рекламная', cls: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' },
}

const adLinkTypeMeta = {
    profile: 'Профиль пользователя',
    listing: 'Объявление',
    product: 'Товар',
}

const drawer   = ref(false)
const editItem = ref(null)
const lang     = ref('ru')
const emptyForm = () => ({ title_ru: '', title_tk: '', content_ru: '', content_tk: '', type: 'regular', ad_link_type: null, ad_link_id: null, is_published: false, image: null, crop_x: 50, crop_y: 50, remove_image: false })
const form          = ref(emptyForm())
const errors        = ref({})
const searchQuery   = ref(props.filters?.search ?? '')
const statusFilter  = ref(props.filters?.published !== undefined ? (props.filters.published === '1' || props.filters.published === true) : null)

// Фильтрованный список (поиск на клиенте)
const filtered = computed(() => {
    let items = props.news.data || []
    if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase()
        items = items.filter(n => n.title_ru.toLowerCase().includes(q))
    }
    return items
})

// Счётчик: N из M
const counts = computed(() => ({
    current: filtered.value.length,
    total: props.news.data?.length ?? 0,
}))

// Новый файл отменяет «Удалить» существующей обложки
watch(() => form.value.image, file => { if (file) form.value.remove_image = false })

const dataTableColumns = computed(() => [
    { key: 'image', label: '', width: '40px', type: 'image' },
    { key: 'title_ru', label: 'Заголовок', type: 'text' },
    { key: 'type', label: 'Тип', type: 'badge', badges: typeMeta },
    { key: 'is_published', label: 'Статус', type: 'status' },
    { key: 'created_at', label: 'Дата', type: 'text' },
])

const dataTableActions = computed(() => [
    {
        icon: form.value.is_published ? 'eyeOff' : 'eye',
        title: form.value.is_published ? 'Снять с публикации' : 'Опубликовать',
        handler: (n) => n.is_published ? unpublish(n) : publish(n),
    },
    { icon: 'pencil', title: 'Редактировать', handler: openEdit },
    { icon: 'trash', title: 'Удалить', handler: destroy, color: 'red' },
])

// RU и TK хранятся раздельно; поля показывают активный язык
const title = computed({
    get: () => lang.value === 'ru' ? form.value.title_ru : form.value.title_tk,
    set: v  => { form.value[lang.value === 'ru' ? 'title_ru' : 'title_tk'] = v },
})
const content = computed({
    get: () => lang.value === 'ru' ? form.value.content_ru : form.value.content_tk,
    set: v  => { form.value[lang.value === 'ru' ? 'content_ru' : 'content_tk'] = v },
})
const canSave = computed(() => (form.value.title_ru || '').trim().length > 0)

function openCreate() {
    editItem.value = null
    lang.value = 'ru'
    form.value = emptyForm()
    errors.value = {}
    drawer.value = true
}
function openEdit(n) {
    editItem.value = n
    lang.value = 'ru'
    form.value = { ...emptyForm(), title_ru: n.title_ru ?? '', title_tk: n.title_tk ?? '', content_ru: n.content_ru ?? '', content_tk: n.content_tk ?? '', type: n.type, is_published: n.is_published }
    errors.value = {}
    drawer.value = true
}
function save() {
    // Файл требует multipart, а multipart-PUT PHP не парсит — POST + _method
    const url  = editItem.value ? route('news.update', editItem.value.id) : route('news.store')
    const data = editItem.value ? { ...form.value, _method: 'put' } : form.value
    router.post(url, data, {
        forceFormData: !!form.value.image,
        onSuccess: () => { drawer.value = false },
        onError: e => { errors.value = e },
    })
}
function publish(n) { router.patch(route('news.publish', n.id)) }
function unpublish(n) { router.patch(route('news.unpublish', n.id)) }
function destroy(n) {
    if (confirm(`Удалить «${n.title_ru}»?`)) router.delete(route('news.destroy', n.id))
}
</script>

<template>
  <AppLayout>
    <template #header>Новости</template>

    <div class="space-y-4">
      <!-- Кнопка добавления -->
      <div class="flex justify-end">
        <button @click="openCreate" class="px-4 py-2 rounded-btn bg-blue text-white text-sm font-bold hover:bg-blue/90 transition">
          + Добавить
        </button>
      </div>

      <!-- Используем компонент DataTable -->
      <DataTable
        :columns="dataTableColumns"
        :items="news.data"
        :pagination="news"
        :actions="dataTableActions"
        :search-field="'title_ru'"
        search-placeholder="Поиск по заголовку..."
        @dblclick="openEdit"
      >
        <!-- Custom ячейка для даты (форматирование) -->
        <template #cell-created_at="{ value }">
          {{ new Date(value).toLocaleDateString('ru') }}
        </template>
      </DataTable>
    </div>

    <AppDrawer :open="drawer" :title="editItem ? 'Редактировать новость' : 'Новая новость'" @close="drawer = false">
      <!-- Переключатель языка формы -->
      <div class="mb-4 inline-flex gap-1 rounded-[11px] border border-[var(--field-border)] bg-[var(--field-bg)] p-1">
        <button
          v-for="l in ['ru', 'tk']" :key="l" type="button"
          @click="lang = l"
          class="rounded-[8px] px-5 py-1.5 text-[12px] font-bold uppercase transition-colors"
          :class="lang === l ? 'bg-[var(--accent)] text-white' : 'text-[var(--text-secondary)] hover:bg-[var(--nav-hover)]'"
        >{{ l }}</button>
      </div>

      <DrawerField :label="lang === 'ru' ? 'Заголовок (RU)' : 'Заголовок (TK)'" :required="lang === 'ru'" :error="lang === 'ru' ? errors.title_ru : errors.title_tk">
        <input v-model="title" class="input" :placeholder="lang === 'ru' ? 'Заголовок новости' : 'Habaryň ady'" />
      </DrawerField>

      <DrawerField :label="lang === 'ru' ? 'Содержание (RU)' : 'Содержание (TK)'" :error="lang === 'ru' ? errors.content_ru : errors.content_tk">
        <RichTextEditor
          v-model="content"
          :placeholder="lang === 'ru' ? 'Отформатируйте текст новости...' : 'Habar tekstini formatla...'"
        />
        <p class="mt-1.5 text-[11px] font-semibold">
          <span :class="(form.content_ru || '').trim() ? 'text-green' : 'text-muted'">
            {{ (form.content_ru || '').trim() ? '✓ RU заполнен' : '○ RU пусто' }}
          </span>
          <span class="text-muted"> · </span>
          <span :class="(form.content_tk || '').trim() ? 'text-green' : 'text-muted'">
            {{ (form.content_tk || '').trim() ? '✓ TK заполнен' : '○ TK пусто' }}
          </span>
        </p>
      </DrawerField>

      <DrawerField label="Обложка" :error="errors.image">
        <ImageCropUpload
          v-model="form.image"
          v-model:crop-x="form.crop_x"
          v-model:crop-y="form.crop_y"
          :existing-url="editItem?.image ? `/storage/${editItem.image}` : null"
          @remove-existing="form.remove_image = true"
        />
      </DrawerField>

      <DrawerField label="Тип" :error="errors.type">
        <div class="grid grid-cols-2 gap-1 rounded-[11px] border border-[var(--field-border)] bg-[var(--field-bg)] p-1">
          <button
            v-for="(meta, value) in typeMeta" :key="value" type="button"
            @click="form.type = value"
            class="rounded-[8px] px-2 py-[7px] text-[12px] font-bold transition-colors"
            :class="form.type === value ? 'bg-[var(--accent)] text-white' : 'text-[var(--text-secondary)] hover:bg-[var(--nav-hover)]'"
          >{{ meta.label }}</button>
        </div>
      </DrawerField>

      <!-- Блок рекламной ссылки (показывается только если тип = ad) -->
      <template v-if="form.type === 'ad'">
        <DrawerField label="Тип ссылки" :required="true" :error="errors.ad_link_type">
          <div class="grid grid-cols-3 gap-1 rounded-[11px] border border-[var(--field-border)] bg-[var(--field-bg)] p-1">
            <button
              v-for="(label, value) in adLinkTypeMeta" :key="value" type="button"
              @click="form.ad_link_type = value"
              class="rounded-[8px] px-2 py-[7px] text-[12px] font-bold transition-colors"
              :class="form.ad_link_type === value ? 'bg-[var(--accent)] text-white' : 'text-[var(--text-secondary)] hover:bg-[var(--nav-hover)]'"
            >{{ label }}</button>
          </div>
        </DrawerField>

        <DrawerField label="ID сущности" :required="true" :error="errors.ad_link_id">
          <input
            v-model.number="form.ad_link_id"
            type="number"
            placeholder="Укажите ID"
            class="input"
          />
        </DrawerField>
      </template>

      <div class="mt-5 flex items-center justify-between gap-4 rounded-[10px] border border-[var(--field-border)] bg-[var(--field-bg)] px-4 py-3">
        <div>
          <div class="text-[13px] font-bold text-[var(--text)]">Опубликовать сразу</div>
          <div class="mt-0.5 text-[11px] text-[var(--text-muted)]">Иначе новость сохранится как черновик</div>
        </div>
        <ToggleSwitch v-model="form.is_published" />
      </div>

      <template #footer>
        <div class="flex justify-end gap-2">
          <button
            @click="drawer = false"
            class="rounded-[10px] border border-[var(--field-border)] bg-transparent px-[18px] py-[10px] text-[13px] font-semibold text-[var(--text-secondary)] transition-colors hover:bg-[var(--nav-hover)]"
          >Отмена</button>
          <button
            @click="save"
            :disabled="!canSave"
            class="rounded-[10px] px-5 py-[10px] text-[13px] font-bold transition-colors"
            :class="canSave
              ? 'bg-[var(--accent)] text-white hover:bg-[var(--accent-hover)] shadow-[0_10px_22px_-8px_var(--accent)]'
              : 'cursor-not-allowed bg-[var(--field-disabled-bg)] text-[var(--text-muted)]'"
          >{{ form.is_published ? 'Опубликовать' : 'Сохранить' }}</button>
        </div>
      </template>
    </AppDrawer>
  </AppLayout>
</template>
