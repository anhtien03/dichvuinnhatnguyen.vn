<template>
  <div class="py-10 lg:py-16 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
      
      <!-- 1. Breadcrumb & Header Section -->
      <div class="space-y-4">
        <!-- Breadcrumb -->
        <nav class="flex items-center text-xs text-slate-500 space-x-2">
          <router-link to="/" class="hover:text-brand-blue flex items-center gap-1 transition-colors">
            <Home class="w-3.5 h-3.5" />
            <span>Trang chủ</span>
          </router-link>
          <span>/</span>
          <router-link
            to="/san-pham"
            :class="activeCategory === 'all' ? 'text-slate-900 font-bold' : 'hover:text-brand-blue transition-colors'"
          >
            Sản phẩm in ấn
          </router-link>
          <template v-if="activeCategory !== 'all' && currentCategory">
            <span>/</span>
            <span class="text-brand-blue font-bold">{{ currentCategory.name }}</span>
          </template>
        </nav>

        <!-- Main Title & Stats -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pt-1">
          <div>
            <span class="text-xs font-bold text-brand-gold uppercase tracking-wider block mb-1">
              DANH MỤC ẤN PHẨM TRỰC TIẾP TẠI XƯỞNG
            </span>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight flex items-center flex-wrap gap-3">
              <span>{{ currentCategoryTitle }}</span>
              <span class="text-xs font-bold px-3 py-1 rounded-full bg-brand-blue/10 text-brand-blue border border-brand-blue/20">
                {{ filteredAndSortedProducts.length }} ấn phẩm
              </span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-2 max-w-2xl leading-relaxed">
              {{ currentCategoryDescription }}
            </p>
          </div>

          <!-- Quick Trust Badges -->
          <div class="flex items-center gap-2 flex-wrap">
            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-700 bg-white px-3 py-1.5 rounded-lg border border-slate-200/90 shadow-2xs">
              <ShieldCheck class="w-3.5 h-3.5 text-emerald-600" />
              Giá gốc tận xưởng
            </span>
            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-700 bg-white px-3 py-1.5 rounded-lg border border-slate-200/90 shadow-2xs">
              <Zap class="w-3.5 h-3.5 text-amber-500" />
              In gấp lấy liền
            </span>
            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-700 bg-white px-3 py-1.5 rounded-lg border border-slate-200/90 shadow-2xs">
              <Sparkles class="w-3.5 h-3.5 text-brand-blue" />
              Free check file in
            </span>
          </div>
        </div>
      </div>

      <!-- 2. Filters & Search Toolbar -->
      <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs space-y-4">
        
        <!-- Category Pill Buttons (Horizontal Scrollable) -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1.5 scrollbar-none">
          <button
            type="button"
            @click="selectCategory('all')"
            :class="[
              'px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all duration-200 flex items-center gap-1.5 shrink-0 cursor-pointer',
              activeCategory === 'all'
                ? 'bg-brand-blue text-white shadow-md'
                : 'bg-slate-100/90 text-slate-700 hover:bg-slate-200/80 hover:text-slate-900'
            ]"
          >
            <span>Tất cả ấn phẩm</span>
            <span
              :class="[
                'text-[10px] px-1.5 py-0.5 rounded-full font-bold',
                activeCategory === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600'
              ]"
            >
              {{ products.length }}
            </span>
          </button>

          <button
            v-for="cat in categories"
            :key="cat.id"
            type="button"
            @click="selectCategory(cat.slug)"
            :class="[
              'px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all duration-200 flex items-center gap-1.5 shrink-0 cursor-pointer',
              activeCategory === cat.slug
                ? 'bg-brand-blue text-white shadow-md'
                : 'bg-slate-100/90 text-slate-700 hover:bg-slate-200/80 hover:text-slate-900'
            ]"
          >
            <span>{{ cat.name }}</span>
            <span
              :class="[
                'text-[10px] px-1.5 py-0.5 rounded-full font-bold',
                activeCategory === cat.slug ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600'
              ]"
            >
              {{ getCategoryProductCount(cat.slug) }}
            </span>
          </button>
        </div>

        <!-- Sub Toolbar: Quick tags + Search input + Sort Dropdown -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pt-3 border-t border-slate-100 text-xs">
          
          <!-- Quick Filter Tags -->
          <div class="flex items-center gap-2 overflow-x-auto pb-1 lg:pb-0">
            <span class="text-slate-400 text-[11px] font-semibold whitespace-nowrap hidden sm:inline">Lọc nhanh:</span>
            <button
              v-for="tag in quickTags"
              :key="tag.value"
              type="button"
              @click="activeQuickTag = activeQuickTag === tag.value ? '' : tag.value"
              :class="[
                'px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all cursor-pointer border',
                activeQuickTag === tag.value
                  ? 'bg-brand-gold/15 text-slate-950 border-brand-gold font-bold shadow-2xs'
                  : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100'
              ]"
            >
              {{ tag.label }}
            </button>
            <button
              v-if="activeQuickTag || searchKeyword"
              @click="clearFilters"
              class="text-xs text-rose-600 hover:text-rose-700 font-semibold px-2 py-1 underline cursor-pointer whitespace-nowrap"
            >
              Đặt lại
            </button>
          </div>

          <!-- Search & Sort Controls -->
          <div class="flex items-center gap-2.5 w-full lg:w-auto">
            <!-- Search Input -->
            <div class="relative flex-1 lg:w-64">
              <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
              <input
                v-model="searchKeyword"
                type="text"
                placeholder="Tìm tên sản phẩm (namecard, túi...)"
                class="w-full pl-9 pr-8 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue bg-slate-50/50 focus:bg-white transition-colors"
              />
              <button
                v-if="searchKeyword"
                @click="searchKeyword = ''"
                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs font-bold p-1 cursor-pointer"
                title="Xóa tìm kiếm"
              >
                ✕
              </button>
            </div>

            <!-- Sort Select -->
            <div class="relative shrink-0">
              <select
                v-model="sortBy"
                class="appearance-none bg-slate-50/50 hover:bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 pr-7 text-xs font-semibold text-slate-700 cursor-pointer focus:outline-none focus:border-brand-blue transition-colors"
              >
                <option value="default">Sắp xếp: Mặc định</option>
                <option value="price-asc">Giá: Thấp đến Cao</option>
                <option value="price-desc">Giá: Cao đến Thấp</option>
                <option value="name-asc">Tên: A &rarr; Z</option>
              </select>
              <ArrowUpDown class="w-3.5 h-3.5 text-slate-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" />
            </div>
          </div>
        </div>
      </div>

      <!-- 3. Products Grid -->
      <div v-if="filteredAndSortedProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
        <ProductCard
          v-for="p in filteredAndSortedProducts"
          :key="p.id"
          :product="p"
        />
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-16 sm:py-20 bg-white rounded-3xl border border-slate-200/90 p-8 shadow-xs">
        <PackageSearch class="w-16 h-16 text-slate-300 mx-auto mb-4" />
        <h3 class="text-lg font-bold text-slate-800">Không tìm thấy ấn phẩm nào phù hợp</h3>
        <p class="text-xs sm:text-sm text-slate-500 mt-2 max-w-md mx-auto leading-relaxed">
          Xưởng in Nhật Nguyên nhận gia công mọi quy cách đặc thù theo yêu cầu thiết kế của bạn.
        </p>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
          <button
            @click="clearFilters"
            class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs cursor-pointer transition-colors"
          >
            Xóa tất cả bộ lọc
          </button>
          <router-link
            to="/yeu-cau-bao-gia"
            class="px-5 py-2.5 rounded-xl bg-brand-gold hover:bg-brand-gold-hover text-slate-950 font-bold text-xs shadow-md transition-all flex items-center gap-1.5"
          >
            <span>Gửi yêu cầu báo giá riêng</span>
            <ArrowRight class="w-3.5 h-3.5" />
          </router-link>
        </div>
      </div>

      <!-- 4. Bottom Custom Spec & Consultation Banner (Khối Báo Giá Quy Cách Riêng) -->
      <div class="bg-gradient-to-br from-brand-blue-dark via-brand-blue to-brand-blue-dark rounded-3xl p-8 sm:p-10 text-white shadow-xl relative overflow-hidden">
        <!-- Background decorative graphic shapes -->
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-64 h-64 rounded-full bg-brand-gold/10 blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
          <div class="space-y-3 max-w-2xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-brand-gold text-xs font-bold tracking-wider uppercase border border-white/15">
              <Sparkles class="w-3.5 h-3.5" />
              Dịch Vụ Gia Công Theo Yêu Cầu Riêng
            </span>
            <h2 class="text-xl sm:text-2xl lg:text-3xl font-black tracking-tight leading-tight">
              Bạn Cần In Ấn Quy Cách Đặc Biệt Hoặc Đơn Hàng Số Lượng Lớn?
            </h2>
            <p class="text-xs sm:text-sm text-slate-200 leading-relaxed">
              Nhật Nguyên hỗ trợ gia công mọi kỹ thuật cao cấp: ép kim ánh kim, dập nổi định hình, phủ UV cục bộ, bế khuôn theo thiết kế, cán màng nhung, in giấy mỹ thuật nhập khẩu. Đội ngũ kỹ thuật viên xưởng in luôn sẵn sàng tư vấn trực tiếp 24/7.
            </p>
          </div>

          <div class="flex flex-col gap-3 shrink-0 w-full lg:w-auto lg:min-w-[330px]">
            <router-link
              to="/yeu-cau-bao-gia"
              class="w-full inline-flex items-center justify-center px-6 py-3.5 rounded-xl bg-brand-gold hover:bg-brand-gold-hover text-slate-950 font-black text-xs sm:text-sm shadow-lg transition-all duration-200 gap-2 group text-center cursor-pointer whitespace-nowrap"
            >
              <span>Gửi Yêu Cầu Báo Giá Nhanh</span>
              <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
            </router-link>

            <div class="grid grid-cols-2 gap-2.5 w-full">
              <a
                href="https://zalo.me/0359005469"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center px-3 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition-all gap-1.5 whitespace-nowrap"
              >
                <MessageCircle class="w-4 h-4 text-emerald-400 shrink-0" />
                <span>Chat Zalo</span>
              </a>

              <a
                href="tel:0359005469"
                class="inline-flex items-center justify-center px-3 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition-all gap-1.5 whitespace-nowrap"
              >
                <PhoneCall class="w-4 h-4 text-amber-400 shrink-0" />
                <span>0359 005 469</span>
              </a>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
  Search,
  PackageSearch,
  Home,
  ShieldCheck,
  Zap,
  Sparkles,
  ArrowRight,
  ArrowUpDown,
  MessageCircle,
  PhoneCall
} from 'lucide-vue-next'
import { products as fallbackProducts } from '../data/products'
import { categories as fallbackCategories } from '../data/categories'
import ProductCard from '../components/products/ProductCard.vue'
import { api } from '../services/api'
import { setMetaTags } from '../composables/useSEO'

