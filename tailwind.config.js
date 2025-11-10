import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, typography],
    // tailwind.config.js

    theme: {
        extend: {
            colors: {
                accent: {
                    500: '#8b5cf6',   // 💜 púrpura neón principal
                    600: '#7240e7ff', // más intenso
                    700: '#6d28d9',
                },
            },
            boxShadow: {
                glow: '0 0 15px rgba(139,92,246,0.4), 0 0 30px rgba(114,64,231,0.25)',
            },
        },
    },
    plugins: [],
};

