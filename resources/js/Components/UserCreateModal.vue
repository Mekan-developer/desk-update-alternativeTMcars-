<script setup>
import { computed, ref, watch } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import Icon from '@/Components/Icon.vue'

const { t } = useI18n()

const props = defineProps({
    open:    { type: Boolean, default: false },
    regions: { type: Array,   default: () => [] },
})
const emit = defineEmits(['close'])

const form = useForm({
    phone: '',
    activation: 'active',
    region_id: '',
    city_id: '',
    district_id: '',
    name: '',
    gender: '',
    birth_date: '',
    avatar: null,
})

// ---- Телефон: +993 фиксирован, 8 цифр маской «61 00 00 00» ----
const phoneDigits = ref('')
const fullPhone   = computed(() => '+993' + phoneDigits.value)
const phoneMasked = computed(() => phoneDigits.value.replace(/(\d{2})(?=\d)/g, '$1 '))

function onPhoneInput(e) {
    phoneDigits.value = e.target.value.replace(/\D/g, '').slice(0, 8)
    e.target.value = phoneMasked.value
}

// Живая проверка уникальности: idle | checking | free | taken
const phoneCheck  = ref('idle')
const takenUserId = ref(null)
let checkTimer = null

watch(phoneDigits, (digits) => {
    clearTimeout(checkTimer)
    takenUserId.value = null

    if (digits.length < 8) {
        phoneCheck.value = 'idle'
        return
    }

    phoneCheck.value = 'checking'
    checkTimer = setTimeout(async () => {
        const requested = digits
        try {
            const res  = await fetch(`${route('users.check-phone')}?phone=${encodeURIComponent('+993' + requested)}`, {
                headers: { Accept: 'application/json' },
            })
            if (phoneDigits.value !== requested) return // ответ устарел — номер уже другой
            if (!res.ok) { phoneCheck.value = 'idle'; return }
            const data = await res.json()
            phoneCheck.value  = data.available ? 'free' : 'taken'
            takenUserId.value = data.user_id
        } catch {
            if (phoneDigits.value === requested) phoneCheck.value = 'idle'
        }
    }, 400)
})

// ---- Локация: Велаят → Город → Район, сброс детей при смене родителя ----
const cities    = computed(() => props.regions.find(r => r.id === form.region_id)?.cities || [])
const districts = computed(() => cities.value.find(c => c.id === form.city_id)?.districts || [])

watch(() => form.region_id, () => { form.city_id = ''; form.district_id = '' })
watch(() => form.city_id,   () => { form.district_id = '' })

// ---- Аватар ----
const avatarPreview = ref(null)
const avatarError   = ref('')
const avatarInput   = ref(null)

function pickAvatar(file) {
    avatarError.value = ''
    if (!file) return
    if (!file.type.startsWith('image/')) { avatarError.value = t('userModal.onlyImage'); return }
    if (file.size > 5 * 1024 * 1024)     { avatarError.value = t('userModal.tooBig'); return }
    form.avatar = file
    if (avatarPreview.value) URL.revokeObjectURL(avatarPreview.value)
    avatarPreview.value = URL.createObjectURL(file)
}

// ---- Submit ----
const canSubmit = computed(() => phoneCheck.value === 'free' && !!form.region_id && !form.processing)

function submit() {
    if (!canSubmit.value) return
    form.transform(data => ({ ...data, phone: fullPhone.value }))
        .post(route('users.store'), {
            forceFormData: true,
            onSuccess: () => emit('close'),
        })
}

// Каждое открытие — чистая форма
watch(() => props.open, (open) => {
    if (!open) return
    form.reset()
    form.clearErrors()
    phoneDigits.value = ''
    phoneCheck.value = 'idle'
    takenUserId.value = null
    avatarError.value = ''
    if (avatarPreview.value) URL.revokeObjectURL(avatarPreview.value)
    avatarPreview.value = null
})
</script>