const route = useRoute()
const router = useRouter()

const products = ref([...fallbackProducts])
const categories = ref([...fallbackCategories])

// Read both params and query for seamless compatibility
function getInitialCategory() {
  return route.params.categorySlug || route.query.cat || 'all'
}

const activeCategory = ref(getInitialCategory())
const searchKeyword = ref('')
const activeQuickTag = ref('')
const sortBy = ref('default')

// Quick filter tags
const quickTags = [
  { label: '⚡ In lấy liền / 24h', value: 'fast' },
  { label: '⭐ Bán chạy nhất', value: 'popular' },
  { label: '🏷️ Giá xưởng', value: 'bulk' }
]

function applyCategorySEO() {
  if (activeCategory.value === 'all') {
    setMetaTags({
      title: 'Tất Cả Sản Phẩm & Dịch Vụ In Ấn Trực Tiếp Tại Xưởng | Nhật Nguyên',
      description: 'Bảng danh mục đầy đủ các ấn phẩm in Offset và In Kỹ Thuật Số trực tiếp tại xưởng in Nhật Nguyên. Nhận in mọi số lượng, chuẩn màu nhận diện, giá gốc xưởng.',
      keywords: 'in hop giay, in tui giay, in decal, in catalogue, in to roi, in namecard, xuong in tphcm',
      url: 'https://dichvuinnhatnguyen.vn/san-pham'
    })
    return
  }

  const cat = currentCategory.value
  if (cat) {
    setMetaTags({
      title: cat.meta_title || `${cat.name} - Giá Gốc Trực Tiếp Tại Xưởng TPHCM | Nhật Nguyên`,
      description: cat.meta_description || cat.description || `Xưởng in Nhật Nguyên chuyên sản xuất ${cat.name} chất lượng cao, công nghệ in hiện đại, giá xưởng trực tiếp không qua trung gian.`,
      keywords: cat.meta_keywords || `${cat.name}, xuong in nhat nguyen, in gia goc, bao gia ${cat.name}`,
      url: cat.canonical_url || `https://dichvuinnhatnguyen.vn/danh-muc/${cat.slug}`,
      schema: {
        '@context': 'https://schema.org',
        '@type': 'CollectionPage',
        name: cat.meta_title || cat.name,
        description: cat.meta_description || cat.description,
        url: `https://dichvuinnhatnguyen.vn/danh-muc/${cat.slug}`
      }
    })
  }
}

