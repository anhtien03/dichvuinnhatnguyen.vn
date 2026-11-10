<template>
  <div class="py-10 lg:py-16 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
        <router-link to="/" class="hover:text-brand-blue">Trang chủ</router-link>
        <span>&rsaquo;</span>
        <router-link to="/tin-tuc" class="hover:text-brand-blue">Cẩm nang in ấn</router-link>
        <span>&rsaquo;</span>
        <span class="text-slate-900 truncate max-w-xs font-semibold">{{ post?.title || 'Bài viết' }}</span>
      </nav>

      <!-- Loading State -->
      <div v-if="isLoading" class="py-20 text-center space-y-3">
        <Loader2 class="w-8 h-8 animate-spin mx-auto text-brand-gold" />
        <p class="text-xs text-slate-400">Đang tải nội dung bài viết...</p>
      </div>

      <!-- Post Not Found -->
      <div v-else-if="!post" class="text-center py-20 bg-white rounded-3xl p-8 border border-slate-200">
        <h2 class="text-xl font-bold text-slate-900 mb-2">Không tìm thấy bài viết</h2>
        <p class="text-xs text-slate-500 mb-6">Bài viết có thể đã bị xóa hoặc đường dẫn không chính xác.</p>
        <router-link to="/tin-tuc" class="px-5 py-2.5 rounded-xl bg-brand-blue text-white font-bold text-xs">
          ← Quay về trang cẩm nang
        </router-link>
      </div>

      <!-- Article Content Card -->
      <article v-else class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200/90 shadow-sm space-y-8">
        <!-- Header Info -->
        <header class="space-y-4">
          <div class="flex flex-wrap items-center gap-2">
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-brand-blue/10 text-brand-blue">
              {{ post.category || 'Cẩm nang in ấn' }}
            </span>
            <span class="text-xs text-slate-400">•</span>
            <span class="text-xs text-slate-500 flex items-center gap-1">
              <Calendar class="w-3.5 h-3.5 text-slate-400" />
              {{ formatDate(post.published_at || post.created_at) }}
            </span>
            <span class="text-xs text-slate-400">•</span>
            <span class="text-xs text-slate-500 font-semibold">Tác giả: Xưởng In Nhật Nguyên</span>
          </div>

          <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 leading-tight">
            {{ post.title }}
          </h1>

          <p v-if="post.excerpt" class="text-sm sm:text-base text-slate-600 font-medium leading-relaxed italic border-l-4 border-brand-gold pl-4 py-1 bg-amber-50/40 rounded-r-xl">
            {{ post.excerpt }}
          </p>
        </header>

        <!-- Featured Image -->
        <div v-if="post.thumbnail" class="aspect-[16/9] rounded-2xl overflow-hidden bg-slate-100 shadow-sm border border-slate-200/70">
          <img
            :src="post.thumbnail"
            :alt="post.title"
            class="w-full h-full object-cover"
          />
        </div>

        <!-- Body Content -->
        <div
          class="prose prose-slate max-w-none text-slate-800 text-sm sm:text-base leading-relaxed space-y-4 post-content"
          v-html="post.content || '<p>Bài viết đang cập nhật nội dung...</p>'"
        ></div>

        <!-- Call to Action Banner -->
        <div class="rounded-2xl p-6 sm:p-8 bg-gradient-to-br from-brand-blue via-blue-700 to-blue-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-6 shadow-xl">
          <div class="space-y-1">
            <h3 class="text-lg sm:text-xl font-black">Bạn cần in ấn phẩm theo yêu cầu?</h3>
            <p class="text-xs text-blue-100/90 leading-relaxed max-w-lg">
              Xưởng in Nhật Nguyên nhận in mọi quy cách, hỗ trợ duyệt file và test màu nhanh trong 15-30 phút.
            </p>
          </div>
          <router-link
            to="/yeu-cau-bao-gia"
            class="px-6 py-3 rounded-full bg-brand-gold hover:bg-amber-400 text-slate-950 font-black text-xs sm:text-sm shadow-md transition-all shrink-0 text-center"
          >
            Nhận Báo Giá Ngay →
          </router-link>
        </div>
      </article>

      <!-- Back Link -->
      <div class="text-center pt-4">
        <router-link to="/tin-tuc" class="text-xs font-bold text-brand-blue hover:underline inline-flex items-center gap-1.5">
          <ArrowLeft class="w-4 h-4" />
          <span>Xem tất cả bài viết cẩm nang khác</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { Calendar, Loader2, ArrowLeft } from 'lucide-vue-next'
import { api } from '../services/api'
import { setMetaTags } from '../composables/useSEO'

const route = useRoute()
const post = ref(null)
const isLoading = ref(true)

function formatDate(dateStr) {
  if (!dateStr) return '—'
  const d = new Date(dateStr)
  return d.toLocaleDateString('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

async function fetchPost() {
  try {
    isLoading.value = true
    const slug = route.params.slug
    const res = await api.getPostBySlug(slug)
    if (res.data) {
      post.value = res.data

      // Apply dynamic SEO
      setMetaTags({
        title: post.value.meta_title || `${post.value.title} - Cẩm Nang In Ấn`,
        description: post.value.meta_description || post.value.excerpt || 'Cẩm nang hướng dẫn kỹ thuật xuất file in và chọn chất liệu giấy tại Xưởng In Nhật Nguyên.',
        image: post.value.thumbnail || 'https://innhatnguyen.vn/logo.png',
        url: window.location.href,
        type: 'article',
        schema: {
          '@context': 'https://schema.org',
          '@type': 'Article',
          headline: post.value.title,
          description: post.value.excerpt || post.value.meta_description,
          image: post.value.thumbnail,
          datePublished: post.value.published_at || post.value.created_at,
          author: {
            '@type': 'Organization',
            name: 'Xưởng In Ấn Nhật Nguyên'
          }
        }
      })
    }
  } catch (err) {
    console.error('Failed to load post', err)
    post.value = null
  } finally {
    isLoading.value = false
  }
}

watch(() => route.params.slug, () => {
  fetchPost()
})

onMounted(() => {
  fetchPost()
})
</script>

<style>
.post-content h2 {
  font-size: 1.35rem;
  font-weight: 800;
  color: #0f172a;
  margin-top: 1.75rem;
  margin-bottom: 0.75rem;
  line-height: 1.3;
}
.post-content h3 {
  font-size: 1.15rem;
  font-weight: 700;
  color: #1e293b;
  margin-top: 1.5rem;
  margin-bottom: 0.5rem;
  line-height: 1.3;
}
.post-content p {
  margin-bottom: 1rem;
  line-height: 1.7;
}
.post-content ul {
  list-style-type: disc;
  padding-left: 1.5rem;
  margin-bottom: 1rem;
}
.post-content li {
  margin-bottom: 0.4rem;
}
.post-content blockquote {
  border-left: 4px solid #f59e0b;
  background-color: #f8fafc;
  padding: 1rem 1.25rem;
  border-radius: 0 0.75rem 0.75rem 0;
  margin: 1.5rem 0;
  font-style: italic;
}
.post-content img {
  max-width: 100%;
  border-radius: 1rem;
  margin: 1.5rem auto;
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
}
</style>

