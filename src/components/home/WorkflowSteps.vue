<template>
  <section
    class="relative overflow-hidden border-b border-slate-100 bg-white py-12 sm:py-16"
    aria-labelledby="workflow-heading"
  >
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

      <!-- HEADER -->
      <div class="mx-auto mb-9 flex max-w-5xl flex-col items-center text-center sm:mb-11">

        <h2
          id="workflow-heading"
          class="inline-block rounded-xl bg-amber-400 px-3.5 py-1.5 text-lg font-extrabold uppercase tracking-tight text-slate-900 sm:px-5 sm:py-2 sm:text-2xl"
        >
          Quy trình đặt in tại In Nhanh Nhật Nguyên
        </h2>

        <p class="mt-3 max-w-3xl text-sm font-medium leading-relaxed text-slate-600 sm:text-base">
          Từng bước đều rõ ràng, minh bạch và nhanh gọn giúp bạn
          đặt in dễ dàng, tiện lợi và tiết kiệm thời gian.
        </p>

        <!-- PROGRESS DOTS -->
        <div
          class="mt-5 flex items-center justify-center gap-2"
          role="group"
          aria-label="Chọn bước quy trình"
        >
          <button
            v-for="(st, idx) in workflowSteps"
            :key="'dot-' + st.step"
            type="button"
            @click="setStep(idx)"
            class="group flex min-h-8 min-w-8 items-center justify-center rounded-full outline-none focus-visible:ring-2 focus-visible:ring-amber-400 focus-visible:ring-offset-2 cursor-pointer"
            :aria-label="`Chuyển đến ${st.title}`"
            :aria-current="activeStep === idx ? 'step' : undefined"
          >
            <span
              class="block h-2 rounded-full transition-[width,background-color] duration-300 ease-out"
              :class="
                activeStep === idx
                  ? 'w-8 bg-amber-400 shadow-sm shadow-amber-400/30'
                  : 'w-2 bg-slate-200 group-hover:bg-amber-300'
              "
            ></span>
          </button>
        </div>
      </div>

      <!-- WORKFLOW CARDS -->
      <div
        class="relative"
        @mouseenter="setHovered(true)"
        @mouseleave="setHovered(false)"
        @focusin="setFocused(true)"
        @focusout="handleFocusOut"
      >

        <div
          class="relative z-10 grid grid-cols-2 items-stretch gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-6 lg:gap-5"
        >

          <!-- CARD -->
          <div
            v-for="(st, idx) in workflowSteps"
            :key="st.step"
            class="group relative flex h-full min-w-0 flex-col"
            @mouseenter="setStep(idx)"
          >

            <button
              type="button"
              @click="setStep(idx)"
              @focus="setStep(idx)"
              :aria-pressed="activeStep === idx"
              :aria-label="`${st.title}. Chọn bước này`"
              class="relative flex h-full w-full flex-col items-center overflow-hidden rounded-2xl border p-2.5 pt-10 text-center outline-none transition-colors duration-300 ease-out focus-visible:ring-2 focus-visible:ring-amber-400 focus-visible:ring-offset-2 sm:p-3 sm:pt-10 lg:p-3.5 lg:pt-10 cursor-pointer"
              :class="
                activeStep === idx
                  ? 'border-amber-400 bg-amber-50/50 shadow-md shadow-amber-900/5'
                  : 'border-slate-200 bg-white hover:border-amber-200 hover:shadow-md hover:shadow-slate-900/5'
              "
            >

              <!-- STEP BADGE -->
              <span
                class="absolute left-1/2 top-2 z-10 -translate-x-1/2 whitespace-nowrap rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wider transition-colors duration-300 sm:text-xs"
                :class="
                  activeStep === idx
                    ? 'bg-amber-400 text-slate-900'
                    : 'bg-slate-100 text-slate-500 group-hover:bg-amber-50 group-hover:text-amber-700'
                "
              >
                Bước {{ st.step }}
              </span>

              <!-- IMAGE -->
              <div
                class="flex h-[180px] w-full items-center justify-center overflow-hidden sm:h-[195px] lg:h-[205px]"
              >
                <img
                  :src="st.image"
                  :alt="st.title"
                  decoding="async"
                  class="h-full w-auto max-w-full object-contain transition-transform duration-500 ease-out motion-reduce:transition-none"
                  :class="
                    activeStep === idx
                      ? 'scale-[1.025]'
                      : 'scale-100 group-hover:scale-[1.02]'
                  "
                />
              </div>

              <!-- ACTIVE INDICATOR -->
              <span
                class="absolute bottom-0 left-5 right-5 h-[3px] rounded-t-full bg-amber-400 transition-opacity duration-300"
                :class="activeStep === idx ? 'opacity-100' : 'opacity-0'"
              ></span>

            </button>

            <!-- NEXT ARROW -->
            <div
              v-if="idx < workflowSteps.length - 1"
              class="pointer-events-none absolute -right-[15px] top-1/2 z-20 hidden h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full border bg-white shadow-sm transition-colors duration-300 lg:flex"
              :class="
                activeStep === idx
                  ? 'border-amber-400 text-amber-500'
                  : 'border-slate-200 text-slate-300'
              "
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="h-4 w-4"
                :class="activeStep === idx ? 'animate-move-forward' : ''"
              >
                <path d="m9 18 6-6-6-6" />
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

