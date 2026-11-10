<template>
  <div
    v-if="isVisible"
    class="fixed inset-0 z-[100000] flex flex-col items-center justify-center bg-white transition-all duration-500 ease-out select-none"
    :class="isFadingOut ? 'opacity-0 scale-[1.02] pointer-events-none' : 'opacity-100 scale-100'"
  >
    <!-- Background Decor Accent Blobs -->
    <div class="absolute -top-32 -left-32 w-80 h-80 bg-brand-blue/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-brand-gold/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col items-center text-center px-4 max-w-sm w-full">
      <!-- Logo Container with Scanner Beam -->
      <div class="relative mb-6 p-4 rounded-3xl bg-white shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden group">
        <!-- Laser Scanner Beam -->
        <div class="absolute left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-brand-gold to-transparent animate-laser-scan shadow-[0_0_10px_#f8ac22] pointer-events-none"></div>

        <img
          src="/logo.png"
          alt="In Ấn Nhật Nguyên"
          class="h-16 sm:h-20 w-auto object-contain mx-auto filter drop-shadow-xs"
        />
      </div>

      <!-- 4 CMYK Color Ink Drops -->
      <div class="flex items-center justify-center gap-2 mb-4">
        <span class="w-2.5 h-2.5 rounded-full bg-[#00AEEF] animate-bounce shadow-xs" style="animation-delay: 0ms;" title="Cyan"></span>
        <span class="w-2.5 h-2.5 rounded-full bg-[#EC008C] animate-bounce shadow-xs" style="animation-delay: 150ms;" title="Magenta"></span>
        <span class="w-2.5 h-2.5 rounded-full bg-[#FFEA00] animate-bounce shadow-xs" style="animation-delay: 300ms;" title="Yellow"></span>
        <span class="w-2.5 h-2.5 rounded-full bg-[#00457F] animate-bounce shadow-xs" style="animation-delay: 450ms;" title="Key/Black"></span>
      </div>

      <!-- Tagline -->
      <div class="text-xs font-bold text-brand-blue tracking-wider uppercase mb-3">
        In Nhanh 24H · Giá Xưởng TPHCM
      </div>

      <!-- Progress Bar Container -->
      <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden relative shadow-inner mb-2">
        <div
          class="h-full bg-gradient-to-r from-brand-blue via-brand-gold to-amber-400 transition-all duration-150 ease-out rounded-full shadow-[0_0_8px_rgba(248,172,34,0.6)]"
          :style="{ width: `${progress}%` }"
        ></div>
      </div>

      <!-- Progress Percentage Text -->
      <div class="flex justify-between w-full text-[11px] font-semibold text-slate-400">
        <span>Đang khởi tạo xưởng in...</span>
        <span class="text-brand-gold font-bold">{{ progress }}%</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const isVisible = ref(true)
const isFadingOut = ref(false)
const progress = ref(0)

onMounted(() => {
  const duration = 750 // 750ms total load time (nhanh, êm, không gây trễ)
  const interval = 25
  const step = 100 / (duration / interval)

  const timer = setInterval(() => {
    if (progress.value < 100) {
      progress.value = Math.min(100, Math.round(progress.value + step))
    } else {
      clearInterval(timer)
      setTimeout(() => {
        isFadingOut.value = true
        setTimeout(() => {
          isVisible.value = false
        }, 450)
      }, 150)
    }
  }, interval)
})
</script>

<style scoped>
@keyframes laserScan {
  0% {
    top: -5%;
    opacity: 0;
  }
  15% {
    opacity: 1;
  }
  85% {
    opacity: 1;
  }
  100% {
    top: 105%;
    opacity: 0;
  }
}

.animate-laser-scan {
  animation: laserScan 1.2s ease-in-out infinite;
}
</style>

