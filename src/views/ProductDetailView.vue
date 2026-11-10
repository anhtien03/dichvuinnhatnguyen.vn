<template>
  <div v-if="product" class="py-10 lg:py-16 bg-slate-50 min-h-screen">
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
                :src="product.image"
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
            <div class="pt-4 border-t border-slate-100 space-y-2.5">
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
          </div>

          <!-- Quick hotline support banner -->
          <div class="bg-brand-blue-light/50 border border-brand-blue/30 rounded-2xl p-4 text-xs flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-brand-blue text-white flex items-center justify-center shrink-0">
                <PhoneCall class="w-5 h-5" />
              </div>
              <div>
                <span class="font-bold text-slate-800 block">Cần tư vấn chọn chất liệu giấy?</span>
                <span class="text-slate-500 text-[11px]">Kỹ thuật viên hỗ trợ kiểm tra file trực tiếp</span>
              </div>
            </div>
            <a
              :href="'tel:' + siteSettings.hotlines[0].number.replace(/\s+/g, '')"
              class="font-black text-brand-blue hover:underline whitespace-nowrap text-sm"
            >
              {{ siteSettings.hotlines[0].number }}
            </a>
          </div>
        </div>

        <!-- Right Column: Product Title, Description & Interactive Price / Quote Module -->
        <div class="lg:col-span-7 space-y-6">
          <!-- Product Title & Basic info (Standard Studio White E-Commerce Header) -->
          <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-sm space-y-2.5">
            <div>
              <span class="font-bold text-brand-blue text-xs uppercase tracking-wider">
                {{ product.categoryName }}
              </span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-snug">
              {{ product.name }}
            </h1>

            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed pt-1">
              {{ product.description }}
            </p>
          </div>

          <!-- Price Calculator / Custom Quote Section -->
          <PriceCalculator :product="product" />
        </div>
      </div>

      <!-- Technical Guide & Tab Section -->
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm mb-16 space-y-6">
        <h3 class="text-xl font-black text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-4">
          <FileCheck class="w-5 h-5 text-brand-gold" />
          <span>Hướng Dẫn Chuẩn Bị File In Chuẩn Đẹp (Zero Error)</span>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs text-slate-600">
          <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-2">
            <strong class="text-slate-900 block font-bold text-sm">1. Hệ màu in CMYK</strong>
            <p>
              Tất cả các phần mềm thiết kế (Photoshop, Illustrator, Corel) bắt buộc phải chuyển về Color Mode <strong>CMYK</strong>. Nếu để hệ màu RGB khi in sẽ bị sai lệch màu sắc đáng kể.
            </p>
          </div>

          <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-2">
            <strong class="text-slate-900 block font-bold text-sm">2. Độ phân giải (DPI)</strong>
            <p>
              Hình ảnh đưa vào file thiết kế phải đạt độ phân giải tối thiểu <strong>300 DPI</strong> theo đúng kích thước thực để đảm bảo bản in ra sắc nét, không bị vỡ hạt hay mờ nét chữ.
            </p>
          </div>

          <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-2">
            <strong class="text-slate-900 block font-bold text-sm">3. Tràn lề cắt xén (Bleed)</strong>
            <p>
              Nền màu thiết kế phải chừa tràn lề tối thiểu <strong>2mm</strong> mỗi bên. Chữ và logo quan trọng phải thụt lùi vào trong mép thành phẩm ít nhất <strong>3mm - 4mm</strong> để tránh bị lưỡi dao chém vào.
            </p>
          </div>
        </div>
      </div>

      <!-- Related Products -->
      <div v-if="relatedProducts.length > 0">
        <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mb-6">
          Ấn Phẩm Liên Quan
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <ProductCard
            v-for="p in relatedProducts"
            :key="p.id"
            :product="p"
          />
        </div>
      </div>
    </div>
  </div>

  <!-- Branded Skeleton Loading State -->
  <div v-else class="py-10 lg:py-16 bg-slate-50 min-h-screen">
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
            <!-- Image Area with Centered Branded Logo & CMYK Wave -->
            <div class="relative aspect-[4/3] rounded-2xl bg-gradient-to-br from-slate-100 via-slate-50 to-slate-100 border border-slate-100 overflow-hidden flex flex-col items-center justify-center p-6 text-center">
              <!-- Glow pulse aura -->
              <div class="relative flex items-center justify-center mb-3">
                <span class="absolute inline-flex h-20 w-20 rounded-full bg-brand-blue/15 animate-ping"></span>
                <div class="relative w-16 h-16 rounded-2xl bg-white shadow-lg border border-slate-100 flex items-center justify-center p-2.5">
                  <img src="/logo-icon.png" alt="In Nhật Nguyên" class="w-full h-full object-contain animate-pulse" />
                </div>
              </div>

              <!-- CMYK 4-Color Wave Animation (Cyan, Magenta, Yellow, Key Black) -->
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
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { CheckCircle2, PhoneCall, FileCheck } from 'lucide-vue-next'
import { products as fallbackProducts } from '../data/products'
import { siteSettings } from '../data/siteSettings'
import PriceCalculator from '../components/products/PriceCalculator.vue'
import ProductCard from '../components/products/ProductCard.vue'
import { api } from '../services/api'
import { setMetaTags } from '../composables/useSEO'

const route = useRoute()
const product = ref(null)
const allProducts = ref([...fallbackProducts])

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
        price: (prod.base_price && prod.base_price > 0) ? prod.base_price : ((prod.basePrice && prod.basePrice > 0) ? prod.basePrice : 500),
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
  try {
    const res = await api.getProductBySlug(slug)
    if (res.data) {
      const p = res.data
      const fallback = fallbackProducts.find(f => f.slug === slug)
      const fallbackSpecs = fallback?.specs || { sizes: [], materials: [], finishings: [], quantities: [] }
      let pSpecs = p.specs
      if (typeof pSpecs === 'string') {
        try { pSpecs = JSON.parse(pSpecs) } catch (e) { pSpecs = {} }
      }
      pSpecs = (pSpecs && typeof pSpecs === 'object') ? pSpecs : {}
      const hasCustomSpecs = Boolean(pSpecs.quantities?.length || pSpecs.sizes?.length || pSpecs.materials?.length)
      const finalSpecs = {
        ...(hasCustomSpecs ? pSpecs : fallbackSpecs),
        hide_pricing: Boolean(pSpecs.hide_pricing)
      }

      product.value = {
        ...p,
        basePrice: p.base_price,
        categoryId: p.category?.slug || p.category_id,
        categoryName: p.category?.name || 'Ấn phẩm in',
        minQuantity: p.min_quantity || 1,
        specs: finalSpecs,
        features: p.features || (fallback?.features || [
          'In sắc nét trên máy Offset & Kỹ thuật số công nghiệp',
          'Miễn phí kiểm tra file in & chuẩn màu CMYK',
          'Giao hàng tận nơi toàn quốc, hỗ trợ gửi chành xe'
        ])
      }
      applyProductSEO(product.value)
      return
    }
  } catch (err) {
    // Fallback to local data
  }

  const found = fallbackProducts.find(p => p.slug === slug)
  if (found) {
    product.value = found
  } else {
    product.value = fallbackProducts[0]
  }
  applyProductSEO(product.value)
}

onMounted(() => {
  loadProduct()
  api.getProducts().then(res => {
    if (res.data?.length) {
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

