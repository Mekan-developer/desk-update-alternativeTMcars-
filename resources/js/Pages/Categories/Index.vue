<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AppDrawer from '@/Components/AppDrawer.vue'
import DrawerField from '@/Components/DrawerField.vue'
import ToggleSwitch from '@/Components/ToggleSwitch.vue'

const props = defineProps({ categories: Array })

// Flatten tree to show indented list
const flatList = computed(() => {
    const result = []
    function walk(items, depth) {
        for (const item of items) {
            result.push({ ...item, depth })
            if (item.children?.length) walk(item.children, depth + 1)
        }
    }
    walk(props.categories, 0)
    return result
})

// Drawer state
const drawerOpen = ref(false)
const editItem   = ref(null)
const form       = ref({ name_ru: '', name_tk: '', parent_id: '', icon: '', is_active: true })
const errors     = ref({})

function openCreate(parent = null) {
    editItem.value = null
    form.value = { name_ru: '', name_tk: '', parent_id: parent?.id || '', icon: '', is_active: true }
    errors.value = {}
    drawerOpen.value = true
}

function openEdit(cat) {
    editItem.value = cat
    form.value = { name_ru: cat.name_ru, name_tk: cat.name_tk, parent_id: cat.parent_id || '', icon: cat.icon || '', is_active: cat.is_active }
    errors.value = {}
    drawerOpen.value = true
}

function save() {
    const url  = editItem.value ? route('categories.update', editItem.value.id) : route('categories.store')
    const method = editItem.value ? 'put' : 'post'
    router[method](url, form.value, {
        onSuccess: () => { drawerOpen.value = false },
        onError: e => { errors.value = e },
    })
}

function destroy(cat) {
    if (confirm(`Удалить «${cat.name_ru}»?`)) {
        router.delete(route('categories.destroy', cat.id))
    }
}

// Parent options (only level 1 and 2 can be parents)
const parentOptions = computed(() => flatList.value.filter(c => c.level < 3))
</script>

