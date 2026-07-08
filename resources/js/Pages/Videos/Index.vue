<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import SearchInput from '@/Components/SearchInput.vue'

const { t } = useI18n()

const props = defineProps({
    videos: Object, rejectionReasons: Array, filters: Object, counts: Object,
})

const search    = ref(props.filters?.search || '')
const statusFil = ref(props.filters?.status || '')

const tabs = computed(() => [
    { label: t('listings.tabAll'), value: '' },
    { label: t('listings.tabPending', { n: props.counts.pending }), value: 'pending' },
    { label: t('listings.tabApproved', { n: props.counts.approved }), value: 'approved' },
    { label: t('listings.tabRejected', { n: props.counts.rejected }), value: 'rejected' },
])

function applyFilters() {
    router.get(route('videos.index'), { search: search.value, status: statusFil.value }, { preserveState: true, replace: true })
}

function setStatus(s) { statusFil.value = s; applyFilters() }
function approve(id) { router.patch(route('videos.approve', id)) }

const rejectTarget = ref(null)
const rejectReason = ref('')

function openReject(video) { rejectTarget.value = video; rejectReason.value = '' }
function doReject() {
    if (!rejectReason.value) return
    router.patch(route('videos.reject', rejectTarget.value.id), { rejection_reason_id: rejectReason.value }, {
        onSuccess: () => { rejectTarget.value = null },
    })
}

function formatDate(d) {
    if (!d) return '—'
    return new Date(d).toLocaleDateString('ru', { day: '2-digit', month: '2-digit', year: '2-digit' })
}
</script>

