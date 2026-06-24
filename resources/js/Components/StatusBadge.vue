<script setup>
defineProps({ status: String, type: { type: String, default: 'listing' } })

const config = {
    pending:  { cls: 'bg-orange/10 text-orange', dot: 'bg-orange', label: 'На проверке' },
    approved: { cls: 'bg-green/10 text-green',   dot: 'bg-green',  label: 'Одобрено' },
    rejected: { cls: 'bg-red/10 text-red',       dot: 'bg-red',    label: 'Отклонено' },
    active:   { cls: 'bg-green/10 text-green',   dot: 'bg-green',  label: 'Активен' },
    blocked:  { cls: 'bg-red/10 text-red',       dot: 'bg-red',    label: 'Заблокирован' },
    new:      { cls: 'bg-blue/10 text-blue',     dot: 'bg-blue',   label: 'Новая' },
    reviewed: { cls: 'bg-orange/10 text-orange', dot: 'bg-orange', label: 'Рассматривается' },
    resolved: { cls: 'bg-green/10 text-green',   dot: 'bg-green',  label: 'Решено' },
    regular:  { cls: 'bg-blue/10 text-blue',     dot: 'bg-blue',   label: 'Обычная' },
    advertising: { cls: 'bg-purple/10 text-purple', dot: 'bg-purple', label: 'Реклама' },
}
</script>

<template>
  <span
    class="inline-flex items-center gap-1 rounded-pill px-2.5 py-0.5 text-[11px] font-bold"
    :class="config[status]?.cls || 'bg-surface text-muted'"
  >
    <span class="h-1.5 w-1.5 rounded-full" :class="config[status]?.dot || 'bg-muted'"></span>
    {{ config[status]?.label || status }}
  </span>
</template>
