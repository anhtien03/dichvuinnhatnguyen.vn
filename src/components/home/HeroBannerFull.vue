<template>
  <section class="bg-slate-50 overflow-hidden">
    <!-- 1. FULL-WIDTH BANNER SLIDER (Phong cách giống Thế Giới In Ấn & In Khánh Việt) -->
    <div class="w-full relative bg-slate-900 group select-none">
      <!-- Slides Container -->
      <div class="relative w-full overflow-hidden aspect-[3/1] min-h-[240px] sm:min-h-[360px] md:min-h-[440px] lg:min-h-[500px] xl:min-h-[560px]">
        <div
          v-for="(slide, index) in slides"
          :key="slide.id"
          class="absolute inset-0 transition-opacity duration-700 ease-in-out"
          :class="currentSlide === index ? 'opacity-100 z-10 pointer-events-auto' : 'opacity-0 z-0 pointer-events-none'"
        >
          <router-link :to="slide.link" class="block w-full h-full relative cursor-pointer">
            <img
              :src="slide.image"
              :alt="slide.title"
              class="w-full h-full object-cover object-center"
              loading="eager"
              fetchpriority="high"
            />
          </router-link>
        </div>
      </div>

      <!-- Left Arrow -->
      <button
        @click="prevSlide"
        aria-label="Slide trước"
        class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 z-20 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-black/40 hover:bg-brand-blue text-white backdrop-blur-md flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 shadow-lg hover:scale-110 active:scale-95"
      >
        <ChevronLeft class="w-6 h-6" />
      </button>

      <!-- Right Arrow -->
      <button
        @click="nextSlide"
        aria-label="Slide kế tiếp"
        class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 z-20 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-black/40 hover:bg-brand-blue text-white backdrop-blur-md flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 shadow-lg hover:scale-110 active:scale-95"
      >
        <ChevronRight class="w-6 h-6" />
      </button>

      <!-- Pagination Bullets -->
      <div class="absolute bottom-3 sm:bottom-5 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2">
        <button
          v-for="(_, index) in slides"
          :key="index"
          @click="goToSlide(index)"
          :aria-label="'Chuyển đến slide ' + (index + 1)"
          class="h-2 sm:h-2.5 rounded-full transition-all duration-300 shadow-sm"
          :class="currentSlide === index ? 'w-8 sm:w-10 bg-brand-gold' : 'w-2 sm:w-2.5 bg-white/60 hover:bg-white'"
        ></button>
      </div>
    </div>

    <!-- 2. QUICK INTERACTIVE BAR (Gõ phím động & Hành động nhanh) -->
    <div class="bg-white border-b border-slate-200/90 shadow-sm relative z-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-5">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
          <!-- Left: Rotating Typewriter (BẠN CẦN IN ...) -->
          <div class="flex flex-wrap items-center justify-center lg:justify-start gap-2 text-center lg:text-left">
            <span class="text-xs sm:text-sm font-black uppercase text-slate-800 tracking-wider flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
              BẠN CẦN IN:
            </span>
            <div class="inline-flex items-center">
              <span class="text-base sm:text-xl md:text-2xl font-black text-brand-blue tracking-tight">
                {{ displayText }}
              </span>
              <span class="inline-block w-0.5 sm:w-1 h-5 sm:h-6 bg-brand-gold ml-1.5 animate-pulse"></span>
            </div>
          </div>

          <!-- Right: Fast Actions -->
          <div class="flex items-center gap-2.5 sm:gap-3 shrink-0">
            <router-link
              to="/yeu-cau-bao-gia"
              class="px-5 sm:px-6 py-2.5 rounded-full bg-brand-gold hover:bg-brand-gold-hover text-slate-950 font-black text-xs sm:text-sm shadow-md transition-all transform hover:-translate-y-0.5 active:translate-y-0 flex items-center gap-2"
            >
              <Calculator class="w-4 h-4" />
              <span>Đặt Hàng Ngay</span>
            </router-link>

            <a
              :href="'tel:' + siteSettings.hotlines[0].number.replace(/\s+/g, '')"
              class="px-5 sm:px-6 py-2.5 rounded-full bg-brand-blue hover:bg-brand-blue-dark text-white font-bold text-xs sm:text-sm shadow-md transition-all flex items-center gap-2"
            >
              <PhoneCall class="w-4 h-4" />
              <span>Hotline: {{ siteSettings.hotlines[0].number }}</span>
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- 3. FOUR CORE VALUE PROPOSITIONS (Cam Kết Cốt Lõi Xưởng In) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div
          v-for="(usp, idx) in uspList"
          :key="idx"
          class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:border-brand-blue hover:shadow-md transition-all flex items-start gap-3 group"
        >
          <div class="w-10 h-10 rounded-xl bg-brand-blue-light group-hover:bg-brand-blue group-hover:text-white text-brand-blue flex items-center justify-center shrink-0 transition-colors">
            <component :is="usp.icon" class="w-5 h-5" />
          </div>
          <div>
            <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 group-hover:text-brand-blue transition-colors">
              {{ usp.title }}
            </h3>
            <p class="text-[11px] text-slate-500 mt-1 leading-snug line-clamp-2">
              {{ usp.desc }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import {
  ChevronLeft, ChevronRight, Calculator, PhoneCall,
  Clock, Palette, BadgePercent, Truck
} from 'lucide-vue-next'
import { siteSettings } from '../../data/siteSettings'

const slides = ref([
  {
    id: 1,
    title: 'Xưởng In Nhật Nguyên - Bạn Cần In Có Ngay Trong Ngày',
    image: '/banner-chinh.jpg',
    link: '/yeu-cau-bao-gia'
  }
])

const currentSlide = ref(0)
let slideTimer = null

function nextSlide() {
  currentSlide.value = (currentSlide.value + 1) % slides.value.length
}

function prevSlide() {
  currentSlide.value = (currentSlide.value - 1 + slides.value.length) % slides.value.length
}

function goToSlide(index) {
  currentSlide.value = index
}

function startSlideAutoplay() {
  if (slides.value.length > 1) {
    slideTimer = setInterval(() => {
      nextSlide()
    }, 5000)
  }
}

const rotatingWords = [
  'Name Card - Danh Thiếp Lấy Liền',
  'Tem Nhãn Decal - Bế Thành Phẩm',
  'Túi Giấy & Hộp Giấy Giá Xưởng',
  'Tờ Rơi - Flyer - Brochure Chuẩn Màu',
  'Catalogue - Hồ Sơ Năng Lực',
  'Voucher - Thẻ Tích Điểm - Phiếu Thu',
  'Standee - Băng Rôn Quảng Cáo',
  'Menu Bìa Bồi Cứng Sang Trọng'
]

const displayText = ref('')
let wordIdx = 0
let charIdx = 0
let isDeleting = false
let typeTimer = null

function typeEffect() {
  const currentWord = rotatingWords[wordIdx]

  if (isDeleting) {
    displayText.value = currentWord.substring(0, charIdx - 1)
    charIdx--
  } else {
    displayText.value = currentWord.substring(0, charIdx + 1)
    charIdx++
  }

  let delay = isDeleting ? 40 : 85

  if (!isDeleting && charIdx === currentWord.length) {
    delay = 2200
    isDeleting = true
  } else if (isDeleting && charIdx === 0) {
    isDeleting = false
    wordIdx = (wordIdx + 1) % rotatingWords.length
    delay = 400
  }

  typeTimer = setTimeout(typeEffect, delay)
}

onMounted(() => {
  typeEffect()
  startSlideAutoplay()
})

onUnmounted(() => {
  if (typeTimer) clearTimeout(typeTimer)
  if (slideTimer) clearInterval(slideTimer)
})

const uspList = [
  {
    icon: Clock,
    title: 'In Nhanh Trong Ngày',
    desc: 'Hệ thống máy KTS Fuji Xerox / Konica công suất lớn, in lấy liền trong 2H – 24H.'
  },
  {
    icon: Palette,
    title: 'Hỗ Trợ Thiết Kế',
    desc: 'Đội ngũ designer chuyên nghiệp hỗ trợ xuất file chuẩn CMYK, 300DPI hoàn toàn miễn phí.'
  },
  {
    icon: BadgePercent,
    title: 'Giá Tận Xưởng',
    desc: 'Xưởng in trực tiếp không qua trung gian, tối ưu 20% – 35% chi phí cho doanh nghiệp.'
  },
  {
    icon: Truck,
    title: 'Giao Hàng Toàn Quốc',
    desc: 'Đóng gói màng co chống ẩm, giao hỏa tốc nội thành TPHCM và chuyển phát nhanh toàn quốc.'
  }
]
</script>

