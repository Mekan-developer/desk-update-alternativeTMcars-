<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import Toasts from '@/Components/Toasts.vue'

const { t, locale } = useI18n()

// ── Theme ──────────────────────────────────────────────────────────────────
const dark = ref(localStorage.getItem('dk') === '1')
watch(dark, v => {
    document.documentElement.classList.toggle('dark', v)
    localStorage.setItem('dk', v ? '1' : '0')
})
onMounted(() => {
    document.documentElement.classList.toggle('dark', dark.value)
})

// ── Sidebar ────────────────────────────────────────────────────────────────
const collapsed = ref(localStorage.getItem('sb') === '1')
watch(collapsed, v => localStorage.setItem('sb', v ? '1' : '0'))

// ── i18n ───────────────────────────────────────────────────────────────────
function setLang(l) {
    locale.value = l
    localStorage.setItem('lang', l)
}

// ── Shared data ────────────────────────────────────────────────────────────
const page = usePage()
const user   = computed(() => page.props.auth?.user)
const counts = computed(() => page.props.counts || {})

// ── Menu ───────────────────────────────────────────────────────────────────
const sections = computed(() => [
    { title: 'ГЛАВНОЕ', items: [
        { label: t('nav.dashboard'),   routeName: 'dashboard',         icon: 'grid' },
        { label: t('nav.users'),       routeName: 'users.index',       icon: 'users',   badge: 'newUsers' },
        { label: t('nav.listings'),    routeName: 'listings.index',    icon: 'listing', badge: 'pendingListings' },
        { label: t('nav.videos'),      routeName: 'videos.index',      icon: 'video',   badge: 'pendingVideos' },
        { label: t('nav.chat'),        routeName: 'chat.index',        icon: 'chat',    badge: 'unreadChats' },
    ]},
    { title: 'КОНТЕНТ', items: [
        { label: t('nav.categories'),  routeName: 'categories.index',  icon: 'folder' },
        { label: t('nav.regions'),     routeName: 'regions.index',     icon: 'map' },
        { label: t('nav.news'),        routeName: 'news.index',        icon: 'news' },
    ]},
    { title: 'МОДЕРАЦИЯ', items: [
        { label: t('nav.complaints'),  routeName: 'complaints.index',  icon: 'flag',  badge: 'newComplaints' },
        { label: t('nav.reviews'),     routeName: 'reviews.index',     icon: 'star',  badge: 'pendingReviews' },
    ]},
    { title: 'СИСТЕМА', items: [
        { label: t('nav.tariffs'),     routeName: 'tariffs.index',     icon: 'tag' },
        { label: t('nav.statistics'), routeName: 'statistics.index',  icon: 'chart' },
        ...(user.value?.role === 'admin' ? [{ label: t('nav.push'), routeName: 'push.index', icon: 'bell' }] : []),
    ]},
])

function isActive(routeName) {
    try { return route().current(routeName) } catch { return false }
}

// ── User menu ──────────────────────────────────────────────────────────────
const userMenuOpen = ref(false)

function logout() {
    router.post(route('logout'))
}
</script>

