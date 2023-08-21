import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/scss/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
                compilerOptions: {
                    isCustomElement: (tag) => ['Head', 'layer', 'inwrapper', 'outwrapper', 'form-input-border', 'form-input-text', 'form-textarea'].includes(tag),
                }
            },
        }),
    ],
    optimizeDeps: {
        exclude: ['ink-mde']
    }
});
