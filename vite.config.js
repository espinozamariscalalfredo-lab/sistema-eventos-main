import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// FRONTEND. 20/10/2026: Vite compila el CSS y JavaScript que usan las vistas de Laravel.
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
