<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import StatCard from '@/Components/StatCard.vue'

const { t } = useI18n()

const props = defineProps({
    stats:       Object,
    tariffStats: Array,
    filters:     Object,
})

const from = ref(props.filters?.from || '')
const to   = ref(props.filters?.to   || '')

function applyFilter() {
    router.get(route('statistics.index'), { from: from.value || undefined, to: to.value || undefined }, { preserveState: true })
}
</script>

<template>
  <AppLayout>
    <template #header>{{ t('nav.statistics') }}</template>

    <div class="space-y-6">
      <!-- Date filter -->
      <div class="flex gap-3 items-end flex-wrap">
        <div>
          <label class="block text-xs font-semibold text-muted mb-1">{{ t('statistics.from') }}</label>
          <input v-model="from" type="date" class="input" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-muted mb-1">{{ t('statistics.to') }}</label>
          <input v-model="to" type="date" class="input" />
        </div>
        <button @click="applyFilter" class="px-4 py-2 rounded-btn bg-blue text-white text-sm font-bold hover:bg-blue/90 transition">
          {{ t('actions.apply') }}
        </button>
      </div>

      <!-- Users -->
      <div>
        <h3 class="text-sm font-extrabold uppercase tracking-widest text-muted mb-3">{{ t('statistics.users') }}</h3>
        <div class="grid gap-4 sm:grid-cols-3">
          <StatCard :label="t('common.total')" :value="stats.users.total" icon="users" color="blue" />
          <StatCard :label="t('statistics.forPeriod')" :value="stats.users.period" icon="users" color="green" />
          <StatCard :label="t('statistics.blocked')" :value="stats.users.blocked" icon="users" color="red" />
        </div>
      </div>

      <!-- Listings -->
      <div>
        <h3 class="text-sm font-extrabold uppercase tracking-widest text-muted mb-3">{{ t('statistics.listings') }}</h3>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
          <StatCard :label="t('common.total')" :value="stats.listings.total" color="blue" />
          <StatCard :label="t('statistics.forPeriod')" :value="stats.listings.period" color="green" />
          <StatCard :label="t('dashboard.onModeration')" :value="stats.listings.pending" color="amber" />
          <StatCard :label="t('statistics.approvedPlural')" :value="stats.listings.approved" color="green" />
          <StatCard :label="t('statistics.rejectedPlural')" :value="stats.listings.rejected" color="red" />
        </div>
      </div>

      <!-- Videos -->
      <div>
        <h3 class="text-sm font-extrabold uppercase tracking-widest text-muted mb-3">{{ t('statistics.videos') }}</h3>
        <div class="grid gap-4 sm:grid-cols-3">
          <StatCard :label="t('common.total')" :value="stats.videos.total" color="blue" />
          <StatCard :label="t('statistics.approvedPlural')" :value="stats.videos.approved" color="green" />
          <StatCard :label="t('statistics.likes')" :value="stats.videos.likes" color="pink" />
        </div>
      </div>

      <!-- Tariffs -->
      <div>
        <h3 class="text-sm font-extrabold uppercase tracking-widest text-muted mb-3">{{ t('statistics.tariffs') }}</h3>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="t in tariffStats" :key="t.name"
            class="rounded-card bg-white dark:bg-dcard border border-line dark:border-dline p-4"
          >
            <div class="flex justify-between items-center mb-2">
              <span class="font-bold text-ink dark:text-slate-100 text-sm">{{ t.name }}</span>
              <span class="text-sm font-extrabold text-blue">{{ t.count }}</span>
            </div>
            <div class="h-2 rounded-full bg-surface dark:bg-dbg overflow-hidden">
              <div class="h-2 rounded-full bg-blue transition-all" :style="{ width: t.pct + '%' }" />
            </div>
            <div class="text-xs text-muted mt-1">{{ t.pct }}%</div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
