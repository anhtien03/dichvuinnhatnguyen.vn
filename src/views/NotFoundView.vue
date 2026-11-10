<script setup>
import { onMounted } from 'vue'
import { setMetaTags } from '@/composables/useSEO'

onMounted(() => {
  setMetaTags({
    title: '404 - Không Tìm Thấy Trang | In Ấn Nhật Nguyên',
    description: 'Trang này không tồn tại hoặc đã bị xóa.',
    robots: 'noindex, nofollow'
  })
})
</script>

<template>
  <div class="min-h-[70vh] flex items-center justify-center py-16 px-4 bg-slate-50">
    <div class="text-center max-w-md bg-white p-8 sm:p-12 rounded-3xl border border-slate-200 shadow-xl space-y-6">
      <div class="text-7xl font-black text-brand-gold">404</div>
      <h1 class="text-2xl font-black text-slate-900">Trang Này Không Tồn Tại</h1>
      <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
        Trang bạn đang tìm kiếm có thể đã bị đổi tên, xóa hoặc tạm thời không khả dụng.
      </p>
      <div class="flex flex-col sm:flex-row justify-center gap-3">
        <router-link
          to="/"
          class="px-6 py-3 rounded-xl bg-brand-blue hover:bg-brand-blue-dark text-white font-bold text-xs shadow-md transition-colors"
        >
          Trở về trang chủ
        </router-link>
        <router-link
          to="/san-pham"
          class="px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors"
        >
          Xem các ấn phẩm in
        </router-link>
      </div>
    </div>
  </div>
</template>

