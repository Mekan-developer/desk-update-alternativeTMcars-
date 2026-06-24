<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
    pushNotifications: Object,
    regions:           Array,
    tariffs:           Array,
})

const form = ref({
    title:     '',
    body:      '',
    target:    'all',
    link_type: '',
    link_id:   '',
    filters: { region_id: '', tariff_id: '' },
})

const sending = ref(false)

function send() {
    sending.value = true
    router.post(route('push.send'), form.value, {
        onFinish: () => { sending.value = false },
        onSuccess: () => {
            form.value = { title: '', body: '', target: 'all', link_type: '', link_id: '', filters: { region_id: '', tariff_id: '' } }
        },
    })
}
</script>

<template>
  <AppLayout>
    <template #header>Push-уведомления</template>

    <div class="grid gap-6 lg:grid-cols-2">
      <!-- Send form -->
      <div class="rounded-card bg-white dark:bg-dcard border border-line dark:border-dline p-6 space-y-4">
        <h3 class="text-base font-extrabold text-ink dark:text-slate-100">Отправить уведомление</h3>

        <div>
          <label class="block text-xs font-semibold text-muted mb-1">Заголовок</label>
          <input v-model="form.title" class="input w-full" placeholder="Заголовок уведомления" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-muted mb-1">Текст</label>
          <textarea v-model="form.body" rows="3" class="input w-full resize-none" placeholder="Текст уведомления" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-muted mb-1">Получатели</label>
          <select v-model="form.target" class="input w-full">
            <option value="all">Все пользователи</option>
            <option value="filtered">По фильтру</option>
          </select>
        </div>

        <template v-if="form.target === 'filtered'">
          <div class="grid grid-cols-2 gap-3 p-3 rounded-[10px] bg-surface dark:bg-dbg">
            <div>
              <label class="block text-xs font-semibold text-muted mb-1">Регион</label>
              <select v-model="form.filters.region_id" class="input w-full">
                <option value="">Все регионы</option>
                <option v-for="r in regions" :key="r.id" :value="r.id">{{ r.name_ru }}</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-muted mb-1">Тариф</label>
              <select v-model="form.filters.tariff_id" class="input w-full">
                <option value="">Все тарифы</option>
                <option v-for="t in tariffs" :key="t.id" :value="t.id">{{ t.name_ru }}</option>
              </select>
            </div>
          </div>
        </template>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-muted mb-1">Тип ссылки</label>
            <select v-model="form.link_type" class="input w-full">
              <option value="">— нет —</option>
              <option value="listing">Объявление</option>
              <option value="user">Пользователь</option>
              <option value="news">Новость</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-muted mb-1">ID ссылки</label>
            <input v-model="form.link_id" type="number" class="input w-full" placeholder="123" />
          </div>
        </div>

        <button
          @click="send"
          :disabled="sending || !form.title || !form.body"
          class="w-full py-2.5 rounded-btn bg-blue text-white font-bold text-sm hover:bg-blue/90 disabled:opacity-50 transition"
        >
          {{ sending ? 'Отправка...' : 'Отправить' }}
        </button>
      </div>

      <!-- History -->
      <div class="rounded-card bg-white dark:bg-dcard border border-line dark:border-dline overflow-hidden">
        <div class="px-5 py-4 border-b border-line dark:border-dline">
          <h3 class="text-base font-extrabold text-ink dark:text-slate-100">История отправок</h3>
        </div>
        <div class="divide-y divide-line dark:divide-dline">
          <div
            v-for="p in pushNotifications.data" :key="p.id"
            class="px-5 py-3 flex items-start gap-3"
          >
            <div class="flex-1 min-w-0">
              <div class="font-semibold text-ink dark:text-slate-100 text-sm truncate">{{ p.title }}</div>
              <div class="text-xs text-muted line-clamp-1">{{ p.body }}</div>
              <div class="text-[11px] text-muted mt-0.5">{{ p.sent_count }} получателей · {{ new Date(p.sent_at).toLocaleString('ru') }}</div>
            </div>
            <span class="flex-shrink-0 text-[11px] px-2 py-0.5 rounded-full font-bold bg-blue/10 text-blue">{{ p.target === 'all' ? 'Все' : 'Фильтр' }}</span>
          </div>
          <div v-if="!pushNotifications.data?.length" class="px-5 py-10 text-center text-muted text-sm">
            Уведомлений ещё не отправлялось
          </div>
        </div>
        <div class="px-5 py-3 border-t border-line dark:border-dline">
          <Pagination :links="pushNotifications.links" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
