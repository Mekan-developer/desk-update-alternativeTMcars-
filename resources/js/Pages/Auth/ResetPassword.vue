<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps({
    email: { type: String, required: true },
    token: { type: String, required: true },
})

const isDark = ref(localStorage.getItem('rootboard_login_dark') === 'true')
watch(isDark, val => {
    document.documentElement.classList.toggle('dark', val)
    localStorage.setItem('rootboard_login_dark', String(val))
})
onMounted(() => document.documentElement.classList.toggle('dark', isDark.value))

const lang = ref(localStorage.getItem('rootboard_lang') || 'ru')
const langOpen = ref(false)
const langDropdownRef = ref(null)
const LANGS = { ru: { flag: '🇷🇺', code: 'RU' }, tk: { flag: '🇹🇲', code: 'TK' }, en: { flag: '🇬🇧', code: 'EN' } }
const setLang = (l) => { lang.value = l; langOpen.value = false; localStorage.setItem('rootboard_lang', l) }
const onDocClick = (e) => { if (langDropdownRef.value && !langDropdownRef.value.contains(e.target)) langOpen.value = false }
onMounted(() => document.addEventListener('click', onDocClick))
onUnmounted(() => document.removeEventListener('click', onDocClick))

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
})
const showPwd = ref(false)
const showConfirm = ref(false)
const success = ref(false)

