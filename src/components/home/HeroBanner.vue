<template>
  <section class="py-6 bg-slate-100/60 border-b border-slate-200/80">
    <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-8 space-y-5">
      
      <!-- HÀNG 1: BỐ CỤC 3 CỘT (Menu trái + BANNER LỚN ở giữa + 2 Thẻ phụ bên phải) -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
        
        <!-- Cột 1 (3/12): Menu Danh Mục Sản Phẩm Nổi Bật -->
        <div class="hidden lg:flex lg:col-span-3 flex-col bg-white rounded-2xl border border-slate-200/90 shadow-sm relative z-20">
          <div class="bg-brand-blue text-white px-4 py-3.5 flex items-center justify-between font-black text-sm uppercase tracking-wider rounded-t-2xl">
            <span class="flex items-center gap-2">
              <Layers class="w-4 h-4 text-brand-gold" />
              SẢN PHẨM NỔI BẬT
            </span>
          </div>


          <!-- Category List (Hiển thị danh sách danh mục trực tiếp, gọn gàng, bấm vào tới trang danh mục, không có popup che màn hình) -->
          <div class="flex-1 divide-y divide-slate-100 text-xs font-semibold">
            <div
              v-for="cat in menuCategories"
              :key="cat.id"
            >
              <router-link
                :to="cat.link"
                class="flex items-center justify-between px-3.5 py-2.5 transition-colors group hover:bg-brand-blue-light/40 text-slate-700 hover:text-brand-blue"
              >
                <div class="flex items-center gap-2.5">
                  <span class="text-base">{{ cat.icon }}</span>
                  <span>{{ cat.name }}</span>
                </div>

                <!-- Mũi tên chuyển hướng -->
                <ChevronRight
                  class="w-3.5 h-3.5 text-slate-400 group-hover:text-brand-blue group-hover:translate-x-0.5 transition-transform"
                />
              </router-link>
            </div>
          </div>

          <!-- Footer View All -->
          <div class="p-2.5 border-t border-slate-100 bg-slate-50/50 text-center rounded-b-2xl">
            <router-link
              to="/san-pham"
              class="text-xs font-bold text-brand-blue flex items-center justify-center gap-1 py-0.5"
            >
              Xem tất cả danh mục <ArrowRight class="w-3.5 h-3.5" />
            </router-link>
          </div>
        </div>

        <!-- Cột 2 (6/12): BANNER LỚN Ở GIỮA (Tràn trọn vẹn 100% bề ngang, hiển thị to rõ) -->
        <div class="lg:col-span-6 rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden relative group bg-slate-900 min-h-[300px] sm:min-h-[350px] lg:min-h-0 flex flex-col justify-between">
          <router-link
            :to="mainBanner?.link_url || '/yeu-cau-bao-gia'"
            class="block w-full h-full relative flex-1 cursor-pointer"
          >
            <img
              :src="mainBanner?.image_url || '/banner-chinh.jpg'"
              :alt="mainBanner?.title || 'Xưởng In Nhật Nguyên'"
              class="w-full h-full object-cover object-center group-hover:scale-[1.02] transition-transform duration-700"
              loading="eager"
            />
          </router-link>
        </div>

        <!-- Cột 3 (3/12): 2 Banner Phụ Bên Phải (Căn Chuẩn Kích Thước Bằng Cột Giữa) -->
        <div class="lg:col-span-3 flex flex-col sm:grid sm:grid-cols-2 lg:flex lg:flex-col gap-4 h-full">
          <!-- Card 1: sub1Banner -->
          <router-link
            :to="sub1Banner?.link_url || '/yeu-cau-bao-gia'"
            class="rounded-2xl overflow-hidden relative shadow-sm hover:shadow-xl transition-all group border border-slate-200/90 bg-slate-900 block aspect-[16/9] lg:aspect-auto flex-1 min-h-[140px] cursor-pointer"
          >
            <img
              :src="sub1Banner?.image_url || '/sub-banner-in-nhanh.jpg'"
              :alt="sub1Banner?.title || 'Banner In Nhanh KTS'"
              class="w-full h-full object-cover object-center group-hover:scale-[1.03] transition-transform duration-500"
            />
          </router-link>

          <!-- Card 2: sub2Banner -->
          <router-link
            :to="sub2Banner?.link_url || '/san-pham/in-tem-nhan-decal-be-thanh-pham'"
            class="rounded-2xl overflow-hidden relative shadow-sm hover:shadow-xl transition-all group border border-slate-200/90 bg-slate-900 block aspect-[16/9] lg:aspect-auto flex-1 min-h-[140px] cursor-pointer"
          >
            <img
              :src="sub2Banner?.image_url || '/sub-banner-tem-nhan.jpg'"
              :alt="sub2Banner?.title || 'Banner Tem Nhãn Decal'"
              class="w-full h-full object-cover object-center group-hover:scale-[1.03] transition-transform duration-500"
            />
          </router-link>
        </div>

      </div>

      <!-- HÀNG 2: PHẦN "BẠN CẦN IN ?" ĐƯỢC ĐƯA XUỐNG DƯỚI (Thoáng, sang trọng, đầy đủ thông tin) -->
      <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-7 relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-80 h-80 bg-brand-blue/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
          
          <!-- Cột Trái (7/12): Tiêu đề, chữ gõ động & Mô tả -->
          <div class="lg:col-span-7 space-y-3 text-left">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200/60 text-brand-blue text-[11px] font-bold uppercase tracking-wider">
              <span class="w-1.5 h-1.5 rounded-full bg-brand-gold animate-ping"></span>
              IN NHANH CHUYÊN NGHIỆP 
            </div>

            <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
              <h2 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight uppercase">
                BẠN CẦN IN ?
              </h2>
              <div class="inline-flex items-center min-h-[40px]">
                <span class="text-xl sm:text-2xl lg:text-3xl font-black text-brand-blue tracking-tight">
                  {{ displayText }}&#8203;
                </span>
                <span class="inline-block w-1 h-6 sm:h-7 bg-brand-gold ml-1.5 animate-pulse shrink-0"></span>
              </div>
            </div>

            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-2xl">
              Dễ dàng đặt in ấn phẩm tùy chỉnh cho doanh nghiệp và thương hiệu của bạn. Hỗ trợ thiết kế, kiểm tra file miễn phí — <strong>In nhanh 24h, In Offset & In KTS sắc nét chuẩn màu</strong>.
            </p>
          </div>

          <!-- Cột Phải (5/12): 4 Cam kết & Hộp Hotline 2 Số -->
          <div class="lg:col-span-5 flex flex-col justify-between gap-4 lg:border-l lg:border-slate-100 lg:pl-6">
            <!-- 4 Cam kết dịch vụ -->
            <div class="grid grid-cols-2 gap-2.5 text-xs font-semibold text-slate-700">
              <div v-for="text in promises" :key="text" class="flex items-center gap-1.5">
                <CheckCircle2 class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                <span>{{ text }}</span>
              </div>
            </div>

            <!-- Tách Thành 2 Ô Hotline Riêng Biệt -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
              <!-- Ô 1: Kinh doanh -->
              <a
                :href="'tel:' + siteSettings.hotlines[0].number.replace(/\s+/g, '')"
                class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-50 hover:bg-blue-50/60 border border-slate-200 hover:border-brand-blue/50 transition-all group cursor-pointer shadow-xs"
              >
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                  <PhoneCall class="w-4 h-4" />
                </div>
                <div class="text-left text-xs leading-tight">
                  <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Kinh Doanh</span>
                  <span class="font-black text-slate-900 group-hover:text-brand-blue transition-colors text-xs sm:text-[13px] block mt-0.5">
                    {{ siteSettings.hotlines[0].number }}
                  </span>
                </div>
              </a>

              <!-- Ô 2: Kinh doanh -->
              <a
                :href="'tel:' + siteSettings.hotlines[1].number.replace(/\s+/g, '')"
                class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-50 hover:bg-blue-50/60 border border-slate-200 hover:border-brand-blue/50 transition-all group cursor-pointer shadow-xs"
              >
                <div class="w-8 h-8 rounded-lg bg-brand-blue/10 text-brand-blue flex items-center justify-center shrink-0 group-hover:bg-brand-blue group-hover:text-white transition-colors">
                  <PhoneCall class="w-4 h-4" />
                </div>
                <div class="text-left text-xs leading-tight">
                  <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Kinh Doanh</span>
                  <span class="font-black text-slate-900 group-hover:text-brand-blue transition-colors text-xs sm:text-[13px] block mt-0.5">
                    {{ siteSettings.hotlines[1].number }}
                  </span>
                </div>
              </a>
            </div>
          </div>

        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { Layers, Search, ChevronRight, ArrowRight, CheckCircle2, Calculator, Sparkles, PhoneCall } from 'lucide-vue-next'
