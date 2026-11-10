<template>
  <div class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 px-3 py-2 shadow-2xl">
    <div class="grid grid-cols-4 items-center text-center">
      <!-- Trang chủ -->
      <router-link
        to="/"
        class="flex flex-col items-center justify-center text-slate-600 hover:text-brand-blue"
        active-class="text-brand-blue font-bold"
      >
        <Home class="w-5 h-5 mb-0.5" />
        <span class="text-[10px]">Trang chủ</span>
      </router-link>

      <!-- Sản phẩm -->
      <router-link
        to="/san-pham"
        class="flex flex-col items-center justify-center text-slate-600 hover:text-brand-blue"
        active-class="text-brand-blue font-bold"
      >
        <Grid class="w-5 h-5 mb-0.5" />
        <span class="text-[10px]">Sản phẩm</span>
      </router-link>

      <!-- Báo giá -->
      <router-link
        to="/yeu-cau-bao-gia"
        class="flex flex-col items-center justify-center text-brand-gold hover:text-brand-gold-hover font-semibold"
        active-class="text-brand-gold font-bold"
      >
        <div class="relative -top-3 w-10 h-10 rounded-full bg-brand-gold text-slate-950 flex items-center justify-center shadow-lg shadow-brand-gold/40 border-2 border-white">
          <Calculator class="w-5 h-5" />
        </div>
        <span class="text-[10px] -mt-2">Báo giá</span>
      </router-link>

      <!-- Gọi điện -->
      <a
        :href="'tel:' + siteSettings.hotlines[0].number.replace(/\s+/g, '')"
        class="flex flex-col items-center justify-center text-emerald-600 hover:text-emerald-700"
      >
        <PhoneCall class="w-5 h-5 mb-0.5" />
        <span class="text-[10px]">Gọi xưởng</span>
      </a>
    </div>
  </div>
</template>

<script setup>
import { Home, Grid, Calculator, PhoneCall } from 'lucide-vue-next'
import { siteSettings } from '../../data/siteSettings'
</script>