// Keep in sync with route changes
watch(
  () => [route.params.categorySlug, route.query.cat],
  ([newParamsSlug, newQueryCat]) => {
    activeCategory.value = newParamsSlug || newQueryCat || 'all'
    applyCategorySEO()
  }
)

const currentCategory = computed(() => {
  return categories.value.find(c => c.slug === activeCategory.value)
})

const currentCategoryTitle = computed(() => {
  if (activeCategory.value === 'all') return 'Tất Cả Sản Phẩm & Dịch Vụ In Ấn'
  return currentCategory.value ? currentCategory.value.name : 'Danh Mục Sản Phẩm'
})

const currentCategoryDescription = computed(() => {
  if (activeCategory.value === 'all') {
    return 'Bảng danh mục đầy đủ các ấn phẩm in Offset và In Kỹ Thuật Số trực tiếp tại xưởng in Nhật Nguyên. Nhận in mọi số lượng, chuẩn màu nhận diện, giá gốc xưởng.'
  }
  return currentCategory.value ? (currentCategory.value.meta_description || currentCategory.value.description) : 'Các ấn phẩm chất lượng cao tại xưởng in Nhật Nguyên.'
})

function getCategoryProductCount(slug) {
  return products.value.filter(p => (p.categoryId === slug || p.category?.slug === slug)).length
}

