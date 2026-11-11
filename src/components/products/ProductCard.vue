<template>
  <div class="bg-white rounded-2xl border border-slate-200/90 hover:border-brand-blue/50 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden group transform-gpu hover:-translate-y-1">
    <!-- Image with Badges -->
    <router-link :to="'/san-pham/' + product.slug" class="relative block overflow-hidden aspect-[4/3] bg-slate-100">
      <img
        :src="product.image"
        :alt="product.name"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
        loading="lazy"
      />
      <!-- Gradient overlay on bottom of image for contrast -->
      <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

      <!-- Category Badge -->
      <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg bg-brand-blue-dark/90 backdrop-blur-sm text-white text-[11px] font-semibold shadow-xs">
        {{ product.categoryName }}
      </span>

      <!-- Tag Badge (e.g. Bán chạy / Lấy liền / Giá sỉ) -->
      <span v-if="product.tag" class="absolute top-3 right-3 px-2.5 py-1 rounded-lg bg-brand-gold text-slate-950 text-[11px] font-black shadow-xs tracking-tight">
        {{ product.tag }}
      </span>

      <!-- Subtle Xưởng In Nhật Nguyên watermark pill on bottom left -->
      <span class="absolute bottom-2.5 left-3 px-2 py-0.5 rounded-md bg-white/90 backdrop-blur-xs text-[10px] font-bold text-slate-700 shadow-xs flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        <ShieldCheck class="w-3 h-3 text-emerald-600" />
        Xưởng in trực tiếp
      </span>
    </router-link>

    <!-- Content -->
    <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between">
      <div>
        <!-- Title -->
        <router-link
          :to="'/san-pham/' + product.slug"
          class="font-bold text-slate-900 group-hover:text-brand-blue text-base sm:text-lg line-clamp-1 transition-colors block leading-snug"
          :title="product.name"
        >
          {{ product.name }}
        </router-link>

        <!-- Short description -->
        <p class="text-xs text-slate-500 mt-1.5 line-clamp-2 leading-relaxed">
          {{ stripHtml(product.description) }}
        </p>
      </div>

      <!-- Pricing & Action Bar -->
      <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between gap-2">
        <div>
          <template v-if="product.specs?.hide_pricing">
            <span class="text-[10px] text-amber-600 block font-semibold uppercase tracking-wider">Báo giá theo số lượng</span>
            <div class="text-sm font-black text-amber-600 flex items-center gap-1">
              <span>Liên hệ báo giá</span>
            </div>
          </template>
          <template v-else>
            <span class="text-[10px] text-slate-400 block font-semibold uppercase tracking-wider">Giá xưởng từ</span>
            <div class="flex items-baseline gap-1">
              <span class="text-lg font-black text-brand-accent">
                {{ formatCurrency(product.basePrice || product.base_price) }}
              </span>
              <span class="text-xs text-slate-400 font-medium">/{{ product.unit }}</span>
            </div>
          </template>
        </div>

        <div class="flex items-center gap-1.5 shrink-0">
          <!-- Quick Zalo consultation button -->
          <a
            href="https://zalo.me/0359005469"
            target="_blank"
            rel="noopener noreferrer"
            class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-600 flex items-center justify-center transition-colors"
            title="Tư vấn Zalo nhanh"
            @click.stop
          >
            <MessageCircle class="w-4 h-4" />
          </a>

          <!-- Price Calculation & Detail button -->
          <router-link
            :to="'/san-pham/' + product.slug"
            class="inline-flex items-center justify-center px-3.5 py-2 rounded-xl bg-brand-blue hover:bg-brand-blue-dark text-white font-bold text-xs shadow-xs hover:shadow-md transition-all gap-1"
          >
            <span>Tính giá</span>
            <ChevronRight class="w-3.5 h-3.5" />
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ChevronRight, ShieldCheck, MessageCircle } from 'lucide-vue-next'

defineProps({
  product: {
    type: Object,
    required: true
  }
})

function formatCurrency(val) {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val)
}

function stripHtml(html) {
  if (!html) return ''
  return String(html).replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim()
}
</script>