const submit = () => {
    form.post(route('password.store'), {
        onSuccess: () => { success.value = true },
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <Head title="Сброс пароля — Rootboard" />

    <div class="flex h-screen overflow-hidden" :style="{ background: isDark ? '#0f1623' : '#f8fafc' }">

        <!-- TOP BAR -->
        <div class="fixed top-4 right-4 z-50 flex items-center gap-2">
            <div ref="langDropdownRef" class="relative">
                <button type="button" @click.stop="langOpen = !langOpen"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-[10px] border-2 text-sm font-semibold transition-colors"
                    :style="{ background: isDark ? '#1a2332' : '#fff', borderColor: isDark ? '#2d3748' : '#e2e8f0', color: isDark ? '#cbd5e0' : '#2d3748', fontFamily: 'Nunito,sans-serif' }">
                    <span>{{ LANGS[lang].flag }}</span><span>{{ LANGS[lang].code }}</span>
                </button>
                <div v-if="langOpen" class="absolute right-0 top-full mt-1 rounded-xl overflow-hidden shadow-xl border-2 min-w-[90px] z-50"
                    :style="{ background: isDark ? '#1a2332' : '#fff', borderColor: isDark ? '#2d3748' : '#e2e8f0' }">
                    <button v-for="(l, key) in LANGS" :key="key" type="button" @click="setLang(key)"
                        class="flex items-center gap-2 w-full px-3 py-2 text-sm font-semibold text-left"
                        :style="{ color: isDark ? '#cbd5e0' : '#2d3748', fontFamily: 'Nunito,sans-serif' }">
                        <span>{{ l.flag }}</span><span>{{ l.code }}</span>
                    </button>
                </div>
            </div>
            <button type="button" @click="isDark = !isDark"
                class="w-9 h-9 flex items-center justify-center rounded-[10px] border-2 transition-colors"
                :style="{ background: isDark ? '#1a2332' : '#fff', borderColor: isDark ? '#2d3748' : '#e2e8f0', color: isDark ? '#cbd5e0' : '#2d3748' }"
                aria-label="Переключить тему">
                <svg v-if="!isDark" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke-linecap="round"/></svg>
            </button>
        </div>

        <!-- LEFT PANEL -->
        <div class="hidden lg:flex w-[52%] flex-col justify-between relative overflow-hidden"
            style="background:linear-gradient(160deg,#1e2a3b 0%,#0f172a 100%);padding:48px 56px;">
            <div class="absolute pointer-events-none" style="top:-80px;right:-80px;width:500px;height:500px;background:radial-gradient(circle,rgba(67,97,238,.25) 0%,transparent 70%);border-radius:50%;"></div>
            <div class="absolute pointer-events-none" style="bottom:-60px;left:-60px;width:400px;height:400px;background:radial-gradient(circle,rgba(0,180,216,.15) 0%,transparent 70%);border-radius:50%;"></div>

            <a href="/" class="inline-flex items-center gap-3 no-underline w-fit">
                <div style="width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,#4361ee,#00b4d8);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                </div>
                <span class="text-white text-xl font-bold" style="font-family:'Nunito',sans-serif;">Rootboard</span>
            </a>

            <div class="flex-1 flex flex-col justify-center py-12">
                <h1 class="text-white font-bold mb-4" style="font-size:36px;font-family:'Nunito',sans-serif;line-height:1.2;">
                    Восстановление<br><span style="color:#00b4d8;">доступа к аккаунту</span>
                </h1>
                <p style="color:rgba(255,255,255,.55);font-size:15px;line-height:1.6;max-width:380px;font-family:'Nunito',sans-serif;">
                    Придумайте надёжный новый пароль — и вы сразу вернётесь к управлению своими объявлениями.
                </p>
            </div>

            <div style="font-size:12px;font-family:'Nunito',sans-serif;color:rgba(255,255,255,.3);">
                <a href="/" style="color:rgba(255,255,255,.3);text-decoration:none;">← На главную</a>
                <span class="mx-2">·</span>
                <a href="#" style="color:rgba(255,255,255,.3);text-decoration:none;">Политика конфиденциальности</a>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="flex-1 flex items-center justify-center overflow-y-auto px-6 py-10 lg:px-14 lg:py-12"
            :style="{ background: isDark ? '#111827' : '#ffffff' }">
            <div class="w-full" style="max-width:400px;">

                <!-- Success state -->
                <div v-if="success" class="text-center">
                    <div class="mx-auto mb-6 flex items-center justify-center" style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#06d6a0,#00b4d8);box-shadow:0 8px 32px rgba(6,214,160,.4);">
                        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <h2 class="font-bold mb-2" style="font-size:26px;font-family:'Nunito',sans-serif;" :style="{ color: isDark ? '#e2e8f0' : '#2d3748' }">Пароль обновлён!</h2>
                    <p class="mb-8 text-sm" style="font-family:'Nunito',sans-serif;color:#718096;">Ваш пароль успешно изменён. Теперь вы можете войти с новым паролем.</p>
                    <a :href="route('login')"
                        class="w-full flex items-center justify-center gap-2 text-white font-bold rounded-[10px] transition-all duration-200"
                        style="padding:14px;font-size:15px;font-family:'Nunito',sans-serif;background:linear-gradient(135deg,#4361ee,#3a0ca3);box-shadow:0 4px 16px rgba(67,97,238,.35);">
                        Войти в аккаунт
                    </a>
                </div>

                <!-- Form -->
                <div v-else>
                    <h2 class="font-bold mb-1" style="font-size:26px;font-family:'Nunito',sans-serif;" :style="{ color: isDark ? '#e2e8f0' : '#2d3748' }">Новый пароль</h2>
                    <p class="mb-6 text-sm" style="font-family:'Nunito',sans-serif;color:#718096;">Придумайте новый пароль для вашего аккаунта.</p>

                    <form @submit.prevent="submit" novalidate>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold mb-1.5" style="font-family:'Nunito',sans-serif;" :style="{ color: isDark ? '#cbd5e0' : '#2d3748' }">Новый пароль</label>
                            <div class="relative">
                                <div class="absolute left-3.5 top-1/2 -translate-y-1/2" style="color:#718096;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                                </div>
                                <input v-model="form.password" :type="showPwd ? 'text' : 'password'" autocomplete="new-password" placeholder="Новый пароль"
                                    class="w-full rounded-[10px] border-2 text-sm font-semibold transition-all duration-200 outline-none"
                                    style="padding:12px 42px 12px 42px;font-family:'Nunito',sans-serif;"
                                    :style="{ background: isDark ? '#1a2332' : '#f8fafc', borderColor: form.errors.password ? '#e53e3e' : (isDark ? '#2d3748' : '#e2e8f0'), color: isDark ? '#cbd5e0' : '#2d3748' }"
                                />
                                <button type="button" @click="showPwd = !showPwd" class="absolute right-3.5 top-1/2 -translate-y-1/2" style="color:#718096;" aria-label="Показать/скрыть пароль">
                                    <svg v-if="!showPwd" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="text-xs mt-1" style="color:#e53e3e;font-family:'Nunito',sans-serif;">{{ form.errors.password }}</p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-semibold mb-1.5" style="font-family:'Nunito',sans-serif;" :style="{ color: isDark ? '#cbd5e0' : '#2d3748' }">Подтвердите пароль</label>
                            <div class="relative">
                                <div class="absolute left-3.5 top-1/2 -translate-y-1/2" style="color:#718096;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                                </div>
                                <input v-model="form.password_confirmation" :type="showConfirm ? 'text' : 'password'" autocomplete="new-password" placeholder="Подтвердите пароль"
                                    class="w-full rounded-[10px] border-2 text-sm font-semibold transition-all duration-200 outline-none"
                                    style="padding:12px 42px 12px 42px;font-family:'Nunito',sans-serif;"
                                    :style="{ background: isDark ? '#1a2332' : '#f8fafc', borderColor: form.errors.password_confirmation ? '#e53e3e' : (isDark ? '#2d3748' : '#e2e8f0'), color: isDark ? '#cbd5e0' : '#2d3748' }"
                                />
                                <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3.5 top-1/2 -translate-y-1/2" style="color:#718096;" aria-label="Показать/скрыть пароль">
                                    <svg v-if="!showConfirm" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                </button>
                            </div>
                            <p v-if="form.errors.password_confirmation" class="text-xs mt-1" style="color:#e53e3e;font-family:'Nunito',sans-serif;">{{ form.errors.password_confirmation }}</p>
                        </div>

                        <button type="submit" :disabled="form.processing"
                            class="w-full flex items-center justify-center gap-2 text-white font-bold rounded-[10px] transition-all duration-200"
                            style="padding:14px;font-size:15px;font-family:'Nunito',sans-serif;background:linear-gradient(135deg,#4361ee,#3a0ca3);box-shadow:0 4px 16px rgba(67,97,238,.35);border:none;"
                            :style="{ opacity: form.processing ? .7 : 1 }">
                            <svg v-if="form.processing" class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                            <span>{{ form.processing ? 'Сохраняем…' : 'Сохранить пароль' }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
