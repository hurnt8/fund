import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/client.css',
                'resources/js/client.js',
                'resources/css/client-app.css',
                'resources/js/client-app.js',
            ],
            refresh: true,
        }),
    ],
});
