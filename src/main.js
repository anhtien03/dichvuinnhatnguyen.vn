import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import './assets/main.css'
import { vReveal } from './directives/vReveal'

const app = createApp(App)

// Tự động tải lại trang khi phát hiện bản build mới (Vite Chunk Mismatch Recovery)
window.addEventListener('vite:preloadError', (event) => {
  event.preventDefault()
  window.location.reload()
})

window.addEventListener('unhandledrejection', (event) => {
  const msg = event?.reason?.message || ''
  if (/Failed to fetch dynamically imported module/i.test(msg) || /Expected a JavaScript-or-Wasm module script/i.test(msg)) {
    event.preventDefault()
    const key = 'chunk_reload_lock'
    const last = sessionStorage.getItem(key)
    const now = Date.now()
    if (!last || now - Number(last) > 8000) {
      sessionStorage.setItem(key, String(now))
      window.location.reload()
    }
  }
})

app.use(createPinia())
app.use(router)
app.directive('reveal', vReveal)

app.mount('#app')

