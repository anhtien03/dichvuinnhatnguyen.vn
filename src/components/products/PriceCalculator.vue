<template>
  <div class="space-y-6">
    <!-- =================================================================== -->
    <!-- CASE A: CHẾ ĐỘ ẨN BẢNG GIÁ (LIÊN HỆ BÁO GIÁ THEO YÊU CẦU)           -->
    <!-- Thiết kế Trắng Studio Cao Cấp, tinh tế theo chuẩn in ấn chuyên nghiệp -->
    <!-- =================================================================== -->
    <div v-if="isContactOnly" class="space-y-6">
      <!-- 1. HỘP QUY CÁCH RIÊNG (Báo Giá Nhanh) -->
      <div class="rounded-3xl border border-blue-200/90 bg-blue-50/25 p-5 sm:p-7 space-y-4 shadow-sm">
        <!-- Tag Quy cách riêng -->
        <div class="flex items-center justify-between">
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white text-blue-700 border border-blue-200 shadow-xs">
            <FileText class="w-3.5 h-3.5 text-blue-600" />
            <span>QUY CÁCH RIÊNG</span>
          </span>
          <span class="text-[11px] font-semibold text-blue-600/80">Xưởng In Trực Tiếp</span>
        </div>

        <!-- Tiêu đề & Thông điệp tư vấn -->
        <div class="space-y-1">
          <h4 class="text-sm sm:text-base font-extrabold text-slate-900 leading-snug">
            Giá phụ thuộc chất liệu, số lượng, kích thước và thời gian cần giao.
          </h4>
          <p class="text-xs text-slate-600 leading-relaxed">
            Liên hệ tư vấn viên để nhận báo giá xưởng nhanh nhất qua gọi điện hoặc Zalo.
          </p>
        </div>

        <!-- Hàng nút bấm Hành Động Trực Tiếp (Mobile-friendly) -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-1">
          <!-- Nút Gửi Yêu Cầu In Nhanh -->
          <button
            type="button"
            @click="proceedToContactQuote"
            class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs sm:text-sm shadow-md shadow-blue-600/20 transition-all flex items-center justify-center gap-2 cursor-pointer transform hover:-translate-y-0.5 active:translate-y-0 shrink-0"
          >
            <Send class="w-4 h-4" />
            <span>GỬI YÊU CẦU IN NHANH</span>
          </button>

          <!-- Nút Zalo Tư Vấn Viên (Thẻ avatar tương tác) -->
          <a
            :href="generateZaloUrl()"
            target="_blank"
            rel="noopener noreferrer"
            class="flex-1 p-2 sm:px-3 sm:py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 shadow-xs flex items-center justify-between gap-2.5 transition-all group cursor-pointer"
          >
            <div class="flex items-center gap-2.5 min-w-0">
              <div class="relative shrink-0">
                <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs">
                  NN
                </div>
                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white"></span>
              </div>
              <div class="min-w-0">
                <span class="text-[11px] font-bold text-slate-800 group-hover:text-blue-600 transition-colors block truncate">
                  Báo giá ngay: chat Zalo với tư vấn viên
                </span>
                <span class="text-[10px] text-slate-400 block">Trực tuyến hỗ trợ kiểm tra file</span>
              </div>
            </div>
            <ArrowRight class="w-4 h-4 text-slate-400 group-hover:text-blue-600 group-hover:translate-x-0.5 transition-all shrink-0 mr-1" />
          </a>
        </div>

        <!-- Tải file thiết kế / mẫu in (Tùy chọn) -->
        <div class="pt-2 border-t border-blue-100/80">
          <label class="block text-xs font-bold text-slate-700 mb-1.5">
            Tải lên file in hoặc ảnh mẫu (nếu có để kỹ thuật báo giá chính xác nhất):
          </label>
          <FileUploadBox v-model="uploadedFiles" />
        </div>
      </div>

      <!-- 2. HỘP QUY CÁCH IN (Bảng Thông Số Kỹ Thuật Tiêu Chuẩn) -->
      <div class="rounded-3xl border border-slate-200/90 bg-white p-5 sm:p-7 space-y-4 shadow-sm">
        <!-- Tag Quy cách in -->
        <div>
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200/80">
            QUY CÁCH IN
          </span>
        </div>

        <!-- Tiêu đề Thông số kỹ thuật -->
        <h4 class="text-base sm:text-lg font-black text-slate-900">
          Thông số kỹ thuật in {{ product.name }}
        </h4>

        <!-- Danh sách thông số kỹ thuật dạng bảng phẳng tối giản -->
        <div class="divide-y divide-slate-100 text-xs sm:text-sm">
          <!-- 1. Màu sắc -->
          <div class="py-3 flex items-center justify-between gap-4">
            <span class="font-bold text-slate-800">Màu sắc:</span>
            <div class="flex items-center gap-1.5 text-right font-medium text-slate-600">
              <span>4 màu (CMYK)</span>
              <span class="relative group/tip inline-flex items-center cursor-help">
                <span class="w-4 h-4 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 text-[10px] font-bold flex items-center justify-center">?</span>
                <span class="absolute bottom-full right-0 mb-2 hidden group-hover/tip:block w-56 p-2.5 bg-slate-900 text-white text-[11px] rounded-xl shadow-xl z-20 pointer-events-none leading-relaxed font-normal">
                  In chuẩn 4 màu CMYK Offset công nghiệp, đảm bảo hình ảnh sắc nét và màu sắc trung thực.
                </span>
              </span>
            </div>
          </div>

          <!-- 2. Loại giấy -->
          <div class="py-3 flex items-center justify-between gap-4">
            <span class="font-bold text-slate-800">Loại giấy:</span>
            <div class="flex items-center gap-1.5 text-right font-medium text-slate-600">
              <span>{{ product.specs?.materials?.[0]?.label || 'Giấy Couche 300gsm (C300)' }}</span>
              <span class="relative group/tip inline-flex items-center cursor-help">
                <span class="w-4 h-4 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 text-[10px] font-bold flex items-center justify-center">?</span>
                <span class="absolute bottom-full right-0 mb-2 hidden group-hover/tip:block w-56 p-2.5 bg-slate-900 text-white text-[11px] rounded-xl shadow-xl z-20 pointer-events-none leading-relaxed font-normal">
                  Độ dày tiêu chuẩn đanh cứng, bề mặt láng mịn, bám mực in tối ưu.
                </span>
              </span>
            </div>
          </div>

          <!-- 3. Cán màng -->
          <div class="py-3 flex items-center justify-between gap-4">
            <span class="font-bold text-slate-800">Cán màng:</span>
            <div class="flex items-center gap-1.5 text-right font-medium text-slate-600">
              <span>{{ product.specs?.finishings?.[0]?.label || 'Màng mờ / Màng bóng' }}</span>
              <span class="relative group/tip inline-flex items-center cursor-help">
                <span class="w-4 h-4 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 text-[10px] font-bold flex items-center justify-center">?</span>
                <span class="absolute bottom-full right-0 mb-2 hidden group-hover/tip:block w-56 p-2.5 bg-slate-900 text-white text-[11px] rounded-xl shadow-xl z-20 pointer-events-none leading-relaxed font-normal">
                  Cán màng bảo vệ bề mặt chống trầy xước, tăng độ dai và chống ẩm mốc nhẹ.
                </span>
              </span>
            </div>
          </div>

          <!-- 4. Kỹ thuật in -->
          <div class="py-3 flex items-center justify-between gap-4">
            <span class="font-bold text-slate-800">Kỹ thuật in:</span>
            <div class="flex items-center gap-1.5 text-right font-medium text-slate-600">
              <span>In ghép / In Offset chuyên nghiệp</span>
              <span class="relative group/tip inline-flex items-center cursor-help">
                <span class="w-4 h-4 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 text-[10px] font-bold flex items-center justify-center">?</span>
                <span class="absolute bottom-full right-0 mb-2 hidden group-hover/tip:block w-56 p-2.5 bg-slate-900 text-white text-[11px] rounded-xl shadow-xl z-20 pointer-events-none leading-relaxed font-normal">
                  Sản xuất bằng hệ thống máy in Offset công nghiệp Komori/Heidelberg hiện đại.
                </span>
              </span>
            </div>
          </div>

          <!-- 5. KTTP (Kích thước) -->
          <div class="py-3 flex items-center justify-between gap-4">
            <span class="font-bold text-slate-800">KTTP (Kích thước):</span>
            <div class="flex items-center gap-1.5 text-right font-medium text-slate-600">
              <span>{{ product.specs?.sizes?.[0]?.label || 'Kích thước chuẩn hoặc theo file' }}</span>
              <span class="relative group/tip inline-flex items-center cursor-help">
                <span class="w-4 h-4 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 text-[10px] font-bold flex items-center justify-center">?</span>
                <span class="absolute bottom-full right-0 mb-2 hidden group-hover/tip:block w-56 p-2.5 bg-slate-900 text-white text-[11px] rounded-xl shadow-xl z-20 pointer-events-none leading-relaxed font-normal">
                  Kích thước thành phẩm thực tế sau khi gia công hoàn thiện hoặc bế theo file thiết kế.
                </span>
              </span>
            </div>
          </div>

          <!-- 6. Thành phẩm -->
          <div class="py-3 flex items-center justify-between gap-4">
            <span class="font-bold text-slate-800">Thành phẩm:</span>
            <div class="flex items-center gap-1.5 text-right font-medium text-slate-600">
              <span>Cắt xén, bế khuôn, đục lỗ theo yêu cầu</span>
              <span class="relative group/tip inline-flex items-center cursor-help">
                <span class="w-4 h-4 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 text-[10px] font-bold flex items-center justify-center">?</span>
                <span class="absolute bottom-full right-0 mb-2 hidden group-hover/tip:block w-56 p-2.5 bg-slate-900 text-white text-[11px] rounded-xl shadow-xl z-20 pointer-events-none leading-relaxed font-normal">
                  Được cắt xén chuẩn xác, đục lỗ xỏ dây hoặc cấn đường gấp theo đúng yêu cầu khách hàng.
                </span>
              </span>
            </div>
          </div>
        </div>

        <!-- Liên hệ hotline footer -->
        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
          <span>Tư vấn kỹ thuật 24/7:</span>
          <a
            :href="'tel:' + siteSettings.hotlines[0].number.replace(/\s+/g, '')"
            class="font-black text-brand-blue hover:underline"
          >
            {{ siteSettings.hotlines[0].number }}
          </a>
        </div>
      </div>
    </div>

    <!-- =================================================================== -->
    <!-- CASE B: CHẾ ĐỘ TÍNH GIÁ TỰ ĐỘNG (KHI BẢNG GIÁ ĐANG BẬT) -->
    <!-- Tone Trắng Sứ Cao Cấp + Bộ chọn quy cách trực quan -->
    <!-- =================================================================== -->
    <div v-else class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
      <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
        <div>
          <span class="text-[10px] font-black uppercase tracking-wider text-brand-blue block">
            Bảng Tính Tự Động
          </span>
          <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 mt-0.5">
            <Sliders class="w-5 h-5 text-brand-blue" />
            <span>Bảng Chọn Quy Cách Đặt In</span>
          </h3>
        </div>
        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
          {{ product.turnaround }}
        </span>
      </div>

      <!-- 1. Kích thước -->
      <div v-if="product.specs?.sizes && product.specs.sizes.length > 0">
        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2.5">
          1. Kích thước ấn phẩm:
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
          <button
            v-for="(s, idx) in product.specs.sizes"
            :key="idx"
            type="button"
            @click="selectedSize = s"
            :class="[
              'p-3 rounded-xl border text-left text-xs transition-all flex items-center justify-between',
              selectedSize?.value === s.value
                ? 'border-brand-blue bg-brand-blue-light/70 text-brand-blue font-bold ring-2 ring-brand-blue/30'
                : 'border-slate-200 hover:border-slate-300 text-slate-700 font-medium'
            ]"
          >
            <span>{{ s.label }}</span>
            <span v-if="s.extra > 0" class="text-[10px] text-amber-600 font-bold shrink-0 ml-2">
              +{{ formatCurrency(s.extra) }}
            </span>
          </button>
        </div>
      </div>

      <!-- 2. Chất liệu giấy / decal -->
      <div v-if="product.specs?.materials && product.specs.materials.length > 0">
        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2.5">
          2. Chất liệu giấy / bạt:
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
          <button
            v-for="(m, idx) in product.specs.materials"
            :key="idx"
            type="button"
            @click="selectedMaterial = m"
            :class="[
              'p-3 rounded-xl border text-left text-xs transition-all flex items-center justify-between',
              selectedMaterial?.value === m.value
                ? 'border-brand-blue bg-brand-blue-light/70 text-brand-blue font-bold ring-2 ring-brand-blue/30'
                : 'border-slate-200 hover:border-slate-300 text-slate-700 font-medium'
            ]"
          >
            <span>{{ m.label }}</span>
            <span v-if="m.extra > 0" class="text-[10px] text-amber-600 font-bold shrink-0 ml-2">
              +{{ formatCurrency(m.extra) }}
            </span>
          </button>
        </div>
      </div>

      <!-- 3. Gia công sau in -->
      <div v-if="product.specs?.finishings && product.specs.finishings.length > 0">
        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2.5">
          3. Quy cách gia công:
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
          <button
            v-for="(f, idx) in product.specs.finishings"
            :key="idx"
            type="button"
            @click="selectedFinishing = f"
            :class="[
              'p-3 rounded-xl border text-left text-xs transition-all flex items-center justify-between',
              selectedFinishing?.value === f.value
                ? 'border-brand-blue bg-brand-blue-light/70 text-brand-blue font-bold ring-2 ring-brand-blue/30'
                : 'border-slate-200 hover:border-slate-300 text-slate-700 font-medium'
            ]"
          >
            <span>{{ f.label }}</span>
            <span v-if="f.extra > 0" class="text-[10px] text-amber-600 font-bold shrink-0 ml-2">
              +{{ formatCurrency(f.extra) }}
            </span>
          </button>
        </div>
      </div>

      <!-- 4. Số lượng đặt in -->
      <div v-if="product.specs?.quantities && product.specs.quantities.length > 0">
        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2.5">
          4. Số lượng đặt in:
        </label>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
          <button
            v-for="(q, idx) in product.specs.quantities"
            :key="idx"
            type="button"
            @click="selectedQuantity = q"
            :class="[
              'p-3 rounded-xl border text-center text-xs transition-all',
              selectedQuantity?.count === q.count
                ? 'border-brand-gold bg-brand-gold-light text-slate-950 font-black ring-2 ring-brand-gold/40'
                : 'border-slate-200 hover:border-slate-300 text-slate-700 font-medium'
            ]"
          >
            <div class="font-bold">{{ q.label ? q.label.split(' - ')[0] : q.count }}</div>
            <div class="text-[10px] text-slate-500 mt-1">
              {{ formatCurrency(q.pricePerUnit) }}/{{ product.unit }}
            </div>
          </button>
        </div>
      </div>

      <!-- 5. Tải file thiết kế -->
      <div>
        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2.5">
          5. Tải file in của bạn (nếu có):
        </label>
        <FileUploadBox v-model="uploadedFiles" />
      </div>

      <!-- Live Calculated Pricing summary box -->
      <div class="p-5 rounded-2xl bg-gradient-to-r from-slate-900 to-brand-blue-dark text-white shadow-xl space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <span class="text-xs text-slate-300 block">Ước tính tổng chi phí (chưa VAT):</span>
            <div class="text-3xl font-black text-brand-gold mt-1">
              {{ calculationResult.formattedTotal }}
            </div>
            <div class="text-xs text-slate-400 mt-0.5">
              Đơn giá tương đương: <strong class="text-white">{{ formatCurrency(calculationResult.unitPrice) }}</strong>/{{ product.unit }}
            </div>
          </div>
          <div class="text-right">
            <span class="text-xs font-bold px-3 py-1 rounded-full bg-brand-gold/20 text-brand-gold border border-brand-gold/40">
              Số lượng: {{ selectedQuantity?.count || 1 }} {{ product.unit }}
            </span>
          </div>
        </div>

        <!-- Action buttons -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
          <button
            type="button"
            @click="proceedToQuote"
            class="w-full py-3.5 px-4 rounded-xl bg-brand-gold hover:bg-brand-gold-hover text-slate-950 font-black text-sm text-center shadow-lg transition-all flex items-center justify-center gap-2 transform hover:-translate-y-0.5 cursor-pointer"
          >
            <Send class="w-4 h-4" />
            <span>Gửi thông tin đặt in</span>
          </button>

          <a
            :href="siteSettings.zalo"
            target="_blank"
            rel="noopener"
            class="w-full py-3.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm text-center transition-all flex items-center justify-center gap-2 cursor-pointer"
          >
            <MessageCircle class="w-4 h-4" />
            <span>Tư vấn nhanh qua Zalo</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import {
  Sliders,
  Send,
  MessageCircle,
  PhoneCall,
  CheckCircle2,
  Sparkles,
  Ruler,
  Layers,
  Printer,
  ShieldCheck,
  FileText,
  ArrowRight
} from 'lucide-vue-next'
import { mockApi } from '../../services/mockApi'
import { siteSettings } from '../../data/siteSettings'
import { useQuoteStore } from '../../stores/quote'
import FileUploadBox from './FileUploadBox.vue'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const router = useRouter()
const quoteStore = useQuoteStore()

