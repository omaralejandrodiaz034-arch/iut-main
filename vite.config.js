import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    // 👇 Agrega esta sección de aquí abajo
    server: {
        host: '0.0.0.0', // Escucha en toda la red local
        hmr: {
            host: '192.168.100.195', // La IP de tu computadora
        },
    },
});
