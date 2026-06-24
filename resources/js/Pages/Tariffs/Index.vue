<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AppDrawer from '@/Components/AppDrawer.vue'
import DrawerField from '@/Components/DrawerField.vue'
import ToggleSwitch from '@/Components/ToggleSwitch.vue'

const props = defineProps({ tariffs: Array })

const drawer    = ref(false)
const editItem  = ref(null)
const form      = ref({ name_ru: '', name_tk: '', listings_limit: 10, videos_limit: 5, boost_limit: 3, duration_days: 30, is_active: true, is_default: false })
const errors    = ref({})

function openCreate() {
    editItem.value = null
    form.value = { name_ru: '', name_tk: '', listings_limit: 10, videos_limit: 5, boost_limit: 3, duration_days: 30, is_active: true, is_default: false }
    errors.value = {}
    drawer.value = true
}
function openEdit(t) {
    editItem.value = t
    form.value = { name_ru: t.name_ru, name_tk: t.name_tk, listings_limit: t.listings_limit, videos_limit: t.videos_limit, boost_limit: t.boost_limit, duration_days: t.duration_days, is_active: t.is_active, is_default: t.is_default }
    errors.value = {}
    drawer.value = true
}
function save() {
    const url    = editItem.value ? route('tariffs.update', editItem.value.id) : route('tariffs.store')
    const method = editItem.value ? 'put' : 'post'
    router[method](url, form.value, {
        onSuccess: () => { drawer.value = false },
        onError: e => { errors.value = e },
    })
}
function toggle(t) { router.patch(route('tariffs.toggle', t.id)) }
function destroy(t) {
    if (confirm(`Удалить тариф «${t.name_ru}»?`)) router.delete(route('tariffs.destroy', t.id))
}
</script>

<template>
  <AppLayout>
    <template #header>Тарифы</template>

    <div class="space-y-4">
      <div class="flex justify-end">
        <button @click="openCreate" class="px-4 py-2 rounded-btn bg-blue text-white text-sm font-bold hover:bg-blue/90 transition">
          + Добавить тариф
        </button>
      </div>

      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="t in tariffs" :key="t.id"
          class="rounded-card bg-white dark:bg-dcard border border-line dark:border-dline p-5 flex flex-col gap-3"
        >
          <div class="flex items-start justify-between">
            <div>
              <div class="font-extrabold text-ink dark:text-slate-100 text-base">{{ t.name_ru }}</div>
              <div class="text-xs text-muted">{{ t.name_tk }}</div>
            </div>
            <ToggleSwitch :modelValue="t.is_active" @update:modelValue="toggle(t)" />
          </div>

          <div class="grid grid-cols-3 gap-2 text-center">
            <div class="rounded-[8px] bg-surface dark:bg-dbg p-2">
              <div class="text-lg font-extrabold text-blue">{{ t.listings_limit }}</div>
              <div class="text-[10px] text-muted">объявлений</div>
            </div>
            <div class="rounded-[8px] bg-surface dark:bg-dbg p-2">
              <div class="text-lg font-extrabold text-blue">{{ t.videos_limit }}</div>
              <div class="text-[10px] text-muted">видео</div>
            </div>
            <div class="rounded-[8px] bg-surface dark:bg-dbg p-2">
              <div class="text-lg font-extrabold text-blue">{{ t.boost_limit }}</div>
              <div class="text-[10px] text-muted">подъёмов</div>
            </div>
          </div>

          <div class="flex items-center gap-2 text-xs text-muted">
            <span>{{ t.duration_days }} дн.</span>
            <span v-if="t.is_default" class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 font-semibold">По умолчанию</span>
            <span class="ml-auto text-[11px]">{{ t.users_count }} польз.</span>
          </div>

          <div class="flex gap-2 pt-1">
            <button @click="openEdit(t)" class="flex-1 py-1.5 rounded-btn border border-line dark:border-dline text-xs font-bold text-ink dark:text-slate-200 hover:bg-surface dark:hover:bg-white/5 transition">
              Изменить
            </button>
            <button @click="destroy(t)" class="px-3 py-1.5 rounded-btn border border-red/30 text-xs font-bold text-red hover:bg-red/5 transition">
              Удалить
            </button>
          </div>
        </div>
      </div>
    </div>

    <AppDrawer :open="drawer" :title="editItem ? 'Редактировать тариф' : 'Новый тариф'" @close="drawer = false" @save="save">
      <DrawerField label="Название (RU)" :error="errors.name_ru">
        <input v-model="form.name_ru" class="input" />
      </DrawerField>
      <DrawerField label="Название (TK)" :error="errors.name_tk">
        <input v-model="form.name_tk" class="input" />
      </DrawerField>
      <div class="grid grid-cols-2 gap-3">
        <DrawerField label="Объявлений" :error="errors.listings_limit">
          <input v-model.number="form.listings_limit" type="number" min="0" class="input" />
        </DrawerField>
        <DrawerField label="Видео" :error="errors.videos_limit">
          <input v-model.number="form.videos_limit" type="number" min="0" class="input" />
        </DrawerField>
        <DrawerField label="Подъёмов" :error="errors.boost_limit">
          <input v-model.number="form.boost_limit" type="number" min="0" class="input" />
        </DrawerField>
        <DrawerField label="Дней действия" :error="errors.duration_days">
          <input v-model.number="form.duration_days" type="number" min="1" class="input" />
        </DrawerField>
      </div>
      <div class="flex items-center gap-6 pt-1">
        <label class="flex items-center gap-2 text-sm font-semibold text-ink dark:text-slate-200">
          <ToggleSwitch v-model="form.is_active" /> Активен
        </label>
        <label class="flex items-center gap-2 text-sm font-semibold text-ink dark:text-slate-200">
          <ToggleSwitch v-model="form.is_default" /> По умолчанию
        </label>
      </div>
    </AppDrawer>
  </AppLayout>
</template>
