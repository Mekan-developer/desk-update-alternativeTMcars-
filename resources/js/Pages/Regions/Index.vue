<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AppDrawer from '@/Components/AppDrawer.vue'
import DrawerField from '@/Components/DrawerField.vue'
import ToggleSwitch from '@/Components/ToggleSwitch.vue'

const props = defineProps({ regions: Array })

const selectedRegion = ref(null)

const cities = computed(() => selectedRegion.value?.cities || [])

// Region drawer
const regionDrawer = ref(false)
const editRegion   = ref(null)
const regionForm   = ref({ name_ru: '', name_tk: '', is_active: true, sort_order: 0 })
const regionErrors = ref({})

function openCreateRegion() {
    editRegion.value = null
    regionForm.value = { name_ru: '', name_tk: '', is_active: true, sort_order: 0 }
    regionErrors.value = {}
    regionDrawer.value = true
}
function openEditRegion(r) {
    editRegion.value = r
    regionForm.value = { name_ru: r.name_ru, name_tk: r.name_tk, is_active: r.is_active, sort_order: r.sort_order }
    regionErrors.value = {}
    regionDrawer.value = true
}
function saveRegion() {
    const url = editRegion.value ? route('regions.update', editRegion.value.id) : route('regions.store')
    const method = editRegion.value ? 'put' : 'post'
    router[method](url, regionForm.value, {
        onSuccess: () => { regionDrawer.value = false },
        onError: e => { regionErrors.value = e },
    })
}
function destroyRegion(r) {
    if (confirm(`Удалить регион «${r.name_ru}»?`)) router.delete(route('regions.destroy', r.id))
}

// City drawer
const cityDrawer = ref(false)
const editCity   = ref(null)
const cityForm   = ref({ name_ru: '', name_tk: '', is_active: true, sort_order: 0 })
const cityErrors = ref({})

function openCreateCity() {
    if (!selectedRegion.value) return
    editCity.value = null
    cityForm.value = { name_ru: '', name_tk: '', is_active: true, sort_order: 0 }
    cityErrors.value = {}
    cityDrawer.value = true
}
function openEditCity(c) {
    editCity.value = c
    cityForm.value = { name_ru: c.name_ru, name_tk: c.name_tk, is_active: c.is_active, sort_order: c.sort_order }
    cityErrors.value = {}
    cityDrawer.value = true
}
function saveCity() {
    const payload = { ...cityForm.value, region_id: selectedRegion.value.id }
    const url = editCity.value ? route('cities.update', editCity.value.id) : route('cities.store')
    const method = editCity.value ? 'put' : 'post'
    router[method](url, payload, {
        onSuccess: () => { cityDrawer.value = false },
        onError: e => { cityErrors.value = e },
    })
}
function destroyCity(c) {
    if (confirm(`Удалить город «${c.name_ru}»?`)) router.delete(route('cities.destroy', c.id))
}
</script>