const filteredAndSortedProducts = computed(() => {
  let list = products.value.filter(p => {
    // Category match
    const pCat = p.categoryId || p.category?.slug
    const matchCat = activeCategory.value === 'all' || pCat === activeCategory.value

    // Search keyword match (name or description)
    const q = searchKeyword.value.toLowerCase().trim()
    const matchKey = !q ||
      p.name.toLowerCase().includes(q) ||
      (p.description && p.description.toLowerCase().includes(q)) ||
      (p.tag && p.tag.toLowerCase().includes(q))

    // Quick tag match
    let matchTag = true
    const pPrice = p.base_price !== undefined ? p.base_price : p.basePrice
    const pTurnaround = p.turnaround || ''
    if (activeQuickTag.value === 'fast') {
      matchTag = pTurnaround.includes('24h') || pTurnaround.includes('2h') || pTurnaround.includes('30 phút') || (p.tag && p.tag.toLowerCase().includes('lấy'))
    } else if (activeQuickTag.value === 'popular') {
      matchTag = p.is_popular || (p.tag && (p.tag.toLowerCase().includes('bán chạy') || p.tag.toLowerCase().includes('nhất'))) || p.id <= 4
    } else if (activeQuickTag.value === 'bulk') {
      matchTag = pPrice <= 5000 || (p.tag && p.tag.toLowerCase().includes('xưởng'))
    }

    return matchCat && matchKey && matchTag
  })

  // Sort logic
  if (sortBy.value === 'price-asc') {
    list = [...list].sort((a, b) => (a.base_price || a.basePrice || 0) - (b.base_price || b.basePrice || 0))
  } else if (sortBy.value === 'price-desc') {
    list = [...list].sort((a, b) => (b.base_price || b.basePrice || 0) - (a.base_price || a.basePrice || 0))
  } else if (sortBy.value === 'name-asc') {
    list = [...list].sort((a, b) => a.name.localeCompare(b.name, 'vi'))
  }

  return list
})

function selectCategory(slug) {
  activeCategory.value = slug
  if (slug === 'all') {
    router.push('/san-pham')
  } else {
    router.push('/danh-muc/' + slug)
  }
}

function clearFilters() {
  activeCategory.value = 'all'
  searchKeyword.value = ''
  activeQuickTag.value = ''
  sortBy.value = 'default'
  router.push('/san-pham')
}

async function loadData() {
  try {
    const [catRes, prodRes] = await Promise.all([
      api.getCategories().catch(() => null),
      api.getProducts().catch(() => null)
    ])
    if (catRes?.data?.length) {
      categories.value = catRes.data
    }
    if (prodRes?.data && Array.isArray(prodRes.data)) {
      products.value = prodRes.data.map(p => ({
        ...p,
        basePrice: p.base_price,
        categoryId: p.category?.slug || p.category_id
      }))
    } else {
      products.value = []
    }
  } catch (err) {
    products.value = []
  }
  applyCategorySEO()
}

onMounted(() => {
  loadData()
})
</script>
