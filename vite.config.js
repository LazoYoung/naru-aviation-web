import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import {nodePolyfills} from "vite-plugin-node-polyfills";

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
                    isCustomElement: (tag) => [
                        'Head',
                        'layer',
                        'inwrapper',
                        'outwrapper',
                        'form-input-border',
                        'form-input-text',
                        'form-textarea',
                        'form-datetime',
                    ].includes(tag),
                }
            },
        }),
        nodePolyfills({
            exclude: ['fs'],
            globals: {
                Buffer: true,
                global: true,
                process: true
            },
            protocolImports: true
        })
    ],
    optimizeDeps: {
        exclude: ['ink-mde']
    }
});