<template>
  <AppLayout>
    <template #header>{{ t('nav.videos') }}</template>

    <!-- Tabs -->
    <div class="mb-4 flex gap-2 flex-wrap">
      <button v-for="tab in tabs" :key="tab.value"
        @click="setStatus(tab.value)"
        class="rounded-btn px-4 py-2 text-[13px] font-bold transition"
        :class="statusFil === tab.value ? 'bg-blue text-white' : 'bg-white text-muted hover:text-ink shadow-soft dark:bg-dcard dark:hover:text-slate-200'"
      >{{ tab.label }}</button>
    </div>

    <div class="mb-4">
      <SearchInput v-model="search" @submit="applyFilters" :placeholder="t('videos.searchPlaceholder')" class="w-64" />
    </div>

    <div class="rounded-card bg-white shadow-soft dark:bg-dcard overflow-hidden">
      <table class="w-full">
        <thead class="bg-surface/50 dark:bg-dbg/50">
          <tr>
            <th class="px-4 py-[11px] text-left text-[11px] font-bold uppercase tracking-[.07em] text-muted border-b-2 border-line dark:border-dline">{{ t('videos.colVideo') }}</th>
            <th class="px-4 py-[11px] text-left text-[11px] font-bold uppercase tracking-[.07em] text-muted border-b-2 border-line dark:border-dline">{{ t('common.author') }}</th>
            <th class="px-4 py-[11px] text-left text-[11px] font-bold uppercase tracking-[.07em] text-muted border-b-2 border-line dark:border-dline">{{ t('videos.likes') }}</th>
            <th class="px-4 py-[11px] text-left text-[11px] font-bold uppercase tracking-[.07em] text-muted border-b-2 border-line dark:border-dline">{{ t('common.views') }}</th>
            <th class="px-4 py-[11px] text-left text-[11px] font-bold uppercase tracking-[.07em] text-muted border-b-2 border-line dark:border-dline">{{ t('common.status') }}</th>
            <th class="px-4 py-[11px] text-left text-[11px] font-bold uppercase tracking-[.07em] text-muted border-b-2 border-line dark:border-dline">{{ t('common.date') }}</th>
            <th class="px-4 py-[11px] text-left text-[11px] font-bold uppercase tracking-[.07em] text-muted border-b-2 border-line dark:border-dline">{{ t('common.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="video in videos.data" :key="video.id" class="hover:bg-surface/30 dark:hover:bg-white/3 transition">
            <td class="px-4 py-[13px] text-[13px] border-b border-line dark:border-dline">
              <div class="flex items-center gap-3">
                <div class="h-10 w-16 rounded-[7px] overflow-hidden flex-shrink-0 bg-navy flex items-center justify-center">
                  <svg class="h-5 w-5 text-white/40" fill="currentColor" viewBox="0 0 24 24"><path d="M5 3l14 9-14 9V3z"/></svg>
                </div>
                <span class="font-bold text-ink dark:text-slate-200 line-clamp-1">{{ video.title }}</span>
              </div>
            </td>
            <td class="px-4 py-[13px] text-[13px] border-b border-line dark:border-dline text-muted">{{ video.user?.name || video.user?.phone || '—' }}</td>
            <td class="px-4 py-[13px] text-[13px] border-b border-line dark:border-dline font-data text-muted">{{ video.likes_count }}</td>
            <td class="px-4 py-[13px] text-[13px] border-b border-line dark:border-dline font-data text-muted">{{ video.views }}</td>
            <td class="px-4 py-[13px] text-[13px] border-b border-line dark:border-dline"><StatusBadge :status="video.status" /></td>
            <td class="px-4 py-[13px] text-[13px] border-b border-line dark:border-dline font-data text-muted">{{ formatDate(video.created_at) }}</td>
            <td class="px-4 py-[13px] text-[13px] border-b border-line dark:border-dline">
              <div class="flex items-center gap-1.5">
                <button v-if="video.status === 'pending'" @click="approve(video.id)" class="flex h-[30px] w-[30px] items-center justify-center rounded-[7px] bg-green/10 text-green transition hover:bg-green hover:text-white" :title="t('actions.approve')">
                  <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline stroke-width="2" stroke-linecap="round" points="20 6 9 17 4 12"/></svg>
                </button>
                <button v-if="video.status !== 'rejected'" @click="openReject(video)" class="flex h-[30px] w-[30px] items-center justify-center rounded-[7px] bg-red/10 text-red transition hover:bg-red hover:text-white" :title="t('actions.reject')">
                  <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18" stroke-width="2" stroke-linecap="round"/><line x1="6" y1="6" x2="18" y2="18" stroke-width="2" stroke-linecap="round"/></svg>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="!videos.data?.length"><td colspan="7" class="px-4 py-10 text-center text-[13px] text-muted">{{ t('videos.notFound') }}</td></tr>
        </tbody>
      </table>
      <Pagination :links="videos.links" />
    </div>

    <!-- Reject modal -->
    <div v-if="rejectTarget" class="fixed inset-0 z-[600] flex items-center justify-center bg-black/40 backdrop-blur-sm" @click.self="rejectTarget = null">
      <div class="w-[440px] rounded-card bg-white p-6 shadow-[0_24px_48px_rgba(0,0,0,.18)] dark:bg-dcard">
        <h3 class="mb-4 text-[17px] font-extrabold text-ink dark:text-slate-100">{{ t('listings.rejectTitle') }}</h3>
        <div class="space-y-1 mb-5">
          <label v-for="r in rejectionReasons" :key="r.id" class="flex items-center gap-3 cursor-pointer rounded-btn p-3 hover:bg-surface dark:hover:bg-white/5 transition">
            <input type="radio" :value="r.id" v-model="rejectReason" class="accent-blue" />
            <span class="text-[13px] font-semibold text-ink dark:text-slate-200">{{ r.name_ru }}</span>
          </label>
        </div>
        <div class="flex gap-2.5">
          <button @click="rejectTarget = null" class="flex-1 rounded-btn border-2 border-line py-[11px] text-[13px] font-bold text-muted hover:border-blue hover:text-blue transition dark:border-dline">{{ t('actions.cancel') }}</button>
          <button @click="doReject" :disabled="!rejectReason" class="flex-1 rounded-btn bg-red py-[11px] text-[13px] font-bold text-white hover:opacity-90 disabled:opacity-40 transition">{{ t('actions.reject') }}</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
