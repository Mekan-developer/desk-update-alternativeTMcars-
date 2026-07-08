<script setup>
import { useI18n } from 'vue-i18n'
import Icon from '@/Components/Icon.vue'

/**
 * Глобальное поле поиска. Используется как в DataTable (живая фильтрация
 * на клиенте), так и на страницах со списками (поиск по Enter → запрос
 * на сервер через Inertia router.get).
 *
 * <SearchInput v-model="search" :placeholder="t('users.searchPlaceholder')" @submit="applyFilters" />
 */

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
})

const emit = defineEmits(['update:modelValue', 'submit', 'clear'])

const { t } = useI18n()

function onInput(e) {
    emit('update:modelValue', e.target.value)
}

function clear() {
    emit('update:modelValue', '')
    emit('submit', '')
    emit('clear')
}
</script>

<template>
  <div class="flex items-center gap-2 rounded-[10px] border border-[var(--field-border)] bg-[var(--field-bg)] px-3 py-2 transition-shadow focus-within:border-[var(--accent)] focus-within:shadow-[0_0_0_3px_var(--accent-tint)]">
    <Icon kind="search" :size="16" class="flex-shrink-0 text-[var(--text-muted)]" />
    <input
      :value="modelValue"
      @input="onInput"
      @keydown.enter="emit('submit', modelValue)"
      type="text"
      :placeholder="placeholder || t('common.search')"
      class="min-w-0 flex-1 bg-transparent text-sm text-[var(--text)] outline-none placeholder-[var(--text-muted)]"
    />
    <button
      v-if="modelValue"
      type="button"
      @click="clear"
      :aria-label="t('common.clear')"
      class="flex-shrink-0 rounded-full p-0.5 text-[var(--text-muted)] transition-colors hover:bg-[var(--nav-hover)] hover:text-[var(--text)]"
    >
      <Icon kind="close" :size="14" />
    </button>
  </div>
</template>
