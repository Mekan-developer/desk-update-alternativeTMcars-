<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import GeoColumn from '@/Components/GeoColumn.vue'

const props = defineProps({ regions: { type: Array, default: () => [] } })

// Selection is kept by id so it survives Inertia partial reloads that swap `regions`.
const selectedRegionId = ref(null)
const selectedCityId   = ref(null)

const selectedRegion = computed(() => props.regions.find(r => r.id === selectedRegionId.value) || null)
const cities         = computed(() => selectedRegion.value?.cities ?? [])
const selectedCity   = computed(() => cities.value.find(c => c.id === selectedCityId.value) || null)
const districts      = computed(() => selectedCity.value?.districts ?? [])

const regionCol   = ref(null)
const cityCol     = ref(null)
const districtCol = ref(null)

const regionErrors   = ref({})
const cityErrors     = ref({})
const districtErrors = ref({})

const opts = { preserveScroll: true, preserveState: true }

// Russian pluralization: 1 город / 2 города / 5 городов
function plural(n, one, few, many) {
    const m10 = n % 10, m100 = n % 100
    if (m10 === 1 && m100 !== 11) return one
    if (m10 >= 2 && m10 <= 4 && (m100 < 10 || m100 >= 20)) return few
    return many
}

const regionSubtitle   = (r) => { const n = r.cities?.length || 0;    return `${r.name_tk} · ${n} ${plural(n, 'город', 'города', 'городов')}` }
const citySubtitle     = (c) => { const n = c.districts?.length || 0; return `${c.name_tk} · ${n} ${plural(n, 'район', 'района', 'районов')}` }
const districtSubtitle = (d) => d.name_tk

// ── Selection ──────────────────────────────────────────────
function selectRegion(r) {
    selectedRegionId.value = selectedRegionId.value === r.id ? null : r.id
    selectedCityId.value = null
    cityCol.value?.closeAdd();     cityCol.value?.closeEdit()
    districtCol.value?.closeAdd(); districtCol.value?.closeEdit()
}
function selectCity(c) {
    selectedCityId.value = selectedCityId.value === c.id ? null : c.id
    districtCol.value?.closeAdd(); districtCol.value?.closeEdit()
}

// ── Regions ────────────────────────────────────────────────
function createRegion(form) {
    regionErrors.value = {}
    router.post(route('regions.store'), form, { ...opts, onSuccess: () => regionCol.value?.closeAdd(), onError: e => (regionErrors.value = e) })
}
function updateRegion(item, form) {
    regionErrors.value = {}
    router.put(route('regions.update', item.id), form, { ...opts, onSuccess: () => regionCol.value?.closeEdit(), onError: e => (regionErrors.value = e) })
}
function toggleRegion(item) { router.patch(route('regions.toggle', item.id), {}, opts) }
function destroyRegion(item) {
    if (!confirm(`Удалить регион «${item.name_ru}»? Все его города и районы будут удалены.`)) return
    router.delete(route('regions.destroy', item.id), {
        ...opts,
        onSuccess: () => { if (selectedRegionId.value === item.id) { selectedRegionId.value = null; selectedCityId.value = null } },
    })
}

// ── Cities ─────────────────────────────────────────────────
function createCity(form) {
    cityErrors.value = {}
    router.post(route('cities.store'), { ...form, region_id: selectedRegionId.value }, { ...opts, onSuccess: () => cityCol.value?.closeAdd(), onError: e => (cityErrors.value = e) })
}
function updateCity(item, form) {
    cityErrors.value = {}
    router.put(route('cities.update', item.id), { ...form, region_id: selectedRegionId.value }, { ...opts, onSuccess: () => cityCol.value?.closeEdit(), onError: e => (cityErrors.value = e) })
}
function toggleCity(item) { router.patch(route('cities.toggle', item.id), {}, opts) }
function destroyCity(item) {
    if (!confirm(`Удалить город «${item.name_ru}»? Все его районы будут удалены.`)) return
    router.delete(route('cities.destroy', item.id), {
        ...opts,
        onSuccess: () => { if (selectedCityId.value === item.id) selectedCityId.value = null },
    })
}

// ── Districts ──────────────────────────────────────────────
function createDistrict(form) {
    districtErrors.value = {}
    router.post(route('districts.store', selectedCityId.value), form, { ...opts, onSuccess: () => districtCol.value?.closeAdd(), onError: e => (districtErrors.value = e) })
}
function updateDistrict(item, form) {
    districtErrors.value = {}
    router.put(route('districts.update', item.id), form, { ...opts, onSuccess: () => districtCol.value?.closeEdit(), onError: e => (districtErrors.value = e) })
}
function toggleDistrict(item) { router.patch(route('districts.toggle', item.id), {}, opts) }
function destroyDistrict(item) {
    if (!confirm(`Удалить район «${item.name_ru}»?`)) return
    router.delete(route('districts.destroy', item.id), opts)
}
</script>

<template>
  <AppLayout>
    <template #header>Регионы, города и районы</template>

    <div class="grid gap-5" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
      <!-- Регионы -->
      <GeoColumn
        ref="regionCol"
        title="Регионы"
        :items="regions"
        :selected-id="selectedRegionId"
        selectable
        empty-text="Нет регионов"
        :subtitle="regionSubtitle"
        :errors="regionErrors"
        @select="selectRegion"
        @create="createRegion"
        @update="updateRegion"
        @toggle="toggleRegion"
        @destroy="destroyRegion"
      />

      <!-- Города -->
      <GeoColumn
        ref="cityCol"
        title="Города"
        :crumb="selectedRegion?.name_ru || ''"
        :items="cities"
        :selected-id="selectedCityId"
        selectable
        :ready="!!selectedRegion"
        not-ready-text="← Выберите регион"
        empty-text="Нет городов"
        :subtitle="citySubtitle"
        :errors="cityErrors"
        @select="selectCity"
        @create="createCity"
        @update="updateCity"
        @toggle="toggleCity"
        @destroy="destroyCity"
      />

      <!-- Районы -->
      <GeoColumn
        ref="districtCol"
        title="Районы"
        :crumb="selectedCity?.name_ru || ''"
        :items="districts"
        :ready="!!selectedCity"
        not-ready-text="← Выберите город"
        empty-text="Нет районов"
        :subtitle="districtSubtitle"
        :errors="districtErrors"
        @create="createDistrict"
        @update="updateDistrict"
        @toggle="toggleDistrict"
        @destroy="destroyDistrict"
      />
    </div>
  </AppLayout>
</template>
