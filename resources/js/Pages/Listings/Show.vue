<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'

const { t } = useI18n()

const props = defineProps({ listing: Object, rejectionReasons: Array })

const rejectReason = ref('')
const showRejectModal = ref(false)

function approve() {
    router.patch(route('listings.approve', props.listing.id))
}

function doReject() {
    if (!rejectReason.value) return
    router.patch(route('listings.reject', props.listing.id), { rejection_reason_id: rejectReason.value }, {
        onSuccess: () => { showRejectModal.value = false },
    })
}

function formatDate(d) {
    if (!d) return '—'
    return new Date(d).toLocaleDateString('ru', { day: '2-digit', month: '2-digit', year: '2-digit', hour: '2-digit', minute: '2-digit' })
}
function categoryPath(category) {
    if (!category) return '—'
    const chain = []
    let c = category
    while (c) { chain.unshift(c.name_ru); c = c.parent }
    return chain.join(' → ')
}

const activePhoto = ref(0)
</script>

<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center gap-2 text-[14px]">
        <Link :href="route('listings.index')" class="text-muted hover:text-blue transition">{{ t('nav.listings') }}</Link>
        <span class="text-muted">/</span>
        <span class="text-ink dark:text-slate-100 max-w-[300px] truncate">{{ listing.title }}</span>
      </div>
    </template>

    <div class="grid gap-5" style="grid-template-columns: 1fr 360px;">
      <!-- Main content -->
      <div class="space-y-5">
        <!-- Media -->
        <div class="rounded-card bg-white shadow-soft dark:bg-dcard p-5">
          <h3 class="text-[15px] font-extrabold text-ink dark:text-slate-100 mb-4">{{ t('listings.media') }}</h3>
          <div v-if="listing.media?.length">
            <div class="rounded-[12px] overflow-hidden bg-surface dark:bg-dbg aspect-video mb-3">
              <img :src="`/storage/${listing.media[activePhoto]?.path}`" class="w-full h-full object-contain" :alt="listing.title" />
            </div>
            <div class="flex gap-2 flex-wrap">
              <button v-for="(m, i) in listing.media" :key="m.id" @click="activePhoto = i"
                class="h-14 w-14 rounded-[7px] overflow-hidden border-2 transition"
                :class="activePhoto === i ? 'border-blue' : 'border-line dark:border-dline'">
                <img :src="`/storage/${m.path}`" class="h-full w-full object-cover" />
              </button>
            </div>
          </div>
          <div v-else class="aspect-video rounded-[12px] bg-surface dark:bg-dbg flex items-center justify-center">
            <svg class="h-10 w-10 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"/><circle cx="8.5" cy="8.5" r="1.5" stroke-width="2"/><polyline stroke-width="2" points="21 15 16 10 5 21"/></svg>
          </div>
        </div>

        <!-- Details -->
        <div class="rounded-card bg-white shadow-soft dark:bg-dcard p-5">
          <h3 class="text-[15px] font-extrabold text-ink dark:text-slate-100 mb-4">{{ t('listings.info') }}</h3>
          <div class="grid grid-cols-2 gap-4 text-[13px]">
            <div><span class="text-muted font-semibold">{{ t('common.title') }}:</span><br><span class="font-bold text-ink dark:text-slate-200">{{ listing.title }}</span></div>
            <div><span class="text-muted font-semibold">{{ t('common.type') }}:</span><br><span class="font-bold text-ink dark:text-slate-200">{{ listing.type === 'goods' ? t('listings.product') : t('listings.service') }}</span></div>
            <div><span class="text-muted font-semibold">{{ t('common.price') }}:</span><br><span class="font-data font-bold text-ink dark:text-slate-200">{{ listing.price ? Number(listing.price).toLocaleString('ru') + ' TMT' : t('listings.negotiable') }}</span></div>
            <div><span class="text-muted font-semibold">{{ t('common.category') }}:</span><br><span class="font-bold text-ink dark:text-slate-200">{{ categoryPath(listing.category) }}</span></div>
            <div><span class="text-muted font-semibold">{{ t('common.region') }}:</span><br><span class="font-bold text-ink dark:text-slate-200">{{ listing.region?.name_ru || '—' }}, {{ listing.city?.name_ru || '—' }}</span></div>
            <div><span class="text-muted font-semibold">{{ t('common.phone') }}:</span><br><span class="font-data font-bold text-ink dark:text-slate-200">{{ listing.phone }}</span></div>
            <div><span class="text-muted font-semibold">{{ t('common.views') }}:</span><br><span class="font-data font-bold text-ink dark:text-slate-200">{{ listing.views || 0 }}</span></div>
            <div><span class="text-muted font-semibold">{{ t('common.date') }}:</span><br><span class="font-data font-bold text-ink dark:text-slate-200">{{ formatDate(listing.created_at) }}</span></div>
          </div>
          <div v-if="listing.description" class="mt-4 pt-4 border-t border-line dark:border-dline">
            <p class="text-[12px] font-semibold text-muted mb-1">{{ t('common.description') }}:</p>
            <p class="text-[13px] text-ink dark:text-slate-200">{{ listing.description }}</p>
          </div>
          <div v-if="listing.tags?.length" class="mt-3 flex flex-wrap gap-1.5">
            <span v-for="tag in listing.tags" :key="tag" class="rounded-pill bg-blue-light px-2.5 py-0.5 text-[11px] font-bold text-blue">#{{ tag }}</span>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-4">
        <!-- Author -->
        <div class="rounded-card bg-white shadow-soft dark:bg-dcard p-5">
          <h3 class="text-[13px] font-extrabold text-ink dark:text-slate-100 mb-3 uppercase tracking-wide">{{ t('common.author') }}</h3>
          <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-full bg-blue flex items-center justify-center text-[14px] font-extrabold text-white flex-shrink-0">
              {{ (listing.user?.name || listing.user?.phone || '?').charAt(0).toUpperCase() }}
            </div>
            <div>
              <div class="text-[13px] font-bold text-ink dark:text-slate-200">{{ listing.user?.name || '—' }}</div>
              <div class="text-[12px] font-data text-muted">{{ listing.user?.phone }}</div>
            </div>
          </div>
          <div class="mt-3">
            <Link :href="route('users.show', listing.user_id)" class="text-[12px] font-bold text-blue hover:underline">{{ t('listings.profileLink') }}</Link>
          </div>
        </div>

        <!-- Moderation -->
        <div class="rounded-card bg-white shadow-soft dark:bg-dcard p-5">
          <h3 class="text-[13px] font-extrabold text-ink dark:text-slate-100 mb-3 uppercase tracking-wide">{{ t('listings.moderation') }}</h3>
          <div class="mb-3"><StatusBadge :status="listing.status" /></div>
          <div v-if="listing.rejection_reason" class="mb-3 rounded-btn bg-red/10 p-3 text-[12px] font-semibold text-red">
            {{ listing.rejection_reason.name_ru }}
          </div>
          <div class="space-y-2">
            <button v-if="listing.status !== 'approved'" @click="approve"
              class="w-full rounded-btn bg-green/10 border-2 border-green/20 py-[9px] text-[13px] font-bold text-green hover:bg-green hover:text-white transition">
              {{ t('listings.approveBtn') }}
            </button>
            <button @click="showRejectModal = true"
              class="w-full rounded-btn bg-red/10 border-2 border-red/20 py-[9px] text-[13px] font-bold text-red hover:bg-red hover:text-white transition">
              {{ t('listings.rejectBtn') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Reject modal -->
    <div v-if="showRejectModal" class="fixed inset-0 z-[600] flex items-center justify-center bg-black/40 backdrop-blur-sm" @click.self="showRejectModal = false">
      <div class="w-[440px] rounded-card bg-white p-6 shadow-[0_24px_48px_rgba(0,0,0,.18)] dark:bg-dcard">
        <h3 class="mb-4 text-[17px] font-extrabold text-ink dark:text-slate-100">{{ t('listings.rejectTitle') }}</h3>
        <div class="space-y-1 mb-5">
          <label v-for="r in rejectionReasons" :key="r.id" class="flex items-center gap-3 cursor-pointer rounded-btn p-3 hover:bg-surface dark:hover:bg-white/5 transition">
            <input type="radio" :value="r.id" v-model="rejectReason" class="accent-blue" />
            <span class="text-[13px] font-semibold text-ink dark:text-slate-200">{{ r.name_ru }}</span>
          </label>
        </div>
        <div class="flex gap-2.5">
          <button @click="showRejectModal = false" class="flex-1 rounded-btn border-2 border-line py-[11px] text-[13px] font-bold text-muted hover:border-blue hover:text-blue transition dark:border-dline">{{ t('actions.cancel') }}</button>
          <button @click="doReject" :disabled="!rejectReason" class="flex-1 rounded-btn bg-red py-[11px] text-[13px] font-bold text-white hover:opacity-90 disabled:opacity-40 transition">{{ t('actions.reject') }}</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
