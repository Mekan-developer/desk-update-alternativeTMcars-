<script setup>
import { Link } from '@inertiajs/vue3'
defineProps({ links: Array })
</script>

<template>
  <div v-if="links?.length > 3" class="flex items-center justify-center gap-1 border-t border-line px-5 py-3.5 dark:border-dline">
    <Link
      v-for="link in links" :key="link.label"
      :href="link.url ?? '#'"
      class="flex h-8 min-w-[32px] items-center justify-center rounded-[7px] px-2.5 text-[12px] font-bold transition"
      :class="link.active
        ? 'bg-blue text-white shadow-[0_2px_8px_rgba(67,97,238,.3)]'
        : link.url
          ? 'text-muted hover:bg-surface dark:hover:bg-dcard'
          : 'text-muted/30 cursor-default'"
      v-html="link.label"
    />
  </div>
</template>
