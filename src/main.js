import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import './assets/main.css'
import { vReveal } from './directives/vReveal'

// Luôn bắt đầu ở đầu trang khi bấm F5 tải lại trang (Back/Forward vẫn giữ qua Vue Router scrollBehavior)
if ('scrollRestoration' in history) {
  history.scrollRestoration = 'manual'
}

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

// Resolve the initial lazy route before mounting the shared layout.
// Otherwise the footer can paint above the still-empty router view.
router.isReady().then(() => app.mount('#app')).catch(error => {
  console.error('[Initial navigation]', error)
  app.mount('#app')
})