import { api } from '../../services/api'
import { siteSettings } from '../../data/siteSettings'

const router = useRouter()
const searchKeyword = ref('')
const activeBanners = ref([])
const loadedProducts = ref([])
const hoveredCategoryId = ref(null)
let flyoutTimeout = null
const primaryHotline = computed(() => siteSettings.hotlines?.[0]?.number || '0359 005 469')

async function loadBanners() {
  try {
    const res = await api.getBanners()
    if (res.data && res.data.length > 0) {
      activeBanners.value = res.data
    }
  } catch (err) {
    // Graceful fallback to static defaults
  }
}

async function loadProducts() {
  try {
    const res = await api.getProducts()
    if (res?.data) {
      loadedProducts.value = Array.isArray(res.data) ? res.data : (res.data.data || [])
    }
  } catch (err) {
    console.error('Failed to load products for HeroBanner', err)
  }
}

const mainBanner = computed(() => {
  return activeBanners.value.find(b => b.position === 'home_main') || null
})

const sub1Banner = computed(() => {
  return activeBanners.value.find(b => b.position === 'home_sub_1') || null
})

const sub2Banner = computed(() => {
  return activeBanners.value.find(b => b.position === 'home_sub_2') || null
})

function handleSearch() {
  if (searchKeyword.value.trim()) {
    router.push({ path: '/san-pham', query: { q: searchKeyword.value.trim() } })
  }
}