<template>
  <div class="flex h-screen overflow-hidden bg-surface dark:bg-dbg">

    <!-- ── SIDEBAR ──────────────────────────────────────────────────────── -->
    <aside
      class="flex flex-col flex-shrink-0 bg-navy transition-all duration-300 overflow-hidden"
      :style="{ width: collapsed ? '68px' : '240px' }"
    >
      <!-- Logo -->
      <div class="flex items-center gap-3 px-4 py-5 border-b border-white/10">
        <div class="flex-shrink-0 flex h-9 w-9 items-center justify-center rounded-[9px] bg-blue">
          <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 6h18M3 12h18M3 18h18"/>
          </svg>
        </div>
        <span v-if="!collapsed" class="text-white text-[16px] font-extrabold leading-tight">Доска<br><span class="text-blue-light opacity-70 text-[11px] font-semibold">объявлений</span></span>
      </div>

      <!-- Nav -->
      <nav class="flex-1 overflow-y-auto py-3 space-y-1">
        <template v-for="section in sections" :key="section.title">
          <div v-if="!collapsed" class="px-4 pt-4 pb-1 text-[10px] font-black uppercase tracking-[.12em] text-white/25">
            {{ section.title }}
          </div>
          <Link
            v-for="item in section.items" :key="item.routeName"
            :href="route(item.routeName)"
            class="flex items-center gap-3 mx-2 px-3 py-2.5 rounded-[10px] transition-all duration-150 group"
            :class="isActive(item.routeName)
              ? 'bg-blue text-white shadow-[0_4px_12px_rgba(67,97,238,.4)]'
              : 'text-white/60 hover:bg-white/8 hover:text-white'"
          >
            <!-- Icons -->
            <span class="flex-shrink-0 w-5 h-5">
              <svg v-if="item.icon === 'grid'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><rect x="3" y="3" width="7" height="7" stroke-width="2" stroke-linecap="round"/><rect x="14" y="3" width="7" height="7" stroke-width="2" stroke-linecap="round"/><rect x="14" y="14" width="7" height="7" stroke-width="2" stroke-linecap="round"/><rect x="3" y="14" width="7" height="7" stroke-width="2" stroke-linecap="round"/></svg>
              <svg v-else-if="item.icon === 'users'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-width="2" stroke-linecap="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4" stroke-width="2"/><path stroke-width="2" stroke-linecap="round" d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
              <svg v-else-if="item.icon === 'listing'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-width="2" stroke-linecap="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
              <svg v-else-if="item.icon === 'video'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><polygon stroke-width="2" stroke-linecap="round" points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2" stroke-width="2"/></svg>
              <svg v-else-if="item.icon === 'chat'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-width="2" stroke-linecap="round" d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
              <svg v-else-if="item.icon === 'folder'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-width="2" stroke-linecap="round" d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
              <svg v-else-if="item.icon === 'map'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><polygon stroke-width="2" stroke-linecap="round" points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18" stroke-width="2"/><line x1="16" y1="6" x2="16" y2="22" stroke-width="2"/></svg>
              <svg v-else-if="item.icon === 'news'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-width="2" stroke-linecap="round" d="M4 6h16M4 10h16M4 14h8M4 18h8"/><rect x="2" y="3" width="20" height="18" rx="2" stroke-width="2"/></svg>
              <svg v-else-if="item.icon === 'flag'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-width="2" stroke-linecap="round" d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15" stroke-width="2"/></svg>
              <svg v-else-if="item.icon === 'star'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><polygon stroke-width="2" stroke-linecap="round" points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <svg v-else-if="item.icon === 'tag'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-width="2" stroke-linecap="round" d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7" stroke-width="2"/></svg>
              <svg v-else-if="item.icon === 'chart'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><line x1="18" y1="20" x2="18" y2="10" stroke-width="2"/><line x1="12" y1="20" x2="12" y2="4" stroke-width="2"/><line x1="6" y1="20" x2="6" y2="14" stroke-width="2"/></svg>
              <svg v-else-if="item.icon === 'bell'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-width="2" stroke-linecap="round" d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path stroke-width="2" d="M13.73 21a2 2 0 01-3.46 0"/></svg>
            </span>
            <span v-if="!collapsed" class="flex-1 text-[13px] font-bold truncate">{{ item.label }}</span>
            <span
              v-if="item.badge && counts[item.badge] > 0 && !collapsed"
              class="rounded-pill bg-pink px-[6px] py-0.5 text-[10px] font-extrabold text-white"
            >{{ counts[item.badge] }}</span>
          </Link>
        </template>
      </nav>

      <!-- Collapse toggle -->
      <button
        @click="collapsed = !collapsed"
        class="flex items-center justify-center h-10 border-t border-white/10 text-white/40 hover:text-white transition"
      >
        <svg class="h-4 w-4 transition-transform duration-300" :class="collapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
      </button>
    </aside>

    <!-- ── MAIN AREA ─────────────────────────────────────────────────────── -->
    <div class="flex flex-1 flex-col min-w-0 overflow-hidden">

      <!-- Topbar -->
      <header class="flex-shrink-0 flex items-center justify-between h-16 px-6 bg-white border-b border-line dark:bg-dcard dark:border-dline shadow-soft">
        <!-- Page title slot -->
        <div class="font-extrabold text-[16px] text-ink dark:text-slate-100">
          <slot name="header" />
        </div>

        <!-- Right controls -->
        <div class="flex items-center gap-3">
          <!-- Lang switcher -->
          <div class="flex items-center gap-1 rounded-pill bg-surface dark:bg-dbg border border-line dark:border-dline px-2 py-1">
            <button
              v-for="l in ['ru', 'tk']" :key="l"
              @click="setLang(l)"
              class="px-2.5 py-0.5 rounded-pill text-[11px] font-extrabold uppercase transition"
              :class="locale === l ? 'bg-blue text-white' : 'text-muted hover:text-ink dark:hover:text-slate-200'"
            >{{ l }}</button>
          </div>

          <!-- Theme toggle -->
          <button
            @click="dark = !dark"
            class="flex h-8 w-8 items-center justify-center rounded-btn text-muted hover:text-blue hover:bg-blue-light dark:hover:bg-white/10 transition"
          >
            <svg v-if="!dark" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
            <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
          </button>

          <!-- User menu -->
          <div class="relative">
            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 rounded-btn px-2 py-1 hover:bg-surface dark:hover:bg-white/5 transition">
              <div class="h-8 w-8 rounded-full bg-blue flex items-center justify-center text-[12px] font-extrabold text-white">
                {{ user?.name?.charAt(0)?.toUpperCase() || 'A' }}
              </div>
              <div v-if="false" class="text-left hidden sm:block">
                <div class="text-[13px] font-bold text-ink dark:text-slate-100">{{ user?.name }}</div>
                <div class="text-[11px] text-muted">{{ user?.phone }}</div>
              </div>
              <svg class="h-3.5 w-3.5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <Transition name="menu">
              <div
                v-if="userMenuOpen"
                @click.outside="userMenuOpen = false"
                v-click-outside="() => userMenuOpen = false"
                class="absolute right-0 top-full mt-2 w-48 rounded-card bg-white shadow-lg2 border border-line dark:bg-dcard dark:border-dline z-50 overflow-hidden"
              >
                <div class="px-4 py-3 border-b border-line dark:border-dline">
                  <div class="text-[13px] font-bold text-ink dark:text-slate-100">{{ user?.name }}</div>
                  <div class="text-[11px] text-muted">{{ user?.phone }}</div>
                </div>
                <button
                  @click="logout"
                  class="flex w-full items-center gap-2 px-4 py-3 text-[13px] font-semibold text-red hover:bg-red/5 transition"
                >
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                  Выйти
                </button>
              </div>
            </Transition>
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main class="flex-1 overflow-y-auto p-6">
        <slot />
      </main>
    </div>

    <!-- Global Toasts -->
    <Toasts />
  </div>
</template>

<style scoped>
.menu-enter-active, .menu-leave-active { transition: all .15s ease; }
.menu-enter-from, .menu-leave-to { opacity: 0; transform: translateY(-4px); }
</style>
