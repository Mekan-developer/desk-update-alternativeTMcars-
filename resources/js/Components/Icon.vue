<script setup>
import { computed } from 'vue'

const props = defineProps({
    kind: { type: String, required: true },
    size: { type: [Number, String], default: 18 },
})

// Path data copied verbatim from design_handoff_admin_ui_redesign/Icon.dc.html (ICONS object).
const ICONS = {
    grid: [
        { isRect: true, x: 3, y: 3, w: 7, h: 7, rx: 1.5 },
        { isRect: true, x: 14, y: 3, w: 7, h: 7, rx: 1.5 },
        { isRect: true, x: 14, y: 14, w: 7, h: 7, rx: 1.5 },
        { isRect: true, x: 3, y: 14, w: 7, h: 7, rx: 1.5 },
    ],
    users: [
        { isPath: true, d: 'M17 21v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1' },
        { isCircle: true, cx: 9, cy: 7, r: 3.2 },
        { isPath: true, d: 'M20 21v-1a3.5 3.5 0 0 0-2.5-3.35' },
        { isPath: true, d: 'M15.2 3.35A3.5 3.5 0 0 1 16 10.15' },
    ],
    listing: [
        { isRect: true, x: 4.5, y: 3, w: 15, h: 18, rx: 2 },
        { isPath: true, d: 'M8 8h8M8 12h8M8 16h5' },
    ],
    video: [
        { isCircle: true, cx: 12, cy: 12, r: 9 },
        { isFilledPath: true, d: 'M10 8.5l6 3.5-6 3.5z' },
    ],
    chat: [
        { isPath: true, d: 'M4 5.5A2.5 2.5 0 0 1 6.5 3h11A2.5 2.5 0 0 1 20 5.5v8A2.5 2.5 0 0 1 17.5 16H10l-4.5 4v-4H6.5A2.5 2.5 0 0 1 4 13.5z' },
    ],
    tag: [
        { isPath: true, d: 'M11.5 3H6a2 2 0 0 0-2 2v5.5a2 2 0 0 0 .59 1.41l8 8a2 2 0 0 0 2.82 0l5.5-5.5a2 2 0 0 0 0-2.82l-8-8A2 2 0 0 0 11.5 3z' },
        { isFilledCircle: true, cx: 7.5, cy: 7.5, r: 1.2 },
    ],
    pin: [
        { isPath: true, d: 'M12 21s7-6.1 7-11.5A7 7 0 0 0 5 9.5C5 14.9 12 21 12 21z' },
        { isCircle: true, cx: 12, cy: 9.5, r: 2.3 },
    ],
    news: [
        { isPath: true, d: 'M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z' },
        { isPath: true, d: 'M14 3v5h5' },
        { isPath: true, d: 'M9 12.5h6M9 16h6' },
    ],
    flag: [
        { isPath: true, d: 'M6 21V4' },
        { isPath: true, d: 'M6 4h10.5l-2 3.5L18 11H6' },
    ],
    star: [
        { isPath: true, d: 'M12 3.5l2.6 5.3 5.9.85-4.25 4.15 1 5.85L12 16.9l-5.25 2.75 1-5.85L3.5 9.65l5.9-.85z' },
    ],
    coin: [
        { isCircle: true, cx: 12, cy: 12, r: 9 },
        { isPath: true, d: 'M9.5 15c.4 1 1.3 1.6 2.5 1.6 1.6 0 2.7-.8 2.7-2 0-1.3-1.1-1.7-2.7-2.1-1.6-.4-2.5-.9-2.5-2.1 0-1.2 1.1-2 2.5-2 1.2 0 2.1.6 2.5 1.6' },
        { isPath: true, d: 'M12 7.3v1.1M12 15.6v1.1' },
    ],
    chart: [
        { isPath: true, d: 'M5 20V10M12 20V4M19 20v-7' },
    ],
    bell: [
        { isPath: true, d: 'M6 10.5a6 6 0 0 1 12 0c0 4 1.5 5.5 1.5 5.5H4.5S6 14.5 6 10.5z' },
        { isPath: true, d: 'M10 19a2 2 0 0 0 4 0' },
    ],
    layers: [
        { isPath: true, d: 'M12 3l8.5 4.5L12 12 3.5 7.5z' },
        { isPath: true, d: 'M3.5 12.5L12 17l8.5-4.5' },
        { isPath: true, d: 'M3.5 16.5L12 21l8.5-4.5' },
    ],
    menu: [
        { isPath: true, d: 'M4 6h16M4 12h16M4 18h16' },
    ],
    chevronLeft: [
        { isPath: true, d: 'M15 6l-6 6 6 6' },
    ],
    send: [
        { isPath: true, d: 'M22 2L11 13' },
        { isPath: true, d: 'M22 2l-7 20-4-9-9-4z' },
    ],
    search: [
        { isCircle: true, cx: 11, cy: 11, r: 7 },
        { isPath: true, d: 'M21 21l-4.3-4.3' },
    ],
    plus: [
        { isPath: true, d: 'M12 5v14M5 12h14' },
    ],
    chevronDown: [
        { isPath: true, d: 'M6 9l6 6 6-6' },
    ],
    sun: [
        { isCircle: true, cx: 12, cy: 12, r: 5 },
        { isPath: true, d: 'M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42' },
    ],
    moon: [
        { isPath: true, d: 'M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z' },
    ],
}

const shapes = computed(() => ICONS[props.kind] || ICONS.grid)
</script>

<template>
  <svg
    :width="size"
    :height="size"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="1.7"
    stroke-linecap="round"
    stroke-linejoin="round"
    style="flex: none; display: block"
  >
    <template v-for="(s, i) in shapes" :key="i">
      <path v-if="s.isPath" :d="s.d" />
      <path v-else-if="s.isFilledPath" :d="s.d" fill="currentColor" stroke="none" />
      <circle v-else-if="s.isCircle" :cx="s.cx" :cy="s.cy" :r="s.r" />
      <circle v-else-if="s.isFilledCircle" :cx="s.cx" :cy="s.cy" :r="s.r" fill="currentColor" stroke="none" />
      <rect v-else-if="s.isRect" :x="s.x" :y="s.y" :width="s.w" :height="s.h" :rx="s.rx" />
    </template>
  </svg>
</template>
