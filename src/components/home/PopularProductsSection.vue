<template>
  <section v-if="popularProducts.length > 0" class="py-14 lg:py-20 bg-gradient-to-b from-white via-slate-50/60 to-white border-b border-slate-200/80">
    <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
      
      <!-- Section Header -->
      <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
          <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200/60 text-brand-gold text-xs font-bold uppercase tracking-wider mb-2">
            <span class="w-1.5 h-1.5 rounded-full bg-brand-gold animate-ping"></span>
            ẤN PHẨM PHỔ BIẾN
          </div>
          <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">
            Ấn Phẩm Bán Chạy
          </h2>
          <p class="text-xs sm:text-sm text-slate-500 mt-1.5">
            Các dòng ấn phẩm in sắc nét, chuẩn màu, hỗ trợ kiểm tra file và nhận in số lượng linh hoạt tại TPHCM.
          </p>
        </div>
        
        <router-link
          to="/san-pham"
          class="text-xs sm:text-sm font-bold text-brand-blue hover:text-brand-blue-dark flex items-center gap-1.5 shrink-0 px-4 py-2 rounded-xl bg-blue-50/70 hover:bg-blue-100/70 transition-colors"
        >
          <span>Xem tất cả ấn phẩm</span>
          <ArrowRight class="w-4 h-4" />
        </router-link>
      </div>

      <!-- 5-Column Modern Product Cards Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 lg:gap-5 xl:gap-6">
        <div
          v-for="item in popularProducts"
          :key="item.id"
          class="w-full bg-white rounded-[12px] border border-slate-200/90 shadow-[0_4px_20px_-2px_rgba(10,25,47,0.08)] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between overflow-hidden group select-none cursor-pointer"
        >
          <!-- 1. Header (Banner) Navy -->
          <div class="bg-[#0A192F] text-white px-3.5 py-2 flex items-center justify-center shrink-0">
            <div class="flex items-center gap-1.5 text-[11px] font-bold tracking-wider uppercase">
              <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B] animate-pulse"></span>
              <span>Xưởng In Nhật Nguyên</span>
            </div>
          </div>

          <!-- 2. Body (Khu vực hình ảnh) -->
          <router-link :to="'/san-pham/' + (item.slug || item.id)" class="block w-full flex-1 flex flex-col justify-between">
            <div class="relative w-full h-[200px] sm:h-[220px] bg-slate-100 overflow-hidden">
              <img
                :src="item.image || '/logo.png'"
                :alt="item.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                loading="lazy"
              />

              <div v-if="item.tag" class="absolute bottom-2.5 right-2.5 bg-[#F59E0B] text-[#0A192F] text-[10px] font-black px-2.5 py-1 rounded-full shadow-md flex items-center gap-1 z-10">
                <Zap class="w-3 h-3 fill-current" />
                <span>{{ item.tag }}</span>
              </div>
            </div>

            <!-- Khối nội dung thông tin -->
            <div class="p-4 flex-1 flex flex-col justify-between gap-3 text-left">
              <div>
                <h3 class="text-sm font-black text-slate-900 group-hover:text-[#1E3A8A] transition-colors leading-snug line-clamp-2">
                  {{ item.name }}
                </h3>
                <p v-if="item.description" class="text-[11px] text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                  {{ stripHtml(item.description) }}
                </p>
              </div>

              <!-- Giá tiền & Quy cách -->
              <div class="pt-2 border-t border-slate-100 flex items-baseline justify-between">
                <div>
                  <span class="text-[10px] text-slate-400 block">Giá từ:</span>
                  <div class="text-[#1E3A8A] font-black text-base">
                    {{ formatCurrency(item.base_price || item.basePrice || 0) }}
                    <span class="text-[10px] text-slate-500 font-normal">/ {{ item.unit || 'cái' }}</span>
                  </div>
                </div>
                <span class="text-[10px] font-semibold text-slate-400">
                  {{ item.turnaround || '24h - 48h' }}
                </span>
              </div>
            </div>
          </router-link>

          <!-- Nút CTA -->
          <div class="px-4 pb-4 pt-1 shrink-0">
            <router-link
              :to="'/san-pham/' + (item.slug || item.id)"
              class="w-full py-2.5 px-3.5 rounded-lg bg-white border-[1.5px] border-dashed border-[#1E3A8A] text-[#0A192F] font-bold text-xs flex items-center justify-between hover:bg-slate-50 transition-colors group/btn cursor-pointer"
            >
              <span>Xem quy cách</span>
              <span class="w-5 h-5 rounded-full bg-[#0A192F] text-white flex items-center justify-center transition-transform group-hover/btn:translate-x-1">
                <ArrowRight class="w-3 h-3" />
              </span>
            </router-link>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Zap, ArrowRight } from 'lucide-vue-next'
import { api } from '../../services/api'

const popularProducts = ref([])

function formatCurrency(val) {
  if (!val) return 'Liên hệ'
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val)
}

function stripHtml(html) {
  if (!html) return ''
  return String(html).replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim()
}

onMounted(async () => {
  try {
    const res = await api.getProducts()
    if (res?.data?.length) {
      const allProds = res.data
      let popular = allProds.filter(p => p.is_popular)
      if (popular.length === 0) {
        // Fallback 1: các ấn phẩm có tag hoặc thuộc danh mục có badge "Bán chạy" / "Phổ biến"
        popular = allProds.filter(p => {
          const tag = (p.tag || '').toLowerCase()
          const catBadge = (p.category?.badge || '').toLowerCase()
          return tag.includes('bán chạy') || tag.includes('phổ biến') || catBadge.includes('bán chạy') || catBadge.includes('phổ biến')
        })
      }
      if (popular.length === 0) {
        // Fallback 2: hiển thị các ấn phẩm đầu tiên để khối Ấn Phẩm Bán Chạy không bị ẩn mất
        popular = allProds
      }
      popularProducts.value = popular.slice(0, 5)
    }
  } catch (err) {
    popularProducts.value = []
  }
})
</script>
