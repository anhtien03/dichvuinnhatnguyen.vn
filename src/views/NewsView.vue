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

      <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <article
          v-for="post in posts"
          :key="post.id"
          class="bg-white rounded-3xl overflow-hidden border border-slate-200/90 shadow-sm hover:shadow-xl transition-all flex flex-col group"
        >
          <router-link :to="`/tin-tuc/${post.slug || post.id}`" class="aspect-[16/10] bg-slate-100 overflow-hidden relative block">
            <img
              :src="post.thumbnail || post.image"
              :alt="post.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <span class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-brand-blue text-white text-[10px] font-bold">
              {{ post.category || 'Cẩm nang in ấn' }}
            </span>
          </router-link>

          <div class="p-6 flex-1 flex flex-col justify-between">
            <div>
              <span class="text-[11px] text-slate-400 block mb-1.5">{{ formatDate(post.published_at || post.date) }}</span>
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
import { Loader2 } from 'lucide-vue-next'
import { api } from '../services/api'
import { setMetaTags } from '../composables/useSEO'

const isLoading = ref(true)
const defaultPosts = [
  {
    id: 1,
    slug: 'phan-biet-he-mau-cmyk-va-rgb',
    title: 'Phân Biệt Hệ Màu CMYK Và RGB: Vì Sao Bản In Bị Lệch Màu?',
    category: 'Kỹ thuật in',
    date: '15/10/2026',
    readTime: '4 phút',
    thumbnail: 'https://images.unsplash.com/photo-1541701494587-cb58502866ab?auto=format&fit=crop&w=800&q=80',
    excerpt: 'Hầu hết các lỗi sai màu khi in ấn xuất phát từ việc file thiết kế để hệ màu RGB (màn hình) thay vì chuyển sang CMYK (mực in). Tìm hiểu cách convert chuẩn xác.'
  },
  {
    id: 2,
    slug: 'bang-so-sanh-cac-dinh-luong-giay-in',
    title: 'Bảng So Sánh Các Định Lượng Giấy In: Couche, Fort, Bristol, Kraft',
    category: 'Vật liệu in',
    date: '12/10/2026',
    readTime: '6 phút',
    thumbnail: 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=800&q=80',
    excerpt: 'Làm sao để biết khi nào nên chọn C150, C300 hay giấy mỹ thuật? Hướng dẫn chi tiết giúp bạn chọn đúng độ dày giấy phù hợp ngân sách và đẳng cấp thương hiệu.'
  },
  {
    id: 3,
    slug: 'ky-thuat-can-mang-mo-vs-mang-bong',
    title: 'Kỹ Thuật Cán Màng Mờ vs Màng Bóng: Ưu Điểm Và Ứng Dụng Thực Tế',
    category: 'Gia công',
    date: '08/10/2026',
    readTime: '5 phút',
    thumbnail: 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?auto=format&fit=crop&w=800&q=80',
    excerpt: 'Cán màng không chỉ tăng tính thẩm mỹ mà còn bảo vệ mực in chống trầy xước và kháng nước. Khi nào nên cán mờ sang trọng, khi nào nên cán bóng bắt sáng?'
  }
]

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
      posts.value = defaultPosts
    }
  } catch (err) {
    posts.value = defaultPosts
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  setMetaTags({
    title: 'Cẩm Nang Kỹ Thuật & Kiến Thức In Ấn Chuyên Sâu',
    description: 'Tổng hợp các bài viết hướng dẫn chọn chất liệu giấy, quy chuẩn xuất file in CMYK và kinh nghiệm tiết kiệm chi phí in ấn từ Xưởng In Nhật Nguyên.'
  })
  loadPosts()
})
</script>

