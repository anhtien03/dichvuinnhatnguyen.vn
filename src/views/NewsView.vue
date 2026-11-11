<template>
  <div class="py-10 lg:py-16 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-2xl mx-auto mb-12">
        <span class="text-xs font-bold text-brand-gold uppercase tracking-wider block mb-1">
          KIẾN THỨC NGHỀ IN
        </span>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">
          Cẩm Nang Kỹ Thuật & Xuất Bản In Ấn
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-2">
          Tổng hợp các bài viết hướng dẫn chọn chất liệu giấy, quy chuẩn xuất file in CMYK và kinh nghiệm tiết kiệm chi phí in ấn cho doanh nghiệp.
        </p>
      </div>

      <!-- Articles Grid -->
      <div v-if="isLoading" class="py-20 text-center space-y-3">
        <Loader2 class="w-8 h-8 animate-spin mx-auto text-brand-gold" />
        <p class="text-xs text-slate-400">Đang tải danh sách bài viết...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="posts.length === 0" class="text-center py-20 bg-white rounded-3xl p-8 border border-slate-200">
        <BookOpen class="w-12 h-12 text-slate-300 mx-auto mb-3" />
        <h3 class="text-lg font-bold text-slate-800">Chưa có bài viết nào</h3>
        <p class="text-xs text-slate-500 mt-1">Các bài viết cẩm nang in ấn hữu ích sẽ sớm được cập nhật tại đây.</p>
        <div class="mt-6">
          <router-link to="/" class="px-5 py-2.5 rounded-xl bg-brand-blue text-white font-bold text-xs">
            Quay về trang chủ
          </router-link>
        </div>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <article
          v-for="post in posts"
          :key="post.id"
          class="bg-white rounded-3xl overflow-hidden border border-slate-200/90 shadow-sm hover:shadow-xl transition-all flex flex-col group"
        >
          <router-link :to="`/tin-tuc/${post.slug || post.id}`" class="aspect-[16/10] bg-slate-100 overflow-hidden relative block">
            <img
              :src="post.thumbnail || post.image || '/logo.png'"
              :alt="post.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <span class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-brand-blue text-white text-[10px] font-bold">
              {{ post.category || 'Cẩm nang in ấn' }}
            </span>
          </router-link>

          <div class="p-6 flex-1 flex flex-col justify-between">
            <div>
              <span class="text-[11px] text-slate-400 block mb-1.5">{{ formatDate(post.published_at || post.created_at) }}</span>
              <router-link
                :to="`/tin-tuc/${post.slug || post.id}`"
                class="font-bold text-slate-900 text-base group-hover:text-brand-blue transition-colors leading-snug line-clamp-2"
              >
                {{ post.title }}
              </router-link>
              <p class="text-xs text-slate-500 mt-2 line-clamp-3 leading-relaxed">
                {{ post.excerpt }}
              </p>
            </div>

            <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs">
              <span class="text-slate-400">{{ post.readTime || '5 phút' }} đọc</span>
              <router-link
                :to="`/tin-tuc/${post.slug || post.id}`"
                class="font-bold text-brand-blue group-hover:underline flex items-center gap-1"
              >
                Đọc bài viết &rarr;
              </router-link>
            </div>
          </div>
        </article>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Loader2, BookOpen } from 'lucide-vue-next'
import { api } from '../services/api'
import { setMetaTags } from '../composables/useSEO'

const isLoading = ref(true)
const posts = ref([])

function formatDate(dateStr) {
  if (!dateStr) return '—'
  if (dateStr.includes('/')) return dateStr
  const d = new Date(dateStr)
  return d.toLocaleDateString('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

async function loadPosts() {
  try {
    isLoading.value = true
    const res = await api.getPosts()
    if (res.data && res.data.length > 0) {
      posts.value = res.data
    } else {
      posts.value = []
    }
  } catch (err) {
    posts.value = []
  } finally {
    isLoading.value = false
  }

  setMetaTags({
    title: 'Cẩm Nang Kỹ Thuật & Xuất Bản In Ấn | Xưởng In Nhật Nguyên',
    description: 'Chuyên mục chia sẻ kinh nghiệm in ấn thực chiến, hướng dẫn xuất file PDF chuẩn CMYK, mẹo chọn giấy in ấn tiết kiệm chi phí cho doanh nghiệp.',
    url: 'https://dichvuinnhatnguyen.vn/tin-tuc'
  })
}

onMounted(() => {
  loadPosts()
})
</script>
