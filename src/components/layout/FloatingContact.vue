<template>
  <div class="fixed right-4 bottom-20 md:bottom-8 z-40 flex flex-col items-end space-y-3 pointer-events-none">
    <!-- Back to top button -->
    <button
      v-show="showBackToTop"
      @click="scrollToTop"
      class="pointer-events-auto w-10 h-10 rounded-full bg-slate-800 text-white shadow-lg hover:bg-slate-700 transition-all flex items-center justify-center transform hover:-translate-y-1"
      aria-label="Về đầu trang"
    >
      <ArrowUp class="w-5 h-5" />
    </button>

    <!-- Zalo floating icon -->
    <a
      :href="siteSettings.zalo"
      target="_blank"
      rel="noopener"
      class="pointer-events-auto flex items-center gap-2 group"
    >
      <span class="hidden sm:inline-block px-3 py-1.5 rounded-full bg-white text-blue-600 font-bold text-xs shadow-md border border-blue-100 group-hover:bg-blue-50 transition-all opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0">
        Chat Zalo tư vấn 24/7
      </span>
      <div class="w-12 h-12 rounded-full bg-blue-600 hover:bg-blue-700 text-white shadow-xl flex items-center justify-center transition-transform hover:scale-110">
        <MessageSquare class="w-6 h-6" />
      </div>
    </a>

    <!-- Hotline floating button with pulsing ring -->
    <a
      :href="'tel:' + siteSettings.hotlines[0].number.replace(/\s+/g, '')"
      class="pointer-events-auto relative flex items-center gap-2 group"
    >
      <span class="hidden sm:inline-block px-3 py-1.5 rounded-full bg-white text-slate-800 font-bold text-xs shadow-md border border-brand-gold/30 group-hover:bg-brand-gold-light transition-all opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0">
        Hotline: {{ siteSettings.hotlines[0].number }}
      </span>
      <div class="relative w-13 h-13 rounded-full bg-brand-gold hover:bg-brand-gold-hover text-slate-950 shadow-xl flex items-center justify-center ring-pulse-gold p-3 transition-transform hover:scale-110">
        <PhoneCall class="w-6 h-6 animate-bounce" />
      </div>
    </a>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { ArrowUp, PhoneCall, MessageSquare } from 'lucide-vue-next'
import { siteSettings } from '../../data/siteSettings'

const showBackToTop = ref(false)

function onScroll() {
  showBackToTop.value = window.scrollY > 300
}

function scrollToTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(() => {
  window.addEventListener('scroll', onScroll, { passive: true })
})

onUnmounted(() => {
  window.removeEventListener('scroll', onScroll)
})
</script>

