<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AppDrawer from '@/Components/AppDrawer.vue'
import DrawerField from '@/Components/DrawerField.vue'
import Pagination from '@/Components/Pagination.vue'
import StatusBadge from '@/Components/StatusBadge.vue'

const props = defineProps({
    news:    Object,
    filters: Object,
})

const drawer   = ref(false)
const editItem = ref(null)
const form     = ref({ title_ru: '', title_tk: '', content_ru: '', content_tk: '', type: 'news', is_published: false })
const errors   = ref({})

function openCreate() {
    editItem.value = null
    form.value = { title_ru: '', title_tk: '', content_ru: '', content_tk: '', type: 'news', is_published: false }
    errors.value = {}
    drawer.value = true
}
function openEdit(n) {
    editItem.value = n
    form.value = { title_ru: n.title_ru, title_tk: n.title_tk, content_ru: n.content_ru, content_tk: n.content_tk, type: n.type, is_published: n.is_published }
    errors.value = {}
    drawer.value = true
}
function save() {
    const url    = editItem.value ? route('news.update', editItem.value.id) : route('news.store')
    const method = editItem.value ? 'put' : 'post'
    router[method](url, form.value, {
        onSuccess: () => { drawer.value = false },
        onError: e => { errors.value = e },
    })
}
function publish(n) { router.patch(route('news.publish', n.id)) }
function unpublish(n) { router.patch(route('news.unpublish', n.id)) }
function destroy(n) {
    if (confirm(`Удалить «${n.title_ru}»?`)) router.delete(route('news.destroy', n.id))
}
</script>

<template>
  <AppLayout>
    <template #header>Новости</template>

    <div class="space-y-4">
      <div class="flex justify-end">
        <button @click="openCreate" class="px-4 py-2 rounded-btn bg-blue text-white text-sm font-bold hover:bg-blue/90 transition">
          + Добавить
        </button>
      </div>

      <div class="rounded-card bg-white dark:bg-dcard border border-line dark:border-dline overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-surface dark:bg-dbg">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-extrabold text-muted uppercase">Заголовок</th>
              <th class="px-4 py-3 text-left text-xs font-extrabold text-muted uppercase">Тип</th>
              <th class="px-4 py-3 text-left text-xs font-extrabold text-muted uppercase">Статус</th>
              <th class="px-4 py-3 text-left text-xs font-extrabold text-muted uppercase">Дата</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-line dark:divide-dline">
            <tr v-for="n in news.data" :key="n.id" class="hover:bg-surface/50 dark:hover:bg-white/2 transition">
              <td class="px-4 py-3">
                <div class="font-semibold text-ink dark:text-slate-100 max-w-[280px] truncate">{{ n.title_ru }}</div>
                <div class="text-xs text-muted truncate max-w-[280px]">{{ n.title_tk }}</div>
              </td>
              <td class="px-4 py-3">
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold" :class="n.type === 'news' ? 'bg-blue/10 text-blue' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'">
                  {{ n.type === 'news' ? 'Новость' : 'Реклама' }}
                </span>
              </td>
              <td class="px-4 py-3">
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold" :class="n.is_published ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-surface dark:bg-dbg text-muted'">
                  {{ n.is_published ? 'Опубликовано' : 'Черновик' }}
                </span>
              </td>
              <td class="px-4 py-3 text-xs text-muted">{{ new Date(n.created_at).toLocaleDateString('ru') }}</td>
              <td class="px-4 py-3">
                <div class="flex gap-1.5 justify-end">
                  <button v-if="!n.is_published" @click="publish(n)" class="px-2.5 py-1 rounded-btn bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-xs font-bold hover:bg-green-100 transition">Опубл.</button>
                  <button v-else @click="unpublish(n)" class="px-2.5 py-1 rounded-btn bg-surface dark:bg-dbg text-muted text-xs font-bold hover:bg-line transition">Снять</button>
                  <button @click="openEdit(n)" class="px-2.5 py-1 rounded-btn border border-line dark:border-dline text-xs font-bold text-ink dark:text-slate-200 hover:bg-surface dark:hover:bg-white/5 transition">Ред.</button>
                  <button @click="destroy(n)" class="px-2.5 py-1 rounded-btn text-red text-xs font-bold hover:bg-red/5 transition">Удалить</button>
                </div>
              </td>
            </tr>
            <tr v-if="!news.data?.length">
              <td colspan="5" class="px-4 py-10 text-center text-muted text-sm">Новостей нет</td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination :links="news.links" />
    </div>

    <AppDrawer :open="drawer" :title="editItem ? 'Редактировать новость' : 'Новая новость'" @close="drawer = false" @save="save">
      <DrawerField label="Заголовок (RU)" :error="errors.title_ru">
        <input v-model="form.title_ru" class="input" />
      </DrawerField>
      <DrawerField label="Заголовок (TK)" :error="errors.title_tk">
        <input v-model="form.title_tk" class="input" />
      </DrawerField>
      <DrawerField label="Содержание (RU)" :error="errors.content_ru">
        <textarea v-model="form.content_ru" rows="5" class="input resize-none" />
      </DrawerField>
      <DrawerField label="Содержание (TK)" :error="errors.content_tk">
        <textarea v-model="form.content_tk" rows="5" class="input resize-none" />
      </DrawerField>
      <DrawerField label="Тип" :error="errors.type">
        <select v-model="form.type" class="input">
          <option value="news">Новость</option>
          <option value="advertisement">Реклама</option>
        </select>
      </DrawerField>
      <label class="flex items-center gap-2 text-sm font-semibold text-ink dark:text-slate-200">
        <input type="checkbox" v-model="form.is_published" class="rounded" /> Опубликовать сразу
      </label>
    </AppDrawer>
  </AppLayout>
</template>