<template>
  <AppLayout>
    <template #header>Регионы и города</template>

    <div class="grid gap-5" style="grid-template-columns: 1fr 1fr;">
      <!-- Regions -->
      <div class="rounded-card bg-white shadow-soft dark:bg-dcard overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-line dark:border-dline">
          <span class="text-[15px] font-extrabold text-ink dark:text-slate-100">Регионы</span>
          <button @click="openCreateRegion" class="rounded-btn bg-blue px-3 py-1.5 text-[12px] font-bold text-white hover:opacity-90 transition">+ Добавить</button>
        </div>
        <div class="divide-y divide-line dark:divide-dline">
          <div
            v-for="region in regions" :key="region.id"
            @click="selectedRegion = selectedRegion?.id === region.id ? null : region"
            class="flex cursor-pointer items-center justify-between px-5 py-3.5 transition hover:bg-surface dark:hover:bg-white/5"
            :class="selectedRegion?.id === region.id ? 'bg-blue/5 dark:bg-blue/10' : ''"
          >
            <div>
              <div class="text-[13px] font-bold text-ink dark:text-slate-200">{{ region.name_ru }}</div>
              <div class="text-[11px] text-muted">{{ region.name_tk }} · {{ region.cities?.length || 0 }} городов</div>
            </div>
            <div class="flex items-center gap-1.5">
              <span :class="region.is_active ? 'bg-green/10 text-green' : 'bg-muted/10 text-muted'" class="rounded-pill px-2 py-0.5 text-[10px] font-bold">{{ region.is_active ? 'Актив' : 'Скрыт' }}</span>
              <button @click.stop="openEditRegion(region)" class="flex h-[28px] w-[28px] items-center justify-center rounded-[7px] bg-surface dark:bg-dbg text-muted hover:bg-blue hover:text-white transition">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
              </button>
              <button @click.stop="destroyRegion(region)" class="flex h-[28px] w-[28px] items-center justify-center rounded-[7px] bg-red/10 text-red hover:bg-red hover:text-white transition">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline stroke-width="2" stroke-linecap="round" points="3 6 5 6 21 6"/><path stroke-width="2" stroke-linecap="round" d="M19 6l-1 14H6L5 6"/></svg>
              </button>
            </div>
          </div>
          <div v-if="!regions?.length" class="px-5 py-8 text-center text-[13px] text-muted">Нет регионов</div>
        </div>
      </div>

      <!-- Cities -->
      <div class="rounded-card bg-white shadow-soft dark:bg-dcard overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-line dark:border-dline">
          <div>
            <span class="text-[15px] font-extrabold text-ink dark:text-slate-100">Города</span>
            <span v-if="selectedRegion" class="ml-2 text-[13px] text-muted">— {{ selectedRegion.name_ru }}</span>
          </div>
          <button @click="openCreateCity" :disabled="!selectedRegion" class="rounded-btn bg-blue px-3 py-1.5 text-[12px] font-bold text-white hover:opacity-90 transition disabled:opacity-40">+ Добавить</button>
        </div>
        <div v-if="!selectedRegion" class="flex items-center justify-center h-48">
          <p class="text-[13px] text-muted">← Выберите регион</p>
        </div>
        <div v-else class="divide-y divide-line dark:divide-dline">
          <div v-for="city in cities" :key="city.id" class="flex items-center justify-between px-5 py-3 hover:bg-surface dark:hover:bg-white/5 transition">
            <div>
              <div class="text-[13px] font-bold text-ink dark:text-slate-200">{{ city.name_ru }}</div>
              <div class="text-[11px] text-muted">{{ city.name_tk }}</div>
            </div>
            <div class="flex items-center gap-1.5">
              <span :class="city.is_active ? 'bg-green/10 text-green' : 'bg-muted/10 text-muted'" class="rounded-pill px-2 py-0.5 text-[10px] font-bold">{{ city.is_active ? 'Актив' : 'Скрыт' }}</span>
              <button @click="openEditCity(city)" class="flex h-[28px] w-[28px] items-center justify-center rounded-[7px] bg-surface dark:bg-dbg text-muted hover:bg-blue hover:text-white transition">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
              </button>
              <button @click="destroyCity(city)" class="flex h-[28px] w-[28px] items-center justify-center rounded-[7px] bg-red/10 text-red hover:bg-red hover:text-white transition">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline stroke-width="2" stroke-linecap="round" points="3 6 5 6 21 6"/><path stroke-width="2" stroke-linecap="round" d="M19 6l-1 14H6L5 6"/></svg>
              </button>
            </div>
          </div>
          <div v-if="!cities.length" class="px-5 py-8 text-center text-[13px] text-muted">Нет городов</div>
        </div>
      </div>
    </div>

    <!-- Region Drawer -->
    <AppDrawer :open="regionDrawer" :title="editRegion ? 'Редактировать регион' : 'Новый регион'" @close="regionDrawer = false">
      <div class="space-y-4 p-5">
        <DrawerField label="Название (рус)" :error="regionErrors.name_ru">
          <input v-model="regionForm.name_ru" class="w-full rounded-btn border-2 border-line bg-surface py-[9px] px-[14px] text-[13px] font-semibold text-ink outline-none focus:border-blue dark:bg-dbg dark:border-dline dark:text-slate-200" />
        </DrawerField>
        <DrawerField label="Название (тур)" :error="regionErrors.name_tk">
          <input v-model="regionForm.name_tk" class="w-full rounded-btn border-2 border-line bg-surface py-[9px] px-[14px] text-[13px] font-semibold text-ink outline-none focus:border-blue dark:bg-dbg dark:border-dline dark:text-slate-200" />
        </DrawerField>
        <DrawerField label="Порядок сортировки">
          <input v-model="regionForm.sort_order" type="number" class="w-full rounded-btn border-2 border-line bg-surface py-[9px] px-[14px] text-[13px] font-semibold text-ink outline-none focus:border-blue dark:bg-dbg dark:border-dline dark:text-slate-200" />
        </DrawerField>
        <DrawerField label="Активен">
          <ToggleSwitch v-model="regionForm.is_active" />
        </DrawerField>
      </div>
      <template #footer>
        <button @click="regionDrawer = false" class="flex-1 rounded-btn border-2 border-line py-[10px] text-[13px] font-bold text-muted hover:border-blue hover:text-blue transition dark:border-dline">Отмена</button>
        <button @click="saveRegion" class="flex-1 rounded-btn bg-blue py-[10px] text-[13px] font-bold text-white hover:opacity-90 transition">{{ editRegion ? 'Сохранить' : 'Создать' }}</button>
      </template>
    </AppDrawer>

    <!-- City Drawer -->
    <AppDrawer :open="cityDrawer" :title="editCity ? 'Редактировать город' : 'Новый город'" @close="cityDrawer = false">
      <div class="space-y-4 p-5">
        <DrawerField label="Название (рус)" :error="cityErrors.name_ru">
          <input v-model="cityForm.name_ru" class="w-full rounded-btn border-2 border-line bg-surface py-[9px] px-[14px] text-[13px] font-semibold text-ink outline-none focus:border-blue dark:bg-dbg dark:border-dline dark:text-slate-200" />
        </DrawerField>
        <DrawerField label="Название (тур)" :error="cityErrors.name_tk">
          <input v-model="cityForm.name_tk" class="w-full rounded-btn border-2 border-line bg-surface py-[9px] px-[14px] text-[13px] font-semibold text-ink outline-none focus:border-blue dark:bg-dbg dark:border-dline dark:text-slate-200" />
        </DrawerField>
        <DrawerField label="Порядок сортировки">
          <input v-model="cityForm.sort_order" type="number" class="w-full rounded-btn border-2 border-line bg-surface py-[9px] px-[14px] text-[13px] font-semibold text-ink outline-none focus:border-blue dark:bg-dbg dark:border-dline dark:text-slate-200" />
        </DrawerField>
        <DrawerField label="Активен">
          <ToggleSwitch v-model="cityForm.is_active" />
        </DrawerField>
      </div>
      <template #footer>
        <button @click="cityDrawer = false" class="flex-1 rounded-btn border-2 border-line py-[10px] text-[13px] font-bold text-muted hover:border-blue hover:text-blue transition dark:border-dline">Отмена</button>
        <button @click="saveCity" class="flex-1 rounded-btn bg-blue py-[10px] text-[13px] font-bold text-white hover:opacity-90 transition">{{ editCity ? 'Сохранить' : 'Создать' }}</button>
      </template>
    </AppDrawer>
  </AppLayout>
</template>
