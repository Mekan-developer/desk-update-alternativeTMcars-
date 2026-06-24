<script setup>
const props  = defineProps({ modelValue: Boolean, label: String, disabled: Boolean })
const emit   = defineEmits(['update:modelValue'])
</script>

<template>
  <label class="flex cursor-pointer items-center gap-3 select-none" :class="disabled ? 'opacity-50 cursor-not-allowed' : ''">
    <label class="tog">
      <input
        type="checkbox"
        :checked="modelValue"
        :disabled="disabled"
        @change="emit('update:modelValue', $event.target.checked)"
      />
      <span class="tog-slider"></span>
    </label>
    <span v-if="label" class="text-[13px] font-semibold text-ink dark:text-slate-200">{{ label }}</span>
  </label>
</template>
