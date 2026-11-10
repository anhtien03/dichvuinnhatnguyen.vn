import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import './assets/main.css'
import { vReveal } from './directives/vReveal'

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.directive('reveal', vReveal)

app.mount('#app')

