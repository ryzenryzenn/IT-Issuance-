import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Brand navy (#003049) — primary. Remapped onto `indigo` so existing
                // components adopt the brand color automatically.
                indigo: {
                    50:  '#e7eef2',
                    100: '#c2d5de',
                    200: '#9bb9c8',
                    300: '#6f97ab',
                    400: '#3f6e88',
                    500: '#1b4d6b',
                    600: '#003049',
                    700: '#002a40',
                    800: '#002233',
                    900: '#001824',
                },
                // Accent amber (#FCBF49) — highlights, active states, logo.
                accent: {
                    50:  '#fff9ec',
                    100: '#fef0cb',
                    200: '#fde29d',
                    300: '#fcd06a',
                    400: '#fcbf49',
                    500: '#f0a91f',
                    600: '#d98b12',
                    700: '#b46a11',
                    800: '#925415',
                    900: '#783f14',
                },
            },
        },
    },

    plugins: [forms],
};
