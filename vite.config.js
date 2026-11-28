import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src')
    }
  },
  server: {
    host: '0.0.0.0',
    port: 8090,
    strictPort: true,
    proxy: {
      '/api': {
        target: 'http://127.0.0.1:8000',
        changeOrigin: true
      },
      '/storage': {
        target: 'http://127.0.0.1:8000',
        changeOrigin: true
      }
    }
  },
  preview: {
    ...(process.env.SEO_RENDER_PROXY === '1' ? { proxy: { '/api': { target: 'http://127.0.0.1:8000', changeOrigin: true }, '/storage': { target: 'http://127.0.0.1:8000', changeOrigin: true } } } : {}),
    host: '0.0.0.0',
    port: 8090,
    strictPort: true
  }
})
