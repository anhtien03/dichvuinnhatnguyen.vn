<template>
  <!-- Global Top Progress Bar on navigation -->
  <TopProgressBar />

  <!-- Global Reading Progress Bar on page scroll -->
  <ScrollProgressBar />

  <!-- Admin layout routes: clean standalone view -->
  <div v-if="isAdminRoute" class="min-h-screen font-sans bg-slate-900 text-slate-100">
    <router-view v-slot="{ Component }">
      <transition name="page-fade" mode="out-in">
        <component :is="Component" />
      </transition>
    </router-view>
  </div>

  <!-- Public client layout -->
  <div v-else class="min-h-screen flex flex-col font-sans bg-slate-50 text-slate-800">
    <!-- Top utility bar with hotlines & working hours -->
    <TopBar />

    <!-- Main Navigation Header with Live Search & Brand Logo -->
    <HeaderNav />

    <!-- Router View Page Content with Smooth Transition -->
    <main class="flex-grow">
      <router-view v-slot="{ Component }">
        <transition name="page-fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <!-- Footer Section with Legal details, Machinery and Quick links -->
    <FooterSection />

    <!-- Mobile Fixed Bottom Tabbar -->
    <MobileBottomBar />

    <!-- Floating Hotlines & Zalo & Scroll to top -->
    <FloatingContact />
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
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

onMounted(async () => {
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

