<script setup>
import { ref, nextTick, onMounted, onUnmounted } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'

const { t } = useI18n()

const props = defineProps({ chatUser: Object, messages: Array, dialogs: Array })

const replyText     = ref('')
const messagesEl    = ref(null)
const localMessages = ref([...(props.messages ?? [])])

onMounted(() => {
    scrollToBottom()

    window.Echo.private(`chat.${props.chatUser.id}`).listen('.new-message', (e) => {
        if (e.sender !== 'user') return
        localMessages.value.push(e)
        scrollToBottom()
        router.patch(route('chat.read', props.chatUser.id), {}, { preserveScroll: true, preserveState: true, only: [] })
    })
})

onUnmounted(() => {
    window.Echo.leave(`chat.${props.chatUser.id}`)
})

function scrollToBottom() {
    nextTick(() => {
        if (messagesEl.value) {
            messagesEl.value.scrollTop = messagesEl.value.scrollHeight
        }
    })
}

function sendReply() {
    if (!replyText.value.trim()) return
    router.post(route('chat.reply', props.chatUser.id), { text: replyText.value }, {
        onSuccess: () => { replyText.value = ''; scrollToBottom() },
        preserveState: false,
    })
}

function formatTime(d) {
    if (!d) return ''
    return new Date(d).toLocaleTimeString('ru', { hour: '2-digit', minute: '2-digit' })
}
function formatDate2(d) {
    if (!d) return '—'
    return new Date(d).toLocaleDateString('ru', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' })
}
</script>

<template>
  <AppLayout>
    <template #header>{{ t('nav.chat') }}</template>

    <div class="flex h-[calc(100vh-136px)] rounded-card overflow-hidden shadow-soft">
      <!-- Dialogs sidebar -->
      <div class="w-[300px] flex-shrink-0 bg-white border-r border-line dark:bg-dcard dark:border-dline overflow-y-auto">
        <div class="px-4 py-3 border-b border-line dark:border-dline">
          <span class="text-[13px] font-extrabold text-ink dark:text-slate-100">{{ t('chat.dialogs') }}</span>
        </div>
        <div class="divide-y divide-line dark:divide-dline">
          <Link
            v-for="d in dialogs" :key="d.id"
            :href="route('chat.show', d.id)"
            class="flex items-center gap-2.5 px-4 py-3 transition hover:bg-surface dark:hover:bg-white/5"
            :class="d.id === chatUser.id ? 'bg-blue/5 dark:bg-blue/10' : ''"
          >
            <div class="h-8 w-8 rounded-full bg-blue flex items-center justify-center text-[11px] font-extrabold text-white flex-shrink-0">
              {{ (d.name || d.phone || '?').charAt(0).toUpperCase() }}
            </div>
            <div class="flex-1 min-w-0">
              <div class="text-[12px] font-bold text-ink dark:text-slate-200 truncate">{{ d.name || d.phone }}</div>
              <div class="text-[11px] text-muted truncate">{{ d.messages?.[0]?.text || '' }}</div>
            </div>
            <span v-if="d.unread_count > 0" class="h-4 min-w-[16px] rounded-full bg-blue text-[9px] font-extrabold text-white flex items-center justify-center px-1">{{ d.unread_count }}</span>
          </Link>
        </div>
      </div>

      <!-- Chat area -->
      <div class="flex flex-1 flex-col bg-white dark:bg-dcard">
        <!-- Header -->
        <div class="flex items-center gap-3 border-b border-line dark:border-dline px-5 py-3.5 flex-shrink-0">
          <div class="h-9 w-9 rounded-full bg-blue flex items-center justify-center text-[13px] font-extrabold text-white">
            {{ (chatUser.name || chatUser.phone || '?').charAt(0).toUpperCase() }}
          </div>
          <div>
            <div class="text-[14px] font-extrabold text-ink dark:text-slate-100">{{ chatUser.name || '—' }}</div>
            <div class="text-[12px] font-data text-muted">{{ chatUser.phone }}</div>
          </div>
          <div class="ml-auto">
            <Link :href="route('users.show', chatUser.id)" class="text-[12px] font-bold text-blue hover:underline">{{ t('chat.profileLink') }}</Link>
          </div>
        </div>

        <!-- Messages -->
        <div ref="messagesEl" class="flex-1 overflow-y-auto p-5 space-y-3">
          <div
            v-for="msg in localMessages" :key="msg.id"
            class="flex"
            :class="msg.sender === 'admin' ? 'justify-end' : 'justify-start'"
          >
            <div
              class="max-w-[70%] rounded-card px-4 py-2.5 text-[13px] font-semibold"
              :class="msg.sender === 'admin'
                ? 'bg-blue text-white'
                : 'bg-surface text-ink dark:bg-dbg dark:text-slate-200'"
            >
              {{ msg.text }}
              <div class="mt-1 text-[10px] opacity-60">{{ formatTime(msg.created_at) }}</div>
            </div>
          </div>
          <div v-if="!localMessages?.length" class="text-center text-[13px] text-muted py-8">{{ t('chat.noMessages') }}</div>
        </div>

        <!-- Input -->
        <div class="flex-shrink-0 flex gap-2 border-t border-line dark:border-dline p-4">
          <input
            v-model="replyText"
            @keydown.enter.prevent="sendReply"
            :placeholder="t('chat.inputPlaceholder')"
            class="flex-1 rounded-btn border-2 border-line bg-surface px-4 py-2.5 text-[13px] font-semibold outline-none focus:border-blue dark:bg-dbg dark:border-dline dark:text-slate-200 transition"
          />
          <button
            @click="sendReply"
            class="rounded-btn bg-blue px-5 py-2.5 text-[13px] font-bold text-white hover:bg-blue-dark transition"
          >{{ t('actions.send') }}</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
