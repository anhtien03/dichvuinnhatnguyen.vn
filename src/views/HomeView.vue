<template>
  <div class="space-y-0">
    <!-- Hero Banner -->
    <HeroBanner />

    <!-- Category Grid -->
    <div v-reveal>
      <CategoryGrid />
    </div>

    <!-- Category Showcase Section (Bộ Sưu Tập Ấn Phẩm Chuyên Nghiệp) -->
    <div v-reveal>
      <CategoryShowcaseSection />
    </div>

    <!-- Popular Products Section (Ấn phẩm phổ biến) -->
    <div v-reveal>
      <PopularProductsSection />
    </div>

    <!-- Tạm thời ẩn công cụ tính giá nhanh theo yêu cầu -->
    <!-- <QuickQuoteSection /> -->

    <!-- 4-Step Production Workflow -->
    <div v-reveal>
      <WorkflowSteps />
    </div>

    <!-- Printing Handbook / Articles (Cẩm nang về in ấn) -->
    <div v-reveal>
      <PrintingHandbookSection />
    </div>
  </div>
</template>

<script setup>
import HeroBanner from '../components/home/HeroBanner.vue'
import CategoryGrid from '../components/home/CategoryGrid.vue'
import PopularProductsSection from '../components/home/PopularProductsSection.vue'
import CategoryShowcaseSection from '../components/home/CategoryShowcaseSection.vue'
import QuickQuoteSection from '../components/home/QuickQuoteSection.vue'
import WorkflowSteps from '../components/home/WorkflowSteps.vue'
import PrintingHandbookSection from '../components/home/PrintingHandbookSection.vue'
</script>

