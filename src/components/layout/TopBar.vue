<template>
  <div class="bg-brand-blue-dark text-white text-xs py-2 border-b border-brand-blue/60 select-none">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
      <!-- Left: Working Hours -->
      <div class="flex items-center text-slate-200">
        <span class="flex items-center gap-1.5">
          <Clock class="w-3.5 h-3.5 text-brand-gold shrink-0" />
          <span class="hidden sm:inline">{{ siteSettings.workingHours }}</span>
          <span class="sm:hidden">T2 - T7: 08:00 - 18:00</span>
        </span>
      </div>

      <!-- Right: Hotlines & Zalo -->
      <div class="flex items-center space-x-3 sm:space-x-4 shrink-0">
        <span class="hidden lg:inline text-slate-300 font-medium">Hotline:</span>
        <div class="flex items-center space-x-2 sm:space-x-3 divide-x divide-white/20">
          <a
            v-for="(h, idx) in siteSettings.hotlines"
            :key="idx"
            :href="'tel:' + h.number.replace(/\s+/g, '')"
            class="font-extrabold text-brand-gold hover:text-white transition-colors flex items-center gap-1.5 pl-2 sm:pl-3 first:pl-0"
            :title="h.label + ' - ' + h.nvkd"
          >
            <Phone class="w-3 h-3 text-brand-gold shrink-0" />
            <span>{{ h.number }}</span>
            <span v-if="h.label" class="hidden xl:inline text-[10px] font-normal text-slate-300">({{ h.label }})</span>
          </a>
        </div>
        <a
          :href="siteSettings.zalo"
          target="_blank"
          rel="noopener"
          class="bg-blue-600 hover:bg-blue-700 text-white px-2.5 py-0.5 rounded text-[11px] font-bold transition-colors flex items-center gap-1 shadow-sm"
        >
          <span>Chat Zalo</span>
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Clock, Phone } from 'lucide-vue-next'
import { siteSettings } from '../../data/siteSettings'
</script>
