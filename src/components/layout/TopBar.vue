<template>
  <div class="bg-brand-blue-dark text-white text-[11px] sm:text-xs py-1.5 sm:py-2 border-b border-brand-blue/60 select-none overflow-hidden">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 flex items-center justify-between gap-2 sm:gap-4">
      <!-- Left: Working Hours (Compact on mobile) -->
      <div class="flex items-center text-slate-200 min-w-0 shrink">
        <span class="flex items-center gap-1.5 truncate">
          <Clock class="w-3.5 h-3.5 text-brand-gold shrink-0" />
          <span class="hidden md:inline">{{ siteSettings.workingHours }}</span>
          <span class="hidden sm:inline md:hidden">T2 - CN: 08:00 - 20:00</span>
          <span class="sm:hidden text-[10.5px]">08:00 - 20:00</span>
        </span>
      </div>

      <!-- Right: Hotlines & Zalo -->
      <div class="flex items-center space-x-2 sm:space-x-4 shrink-0">
        <span class="hidden lg:inline text-slate-300 font-medium">Hotline:</span>
        <div class="flex items-center space-x-2 sm:space-x-3 divide-x divide-white/20">
          <a
            v-for="(h, idx) in siteSettings.hotlines"
            :key="idx"
            :href="'tel:' + h.number.replace(/\s+/g, '')"
            class="font-extrabold text-brand-gold hover:text-white transition-colors items-center gap-1 whitespace-nowrap"
            :class="idx === 0 ? 'flex pl-0 text-[11px] sm:text-xs' : (idx === 1 ? 'hidden sm:flex pl-2 sm:pl-3' : 'hidden md:flex pl-2 sm:pl-3')"
            :title="h.label + ' - ' + h.nvkd"
          >
            <Phone class="w-3 h-3 text-brand-gold shrink-0" />
            <span>{{ h.number }}</span>
            <span v-if="h.label && idx === 0" class="hidden xl:inline text-[10px] font-normal text-slate-300">({{ h.label }})</span>
          </a>
        </div>
        <!-- Zalo Chat Button -->
        <a
          :href="siteSettings.zalo"
          target="_blank"
          rel="noopener"
          class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-0.5 rounded text-[10.5px] sm:text-[11px] font-bold transition-colors flex items-center gap-1 shadow-sm shrink-0 whitespace-nowrap"
        >
          <span class="sm:hidden">Zalo</span>
          <span class="hidden sm:inline">Chat Zalo</span>
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Clock, Phone } from 'lucide-vue-next'
import { siteSettings } from '../../data/siteSettings'
</script>
