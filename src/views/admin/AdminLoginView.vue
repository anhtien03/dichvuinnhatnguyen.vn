<template>
  <div class="min-h-screen bg-slate-950 flex items-center justify-center p-4">
    <!-- Background glow decorative effects -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-1/4 left-1/3 w-96 h-96 bg-brand-gold/10 rounded-full blur-3xl"></div>
      <div class="absolute bottom-1/4 right-1/3 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-8 backdrop-blur-xl">
      <!-- Brand Header -->
      <div class="text-center mb-8">
        <img
          src="/logo-icon.png"
          alt="Nhật Nguyên"
          class="w-14 h-14 object-contain rounded-2xl mx-auto mb-4"
        />
        <h1 class="text-2xl font-black text-white tracking-tight">Trang Quản Trị Hệ Thống</h1>
        <p class="text-xs text-slate-400 mt-1">Xưởng In Offset & Kỹ Thuật Số Nhật Nguyên</p>
      </div>

      <!-- Error Alert -->
      <div v-if="errorMessage" class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs flex items-center gap-2">
        <AlertCircle class="w-4 h-4 shrink-0" />
        <span>{{ errorMessage }}</span>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="handleLogin" class="space-y-5">
        <div>
          <label class="block text-xs font-semibold text-slate-300 mb-2">Email Quản Trị</label>
          <div class="relative">
            <Mail class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
            <input
              v-model="email"
              type="email"
              required
              placeholder="name@example.com"
              class="w-full bg-slate-800/80 border border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold transition-colors"
            />
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-300 mb-2">Mật Khẩu</label>
          <div class="relative">
            <Lock class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
            <input
              v-model="password"
              :type="showPassword ? 'text' : 'password'"
              required
              placeholder="••••••••"
              class="w-full bg-slate-800/80 border border-slate-700 rounded-xl pl-10 pr-10 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold transition-colors"
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors"
            >
              <EyeOff v-if="showPassword" class="w-4 h-4" />
              <Eye v-else class="w-4 h-4" />
            </button>
          </div>
        </div>

        <button
          type="submit"
          :disabled="isLoading"
          class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-brand-gold to-amber-500 hover:from-amber-400 hover:to-brand-gold text-slate-950 font-black text-sm shadow-lg shadow-brand-gold/25 transition-all transform hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-60 flex items-center justify-center gap-2 cursor-pointer"
        >
          <Loader2 v-if="isLoading" class="w-4 h-4 animate-spin" />
          <span>{{ isLoading ? 'Đang xác thực...' : 'Đăng Nhập Quản Trị' }}</span>
        </button>
      </form>

      <!-- Return link -->
      <div class="mt-8 pt-6 border-t border-slate-800/80 text-center">
        <router-link to="/" class="inline-block text-xs text-brand-gold hover:underline">
          ← Quay về trang chủ website
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Mail, Lock, Eye, EyeOff, AlertCircle, Loader2 } from 'lucide-vue-next'
import { api, setAdminToken, setAdminUser } from '../../services/api'

const router = useRouter()
const email = ref('')
const password = ref('')
const showPassword = ref(false)
const isLoading = ref(false)
const errorMessage = ref('')

async function handleLogin() {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const res = await api.login({
      email: email.value,
      password: password.value
    })

    if (res.token) {
      setAdminToken(res.token)
      setAdminUser(res.user)
      router.push('/admin/dashboard')
    }
  } catch (err) {
    errorMessage.value = err.message || 'Email hoặc mật khẩu không chính xác.'
  } finally {
    isLoading.value = false
  }
}
</script>

