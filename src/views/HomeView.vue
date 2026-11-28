<template>
  <div class="space-y-0">
    <!-- Hero Banner -->
    <HeroBanner />

    <!-- Category Grid -->
    <CategoryGrid />

    <!-- Category Showcase Section (Bộ Sưu Tập Ấn Phẩm Chuyên Nghiệp) -->
    <div class="cv-auto">
      <CategoryShowcaseSection />
    </div>

    <!-- Popular Products Section (Ấn phẩm phổ biến) -->
    <div class="cv-auto">
      <PopularProductsSection />
    </div>

    <!-- Tạm thời ẩn công cụ tính giá nhanh theo yêu cầu -->
    <!-- <QuickQuoteSection /> -->

    <!-- 4-Step Production Workflow -->
    <div class="cv-auto">
      <WorkflowSteps />
    </div>

    <!-- Printing Handbook / Articles (Cẩm nang về in ấn) -->
    <div class="cv-auto">
      <PrintingHandbookSection />
    </div>
  </div>
</template>

<style scoped>
.cv-auto {
  content-visibility: auto;
  contain-intrinsic-size: 1px 650px;
}
</style>

<script setup>
import { onMounted } from 'vue'
import { setMetaTags } from '../composables/useSEO'
import HeroBanner from '../components/home/HeroBanner.vue'
import CategoryGrid from '../components/home/CategoryGrid.vue'
import PopularProductsSection from '../components/home/PopularProductsSection.vue'
import CategoryShowcaseSection from '../components/home/CategoryShowcaseSection.vue'
import QuickQuoteSection from '../components/home/QuickQuoteSection.vue'
import WorkflowSteps from '../components/home/WorkflowSteps.vue'
import PrintingHandbookSection from '../components/home/PrintingHandbookSection.vue'

onMounted(() => {
  setMetaTags({
    title: 'Xưởng In Ấn Nhật Nguyên - In Nhanh KTS & In Offset Giá Gốc TP.HCM',
    description: 'Xưởng In Ấn Nhật Nguyên tại Quận 12, TP.HCM chuyên in Offset & Kỹ thuật số lấy liền: hộp giấy, túi giấy, tem nhãn decal, namecard, catalogue, tờ rơi giá gốc tận xưởng.',
    keywords: 'xưởng in nhật nguyên, in nhanh tphcm, in offset quận 12, in hộp giấy, in túi giấy, in tem nhãn decal, in namecard',
    url: 'https://dichvuinnhatnguyen.vn/',
    schema: {
      '@context': 'https://schema.org',
      '@graph': [
        {
          '@type': ['LocalBusiness', 'PrintingService'],
          name: 'Xưởng In Ấn Nhật Nguyên',
          url: 'https://dichvuinnhatnguyen.vn/',
          image: 'https://dichvuinnhatnguyen.vn/og-banner.jpg',
          logo: 'https://dichvuinnhatnguyen.vn/logo.png',
          telephone: '0359005469',
          email: 'nhatnguyenprinting@gmail.com',
          address: {
            '@type': 'PostalAddress',
            streetAddress: 'Đông Hưng Thuận 40, Phường Đông Hưng Thuận',
            addressLocality: 'Quận 12',
            addressRegion: 'TP. Hồ Chí Minh',
            addressCountry: 'VN'
          },
          openingHours: 'Mo-Su 08:00-20:00',
          priceRange: '$$'
        },
        {
          '@type': 'WebSite',
          name: 'Xưởng In Ấn Nhật Nguyên',
          url: 'https://dichvuinnhatnguyen.vn/'
        }
      ]
    }
  })
})
</script>

