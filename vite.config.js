import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/custom.scss',
                'resources/sass/jstree-custom-table.scss',
            ],
            refresh: true,
        }),
    ],
});
