import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/filament.css'],
            refresh: true,
            // Disable the plugin's automatic output directory management
        }),
        tailwindcss(),
    ],
    build: {
        outDir: 'dist',
        emptyOutDir: true,
    }
});
