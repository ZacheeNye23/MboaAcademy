import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            // ── Couleurs MboaAcademy ──────────────────────────
            colors: {
                'green-deep':   '#0d5c2e',
                'green-mid':    '#1a8a47',
                'green-bright': '#25c26e',
                'green-light':  '#d4f5e2',
                'gold':         '#e8b84b',
                'gold-light':   '#fdf0c7',
                'earth':        '#7a3b1e',
                'cream':        '#faf6ef',
                'dark':         '#0a1a0f',
                'text':         '#1c2b1f',
                'text-light':   '#5a7060',
            },

            // ── Typographie ───────────────────────────────────
            fontFamily: {
                'playfair': ['"Playfair Display"', ...defaultTheme.fontFamily.serif],
                'outfit':   ['Outfit', ...defaultTheme.fontFamily.sans],
                'sans':     ['Outfit', ...defaultTheme.fontFamily.sans],
            },

            // ── Animations ────────────────────────────────────
            keyframes: {
                fadeUp: {
                    '0%':   { opacity: '0', transform: 'translateY(30px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                fadeIn: {
                    '0%':   { opacity: '0' },
                    '100%': { opacity: '1' },
                },
            },
            animation: {
                'fadeUp':  'fadeUp 0.9s ease both',
                'fadeIn':  'fadeIn 1.2s ease both 0.3s',
            },
        },
    },

    plugins: [forms],
};