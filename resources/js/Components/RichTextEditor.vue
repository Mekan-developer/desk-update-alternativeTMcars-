<script setup>
import { ref, watch, nextTick } from 'vue'

const props = defineProps({
    modelValue: String,
    placeholder: String,
})
const emit = defineEmits(['update:modelValue'])

const editor = ref(null)
const isUpdating = ref(false)

// Синхронизируем содержимое при изменении modelValue (например, при смене языка)
watch(() => props.modelValue, (newValue) => {
    if (!isUpdating.value && editor.value) {
        editor.value.innerHTML = newValue || ''
    }
}, { immediate: true })

const sanitizeHtml = (html) => {
    // Разрешённые теги
    const allowedTags = ['b', 'i', 'strong', 'em', 'ul', 'ol', 'li', 'a', 'br', 'p']
    const parser = new DOMParser()
    const doc = parser.parseFromString(`<div>${html}</div>`, 'text/html')

    const walk = (node) => {
        let i = node.childNodes.length
        while (i--) {
            const child = node.childNodes[i]
            if (child.nodeType === 1) { // Element
                if (!allowedTags.includes(child.tagName.toLowerCase())) {
                    while (child.firstChild) node.insertBefore(child.firstChild, child)
                    node.removeChild(child)
                } else {
                    // Удаляем опасные атрибуты
                    if (child.tagName.toLowerCase() === 'a') {
                        child.removeAttribute('onclick')
                        child.removeAttribute('onerror')
                        const href = child.getAttribute('href')
                        if (href && !href.includes('javascript:')) {
                            child.setAttribute('target', '_blank')
                        }
                    }
                    walk(child)
                }
            }
        }
    }

    walk(doc.body)
    return doc.body.innerHTML
}

const updateContent = () => {
    if (editor.value) {
        isUpdating.value = true
        const html = editor.value.innerHTML
        const sanitized = sanitizeHtml(html)
        emit('update:modelValue', sanitized)
        nextTick(() => {
            isUpdating.value = false
        })
    }
}

const execCommand = (cmd, arg = null) => {
    document.execCommand(cmd, false, arg)
    editor.value?.focus()
    updateContent()
}

const insertLink = () => {
    const url = prompt('Введите URL:', 'https://')
    if (url) {
        // Проверяем протокол
        const finalUrl = url.startsWith('http') ? url : `https://${url}`
        execCommand('createLink', finalUrl)
    }
}

const onPaste = (e) => {
    e.preventDefault()
    const text = e.clipboardData?.getData('text/plain') || ''
    if (text) {
        document.execCommand('insertText', false, text)
        updateContent()
    }
}

const onKeyDown = (e) => {
    // Ctrl+B для жирного
    if (e.ctrlKey && e.key === 'b') {
        e.preventDefault()
        execCommand('bold')
    }
    // Ctrl+I для курсива
    if (e.ctrlKey && e.key === 'i') {
        e.preventDefault()
        execCommand('italic')
    }
}

const onBlur = () => {
    // Санитизируем при blur
    if (editor.value && editor.value.innerHTML) {
        const sanitized = sanitizeHtml(editor.value.innerHTML)
        if (sanitized !== editor.value.innerHTML) {
            editor.value.innerHTML = sanitized
            updateContent()
        }
    }
}
</script>

<template>
  <div class="rounded-[10px] border border-[var(--field-border)] overflow-hidden">
    <!-- Toolbar -->
    <div class="flex items-center gap-1 bg-[var(--field-bg)] border-b border-[var(--field-border)] px-2 py-1.5">
      <button
        type="button"
        @click="() => execCommand('bold')"
        @mousedown.prevent
        title="Жирный (Ctrl+B)"
        class="h-7 px-2 rounded-[6px] font-black text-[12px] hover:bg-[var(--accent)]/20 transition text-[var(--text)]"
      >B</button>
      <button
        type="button"
        @click="() => execCommand('italic')"
        @mousedown.prevent
        title="Курсив (Ctrl+I)"
        class="h-7 px-2 rounded-[6px] italic font-black text-[12px] hover:bg-[var(--accent)]/20 transition text-[var(--text)]"
      >I</button>

      <div class="w-px h-5 bg-[var(--field-border)]"></div>

      <button
        type="button"
        @click="() => execCommand('insertUnorderedList')"
        @mousedown.prevent
        title="Маркированный список"
        class="h-7 w-7 flex items-center justify-center rounded-[6px] hover:bg-[var(--accent)]/20 transition text-[var(--text)]"
      >
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
          <circle cx="6" cy="6" r="1.5" />
          <line x1="10" y1="6" x2="20" y2="6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
          <circle cx="6" cy="12" r="1.5" />
          <line x1="10" y1="12" x2="20" y2="12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
          <circle cx="6" cy="18" r="1.5" />
          <line x1="10" y1="18" x2="20" y2="18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
        </svg>
      </button>

      <button
        type="button"
        @click="() => execCommand('insertOrderedList')"
        @mousedown.prevent
        title="Нумерованный список"
        class="h-7 w-7 flex items-center justify-center rounded-[6px] hover:bg-[var(--accent)]/20 transition text-[var(--text)]"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
          <text x="3" y="11" font-size="8" fill="currentColor" font-weight="bold">1.</text>
          <line x1="10" y1="8" x2="20" y2="8" stroke="currentColor" stroke-width="1.5" />
          <text x="3" y="16" font-size="8" fill="currentColor" font-weight="bold">2.</text>
          <line x1="10" y1="14" x2="20" y2="14" stroke="currentColor" stroke-width="1.5" />
          <text x="3" y="21" font-size="8" fill="currentColor" font-weight="bold">3.</text>
          <line x1="10" y1="20" x2="20" y2="20" stroke="currentColor" stroke-width="1.5" />
        </svg>
      </button>

      <div class="w-px h-5 bg-[var(--field-border)]"></div>

      <button
        type="button"
        @click="insertLink"
        @mousedown.prevent
        title="Вставить ссылку"
        class="h-7 w-7 flex items-center justify-center rounded-[6px] hover:bg-[var(--accent)]/20 transition text-[var(--text)]"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
          <path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.658 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </button>
    </div>

    <!-- Editor -->
    <div
      ref="editor"
      contenteditable
      :data-placeholder="placeholder"
      class="min-h-[120px] p-3 outline-none text-sm bg-white dark:bg-[var(--field-bg)] text-[var(--text)]"
      @input="updateContent"
      @paste="onPaste"
      @blur="onBlur"
      @keydown="onKeyDown"
    ></div>
  </div>
</template>

<style scoped>
[contenteditable]:empty::before {
  content: attr(data-placeholder);
  color: var(--text-muted);
  pointer-events: none;
}

[contenteditable] {
  word-wrap: break-word;
  white-space: pre-wrap;
}

[contenteditable]:focus {
  outline: none;
}

a {
  color: var(--accent);
  text-decoration: underline;
}
</style>
