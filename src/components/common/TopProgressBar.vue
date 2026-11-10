<template>
  <div
    v-show="isNavLoading || navProgress > 0"
    class="fixed top-0 left-0 right-0 z-[99999] pointer-events-none transition-opacity duration-300"
    :class="isNavLoading ? 'opacity-100' : 'opacity-0'"
  >
    <div
      class="h-[3px] bg-gradient-to-r from-brand-blue via-brand-gold to-amber-300 transition-all duration-200 ease-out shadow-[0_0_12px_rgba(248,172,34,0.9)]"
      :style="{ width: `${navProgress}%` }"
    ></div>
  </div>
</template>

<script setup>
import { usePageLoader } from '../../composables/usePageLoader'

const { isNavLoading, navProgress } = usePageLoader()
</script>

