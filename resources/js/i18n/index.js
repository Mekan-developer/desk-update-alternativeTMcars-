import { createI18n } from 'vue-i18n'
import ru from './ru'
import tk from './tk'

// Русская плюрализация: 'город | города | городов' → индекс формы 0/1/2.
// Для tk достаточно одной формы (после числительных мн. число не меняется).
function ruPluralRule(choice) {
    const m10 = choice % 10
    const m100 = choice % 100
    if (m10 === 1 && m100 !== 11) return 0
    if (m10 >= 2 && m10 <= 4 && (m100 < 10 || m100 >= 20)) return 1
    return 2
}

export const i18n = createI18n({
    legacy: false,
    locale: localStorage.getItem('lang') || 'ru',
    fallbackLocale: 'ru',
    pluralRules: { ru: ruPluralRule },
    messages: { ru, tk },
})