// ========================================
// CONFIGURATION
// ========================================

const AUTOPLAY_DELAY = 4000

// ========================================
// STATE
// ========================================

const activeStep = ref(0)

const isHovered = ref(false)
const isFocused = ref(false)
const isReducedMotion = ref(false)

let timer = null
let motionQuery = null

// ========================================
// WORKFLOW DATA
// ========================================

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

// ========================================
// AUTOPLAY
// ========================================

const stopAutoPlay = () => {
  if (timer !== null) {
    clearInterval(timer)
    timer = null
  }
}

const startAutoPlay = () => {
  stopAutoPlay()

  if (isReducedMotion.value) return
  if (isHovered.value || isFocused.value) return
  if (document.visibilityState !== 'visible') return

  timer = setInterval(() => {
    activeStep.value =
      (activeStep.value + 1) % workflowSteps.length
  }, AUTOPLAY_DELAY)
}

// ========================================
// INTERACTION
// ========================================

const setStep = (idx) => {
  if (idx < 0 || idx >= workflowSteps.length) return
  activeStep.value = idx
}

const setHovered = (value) => {
  isHovered.value = value
  if (value) {
    stopAutoPlay()
  } else {
    startAutoPlay()
  }
}

const setFocused = (value) => {
  isFocused.value = value
  if (value) {
    stopAutoPlay()
  } else {
    startAutoPlay()
  }
}

const handleFocusOut = (event) => {
  const nextTarget = event.relatedTarget
  if (!event.currentTarget.contains(nextTarget)) {
    setFocused(false)
  }
}

// ========================================
// BROWSER VISIBILITY
// ========================================

const handleVisibilityChange = () => {
  if (document.visibilityState === 'hidden') {
    stopAutoPlay()
  } else {
    startAutoPlay()
  }
}

// ========================================
// REDUCED MOTION
// ========================================

const handleMotionChange = (event) => {
  isReducedMotion.value = event.matches
  if (event.matches) {
    stopAutoPlay()
  } else {
    startAutoPlay()
  }
}

// ========================================
// LIFECYCLE
// ========================================

onMounted(() => {
  motionQuery = window.matchMedia(
    '(prefers-reduced-motion: reduce)'
  )

  isReducedMotion.value = motionQuery.matches

  if (motionQuery.addEventListener) {
    motionQuery.addEventListener('change', handleMotionChange)
  } else {
    motionQuery.addListener(handleMotionChange)
  }

  document.addEventListener(
    'visibilitychange',
    handleVisibilityChange
  )

  startAutoPlay()
})

onBeforeUnmount(() => {
  stopAutoPlay()

  document.removeEventListener(
    'visibilitychange',
    handleVisibilityChange
  )

  if (motionQuery) {
    if (motionQuery.removeEventListener) {
      motionQuery.removeEventListener(
        'change',
        handleMotionChange
      )
    } else {
      motionQuery.removeListener(handleMotionChange)
    }
  }
})
</script>

<style scoped>
@keyframes moveForward {
  0%,
  100% {
    transform: translateX(0);
  }

  50% {
    transform: translateX(3px);
  }
}

.animate-move-forward {
  animation: moveForward 1s ease-in-out infinite;
}

@media (prefers-reduced-motion: reduce) {
  .animate-move-forward {
    animation: none !important;
  }
}
</style>
