<template>
  <!-- 1. Branded Skeleton Loading State -->
  <div v-if="isLoading" class="py-10 lg:py-16 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Breadcrumb Skeleton -->
      <div class="flex items-center gap-2 mb-6">
        <div class="h-3 w-16 bg-slate-200/80 rounded-md animate-pulse"></div>
        <span class="text-slate-300">/</span>
        <div class="h-3 w-20 bg-slate-200/80 rounded-md animate-pulse"></div>
        <span class="text-slate-300">/</span>
        <div class="h-3 w-28 bg-slate-200/80 rounded-md animate-pulse"></div>
      </div>

      <!-- Main Layout Skeleton with Branded Central Indicator -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
        <!-- Left Column Skeleton -->
        <div class="lg:col-span-5 space-y-6">
          <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm space-y-4">
            <div class="relative aspect-[4/3] rounded-2xl bg-gradient-to-br from-slate-100 via-slate-50 to-slate-100 border border-slate-100 overflow-hidden flex flex-col items-center justify-center p-6 text-center">
              <div class="relative flex items-center justify-center mb-3">
                <span class="absolute inline-flex h-20 w-20 rounded-full bg-brand-blue/15 animate-ping"></span>
                <div class="relative w-16 h-16 rounded-2xl bg-white shadow-lg border border-slate-100 flex items-center justify-center p-2.5">
                  <img src="/logo-icon.png" alt="In Nhật Nguyên" class="w-full h-full object-contain animate-pulse" />
                </div>
              </div>

              <!-- CMYK 4-Color Wave Animation -->
              <div class="flex items-center justify-center gap-1.5 mb-2">
                <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-bounce" style="animation-delay: -0.3s;"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-pink-500 animate-bounce" style="animation-delay: -0.15s;"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-bounce" style="animation-delay: 0s;"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-slate-900 animate-bounce" style="animation-delay: 0.15s;"></span>
              </div>

              <span class="text-xs font-black text-slate-800 tracking-tight block">Xưởng In Nhật Nguyên</span>
              <span class="text-[11px] text-slate-400 block mt-0.5">Đang tải thông số kỹ thuật ấn phẩm...</span>
            </div>

            <!-- Trust badges skeletons -->
            <div class="flex items-center gap-2 pt-1">
              <div class="h-6 w-28 bg-slate-200/70 rounded-full animate-pulse"></div>
              <div class="h-6 w-32 bg-slate-200/70 rounded-full animate-pulse"></div>
            </div>
          </div>
        </div>

        <!-- Right Column Skeleton -->
        <div class="lg:col-span-7 space-y-6">
          <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm space-y-3">
            <div class="h-3 w-24 bg-blue-100 rounded-md animate-pulse"></div>
            <div class="h-7 w-3/4 bg-slate-200/80 rounded-xl animate-pulse"></div>
            <div class="h-4 w-full bg-slate-100 rounded-md animate-pulse"></div>
            <div class="h-4 w-5/6 bg-slate-100 rounded-md animate-pulse"></div>
          </div>

          <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm space-y-4">
            <div class="h-4 w-32 bg-slate-200 rounded-md animate-pulse"></div>
            <div class="space-y-3">
              <div class="h-12 w-full bg-slate-50 rounded-xl animate-pulse border border-slate-100"></div>
              <div class="h-12 w-full bg-slate-50 rounded-xl animate-pulse border border-slate-100"></div>
              <div class="h-12 w-full bg-slate-50 rounded-xl animate-pulse border border-slate-100"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 2. Main Product Content -->
  <div v-else-if="product" class="py-10 lg:py-16 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Breadcrumb -->
      <nav class="flex text-xs text-slate-500 mb-6 space-x-2">
        <router-link to="/" class="hover:text-brand-blue">Trang chủ</router-link>
        <span>/</span>
        <router-link to="/san-pham" class="hover:text-brand-blue">Sản phẩm</router-link>
        <span>/</span>
        <router-link :to="'/danh-muc/' + product.categoryId" class="hover:text-brand-blue">
          {{ product.categoryName }}
        </router-link>
        <span>/</span>
        <span class="text-slate-800 font-semibold truncate">{{ product.name }}</span>
      </nav>

      <!-- Main Layout: 2 Columns -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start mb-16">
        <!-- Left Column: Product Visuals & Trust Badges -->
        <div class="lg:col-span-5 space-y-6">
          <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/90 shadow-sm space-y-4">
            <!-- Main Hero Image -->
            <div class="relative aspect-[4/3] rounded-2xl overflow-hidden bg-slate-100 border border-slate-100">
              <img
                :src="product.image || '/logo.png'"
                :alt="product.name"
                class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
              />
              <span v-if="product.tag" class="absolute top-3 right-3 px-3 py-1 rounded-lg bg-brand-gold text-slate-950 font-black text-xs shadow-md">
                {{ product.tag }}
              </span>
            </div>

            <!-- 3 Trust Badges -->
            <div class="flex items-center gap-2 flex-wrap pt-1">
              <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                <CheckCircle2 class="w-3.5 h-3.5" /> Xưởng In Trực Tiếp
              </span>
              <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-brand-gold/15 text-amber-800 border border-brand-gold/30 flex items-center gap-1">
                <CheckCircle2 class="w-3.5 h-3.5" /> Chuẩn Màu Offset 98%
              </span>
              <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-brand-blue border border-blue-200 flex items-center gap-1">
                <CheckCircle2 class="w-3.5 h-3.5" /> Giao Nhanh 24H - 48H
              </span>
            </div>

            <!-- Features checklist -->
            <div v-if="product.features?.length" class="pt-4 border-t border-slate-100 space-y-2.5">
              <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                Đặc Điểm & Cam Kết Kỹ Thuật:
              </h4>
              <ul class="space-y-2 text-xs text-slate-600">
                <li v-for="(feat, idx) in product.features" :key="idx" class="flex items-start gap-2">
                  <CheckCircle2 class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>{{ feat }}</span>
                </li>
              </ul>
            </div>

            <!-- Description in Left Card -->
            <div v-if="product.description" class="pt-4 border-t border-slate-100 space-y-2">
              <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                <FileText class="w-3.5 h-3.5 text-brand-gold" />
                <span>Quy Cách & Mô Tả Xưởng:</span>
              </h4>
              <div class="text-xs text-slate-600 leading-relaxed whitespace-pre-line bg-slate-50 p-3.5 rounded-2xl border border-slate-100 font-medium">
                {{ product.description }}
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Interactive Price Calculator Component -->
        <div class="lg:col-span-7">
          <PriceCalculator :product="product" />
        </div>
      </div>

      <!-- Full-Width Detailed Description Section -->
      <div v-if="product.description" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm mb-12">
        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
          <FileText class="w-5 h-5 text-brand-gold" />
          <h3 class="text-base sm:text-lg font-black text-slate-900">
            Thông Tin Chi Tiết Ấn Phẩm {{ product.name }}
          </h3>
        </div>
        <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
          {{ product.description }}
        </div>
      </div>

      <!-- Related Products Section -->
      <div v-if="relatedProducts.length > 0" class="pt-8 border-t border-slate-200/80">
        <h3 class="text-xl font-black text-slate-900 mb-6">
          Ấn phẩm liên quan cùng danh mục
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
          <ProductCard
            v-for="p in relatedProducts"
            :key="p.id"
            :product="p"
          />
        </div>
      </div>
    </div>
  </div>

  <!-- 3. Not Found Empty State -->
  <div v-else class="py-20 bg-slate-50 min-h-screen flex items-center justify-center">
    <div class="text-center bg-white rounded-3xl p-8 border border-slate-200/90 max-w-md mx-auto shadow-xs">
      <h2 class="text-lg font-bold text-slate-800 mb-2">Ấn phẩm đang được cập nhật</h2>
      <p class="text-xs text-slate-500 mb-6">Sản phẩm này chưa được đưa lên hệ thống hoặc đang trong quá trình cập nhật bảng giá.</p>
      <div class="flex justify-center gap-3">
        <router-link to="/san-pham" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
          Xem các ấn phẩm khác
        </router-link>
        <router-link to="/yeu-cau-bao-gia" class="px-5 py-2.5 rounded-xl bg-brand-gold text-slate-950 font-bold text-xs">
          Báo giá riêng
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { CheckCircle2, PhoneCall, FileCheck, FileText } from 'lucide-vue-next'
import { siteSettings } from '../data/siteSettings'
import PriceCalculator from '../components/products/PriceCalculator.vue'
import ProductCard from '../components/products/ProductCard.vue'
import { api } from '../services/api'
import { setMetaTags } from '../composables/useSEO'

