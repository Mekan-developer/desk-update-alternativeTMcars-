<script setup>
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const { t } = useI18n()

defineProps({ dialogs: Object })

function formatDate(d) {
    if (!d) return '—'
    return new Date(d).toLocaleDateString('ru', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' })
}
</script>

<template>
  <AppLayout>
    <template #header>{{ t('nav.chat') }}</template>

    <div class="rounded-card bg-white shadow-soft dark:bg-dcard overflow-hidden">
      <div class="px-[22px] py-[18px] border-b border-line dark:border-dline">
        <span class="text-[15px] font-extrabold text-ink dark:text-slate-100">{{ t('chat.dialogsWithUsers') }}</span>
      </div>
      <div class="divide-y divide-line dark:divide-dline">
        <Link
          v-for="dialog in dialogs.data" :key="dialog.id"
          :href="route('chat.show', dialog.id)"
          class="flex cursor-pointer items-center gap-3 px-5 py-4 transition hover:bg-surface dark:hover:bg-white/5"
          :class="dialog.unread_count > 0 ? 'bg-blue/5 dark:bg-blue/10' : ''"
        >
          <div class="h-10 w-10 rounded-full bg-blue flex items-center justify-center text-[14px] font-extrabold text-white flex-shrink-0">
            {{ (dialog.name || dialog.phone || '?').charAt(0).toUpperCase() }}
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
              <span class="text-[13px] font-bold text-ink dark:text-slate-200">{{ dialog.name || dialog.phone }}</span>
              <span class="text-[11px] font-data text-muted flex-shrink-0">{{ formatDate(dialog.messages?.[0]?.created_at) }}</span>
            </div>
            <p class="mt-0.5 truncate text-[12px] text-muted">{{ dialog.messages?.[0]?.text || t('chat.noMessages') }}</p>
          </div>
          <span
            v-if="dialog.unread_count > 0"
            class="flex h-5 min-w-[20px] items-center justify-center rounded-full bg-blue text-[10px] font-extrabold text-white px-1 flex-shrink-0"
          >{{ dialog.unread_count }}</span>
        </Link>
        <div v-if="!dialogs.data?.length" class="px-5 py-10 text-center text-[13px] text-muted">
          {{ t('chat.noDialogs') }}
        </div>
      </div>
      <Pagination :links="dialogs.links" />
    </div>
  </AppLayout>
</template>