const selectedSize = ref(props.product.specs?.sizes?.[0] || {})
const selectedMaterial = ref(props.product.specs?.materials?.[0] || {})
const selectedFinishing = ref(props.product.specs?.finishings?.[0] || {})
const selectedQuantity = ref(props.product.specs?.quantities?.[1] || props.product.specs?.quantities?.[0] || { count: 1, pricePerUnit: props.product.basePrice || props.product.base_price || 0 })
const uploadedFiles = ref([])

// Check whether this product is in "Contact for Quote" mode (Ẩn bảng tính giá)
const isContactOnly = computed(() => {
  return Boolean(props.product?.specs?.hide_pricing) ||
         !Array.isArray(props.product?.specs?.quantities) ||
         props.product?.specs?.quantities.length === 0
})

watch(() => props.product, (newVal) => {
  if (newVal?.specs) {
    selectedSize.value = newVal.specs.sizes?.[0] || {}
    selectedMaterial.value = newVal.specs.materials?.[0] || {}
    selectedFinishing.value = newVal.specs.finishings?.[0] || {}
    selectedQuantity.value = newVal.specs.quantities?.[1] || newVal.specs.quantities?.[0] || { count: 1, pricePerUnit: newVal.basePrice || newVal.base_price || 0 }
  }
}, { immediate: true, deep: true })

