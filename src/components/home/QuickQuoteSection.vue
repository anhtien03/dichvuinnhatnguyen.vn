<template>
  <section class="py-16 bg-slate-900 text-white relative overflow-hidden">
    <!-- Background accent gradients -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-brand-blue/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-brand-gold/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        <!-- Left: Pitch & Value -->
        <div class="lg:col-span-5 space-y-5">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800 text-brand-gold text-xs font-bold border border-slate-700">
            <Calculator class="w-3.5 h-3.5" />
            <span>TÍNH GIÁ NHANH TRỰC TUYẾN</span>
          </div>

          <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight leading-tight">
            Ước Tính Chi Phí In <br />
            <span class="text-brand-gold">Chỉ Trong 10 Giây</span>
          </h2>

          <p class="text-sm text-slate-300 leading-relaxed font-normal">
            Không cần chờ đợi nhân viên phản hồi! Chọn nhanh loại ấn phẩm và số lượng để biết ngay khoảng giá dự kiến tại xưởng.
          </p>

          <div class="space-y-3 pt-2 text-xs text-slate-300">
            <div class="flex items-center gap-2.5">
              <CheckCircle class="w-4 h-4 text-brand-gold shrink-0" />
              <span>Giá đã bao gồm gia công cán màng bảo vệ cơ bản</span>
            </div>
            <div class="flex items-center gap-2.5">
              <CheckCircle class="w-4 h-4 text-brand-gold shrink-0" />
              <span>Hỗ trợ xuất hóa đơn GTGT (VAT 8% - 10%) đầy đủ</span>
            </div>
            <div class="flex items-center gap-2.5">
              <CheckCircle class="w-4 h-4 text-brand-gold shrink-0" />
              <span>Chiết khấu thêm 5% - 10% cho khách hàng in định kỳ</span>
            </div>
          </div>

          <div class="pt-4 flex items-center gap-3">
            <span class="text-xs text-slate-400">Hoặc tư vấn trực tiếp:</span>
            <a
              :href="'tel:' + siteSettings.hotlines[0].number.replace(/\s+/g, '')"
              class="font-black text-brand-gold hover:underline text-sm"
            >
              {{ siteSettings.hotlines[0].number }}
            </a>
          </div>
        </div>

        <!-- Right: Interactive Live Calculator Card -->
        <div class="lg:col-span-7">
          <div class="bg-white text-slate-900 rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 mb-5 flex items-center justify-between">
              <span>Công Cụ Tính Giá Tạm Tính</span>
              <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800">
                Giá gốc xưởng
              </span>
            </h3>

            <!-- Step 1: Select Product -->
            <div class="mb-5">
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                1. Chọn loại ấn phẩm cần in:
              </label>
              <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                <button
                  v-for="p in selectableProducts"
                  :key="p.id"
                  @click="selectedProductId = p.id"
                  :class="[
                    'p-2.5 rounded-xl border text-left text-xs font-bold transition-all flex flex-col justify-between',
                    selectedProductId === p.id
                      ? 'border-brand-blue bg-brand-blue-light text-brand-blue ring-2 ring-brand-blue/30'
                      : 'border-slate-200 hover:border-slate-300 bg-white text-slate-700'
                  ]"
                >
                  <span class="truncate">{{ p.name.replace('In ', '') }}</span>
                  <span class="text-[10px] text-slate-400 font-normal mt-1">Từ {{ formatCurrency(p.basePrice) }}</span>
                </button>
              </div>
            </div>

            <!-- Step 2: Select Quantity Option -->
            <div class="mb-6">
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                2. Chọn mức số lượng in:
              </label>
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                <button
                  v-for="(qty, idx) in currentProduct.specs.quantities"
                  :key="idx"
                  @click="selectedQuantityIndex = idx"
                  :class="[
                    'p-3 rounded-xl border text-center transition-all',
                    selectedQuantityIndex === idx
                      ? 'border-brand-gold bg-brand-gold-light text-slate-950 font-black ring-2 ring-brand-gold/40'
                      : 'border-slate-200 hover:border-slate-300 text-slate-700 text-xs font-medium'
                  ]"
                >
                  <div class="text-xs font-bold">{{ qty.label.split(' - ')[0] }}</div>
                  <div class="text-[10px] text-slate-500 mt-0.5">
                    {{ formatCurrency(qty.pricePerUnit) }}/{{ currentProduct.unit }}
                  </div>
                </button>
              </div>
            </div>

            <!-- Result Box -->
            <div class="bg-slate-50 rounded-2xl p-4 sm:p-5 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div>
                <span class="text-xs text-slate-500 block">Ước tính tổng chi phí (chưa VAT):</span>
                <div class="flex items-baseline gap-2 mt-0.5">
                  <span class="text-2xl sm:text-3xl font-black text-brand-accent">
                    {{ formatCurrency(estimatedTotal) }}
                  </span>
                  <span class="text-xs text-slate-400">
                    ({{ currentQuantityObj.count }} {{ currentProduct.unit }})
                  </span>
                </div>
                <span class="text-[11px] text-slate-400 block mt-1">
                  * Đơn giá có thể thay đổi tùy theo quy cách cán màng / ép kim hoặc file thiết kế đặc biệt.
                </span>
              </div>

              <div class="flex sm:flex-col gap-2 shrink-0">
                <router-link
                  :to="'/san-pham/' + currentProduct.slug"
                  class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-blue-dark text-white text-xs font-bold text-center transition-colors"
                >
                  Tùy chỉnh chi tiết &rarr;
                </router-link>
                <router-link
                  :to="{ path: '/yeu-cau-bao-gia', query: { product: currentProduct.slug, qty: currentQuantityObj.count } }"
                  class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-brand-gold hover:bg-brand-gold-hover text-slate-950 text-xs font-extrabold text-center shadow-md transition-all"
                >
                  Đặt in theo giá này
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Calculator, CheckCircle } from 'lucide-vue-next'
import { products } from '../../data/products'
import { siteSettings } from '../../data/siteSettings'

const selectableProducts = products.slice(0, 6)
const selectedProductId = ref(selectableProducts[0].id)
const selectedQuantityIndex = ref(1) // default choose popular quantity

const currentProduct = computed(() => {
  return selectableProducts.find(p => p.id === selectedProductId.value) || selectableProducts[0]
})

const currentQuantityObj = computed(() => {
  const qtys = currentProduct.value.specs.quantities
  return qtys[selectedQuantityIndex.value] || qtys[0]
})

const estimatedTotal = computed(() => {
  const q = currentQuantityObj.value
  return q.count * q.pricePerUnit
})

function formatCurrency(val) {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val)
}
</script>