<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between w-full">
        <span>Категории</span>
        <button @click="openCreate()" class="rounded-btn bg-blue px-4 py-2 text-[13px] font-bold text-white hover:opacity-90 transition">+ Добавить</button>
      </div>
    </template>

    <div class="rounded-card bg-white shadow-soft dark:bg-dcard overflow-hidden">
      <table class="w-full">
        <thead class="bg-surface/50 dark:bg-dbg/50">
          <tr>
            <th class="px-4 py-[11px] text-left text-[11px] font-bold uppercase tracking-[.07em] text-muted border-b-2 border-line dark:border-dline w-10">#</th>
            <th class="px-4 py-[11px] text-left text-[11px] font-bold uppercase tracking-[.07em] text-muted border-b-2 border-line dark:border-dline">Название</th>
            <th class="px-4 py-[11px] text-left text-[11px] font-bold uppercase tracking-[.07em] text-muted border-b-2 border-line dark:border-dline">Туркменский</th>
            <th class="px-4 py-[11px] text-left text-[11px] font-bold uppercase tracking-[.07em] text-muted border-b-2 border-line dark:border-dline w-20">Уровень</th>
            <th class="px-4 py-[11px] text-left text-[11px] font-bold uppercase tracking-[.07em] text-muted border-b-2 border-line dark:border-dline">Статус</th>
            <th class="px-4 py-[11px] text-right text-[11px] font-bold uppercase tracking-[.07em] text-muted border-b-2 border-line dark:border-dline">Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="cat in flatList" :key="cat.id" class="hover:bg-surface/30 dark:hover:bg-white/3 transition">
            <td class="px-4 py-[12px] text-[12px] border-b border-line dark:border-dline font-data text-muted">{{ cat.id }}</td>
            <td class="px-4 py-[12px] text-[13px] border-b border-line dark:border-dline">
              <div class="flex items-center" :style="{ paddingLeft: cat.depth * 20 + 'px' }">
                <svg v-if="cat.depth > 0" class="h-3 w-3 text-muted mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-width="2" stroke-linecap="round" d="M9 18l-4-6 4-6"/>
                </svg>
                <span v-if="cat.icon" class="mr-2 text-base">{{ cat.icon }}</span>
                <span class="font-bold text-ink dark:text-slate-200">{{ cat.name_ru }}</span>
                <span v-if="cat.children?.length" class="ml-2 text-[10px] font-data font-bold text-muted">({{ cat.children_count || 0 }})</span>
              </div>
            </td>
            <td class="px-4 py-[12px] text-[13px] border-b border-line dark:border-dline text-muted">{{ cat.name_tk }}</td>
            <td class="px-4 py-[12px] text-[12px] border-b border-line dark:border-dline">
              <span class="rounded-pill px-2.5 py-0.5 text-[11px] font-bold"
                :class="cat.depth === 0 ? 'bg-blue-light text-blue' : cat.depth === 1 ? 'bg-green/10 text-green' : 'bg-orange/10 text-orange'">
                L{{ cat.depth + 1 }}
              </span>
            </td>
            <td class="px-4 py-[12px] text-[13px] border-b border-line dark:border-dline">
              <span :class="cat.is_active ? 'text-green font-bold' : 'text-muted'">{{ cat.is_active ? 'Активна' : 'Скрыта' }}</span>
            </td>
            <td class="px-4 py-[12px] text-[13px] border-b border-line dark:border-dline">
              <div class="flex items-center justify-end gap-1.5">
                <button v-if="cat.depth < 2" @click="openCreate(cat)" title="Добавить подкатегорию"
                  class="flex h-[28px] w-[28px] items-center justify-center rounded-[7px] bg-blue-light text-blue transition hover:bg-blue hover:text-white">
                  <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19" stroke-width="2.5" stroke-linecap="round"/><line x1="5" y1="12" x2="19" y2="12" stroke-width="2.5" stroke-linecap="round"/></svg>
                </button>
                <button @click="openEdit(cat)"
                  class="flex h-[28px] w-[28px] items-center justify-center rounded-[7px] bg-surface text-muted transition hover:bg-blue hover:text-white dark:bg-dbg">
                  <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <button @click="destroy(cat)"
                  class="flex h-[28px] w-[28px] items-center justify-center rounded-[7px] bg-red/10 text-red transition hover:bg-red hover:text-white">
                  <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline stroke-width="2" stroke-linecap="round" points="3 6 5 6 21 6"/><path stroke-width="2" stroke-linecap="round" d="M19 6l-1 14H6L5 6M10 11v6M14 11v6"/></svg>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="!flatList.length"><td colspan="6" class="px-4 py-10 text-center text-[13px] text-muted">Нет категорий</td></tr>
        </tbody>
      </table>
    </div>

    <AppDrawer :open="drawerOpen" :title="editItem ? 'Редактировать категорию' : 'Новая категория'" @close="drawerOpen = false">
      <div class="space-y-4 p-5">
        <DrawerField label="Родительская категория">
          <select v-model="form.parent_id" class="w-full rounded-btn border-2 border-line bg-surface py-[9px] px-[14px] text-[13px] font-semibold text-ink outline-none focus:border-blue dark:bg-dbg dark:border-dline dark:text-slate-200">
            <option value="">— Корневая категория —</option>
            <option v-for="p in parentOptions" :key="p.id" :value="p.id"
              :style="{ paddingLeft: p.depth * 16 + 'px' }">
              {{ '  '.repeat(p.depth) }}{{ p.name_ru }}
            </option>
          </select>
        </DrawerField>
        <DrawerField label="Название (рус)" :error="errors.name_ru">
          <input v-model="form.name_ru" class="w-full rounded-btn border-2 border-line bg-surface py-[9px] px-[14px] text-[13px] font-semibold text-ink outline-none focus:border-blue dark:bg-dbg dark:border-dline dark:text-slate-200" :class="errors.name_ru ? 'border-red' : ''" />
        </DrawerField>
        <DrawerField label="Название (тур)" :error="errors.name_tk">
          <input v-model="form.name_tk" class="w-full rounded-btn border-2 border-line bg-surface py-[9px] px-[14px] text-[13px] font-semibold text-ink outline-none focus:border-blue dark:bg-dbg dark:border-dline dark:text-slate-200" :class="errors.name_tk ? 'border-red' : ''" />
        </DrawerField>
        <DrawerField label="Иконка (эмодзи)">
          <input v-model="form.icon" class="w-full rounded-btn border-2 border-line bg-surface py-[9px] px-[14px] text-[13px] font-semibold text-ink outline-none focus:border-blue dark:bg-dbg dark:border-dline dark:text-slate-200" placeholder="🏠" />
        </DrawerField>
        <DrawerField label="Активна">
          <ToggleSwitch v-model="form.is_active" />
        </DrawerField>
      </div>
      <template #footer>
        <button @click="drawerOpen = false" class="flex-1 rounded-btn border-2 border-line py-[10px] text-[13px] font-bold text-muted hover:border-blue hover:text-blue transition dark:border-dline">Отмена</button>
        <button @click="save" class="flex-1 rounded-btn bg-blue py-[10px] text-[13px] font-bold text-white hover:opacity-90 transition">{{ editItem ? 'Сохранить' : 'Создать' }}</button>
      </template>
    </AppDrawer>
  </AppLayout>
</template>
