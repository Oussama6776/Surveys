import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/css/survey-advanced.css',
                'resources/js/survey-advanced.js',
                'resources/js/analytics.js',
                'resources/js/themes.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
