<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import Pagination from '@/Components/Pagination.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    reviews:          Object,
    rejectionReasons: Array,
    filters:          Object,
    counts:           Object,
})

const filter       = ref(props.filters?.status || '')
const rejectModal  = ref(false)
const rejectTarget = ref(null)
const rejectReason = ref('')

function applyFilter(s) {
    filter.value = s
    router.get(route('reviews.index'), { status: s || undefined }, { preserveState: true })
}
function approve(r) {
    router.patch(route('reviews.approve', r.id))
}
function openReject(r) {
    rejectTarget.value = r
    rejectReason.value = ''
    rejectModal.value  = true
}
function submitReject() {
    router.patch(route('reviews.reject', rejectTarget.value.id), {
        rejection_reason_id: rejectReason.value,
    }, { onSuccess: () => { rejectModal.value = false } })
}
</script>

<template>
  <AppLayout>
    <template #header>Отзывы</template>

    <div class="space-y-4">
      <div class="flex gap-2 flex-wrap">
        <button
          v-for="tab in [{ key: '', label: 'Все' }, { key: 'pending', label: 'На модерации', count: counts?.pending }, { key: 'approved', label: 'Одобренные', count: counts?.approved }, { key: 'rejected', label: 'Отклонённые', count: counts?.rejected }]"
          :key="tab.key"
          @click="applyFilter(tab.key)"
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-btn text-sm font-bold transition"
          :class="filter === tab.key ? 'bg-blue text-white' : 'bg-white dark:bg-dcard border border-line dark:border-dline text-ink dark:text-slate-200 hover:bg-surface dark:hover:bg-white/5'"
        >
          {{ tab.label }}
          <span v-if="tab.count" class="rounded-full bg-white/20 px-1.5 text-[10px]">{{ tab.count }}</span>
        </button>
      </div>

      <div class="rounded-card bg-white dark:bg-dcard border border-line dark:border-dline overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-surface dark:bg-dbg">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-extrabold text-muted uppercase">Автор</th>
              <th class="px-4 py-3 text-left text-xs font-extrabold text-muted uppercase">Текст</th>
              <th class="px-4 py-3 text-left text-xs font-extrabold text-muted uppercase">Объект</th>
              <th class="px-4 py-3 text-left text-xs font-extrabold text-muted uppercase">Статус</th>
              <th class="px-4 py-3 text-left text-xs font-extrabold text-muted uppercase">Дата</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-line dark:divide-dline">
            <tr v-for="r in reviews.data" :key="r.id" class="hover:bg-surface/50 dark:hover:bg-white/2 transition">
              <td class="px-4 py-3">
                <div class="font-semibold text-ink dark:text-slate-100">{{ r.user?.name }}</div>
                <div class="text-xs text-muted">{{ r.user?.phone }}</div>
              </td>
              <td class="px-4 py-3">
                <div class="max-w-[300px] line-clamp-2 text-ink dark:text-slate-200">{{ r.content }}</div>
              </td>
              <td class="px-4 py-3 text-xs text-muted">{{ r.reviewable_type?.split('\\').pop() }} #{{ r.reviewable_id }}</td>
              <td class="px-4 py-3"><StatusBadge :status="r.status" /></td>
              <td class="px-4 py-3 text-xs text-muted">{{ new Date(r.created_at).toLocaleDateString('ru') }}</td>
              <td class="px-4 py-3">
                <div v-if="r.status === 'pending'" class="flex gap-1.5 justify-end">
                  <button @click="approve(r)" class="px-2.5 py-1 rounded-btn bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-xs font-bold hover:bg-green-100 transition">Одобрить</button>
                  <button @click="openReject(r)" class="px-2.5 py-1 rounded-btn bg-red/5 text-red text-xs font-bold hover:bg-red/10 transition">Отклонить</button>
                </div>
              </td>
            </tr>
            <tr v-if="!reviews.data?.length">
              <td colspan="6" class="px-4 py-10 text-center text-muted text-sm">Отзывов нет</td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination :links="reviews.links" />
    </div>

    <!-- Reject modal -->
    <Modal :show="rejectModal" @close="rejectModal = false">
      <div class="p-6 space-y-4">
        <h3 class="text-base font-extrabold text-ink dark:text-slate-100">Отклонить отзыв</h3>
        <div>
          <label class="block text-sm font-semibold text-ink dark:text-slate-200 mb-1">Причина</label>
          <select v-model="rejectReason" class="input w-full">
            <option value="">— выберите —</option>
            <option v-for="rr in rejectionReasons" :key="rr.id" :value="rr.id">{{ rr.name_ru }}</option>
          </select>
        </div>
        <div class="flex gap-3 justify-end">
          <button @click="rejectModal = false" class="px-4 py-2 rounded-btn border border-line dark:border-dline text-sm font-bold text-ink dark:text-slate-200">Отмена</button>
          <button @click="submitReject" :disabled="!rejectReason" class="px-4 py-2 rounded-btn bg-red text-white text-sm font-bold hover:bg-red/90 disabled:opacity-50 transition">Отклонить</button>
        </div>
      </div>
    </Modal>
  </AppLayout>
</template>
