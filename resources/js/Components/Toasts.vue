<script setup>
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

const toasts = ref([])

watch(() => usePage().props.flash?.toast, (t) => {
    if (!t) return
    const id = Date.now()
    toasts.value.push({ ...t, id })
    setTimeout(() => { toasts.value = toasts.value.filter(x => x.id !== id) }, 3500)
}, { immediate: true })

function dismiss(id) {
    toasts.value = toasts.value.filter(x => x.id !== id)
}

const icons = { success: '✓', error: '✕', info: 'ℹ', warning: '⚠' }
const bgMap  = { success: 'bg-green', error: 'bg-red', info: 'bg-blue', warning: 'bg-orange' }
</script>

<template>
  <Teleport to="body">
    <div class="fixed bottom-6 right-6 z-[1000] flex flex-col gap-2.5 pointer-events-none">
      <TransitionGroup name="toast">
        <div
          v-for="t in toasts" :key="t.id"
          @click="dismiss(t.id)"
          class="flex min-w-[280px] items-center gap-3 rounded-card px-4 py-3.5 text-[13px] font-bold text-white shadow-lg2 cursor-pointer pointer-events-auto"
          :class="bgMap[t.type] || 'bg-blue'"
        >
          <span class="text-lg flex-shrink-0">{{ icons[t.type] || 'ℹ' }}</span>
          <span class="flex-1">{{ t.message }}</span>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<style scoped>
.toast-enter-active { transition: all .3s ease; }
.toast-leave-active { transition: all .25s ease; }
.toast-enter-from   { opacity: 0; transform: translateX(100%); }
.toast-leave-to     { opacity: 0; transform: translateX(30px); }
</style>
