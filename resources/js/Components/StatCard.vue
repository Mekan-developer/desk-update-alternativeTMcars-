<script setup>
defineProps({
    label:      String,
    value:      [String, Number],
    iconBg:     { type: String, default: 'rgba(67,97,238,.12)' },
    iconColor:  { type: String, default: '#4361ee' },
    sub:        String,
})
</script>

<template>
  <div class="rounded-card bg-white shadow-soft dark:bg-dcard px-5 py-5 flex items-center gap-4">
    <div
      class="flex-shrink-0 flex h-12 w-12 items-center justify-center rounded-[12px]"
      :style="{ background: iconBg }"
    >
      <slot name="icon">
        <span class="text-xl font-bold" :style="{ color: iconColor }">?</span>
      </slot>
    </div>
    <div class="min-w-0">
      <div class="font-data text-[22px] font-black text-ink dark:text-slate-100 leading-none">{{ value ?? '—' }}</div>
      <div class="mt-1 text-[12px] font-semibold text-muted truncate">{{ label }}</div>
      <div v-if="sub" class="mt-0.5 text-[11px] text-muted/70">{{ sub }}</div>
    </div>
  </div>
</template>