const calculationResult = computed(() => {
  return mockApi.calculateEstimate({
    basePrice: props.product.basePrice || props.product.base_price,
    selectedSize: selectedSize.value,
    selectedMaterial: selectedMaterial.value,
    selectedFinishing: selectedFinishing.value,
    selectedQuantity: selectedQuantity.value
  })
})

function formatCurrency(val) {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val)
}

function generateZaloUrl() {
  const parts = []
  parts.push(`Xin chào Xưởng In Nhật Nguyên! Tôi cần tư vấn báo giá ấn phẩm: ${props.product.name}`)
  parts.push(`Xem sản phẩm: ${window.location.href}`)
  
  const text = encodeURIComponent(parts.join('\n'))
  const base = siteSettings.zalo || 'https://zalo.me/0359005469'
  return `${base}?text=${text}`
}

function proceedToContactQuote() {
  const specs = {
    note: 'Yêu cầu tư vấn báo giá theo yêu cầu riêng'
  }

  quoteStore.setProductQuote(props.product, specs, 0)
  quoteStore.currentQuote.files = uploadedFiles.value

  router.push({
    path: '/yeu-cau-bao-gia',
    query: {
      product: props.product.slug
    }
  })
}

function proceedToQuote() {
  const specs = {
    size: selectedSize.value?.label || '',
    material: selectedMaterial.value?.label || '',
    finishing: selectedFinishing.value?.label || '',
    quantity: `${selectedQuantity.value?.count || 1} ${props.product.unit || 'cái'}`
  }

  quoteStore.setProductQuote(props.product, specs, calculationResult.value.total)
  quoteStore.currentQuote.files = uploadedFiles.value

  router.push({
    path: '/yeu-cau-bao-gia',
    query: {
      product: props.product.slug,
      qty: selectedQuantity.value?.count || 1
    }
  })
}
</script>
