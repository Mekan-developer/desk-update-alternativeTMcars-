import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                navy:    '#1e2a3b',
                blue:    { DEFAULT: '#4361ee', dark: '#3451d1', light: '#e8ecfd' },
                teal:    '#00b4d8',
                green:   '#06d6a0',
                orange:  '#fb8500',
                pink:    '#f72585',
                purple:  '#7c3aed',
                red:     '#ef4444',
                ink:     '#2d3748',
                muted:   '#718096',
                line:    '#e2e8f0',
                surface: '#f4f6fb',
                dnavy:   '#141d2b',
                dbg:     '#0f1623',
                dcard:   '#1a2332',
                dline:   '#2d3748',
            },
            borderRadius: {
                card: '16px',
                btn:  '10px',
                pill: '20px',
            },
            boxShadow: {
                soft:  '0 1px 8px rgba(0,0,0,0.05)',
                'lg2': '0 8px 24px rgba(0,0,0,0.09)',
            },
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
                data: ['Inter', 'sans-serif'],
            },
        },
    },
    plugins: [forms],
};
