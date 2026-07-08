<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import Pagination from '@/Components/Pagination.vue'

const { t } = useI18n()

const props = defineProps({
    complaints: Object,
    reasons:    Array,
    filters:    Object,
    counts:     Object,
})

const filter = ref(props.filters?.status || '')

const tabs = computed(() => [
    { key: '', label: t('common.all') },
    { key: 'new', label: t('complaints.tabPending'), count: props.counts?.pending },
    { key: 'resolved', label: t('complaints.tabResolved'), count: props.counts?.resolved },
])

function applyFilter(s) {
    filter.value = s
    router.get(route('complaints.index'), { status: s || undefined }, { preserveState: true })
}
function resolve(c) {
    if (confirm(t('complaints.confirmResolve'))) {
        router.patch(route('complaints.resolve', c.id), { status: 'resolved' })
    }
}
</script>

<template>
  <AppLayout>
    <template #header>{{ t('nav.complaints') }}</template>

    <div class="space-y-4">
      <!-- Status tabs -->
      <div class="flex gap-2 flex-wrap">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          @click="applyFilter(tab.key)"
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-btn text-sm font-bold transition"
          :class="filter === tab.key ? 'bg-blue text-white' : 'bg-white dark:bg-dcard border border-line dark:border-dline text-ink dark:text-slate-200 hover:bg-surface dark:hover:bg-white/5'"
        >
          {{ tab.label }}
          <span v-if="tab.count" class="rounded-full bg-white/20 px-1.5 text-[10px]">{{ tab.count }}</span>
        </button>
      </div>

      <!-- Table -->
      <div class="rounded-card bg-white dark:bg-dcard border border-line dark:border-dline overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-surface dark:bg-dbg">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-extrabold text-muted uppercase">{{ t('users.colUser') }}</th>
              <th class="px-4 py-3 text-left text-xs font-extrabold text-muted uppercase">{{ t('listings.colListing') }}</th>
              <th class="px-4 py-3 text-left text-xs font-extrabold text-muted uppercase">{{ t('complaints.colReason') }}</th>
              <th class="px-4 py-3 text-left text-xs font-extrabold text-muted uppercase">{{ t('common.status') }}</th>
              <th class="px-4 py-3 text-left text-xs font-extrabold text-muted uppercase">{{ t('common.date') }}</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-line dark:divide-dline">
            <tr v-for="c in complaints.data" :key="c.id" class="hover:bg-surface/50 dark:hover:bg-white/2 transition">
              <td class="px-4 py-3">
                <div class="font-semibold text-ink dark:text-slate-100">{{ c.user?.name }}</div>
                <div class="text-xs text-muted">{{ c.user?.phone }}</div>
              </td>
              <td class="px-4 py-3">
                <div class="max-w-[200px] truncate text-ink dark:text-slate-200">{{ c.listing?.title || '—' }}</div>
              </td>
              <td class="px-4 py-3 text-muted">{{ c.complaint_reason?.name_ru || '—' }}</td>
              <td class="px-4 py-3">
                <StatusBadge :status="c.status" />
              </td>
              <td class="px-4 py-3 text-xs text-muted">{{ new Date(c.created_at).toLocaleDateString('ru') }}</td>
              <td class="px-4 py-3 text-right">
                <button
                  v-if="c.status !== 'resolved'"
                  @click="resolve(c)"
                  class="px-3 py-1 rounded-btn bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-xs font-bold hover:bg-green-100 transition"
                >
                  {{ t('complaints.resolveBtn') }}
                </button>
              </td>
            </tr>
            <tr v-if="!complaints.data?.length">
              <td colspan="6" class="px-4 py-10 text-center text-muted text-sm">{{ t('complaints.empty') }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination :links="complaints.links" />
    </div>
  </AppLayout>
</template>