const route = useRoute()
const isLoading = ref(true)
const product = ref(null)
const allProducts = ref([])

function applyProductSEO(prod) {
  if (!prod) return
  setMetaTags({
    title: prod.meta_title || `${prod.name} - Giá Gốc Xưởng In Nhật Nguyên`,
    description: prod.meta_description || prod.description || 'Xưởng in Offset & Kỹ thuật số Nhật Nguyên — Chuyên sản xuất ấn phẩm bao bì, hộp giấy, túi giấy chất lượng cao, giá gốc trực tiếp.',
    keywords: prod.meta_keywords || `${prod.name}, in bao bi, xuong in nhat nguyen, in gia goc`,
    image: prod.image,
    url: prod.canonical_url || window.location.href,
    type: 'product',
    schema: {
      '@context': 'https://schema.org',
      '@type': 'Product',
      name: prod.name,
      description: prod.meta_description || prod.description || 'Sản phẩm in ấn chất lượng cao tại Xưởng In Nhật Nguyên',
      image: prod.image ? (prod.image.startsWith('http') ? prod.image : `https://innhatnguyen.vn${prod.image}`) : 'https://innhatnguyen.vn/logo.png',
      sku: `NN-${prod.id || prod.slug || 'IN-AN'}`,
      mpn: `NN-${prod.id || 'PRODUCT'}`,
      brand: {
        '@type': 'Brand',
        name: 'In Ấn Nhật Nguyên'
      },
      aggregateRating: {
        '@type': 'AggregateRating',
        ratingValue: '4.9',
        reviewCount: '156',
        bestRating: '5',
        worstRating: '1'
      },
      offers: {
        '@type': 'Offer',
        price: (prod.base_price && prod.base_price > 0) ? prod.base_price : 500,
        priceCurrency: 'VND',
        priceValidUntil: '2027-12-31',
        itemCondition: 'https://schema.org/NewCondition',
        availability: 'https://schema.org/InStock',
        url: prod.canonical_url || window.location.href,
        seller: {
          '@type': 'Organization',
          name: 'Xưởng In Ấn Nhật Nguyên'
        }
      }
    }
  })
}

