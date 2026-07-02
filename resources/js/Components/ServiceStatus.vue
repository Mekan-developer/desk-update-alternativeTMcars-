<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

defineProps({ collapsed: { type: Boolean, default: false } })

const horizon = ref(null)
const reverb  = ref(null)
let timer = null

async function check() {
    try {
        const res  = await fetch('/status', { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
        const data = await res.json()
        horizon.value = !!data.horizon
        reverb.value  = !!data.reverb
    } catch {
        horizon.value = false
        reverb.value  = false
    }
}

onMounted(() => { check(); timer = setInterval(check, 30_000) })
onUnmounted(() => clearInterval(timer))

function dotColor(val) {
    if (val === null) return '#fb8500'
    return val ? 'var(--status-ok)' : 'var(--status-bad)'
}
</script>

<template>
  <div class="flex flex-col" :class="collapsed ? 'items-center gap-2 py-1' : ''">
    <!-- Очереди -->
    <a
      href="/horizon" target="_blank" rel="noopener"
      class="flex items-center gap-2.5 rounded-lg text-[13.5px] font-medium text-[var(--sidebar-text)] hover:bg-[var(--nav-hover)] transition-colors"
      :style="{ padding: collapsed ? '8px' : '8px 10px' }"
    >
      <span class="relative flex h-2 w-2 flex-none rounded-full dark:shadow-[0_0_6px_currentColor]" :style="{ backgroundColor: dotColor(horizon), color: dotColor(horizon) }"></span>
      <span v-if="!collapsed">Очереди</span>
    </a>

    <!-- WS -->
    <div
      class="flex items-center gap-2.5 rounded-lg text-[13.5px] font-medium text-[var(--sidebar-text)]"
      :style="{ padding: collapsed ? '8px' : '8px 10px' }"
    >
      <span class="relative flex h-2 w-2 flex-none rounded-full dark:shadow-[0_0_6px_currentColor]" :style="{ backgroundColor: dotColor(reverb), color: dotColor(reverb) }"></span>
      <span v-if="!collapsed">WS</span>
    </div>
  </div>
</template>