const menuCategories = [
  { id: 'hop-giay', name: 'Hộp giấy', icon: '📦', link: '/san-pham?cat=bao-bi-tui-giay', slugMatch: ['hop', 'hop-giay', 'hop-cung'] },
  { id: 'tui-giay', name: 'Túi giấy - Paper bags', icon: '🛍️', link: '/san-pham?cat=bao-bi-tui-giay', slugMatch: ['tui', 'tui-giay'] },
  { id: 'nhan-decal', name: 'Nhãn Decal', icon: '🏷️', link: '/san-pham?cat=tem-nhan-decal', slugMatch: ['decal', 'tem', 'nhan-mac', 'tag'] },
  { id: 'bia-so-mi', name: 'Bìa Sơ Mi (Folder) - Folder', icon: '📁', link: '/san-pham?cat=an-pham-van-phong', slugMatch: ['folder', 'bia', 'kep-file'] },
  { id: 'to-roi', name: 'Tờ Rơi - Leaflets, Brochures', icon: '📰', link: '/san-pham?cat=tiep-thi-quang-cao', slugMatch: ['to-roi', 'flyer'] },
  { id: 'catalogue', name: 'Catalogue - Cẩm Nang', icon: '📖', link: '/san-pham?cat=tiep-thi-quang-cao', slugMatch: ['catalogue', 'cam-nang'] },
  { id: 'brochure', name: 'Brochure - Tờ Gấp', icon: '📑', link: '/san-pham?cat=tiep-thi-quang-cao', slugMatch: ['brochure', 'to-gap'] },
  { id: 'ho-so-nang-luc', name: 'Hồ sơ năng lực', icon: '🖼️', link: '/san-pham?cat=tiep-thi-quang-cao', slugMatch: ['ho-so-nang-luc', 'profile'] },
  { id: 'the-nhua', name: 'In Thẻ Nhựa', icon: '💳', link: '/san-pham?cat=an-pham-van-phong', slugMatch: ['the-nhua', 'the-pvc'] }
]

function getCategoryProducts(cat) {
  if (!loadedProducts.value || loadedProducts.value.length === 0) return []
  return loadedProducts.value.filter(p => {
    const slug = (p.slug || '').toLowerCase()
    const name = (p.name || '').toLowerCase()
    const catSlug = (p.category?.slug || '').toLowerCase()
    return cat.slugMatch.some(k => slug.includes(k) || name.includes(k) || catSlug.includes(k))
  })
}

const activeHoveredCategory = computed(() => {
  if (!hoveredCategoryId.value) return null
  return menuCategories.find(c => c.id === hoveredCategoryId.value) || null
})

const activeCategoryProducts = computed(() => {
  if (!activeHoveredCategory.value) return []
  return getCategoryProducts(activeHoveredCategory.value)
})

function handleCategoryMouseEnter(cat) {
  if (flyoutTimeout) {
    clearTimeout(flyoutTimeout)
    flyoutTimeout = null
  }
  hoveredCategoryId.value = cat.id
}

function handleCategoryMouseLeave() {
  flyoutTimeout = setTimeout(() => {
    hoveredCategoryId.value = null
  }, 180)
}

function cancelFlyoutClose() {
  if (flyoutTimeout) {
    clearTimeout(flyoutTimeout)
    flyoutTimeout = null
  }
}

const rotatingWords = [
  'Menu Bìa Bồi Cứng',
  'Name Card - Danh Thiếp',
  'Tem Nhãn Decal Bế Sẵn',
  'Túi Giấy & Hộp Giấy',
  'Tờ Rơi - Flyer - Brochure',
  'Catalogue - Hồ Sơ Năng Lực',
  'Voucher - Thẻ Tích Điểm',
  'Standee - Băng Rôn Lấy Liền'
]

const promises = ['Có ngay trong ngày', 'Hỗ trợ thiết kế', 'Chuẩn màu sắc nét', 'Giao hàng tận nơi']

const displayText = ref('')
let wordIdx = 0
let charIdx = 0
let isDeleting = false
let timer = null

function typeEffect() {
  const word = rotatingWords[wordIdx]
  if (isDeleting) {
    displayText.value = word.substring(0, charIdx - 1)
    charIdx--
  } else {
    displayText.value = word.substring(0, charIdx + 1)
    charIdx++
  }

  let delay = isDeleting ? 40 : 85
  if (!isDeleting && charIdx === word.length) {
    delay = 2000
    isDeleting = true
  } else if (isDeleting && charIdx === 0) {
    isDeleting = false
    wordIdx = (wordIdx + 1) % rotatingWords.length
    delay = 400
  }

  timer = setTimeout(typeEffect, delay)
}

onMounted(() => {
  typeEffect()
  loadBanners()
  loadProducts()
})

onUnmounted(() => {
  if (timer) clearTimeout(timer)
})
</script>
