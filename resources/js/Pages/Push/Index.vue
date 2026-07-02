<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import Icon from '@/Components/Icon.vue'

const props = defineProps({
    pushNotifications: Object,
    regions:           Array,
    tariffs:           Array,
})

const emptyForm = () => ({
    title:     '',
    body:      '',
    target:    'all',
    link_type: '',
    link_id:   '',
    filters:   { region_id: '', tariff_id: '' },
})

const form         = ref(emptyForm())
const singleUserId = ref('')
const sending      = ref(false)

const recipients = [
    { value: 'all',      label: 'Все пользователи' },
    { value: 'filtered', label: 'Сегмент' },
    { value: 'selected', label: 'Один пользователь' },
]

const recipientHelper = computed(() => ({
    all:      'Уведомление получат все пользователи приложения.',
    filtered: 'Уведомление получат пользователи, подходящие под выбранные ниже фильтры.',
    selected: 'Укажите ID пользователя ниже — уведомление получит только он.',
}[form.value.target]))

const linkTypes = [
    { value: '',        label: '— нет —' },
    { value: 'listing', label: 'Объявление' },
    { value: 'user',    label: 'Пользователь' },
    { value: 'news',    label: 'Новость' },
]

const idDisabled = computed(() => form.value.link_type === '')

function send() {
    sending.value = true
    router.post(route('push.send'), {
        title:     form.value.title,
        body:      form.value.body,
        target:    form.value.target,
        link_type: form.value.link_type || null,
        link_id:   form.value.link_id || null,
        filters:   form.value.target === 'filtered' ? form.value.filters : null,
        user_ids:  form.value.target === 'selected' ? [singleUserId.value].filter(Boolean) : null,
    }, {
        onFinish: () => { sending.value = false },
        onSuccess: () => {
            form.value = emptyForm()
            singleUserId.value = ''
        },
    })
}

function scrollToHistory() {
    document.getElementById('push-history')?.scrollIntoView({ behavior: 'smooth' })
}
</script>