async function loadProduct() {
  const slug = route.params.slug
  isLoading.value = true
  try {
    const res = await api.getProductBySlug(slug)
    if (res?.data) {
      const p = res.data
      let pSpecs = p.specs
      if (typeof pSpecs === 'string') {
        try { pSpecs = JSON.parse(pSpecs) } catch (e) { pSpecs = {} }
      }
      pSpecs = (pSpecs && typeof pSpecs === 'object') ? pSpecs : {}

      product.value = {
        ...p,
        basePrice: p.base_price,
        categoryId: p.category?.slug || p.category_id,
        categoryName: p.category?.name || 'Ấn phẩm in',
        minQuantity: p.min_quantity || 1,
        specs: pSpecs,
        features: p.features || [
          'In sắc nét trên máy Offset & Kỹ thuật số công nghiệp',
          'Miễn phí kiểm tra file in & chuẩn màu CMYK',
          'Giao hàng tận nơi toàn quốc, hỗ trợ gửi chành xe'
        ]
      }
      applyProductSEO(product.value)
      return
    }
  } catch (err) {
    // API error
  } finally {
    isLoading.value = false
  }
  product.value = null
}

onMounted(() => {
  loadProduct()
  api.getProducts().then(res => {
    if (res?.data?.length) {
      allProducts.value = res.data
    }
  }).catch(() => {})
})

watch(() => route.params.slug, () => {
  loadProduct()
  window.scrollTo({ top: 0, behavior: 'smooth' })
})

const relatedProducts = computed(() => {
  if (!product.value) return []
  return allProducts.value.filter(p => p.id !== product.value.id && p.slug !== product.value.slug).slice(0, 3)
})
</script>
