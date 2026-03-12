import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/store.css',
                'resources/css/store-overrides.css',
                'resources/js/store.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5175,
        strictPort: true,
        hmr: {
            host: 'localhost',
        },
    },
});
