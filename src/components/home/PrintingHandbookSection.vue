<template>
  <section v-if="posts.length > 0" class="py-16 lg:py-20 bg-slate-50 border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section Header -->
      <div class="flex flex-col md:flex-row md:items-end md:justify-between mb-10 gap-4">
        <div>
          <span class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-gold uppercase tracking-wider mb-2">
            <BookOpen class="w-4 h-4 text-brand-gold" />
            CẨM NANG VỀ IN ẤN
          </span>
          <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
            Kiến Thức & Kinh Nghiệm In Ấn Hữu Ích
          </h2>
          <p class="text-sm text-slate-500 mt-2 max-w-2xl">
            Tổng hợp hướng dẫn chọn chất liệu giấy, quy chuẩn xuất file in CMYK chuẩn màu và bí quyết tiết kiệm chi phí in ấn tối đa cho doanh nghiệp.
          </p>
        </div>

        <router-link
          to="/tin-tuc"
          class="inline-flex items-center gap-2 self-start md:self-auto px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-bold text-brand-blue hover:text-white hover:bg-brand-blue hover:border-brand-blue shadow-sm transition-all group shrink-0"
        >
          <span>Xem tất cả bài viết</span>
          <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
        </router-link>
      </div>

      <!-- 3 Featured Articles Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
        <router-link
          v-for="post in posts"
          :key="post.id"
          :to="'/tin-tuc/' + (post.slug || post.id)"
          class="bg-white rounded-2xl overflow-hidden border border-slate-200/90 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col group hover:-translate-y-1.5"
        >
          <!-- Thumbnail Image -->
          <div class="aspect-[16/10] bg-slate-100 overflow-hidden relative">
            <img
              :src="post.thumbnail || '/logo.png'"
              :alt="post.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              loading="lazy"
            />
            <!-- Category Tag -->
            <span class="absolute top-3.5 left-3.5 px-3 py-1 rounded-lg bg-brand-blue text-white text-xs font-bold shadow-md shadow-brand-blue/30 backdrop-blur-sm">
              {{ post.category || 'Cẩm nang in ấn' }}
            </span>
          </div>

          <!-- Article Content -->
          <div class="p-6 flex-1 flex flex-col justify-between">
            <div>
              <div class="flex items-center gap-3 text-xs text-slate-400 mb-2.5">
                <span class="flex items-center gap-1">
                  <Calendar class="w-3.5 h-3.5 text-slate-400" />
                  {{ formatDate(post.published_at || post.created_at) }}
                </span>
              </div>
              <h3 class="text-base font-bold text-slate-900 group-hover:text-brand-blue transition-colors line-clamp-2 leading-snug">
                {{ post.title }}
              </h3>
              <p class="text-xs text-slate-500 mt-2 line-clamp-3 leading-relaxed">
                {{ post.excerpt }}
              </p>
            </div>

            <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
              <span class="text-xs font-bold text-brand-blue group-hover:text-brand-gold transition-colors inline-flex items-center gap-1">
                <span>Đọc bài viết</span>
                <ArrowRight class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
              </span>
            </div>
          </div>
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { BookOpen, ArrowRight, Calendar } from 'lucide-vue-next'
import { api } from '../../services/api'

const posts = ref([])

function formatDate(dateStr) {
  if (!dateStr) return 'Mới đây'
  const d = new Date(dateStr)
  return d.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

onMounted(async () => {
  try {
    const res = await api.getPosts()
    if (res?.data?.length) {
      posts.value = res.data.slice(0, 3)
    }
  } catch (err) {
    posts.value = []
  }
})
</script>
