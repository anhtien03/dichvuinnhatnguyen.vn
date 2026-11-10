<template>
  <!-- Scroll Reading Progress Bar on Top of Page -->
  <div class="fixed top-0 left-0 right-0 h-[3px] z-[99999] pointer-events-none bg-transparent">
    <div
      class="h-full bg-gradient-to-r from-brand-blue via-brand-gold to-amber-300 transition-all duration-75 ease-out shadow-[0_0_10px_rgba(248,172,34,0.8)]"
      :style="{ width: `${scrollPercent}%` }"
    ></div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

const scrollPercent = ref(0)

const updateScroll = () => {
  const scrollTop = window.scrollY || document.documentElement.scrollTop
  const docHeight = document.documentElement.scrollHeight - window.innerHeight
  if (docHeight > 0) {
    scrollPercent.value = Math.min(100, Math.max(0, (scrollTop / docHeight) * 100))
  } else {
    scrollPercent.value = 0
  }
}

onMounted(() => {
  window.addEventListener('scroll', updateScroll, { passive: true })
  updateScroll()
})

onBeforeUnmount(() => {
  window.removeEventListener('scroll', updateScroll)
})
</script>

