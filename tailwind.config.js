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
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },
            // Central admin design tokens (see the "Central Admin Design" canvas).
            // Accent = Tailwind teal-700 / teal-500.
            colors: {
                ink: '#0F1B2D',      // sidebar, headings
                ground: '#F4F6F8',   // page background
                line: '#E3E8EE',     // borders
                muted: '#5B6B7F',    // secondary text (5:1 on white)
            },
        },
    },

    plugins: [forms],
};