<template>
  <Teleport to="body">
    <Transition name="ov">
      <div
        v-if="open"
        class="fixed inset-0 z-[600] flex items-center justify-center bg-black/40 p-4 backdrop-blur-sm"
        @click.self="$emit('close')"
      >
        <div
          class="flex max-h-[92vh] w-[560px] max-w-full flex-col overflow-hidden rounded-[16px] font-golos shadow-[0_24px_48px_rgba(0,0,0,.28)]"
          :style="{ background: 'var(--card-bg)', border: '1px solid var(--card-border)', color: 'var(--text)' }"
        >
          <!-- Header -->
          <div class="flex items-center justify-between px-6 pt-5 pb-4" :style="{ borderBottom: '1px solid var(--card-border)' }">
            <h2 class="text-[16px] font-extrabold">{{ t('userModal.title') }}</h2>
            <button
              class="flex h-8 w-8 items-center justify-center rounded-[8px] transition hover:bg-black/10 dark:hover:bg-white/10"
              :style="{ color: 'var(--text-muted)' }"
              @click="$emit('close')"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>

          <!-- Body -->
          <div class="flex-1 overflow-y-auto px-6 py-5">
            <!-- 1. Телефон -->
            <label class="mb-1.5 block text-[12px] font-bold uppercase tracking-wide" :style="{ color: 'var(--text-muted)' }">
              {{ t('common.phone') }}<span class="ml-0.5 text-red">*</span>
            </label>
            <div
              class="phone-box flex items-stretch overflow-hidden rounded-[10px]"
              :class="{ 'phone-box--error': phoneCheck === 'taken' || form.errors.phone }"
              :style="{ background: 'var(--field-bg)' }"
            >
              <span
                class="flex select-none items-center px-3.5 text-sm font-semibold"
                :style="{ color: 'var(--text-secondary)', background: 'var(--field-disabled-bg)', borderRight: '1px solid var(--field-border)' }"
              >+993</span>
              <input
                :value="phoneMasked"
                @input="onPhoneInput"
                type="tel" inputmode="numeric" placeholder="61 00 00 00" autocomplete="off"
                class="min-w-0 flex-1 border-0 bg-transparent px-3.5 py-[10px] text-sm outline-none focus:ring-0"
                :style="{ color: 'var(--text)' }"
              />
              <span class="flex w-9 items-center justify-center">
                <Icon v-if="phoneCheck === 'free'" kind="check" :size="16" :style="{ color: 'var(--status-ok)' }" />
                <svg v-else-if="phoneCheck === 'checking'" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" :style="{ color: 'var(--text-muted)' }">
                  <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/>
                  <path d="M22 12a10 10 0 0 0-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                </svg>
              </span>
            </div>
            <p v-if="phoneCheck === 'taken'" class="mt-1.5 text-[12px] font-semibold" :style="{ color: 'var(--status-bad)' }">
              {{ t('userModal.phoneTaken') }}
              <Link v-if="takenUserId" :href="route('users.show', takenUserId)" class="underline underline-offset-2">{{ t('userModal.openUser') }}</Link>
            </p>
            <p v-else-if="form.errors.phone" class="mt-1.5 text-[12px] font-semibold" :style="{ color: 'var(--status-bad)' }">{{ form.errors.phone }}</p>

            <!-- 2. Активация -->
            <label class="mt-5 mb-1.5 block text-[12px] font-bold uppercase tracking-wide" :style="{ color: 'var(--text-muted)' }">{{ t('userModal.activation') }}</label>
            <div class="grid grid-cols-2 gap-2.5">
              <button
                v-for="opt in [
                  { value: 'active', title: t('userModal.activeNow'),  hint: t('userModal.activeNowHint') },
                  { value: 'sms',    title: t('userModal.smsConfirm'), hint: t('userModal.smsConfirmHint') },
                ]"
                :key="opt.value" type="button"
                class="rounded-[12px] p-3.5 text-left transition"
                :style="form.activation === opt.value
                  ? { border: '1px solid var(--accent)', background: 'var(--accent-tint)' }
                  : { border: '1px solid var(--field-border)', background: 'var(--field-bg)' }"
                @click="form.activation = opt.value"
              >
                <span class="flex items-center gap-2 text-[13px] font-bold" :style="{ color: form.activation === opt.value ? 'var(--accent)' : 'var(--text)' }">
                  <span
                    class="inline-flex h-3.5 w-3.5 flex-shrink-0 items-center justify-center rounded-full"
                    :style="{ border: form.activation === opt.value ? '4.5px solid var(--accent)' : '1.5px solid var(--field-border)' }"
                  ></span>
                  {{ opt.title }}
                </span>
                <span class="mt-1 block text-[11.5px] leading-snug" :style="{ color: 'var(--text-secondary)' }">{{ opt.hint }}</span>
              </button>
            </div>

            <!-- 3. Локация -->
            <label class="mt-5 mb-1.5 block text-[12px] font-bold uppercase tracking-wide" :style="{ color: 'var(--text-muted)' }">
              {{ t('userModal.location') }}<span class="ml-0.5 text-red">*</span>
            </label>
            <div class="grid grid-cols-3 gap-2.5">
              <select v-model="form.region_id" class="input">
                <option value="">{{ t('userModal.velayat') }}</option>
                <option v-for="r in regions" :key="r.id" :value="r.id">{{ r.name_ru }}</option>
              </select>
              <select v-model="form.city_id" :disabled="!form.region_id" class="input disabled:cursor-not-allowed disabled:opacity-50">
                <option value="">{{ form.region_id ? t('userModal.cityOption') : t('userModal.firstVelayat') }}</option>
                <option v-for="c in cities" :key="c.id" :value="c.id">{{ c.name_ru }}</option>
              </select>
              <select v-model="form.district_id" :disabled="!form.city_id" class="input disabled:cursor-not-allowed disabled:opacity-50">
                <option value="">{{ form.city_id ? t('userModal.districtOption') : t('userModal.firstCity') }}</option>
                <option v-for="d in districts" :key="d.id" :value="d.id">{{ d.name_ru }}</option>
              </select>
            </div>
            <p class="mt-1.5 text-[11.5px]" :style="{ color: 'var(--text-muted)' }">
              {{ t('userModal.locationHint') }}
            </p>
            <p v-if="form.errors.region_id || form.errors.city_id || form.errors.district_id" class="mt-1 text-[12px] font-semibold" :style="{ color: 'var(--status-bad)' }">
              {{ form.errors.region_id || form.errors.city_id || form.errors.district_id }}
            </p>

            <!-- 4. Разделитель -->
            <div class="mt-6 mb-5 flex items-center gap-3">
              <span class="h-px flex-1" :style="{ background: 'var(--field-border)' }"></span>
              <span class="text-[11.5px] font-semibold" :style="{ color: 'var(--text-muted)' }">{{ t('userModal.profileDivider') }}</span>
              <span class="h-px flex-1" :style="{ background: 'var(--field-border)' }"></span>
            </div>

            <!-- 5. Аватар + Имя -->
            <div class="flex items-end gap-3.5">
              <button
                type="button" :title="t('userModal.uploadAvatar')"
                class="flex h-14 w-14 flex-shrink-0 items-center justify-center overflow-hidden rounded-full transition"
                :style="{ border: avatarPreview ? '1px solid var(--field-border)' : '1.5px dashed var(--field-border)', background: 'var(--field-bg)', color: 'var(--text-muted)' }"
                @click="avatarInput.click()"
                @dragover.prevent
                @drop.prevent="pickAvatar($event.dataTransfer.files[0])"
              >
                <img v-if="avatarPreview" :src="avatarPreview" class="h-full w-full object-cover" alt="" />
                <Icon v-else kind="image" :size="18" />
              </button>
              <input ref="avatarInput" type="file" accept="image/*" class="hidden" @change="pickAvatar($event.target.files[0]); $event.target.value = ''" />
              <div class="min-w-0 flex-1">
                <label class="mb-1.5 block text-[12px] font-bold uppercase tracking-wide" :style="{ color: 'var(--text-muted)' }">{{ t('common.name') }}</label>
                <input v-model="form.name" type="text" :placeholder="t('userModal.namePlaceholder')" class="input" />
              </div>
            </div>
            <p v-if="avatarError || form.errors.avatar" class="mt-1.5 text-[12px] font-semibold" :style="{ color: 'var(--status-bad)' }">{{ avatarError || form.errors.avatar }}</p>
            <p v-if="form.errors.name" class="mt-1.5 text-[12px] font-semibold" :style="{ color: 'var(--status-bad)' }">{{ form.errors.name }}</p>

            <!-- Пол + Дата рождения -->
            <div class="mt-4 grid grid-cols-2 gap-3.5">
              <div>
                <label class="mb-1.5 block text-[12px] font-bold uppercase tracking-wide" :style="{ color: 'var(--text-muted)' }">{{ t('userModal.gender') }}</label>
                <div class="flex rounded-[10px] p-1" :style="{ background: 'var(--field-bg)', border: '1px solid var(--field-border)' }">
                  <button
                    v-for="g in [{ value: '', label: t('userModal.notSet') }, { value: 'male', label: t('userModal.maleShort') }, { value: 'female', label: t('userModal.femaleShort') }]"
                    :key="g.value" type="button"
                    class="flex-1 rounded-[7px] py-[7px] text-[12.5px] font-semibold transition"
                    :style="form.gender === g.value ? { background: 'var(--accent)', color: '#fff' } : { color: 'var(--text-secondary)' }"
                    @click="form.gender = g.value"
                  >{{ g.label }}</button>
                </div>
              </div>
              <div>
                <label class="mb-1.5 block text-[12px] font-bold uppercase tracking-wide" :style="{ color: 'var(--text-muted)' }">{{ t('userModal.birthDate') }}</label>
                <input v-model="form.birth_date" type="date" :max="new Date().toISOString().slice(0, 10)" class="input" />
                <p v-if="form.errors.birth_date" class="mt-1 text-[12px] font-semibold" :style="{ color: 'var(--status-bad)' }">{{ form.errors.birth_date }}</p>
              </div>
            </div>
          </div>

          <!-- 6. Футер -->
          <div class="flex justify-end gap-2.5 px-6 py-4" :style="{ borderTop: '1px solid var(--card-border)' }">
            <button
              type="button"
              class="rounded-[10px] px-4 py-[10px] text-[13px] font-bold transition hover:bg-black/5 dark:hover:bg-white/5"
              :style="{ color: 'var(--text-secondary)' }"
              @click="$emit('close')"
            >{{ t('actions.cancel') }}</button>
            <button
              type="button" :disabled="!canSubmit"
              class="rounded-[10px] px-5 py-[10px] text-[13px] font-bold text-white transition disabled:cursor-not-allowed disabled:opacity-50"
              :style="{ background: 'var(--accent)' }"
              @click="submit"
            >{{ t('userModal.submit') }}</button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.ov-enter-active, .ov-leave-active { transition: all .2s ease; }
.ov-enter-from, .ov-leave-to { opacity: 0; }

.phone-box { border: 1px solid var(--field-border); transition: border-color .15s, box-shadow .15s; }
.phone-box:focus-within { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-tint); }
.phone-box--error, .phone-box--error:focus-within { border-color: var(--status-bad); box-shadow: none; }
</style>
