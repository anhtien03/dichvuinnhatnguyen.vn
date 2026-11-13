<template>
  <!-- Global Top Progress Bar on navigation -->
  <TopProgressBar />

  <!-- Global Reading Progress Bar on page scroll -->
  <ScrollProgressBar />

  <!-- Admin layout routes: clean standalone view -->
  <div v-if="isAdminRoute" class="min-h-screen font-sans bg-slate-900 text-slate-100">
    <router-view />
  </div>

  <!-- Public client layout -->
  <div v-else class="min-h-screen flex flex-col font-sans bg-slate-50 text-slate-800 overflow-x-hidden max-w-full">
    <!-- Top utility bar with hotlines & working hours -->
    <TopBar />

    <!-- Main Navigation Header with Live Search & Brand Logo -->
    <HeaderNav />

    <!-- Router View Page Content with Smooth Transition -->
    <main class="flex-grow">
      <router-view v-slot="{ Component, route }">
        <transition name="page-fade" mode="out-in">
          <component :is="Component" :key="route.fullPath" />
        </transition>
      </router-view>
    </main>

    <!-- Footer Section with Legal details, Machinery and Quick links -->
    <FooterSection />

    <!-- Mobile Fixed Bottom Tabbar -->
    <MobileBottomBar />

    <!-- Floating Hotlines & Zalo & Scroll to top -->
    <FloatingContact />

    <!-- Desktop Phone Copy Toast Notification -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 translate-y-3"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-3"
    >
      <div
        v-if="phoneToast"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-slate-900/95 text-white px-5 py-3 rounded-2xl shadow-2xl border border-slate-700/80 backdrop-blur-md flex items-center gap-3 text-xs max-w-md w-[92vw] sm:w-auto"
      >
        <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-brand-gold flex items-center justify-center shrink-0">
          <PhoneCall class="w-4 h-4" />
        </div>
        <div class="flex-1 min-w-0">
          <div class="font-bold text-white flex items-center gap-1.5">
            <span>Đã sao chép:</span>
            <span class="text-brand-gold font-mono text-sm font-black">{{ phoneToast }}</span>
          </div>
          <div class="text-[11px] text-slate-300">Quý khách có thể dán vào Zalo hoặc điện thoại</div>
        </div>
        <a
          :href="'https://zalo.me/' + phoneToast.replace(/\s+/g, '')"
          target="_blank"
          rel="noopener noreferrer"
          class="px-2.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] shrink-0 transition-colors"
        >
          Mở Zalo
        </a>
        <button
          @click="phoneToast = ''"
          class="text-slate-400 hover:text-white p-1 text-sm cursor-pointer"
          aria-label="Đóng"
        >
          ✕
        </button>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { PhoneCall } from 'lucide-vue-next'
import { api } from './services/api'
import TopBar from './components/layout/TopBar.vue'
import HeaderNav from './components/layout/HeaderNav.vue'
import FooterSection from './components/layout/FooterSection.vue'
import MobileBottomBar from './components/layout/MobileBottomBar.vue'
import FloatingContact from './components/layout/FloatingContact.vue'
import TopProgressBar from './components/common/TopProgressBar.vue'
import ScrollProgressBar from './components/common/ScrollProgressBar.vue'

const route = useRoute()
const isAdminRoute = computed(() => route.path.startsWith('/he-thong-admin'))
const phoneToast = ref('')
let phoneToastTimer = null

function formatDisplayPhone(raw) {
  if (!raw) return ''
  const clean = raw.replace(/\D+/g, '')
  if (clean.length === 10) {
    return clean.replace(/(\d{4})(\d{3})(\d{3})/, '$1 $2 $3')
  }
  return raw
}

function handleGlobalPhoneClick(e) {
  const link = e.target.closest('a[href^="tel:"]')
  if (!link) return

  // Check if device is a mobile phone (Android, iPhone, iPad)
  const isMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
  if (isMobile) {
    // Let mobile native dialer open smoothly
    return
  }

  // On Desktop/Laptop: Prevent browser error "Failed to launch 'tel:...' because the scheme does not have a registered handler"
  e.preventDefault()

  const rawHref = link.getAttribute('href') || ''
  const rawNum = rawHref.replace(/^tel:/i, '').trim()
  const displayNum = formatDisplayPhone(rawNum)

  // Copy to clipboard
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(rawNum.replace(/\s+/g, '')).catch(() => {})
  }

  // Show friendly desktop toast notification
  phoneToast.value = displayNum || rawNum
  if (phoneToastTimer) clearTimeout(phoneToastTimer)
  phoneToastTimer = setTimeout(() => {
    phoneToast.value = ''
  }, 4500)
}

onMounted(async () => {
  document.addEventListener('click', handleGlobalPhoneClick, true)
  try {
    const res = await api.getSettings()
    if (res.data?.google_site_verification) {
      let meta = document.querySelector('meta[name="google-site-verification"]')
      if (!meta) {
        meta = document.createElement('meta')
        meta.setAttribute('name', 'google-site-verification')
        document.head.appendChild(meta)
      }
      meta.setAttribute('content', res.data.google_site_verification)
    }

    if (res.data?.google_analytics_id) {
      // Dynamic Google Analytics 4 loader
      const gaId = res.data.google_analytics_id
      if (!document.querySelector(`script[src*="${gaId}"]`)) {
        const gaScript = document.createElement('script')
        gaScript.async = true
        gaScript.src = `https://www.googletagmanager.com/gtag/js?id=${gaId}`
        document.head.appendChild(gaScript)

        const gaInline = document.createElement('script')
        gaInline.textContent = `
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
          gtag('config', '${gaId}');
        `
        document.head.appendChild(gaInline)
      }
    }
  } catch (err) {
    // silent fallback
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleGlobalPhoneClick, true)
  if (phoneToastTimer) clearTimeout(phoneToastTimer)
})
</script>

<style>
/* Page transition styles */
.page-fade-enter-active,
.page-fade-leave-active {
  transition: opacity 0.22s ease, transform 0.22s ease;
}

.page-fade-enter-from {
  opacity: 0;
  transform: translateY(5px);
}

.page-fade-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}
</style>

