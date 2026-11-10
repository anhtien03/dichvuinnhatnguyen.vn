<template>
  <section class="py-12 sm:py-16 bg-white border-b border-slate-100 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Tiêu đề Banner -->
      <div class="text-center max-w-5xl mx-auto flex flex-col items-center mb-8 sm:mb-10">
        <img
          src="/quy-trinh/tieu-de.webp"
          alt="Quy trình đặt in tại In Nhanh Nhật Nguyên"
          class="w-full max-w-3xl sm:max-w-4xl lg:max-w-5xl h-auto object-contain mx-auto"
          loading="lazy"
        />

        <!-- Thanh chỉ báo bước chuyển tiếp (Progress Dots) -->
        <div class="mt-4 flex items-center justify-center gap-2">
          <button
            v-for="(st, idx) in workflowSteps"
            :key="'dot-' + idx"
            @click="setStep(idx)"
            class="group p-1 focus:outline-none"
            :aria-label="st.title"
          >
            <span
              class="h-2 rounded-full transition-all duration-500 block"
              :class="activeStep === idx 
                ? 'w-8 bg-brand-gold shadow-sm shadow-brand-gold/50' 
                : 'w-2 bg-slate-200 group-hover:bg-slate-300'"
            ></span>
          </button>
        </div>
      </div>

      <!-- 6 Tấm Quy Trình Đặt In kèm Animation Chuyển Tới Liên Tục -->
      <div 
        class="relative"
        @mouseenter="pauseAutoPlay"
        @mouseleave="resumeAutoPlay"
      >
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4 lg:gap-5 items-stretch relative z-10">
          <div
            v-for="(st, idx) in workflowSteps"
            :key="idx"
            class="relative flex flex-col h-full group"
            @mouseenter="setStep(idx)"
          >
            <!-- Card từng bước -->
            <div
              class="h-full rounded-2xl p-2 sm:p-3.5 border transition-all duration-500 flex flex-col items-center justify-center text-center cursor-pointer transform-gpu relative overflow-hidden"
              :class="activeStep === idx 
                ? 'bg-gradient-to-b from-amber-50/50 via-white to-white border-brand-gold ring-2 ring-brand-gold/40 shadow-xl shadow-brand-gold/20 -translate-y-2' 
                : 'bg-white border-slate-200/80 hover:border-slate-300 hover:shadow-md hover:-translate-y-1 opacity-85 hover:opacity-100'"
            >
              <!-- Tia sáng quét qua (Shimmer Sweep Effect) khi active -->
              <div 
                v-if="activeStep === idx"
                class="absolute inset-0 -translate-x-full animate-shimmer-sweep bg-gradient-to-r from-transparent via-white/60 to-transparent pointer-events-none"
              ></div>

              <!-- Badge bước -->
              <div 
                class="absolute top-2 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider transition-all duration-300 z-10"
                :class="activeStep === idx 
                  ? 'bg-brand-gold text-slate-900 shadow-xs scale-105' 
                  : 'bg-slate-100 text-slate-400 group-hover:text-slate-600'"
              >
                Bước 0{{ idx + 1 }}
              </div>

              <!-- Khung ảnh cố định chiều cao đồng bộ chuẩn 1:1 -->
              <div class="w-full h-[185px] sm:h-[200px] lg:h-[205px] flex items-center justify-center pt-3">
                <img
                  :src="st.image"
                  :alt="st.title"
                  class="h-full w-auto max-w-full object-contain mx-auto transition-all duration-500 filter"
                  :class="activeStep === idx ? 'scale-108 drop-shadow-md' : 'scale-100 drop-shadow-xs'"
                  loading="lazy"
                />
              </div>

              <!-- Vệt sáng chân card (Active Bottom Glow Bar) -->
              <div 
                class="absolute bottom-0 left-0 right-0 h-1 transition-all duration-500"
                :class="activeStep === idx ? 'bg-gradient-to-r from-brand-gold/20 via-brand-gold to-brand-gold/20 opacity-100' : 'opacity-0'"
              ></div>
            </div>

            <!-- Mũi tên động "chuyển tới" bước kế tiếp (Hiển thị từ bước 1 -> 5 trên desktop) -->
            <div 
              v-if="idx < workflowSteps.length - 1"
              class="hidden lg:flex absolute -right-3.5 top-1/2 -translate-y-1/2 z-20 pointer-events-none items-center justify-center w-7 h-7 rounded-full bg-white border shadow-xs transition-all duration-500"
              :class="activeStep === idx 
                ? 'border-brand-gold text-brand-gold shadow-md shadow-brand-gold/30 translate-x-1 scale-110' 
                : 'border-slate-200 text-slate-300'"
            >
              <svg 
                xmlns="http://www.w3.org/2000/svg" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2.5" 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                class="w-3.5 h-3.5 transition-transform duration-300"
                :class="activeStep === idx ? 'animate-move-forward' : ''"
              >
                <path d="m9 18 6-6-6-6"/>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

const activeStep = ref(0)
let timer = null
const isPaused = ref(false)

const workflowSteps = [
  {
    step: '01',
    title: '1. Tiếp Nhận Yêu Cầu',
    image: '/quy-trinh/buoc-1.webp'
  },
  {
    step: '02',
    title: '2. Tư Vấn & Báo Giá',
    image: '/quy-trinh/buoc-2.webp'
  },
  {
    step: '03',
    title: '3. Thiết Kế & Duyệt File',
    image: '/quy-trinh/buoc-3.webp'
  },
  {
    step: '04',
    title: '4. Tiến Hành In Ấn',
    image: '/quy-trinh/buoc-4.webp'
  },
  {
    step: '05',
    title: '5. Hỗ Trợ Gửi Giao Hàng',
    image: '/quy-trinh/buoc-5.webp'
  },
  {
    step: '06',
    title: '6. Hỗ Trợ Sau In',
    image: '/quy-trinh/buoc-6.webp'
  }
]

const startAutoPlay = () => {
  stopAutoPlay()
  timer = setInterval(() => {
    if (!isPaused.value) {
      activeStep.value = (activeStep.value + 1) % workflowSteps.length
    }
  }, 2200)
}

const stopAutoPlay = () => {
  if (timer) {
    clearInterval(timer)
    timer = null
  }
}

const pauseAutoPlay = () => {
  isPaused.value = true
}

const resumeAutoPlay = () => {
  isPaused.value = false
}

const setStep = (idx) => {
  activeStep.value = idx
}

onMounted(() => {
  startAutoPlay()
})

onBeforeUnmount(() => {
  stopAutoPlay()
})
</script>

<style scoped>
@keyframes moveForward {
  0%, 100% {
    transform: translateX(0);
  }
  50% {
    transform: translateX(3px);
  }
}

.animate-move-forward {
  animation: moveForward 0.9s ease-in-out infinite;
}

@keyframes shimmerSweep {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(200%);
  }
}

.animate-shimmer-sweep {
  animation: shimmerSweep 1.2s ease-out forwards;
}
</style>

