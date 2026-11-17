<template>
  <div class="fixed right-4 bottom-20 md:bottom-8 z-40 flex flex-col items-end space-y-3 pointer-events-none">
    <!-- Popup danh sách 3 số Hotline / Zalo khi bấm -->
    <transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="opacity-0 translate-y-2 scale-95"
      enter-to-class="opacity-100 translate-y-0 scale-100"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="opacity-100 translate-y-0 scale-100"
      leave-to-class="opacity-0 translate-y-2 scale-95"
    >
      <div
        v-if="isMenuOpen"
        class="pointer-events-auto w-72 sm:w-80 rounded-2xl bg-white border border-slate-200 shadow-2xl p-3.5 space-y-2.5"
      >
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
          <span class="text-xs font-black uppercase tracking-wider text-slate-800">
            {{ activeMode === 'zalo' ? 'Chat Zalo Tư Vấn Nhanh' : 'Hotline Tư Vấn 24/7' }}
          </span>
          <button
            type="button"
            @click="isMenuOpen = false"
            class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 cursor-pointer"
            aria-label="Đóng"
          >
            <X class="w-4 h-4" />
          </button>
        </div>

        <div class="space-y-2">
          <div
            v-for="(h, idx) in siteSettings.hotlines"
            :key="idx"
            class="flex items-center justify-between gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-100 hover:border-brand-gold/50 transition-colors"
          >
            <div class="min-w-0">
              <div class="text-xs font-black text-slate-900">{{ h.number }}</div>
              <div class="text-[11px] text-slate-500 truncate">{{ h.nvkd || h.label }}</div>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <a
                :href="'tel:' + h.number.replace(/\s+/g, '')"
                class="px-2.5 py-1.5 rounded-lg bg-brand-gold hover:bg-brand-gold-hover text-slate-950 font-bold text-[11px] flex items-center gap-1 transition-colors"
                title="Gọi ngay"
              >
                <PhoneCall class="w-3.5 h-3.5" />
                <span>Gọi</span>
              </a>
              <a
                :href="'https://zalo.me/' + h.number.replace(/\s+/g, '')"
                target="_blank"
                rel="noopener"
                class="px-2.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] flex items-center gap-1 transition-colors"
                title="Nhắn Zalo"
              >
                <MessageSquare class="w-3.5 h-3.5" />
                <span>Zalo</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- Back to top button -->
    <button
      v-show="showBackToTop"
      @click="scrollToTop"
      class="pointer-events-auto w-10 h-10 rounded-full bg-slate-800 text-white shadow-lg hover:bg-slate-700 transition-all flex items-center justify-center transform hover:-translate-y-1 cursor-pointer"
      aria-label="Về đầu trang"
    >
      <ArrowUp class="w-5 h-5" />
    </button>

    <!-- Zalo floating icon -->
    <button
      type="button"
      @click="toggleMenu('zalo')"
      class="pointer-events-auto flex items-center gap-2 group cursor-pointer"
      aria-label="Chat Zalo tư vấn"
    >
      <span class="hidden sm:inline-block px-3 py-1.5 rounded-full bg-white text-blue-600 font-bold text-xs shadow-md border border-blue-100 group-hover:bg-blue-50 transition-all opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0">
        Chat Zalo tư vấn 24/7
      </span>
      <div class="w-12 h-12 rounded-full bg-blue-600 hover:bg-blue-700 text-white shadow-xl flex items-center justify-center transition-transform hover:scale-110">
        <MessageSquare class="w-6 h-6" />
      </div>
    </button>

    <!-- Hotline floating button with pulsing ring -->
    <button
      type="button"
      @click="toggleMenu('call')"
      class="pointer-events-auto relative flex items-center gap-2 group cursor-pointer"
      aria-label="Gọi Hotline"
    >
      <span class="hidden sm:inline-block px-3 py-1.5 rounded-full bg-white text-slate-800 font-bold text-xs shadow-md border border-brand-gold/30 group-hover:bg-brand-gold-light transition-all opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0 whitespace-nowrap">
        Hotline: {{ siteSettings.hotlines.map(h => h.number).join(' • ') }}
      </span>
      <div class="relative w-13 h-13 rounded-full bg-brand-gold hover:bg-brand-gold-hover text-slate-950 shadow-xl flex items-center justify-center ring-pulse-gold p-3 transition-transform hover:scale-110">
        <PhoneCall class="w-6 h-6 animate-bounce" />
      </div>
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { ArrowUp, PhoneCall, MessageSquare, X } from 'lucide-vue-next'
import { siteSettings } from '../../data/siteSettings'

const showBackToTop = ref(false)
const isMenuOpen = ref(false)
const activeMode = ref('call')

function toggleMenu(mode) {
  if (isMenuOpen.value && activeMode.value === mode) {
    isMenuOpen.value = false
  } else {
    activeMode.value = mode
    isMenuOpen.value = true
  }
}

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