<template>
  <AppLayout>
    <template #header>Push-уведомления</template>

    <template #actions>
      <button
        @click="scrollToHistory"
        class="rounded-[9px] border border-[var(--field-border)] bg-[var(--card-bg)] px-4 py-[9px] text-[13px] font-semibold text-[var(--text-secondary)] shadow-[var(--card-shadow)] transition-colors hover:bg-[var(--nav-hover)]"
      >История рассылок</button>
    </template>

    <div class="flex flex-wrap items-start gap-6">
      <!-- Form card -->
      <div class="flex min-w-0 flex-[1_1_520px] flex-col gap-5 rounded-2xl bg-[var(--card-bg)] border border-[var(--card-border)] p-7 shadow-[var(--card-shadow)]">
        <div>
          <div class="text-[15px] font-bold text-[var(--text)]">Отправить уведомление</div>
          <div class="mt-[3px] text-[12.5px] text-[var(--text-muted)]">Придёт как push и сохранится в истории рассылок</div>
        </div>

        <div>
          <label class="mb-[7px] block text-[12.5px] font-semibold text-[var(--text-secondary)]">Заголовок</label>
          <input
            v-model="form.title" type="text" placeholder="Заголовок уведомления"
            class="w-full box-border rounded-[10px] border border-[var(--field-border)] bg-[var(--field-bg)] px-[13px] py-[11px] text-sm text-[var(--text)] outline-none transition-shadow focus:border-[var(--accent)] focus:shadow-[0_0_0_3px_var(--accent-tint)]"
          >
        </div>

        <div>
          <label class="mb-[7px] block text-[12.5px] font-semibold text-[var(--text-secondary)]">Текст</label>
          <textarea
            v-model="form.body" rows="3" placeholder="Текст уведомления"
            class="w-full box-border resize-y rounded-[10px] border border-[var(--field-border)] bg-[var(--field-bg)] px-[13px] py-[11px] text-sm text-[var(--text)] outline-none transition-shadow focus:border-[var(--accent)] focus:shadow-[0_0_0_3px_var(--accent-tint)]"
          ></textarea>
        </div>

        <div>
          <label class="mb-[7px] block text-[12.5px] font-semibold text-[var(--text-secondary)]">Получатели</label>
          <div class="inline-flex gap-1 rounded-[11px] border border-[var(--field-border)] bg-[var(--field-bg)] p-1">
            <button
              v-for="r in recipients" :key="r.value"
              @click="form.target = r.value"
              type="button"
              class="rounded-[8px] px-3.5 py-[7px] text-[13px] font-semibold transition-colors"
              :class="form.target === r.value ? 'bg-[var(--accent)] text-white' : 'text-[var(--text-secondary)] hover:bg-[var(--nav-hover)]'"
            >{{ r.label }}</button>
          </div>
          <div class="mt-[7px] text-xs text-[var(--text-muted)]">{{ recipientHelper }}</div>

          <div v-if="form.target === 'filtered'" class="mt-3 grid grid-cols-2 gap-3 rounded-xl bg-[var(--content-bg)] p-3">
            <div>
              <label class="mb-[7px] block text-[12.5px] font-semibold text-[var(--text-secondary)]">Регион</label>
              <select v-model="form.filters.region_id" class="w-full box-border rounded-[10px] border border-[var(--field-border)] bg-[var(--field-bg)] px-[13px] py-[11px] text-sm text-[var(--text)] outline-none focus:border-[var(--accent)] focus:shadow-[0_0_0_3px_var(--accent-tint)]">
                <option value="">Все регионы</option>
                <option v-for="r in regions" :key="r.id" :value="r.id">{{ r.name_ru }}</option>
              </select>
            </div>
            <div>
              <label class="mb-[7px] block text-[12.5px] font-semibold text-[var(--text-secondary)]">Тариф</label>
              <select v-model="form.filters.tariff_id" class="w-full box-border rounded-[10px] border border-[var(--field-border)] bg-[var(--field-bg)] px-[13px] py-[11px] text-sm text-[var(--text)] outline-none focus:border-[var(--accent)] focus:shadow-[0_0_0_3px_var(--accent-tint)]">
                <option value="">Все тарифы</option>
                <option v-for="t in tariffs" :key="t.id" :value="t.id">{{ t.name_ru }}</option>
              </select>
            </div>
          </div>

          <div v-if="form.target === 'selected'" class="mt-3 rounded-xl bg-[var(--content-bg)] p-3">
            <label class="mb-[7px] block text-[12.5px] font-semibold text-[var(--text-secondary)]">ID пользователя</label>
            <input
              v-model="singleUserId" type="text" placeholder="12345" inputmode="numeric"
              class="w-full box-border rounded-[10px] border border-[var(--field-border)] bg-[var(--field-bg)] px-[13px] py-[11px] font-plexmono text-sm text-[var(--text)] outline-none focus:border-[var(--accent)] focus:shadow-[0_0_0_3px_var(--accent-tint)]"
            >
          </div>
        </div>

        <div class="flex gap-3.5">
          <div class="flex-1">
            <label class="mb-[7px] block text-[12.5px] font-semibold text-[var(--text-secondary)]">Тип ссылки</label>
            <select
              v-model="form.link_type"
              class="w-full box-border rounded-[10px] border border-[var(--field-border)] bg-[var(--field-bg)] px-[13px] py-[11px] text-sm text-[var(--text)] outline-none focus:border-[var(--accent)] focus:shadow-[0_0_0_3px_var(--accent-tint)]"
            >
              <option v-for="lt in linkTypes" :key="lt.value" :value="lt.value">{{ lt.label }}</option>
            </select>
          </div>
          <div class="w-40 flex-none">
            <label class="mb-[7px] block text-[12.5px] font-semibold text-[var(--text-secondary)]">ID ссылки</label>
            <input
              v-model="form.link_id" type="text" placeholder="123" :disabled="idDisabled"
              class="w-full box-border rounded-[10px] border border-[var(--field-border)] px-[13px] py-[11px] font-plexmono text-sm outline-none transition-shadow focus:border-[var(--accent)] focus:shadow-[0_0_0_3px_var(--accent-tint)]"
              :style="idDisabled
                ? { background: 'var(--field-disabled-bg)', color: 'var(--text-muted)' }
                : { background: 'var(--field-bg)', color: 'var(--text)' }"
            >
          </div>
        </div>

        <div class="flex items-center justify-end gap-2.5 border-t border-[var(--card-border)] pt-[18px]">
          <button
            type="button" disabled title="Пока не подключено"
            class="cursor-not-allowed rounded-[10px] border border-[var(--field-border)] bg-transparent px-[18px] py-[11px] text-[13.5px] font-semibold text-[var(--text-secondary)] opacity-50"
          >Тестовая отправка</button>
          <button
            @click="send"
            :disabled="sending || !form.title || !form.body"
            class="flex items-center gap-2 rounded-[10px] border-none bg-[var(--accent)] px-5 py-[11px] text-[13.5px] font-bold text-white shadow-[0_10px_22px_-8px_var(--accent)] transition-colors hover:bg-[var(--accent-hover)] disabled:cursor-not-allowed disabled:opacity-50"
          >
            <Icon kind="send" :size="15" />
            {{ sending ? 'Отправка…' : 'Отправить' }}
          </button>
        </div>
      </div>

      <!-- Preview card -->
      <div class="w-[300px] flex-none flex flex-col gap-3.5 rounded-2xl bg-[var(--card-bg)] border border-[var(--card-border)] p-[22px] shadow-[var(--card-shadow)]">
        <div class="text-[13px] font-bold text-[var(--text)]">Предпросмотр</div>
        <div class="flex gap-2.5 rounded-xl bg-[var(--content-bg)] p-3.5">
          <div class="flex h-9 w-9 flex-none items-center justify-center rounded-[10px] bg-[var(--accent)] text-white">
            <Icon kind="bell" :size="17" />
          </div>
          <div class="min-w-0">
            <div class="flex items-baseline justify-between gap-2">
              <span class="text-xs font-bold text-[var(--text)]">Доска объявлений</span>
              <span class="flex-none text-[11px] text-[var(--text-muted)]">сейчас</span>
            </div>
            <div class="mt-[3px] truncate text-[13px] font-bold text-[var(--text)]">{{ form.title || 'Заголовок уведомления' }}</div>
            <div class="mt-0.5 text-[12.5px] leading-[1.4] text-[var(--text-secondary)]">{{ form.body || 'Текст уведомления, который увидит пользователь на экране блокировки.' }}</div>
          </div>
        </div>
        <div class="text-xs leading-[1.5] text-[var(--text-muted)]">Так уведомление будет выглядеть на устройстве получателя.</div>
      </div>
    </div>

    <!-- History -->
    <div id="push-history" class="mt-6 overflow-hidden rounded-2xl bg-[var(--card-bg)] border border-[var(--card-border)] shadow-[var(--card-shadow)]">
      <div class="border-b border-[var(--card-border)] px-5 py-4">
        <h3 class="text-[15px] font-bold text-[var(--text)]">История рассылок</h3>
      </div>
      <div class="divide-y divide-[var(--card-border)]">
        <div
          v-for="p in pushNotifications.data" :key="p.id"
          class="flex items-start gap-3 px-5 py-3"
        >
          <div class="min-w-0 flex-1">
            <div class="truncate text-sm font-semibold text-[var(--text)]">{{ p.title }}</div>
            <div class="line-clamp-1 text-xs text-[var(--text-muted)]">{{ p.body }}</div>
            <div class="mt-0.5 text-[11px] text-[var(--text-muted)]">{{ p.sent_count }} получателей · {{ new Date(p.sent_at).toLocaleString('ru') }}</div>
          </div>
          <span class="flex-none rounded-full bg-[var(--accent-tint)] px-2 py-0.5 text-[11px] font-bold text-[var(--accent)]">
            {{ { all: 'Все', filtered: 'Сегмент', selected: 'Один' }[p.target] ?? p.target }}
          </span>
        </div>
        <div v-if="!pushNotifications.data?.length" class="px-5 py-10 text-center text-sm text-[var(--text-muted)]">
          Уведомлений ещё не отправлялось
        </div>
      </div>
      <div class="border-t border-[var(--card-border)] px-5 py-3">
        <Pagination :links="pushNotifications.links" />
      </div>
    </div>
  </AppLayout>
</template>
