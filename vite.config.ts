import {defineConfig} from 'vite'
import laravel from 'vite-plugin-laravel'
import vue from '@vitejs/plugin-vue'
import vueJsx from '@vitejs/plugin-vue-jsx'
import tailwind from 'tailwindcss'
import autoprefixer from 'autoprefixer'

export default defineConfig({
  plugins: [vue(), vueJsx(), laravel({
    postcss: [
      tailwind(),
      autoprefixer(),
    ],
    watch: []
  })],
})
