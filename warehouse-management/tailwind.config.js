import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                // Primary - Biru (Indigo 600)
                'primary': '#4f46e5',
                // Secondary Background - Abu-abu Terang (Gray 50)
                'secondary-bg': '#f9fafb',
            },
            fontFamily: {
                // Mengubah font default menjadi Inter
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};