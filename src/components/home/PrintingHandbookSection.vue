<template>
  <section class="py-16 lg:py-20 bg-slate-50 border-b border-slate-100">
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
          v-for="post in displayPosts"
          :key="post.id"
          :to="'/tin-tuc/' + (post.slug || post.id)"
          class="bg-white rounded-2xl overflow-hidden border border-slate-200/90 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col group hover:-translate-y-1.5"
        >
          <!-- Thumbnail Image -->
          <div class="aspect-[16/10] bg-slate-100 overflow-hidden relative">
            <img
              :src="post.image"
              :alt="post.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              loading="lazy"
            />
            <!-- Category Tag -->
            <span class="absolute top-3.5 left-3.5 px-3 py-1 rounded-lg bg-brand-blue text-white text-xs font-bold shadow-md shadow-brand-blue/30 backdrop-blur-sm">
              {{ post.category }}
            </span>
          </div>

          <!-- Article Content -->
          <div class="p-6 flex-1 flex flex-col justify-between">
            <div>
              <!-- Meta Info -->
              <div class="flex items-center gap-3 text-xs text-slate-400 mb-2.5">
                <span class="flex items-center gap-1">
                  <Calendar class="w-3.5 h-3.5 text-slate-400" />
                  {{ post.date }}
                </span>
                <span class="inline-block w-1 h-1 rounded-full bg-slate-300"></span>
                <span class="flex items-center gap-1">
                  <Clock class="w-3.5 h-3.5 text-slate-400" />
                  {{ post.readTime }} đọc
                </span>
              </div>

              <!-- Title -->
              <h3 class="font-bold text-slate-900 text-base sm:text-lg group-hover:text-brand-blue transition-colors leading-snug line-clamp-2 mb-2.5">
                {{ post.title }}
              </h3>

              <!-- Excerpt -->
              <p class="text-xs sm:text-sm text-slate-500 line-clamp-3 leading-relaxed">
                {{ post.excerpt }}
              </p>
            </div>

            <!-- Card Footer -->
            <div class="pt-4 mt-5 border-t border-slate-100 flex items-center justify-between text-xs">
              <span class="text-slate-400 font-medium">Xưởng In Nhật Nguyên</span>
              <span class="font-bold text-brand-blue flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                <span>Đọc bài viết</span>
                <ArrowRight class="w-3.5 h-3.5" />
              </span>
            </div>
          </div>
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { BookOpen, Calendar, Clock, ArrowRight } from 'lucide-vue-next'
import { api } from '@/services/api'

const defaultPosts = [
  {
    id: 1,
    slug: 'phan-biet-he-mau-cmyk-va-rgb-vi-sao-ban-in-bi-lech-mau',
    title: 'Phân Biệt Hệ Màu CMYK Và RGB: Vì Sau Bản In Bị Lệch Màu?',
    category: 'Kỹ thuật in',
    date: '15/10/2026',
    readTime: '4 phút',
    image: 'https://images.unsplash.com/photo-1541701494587-cb58502866ab?auto=format&fit=crop&w=800&q=80',
    excerpt: 'Hầu hết các lỗi sai màu khi in ấn xuất phát từ việc file thiết kế để hệ màu RGB (màn hình) thay vì chuyển sang CMYK (mực in). Tìm hiểu cách convert chuẩn xác.'
  },
  {
    id: 2,
    slug: 'bang-so-sanh-cac-dinh-luong-giay-in-couche-fort-bristol-kraft',
    title: 'Bảng So Sánh Các Định Lượng Giấy In: Couche, Fort, Bristol, Kraft',
    category: 'Vật liệu in',
    date: '12/10/2026',
    readTime: '6 phút',
    image: 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=800&q=80',
    excerpt: 'Làm sao để biết khi nào nên chọn C150, C300 hay giấy mỹ thuật? Hướng dẫn chi tiết giúp bạn chọn đúng độ dày giấy phù hợp ngân sách và đẳng cấp thương hiệu.'
  },
  {
    id: 3,
    slug: 'ky-thuat-can-mang-mo-vs-mang-bong-uu-diem-va-ung-dung-thuc-te',
    title: 'Kỹ Thuật Cán Màng Mờ vs Màng Bóng: Ưu Điểm Và Ứng Dụng Thực Tế',
    category: 'Gia công sau in',
    date: '08/10/2026',
    readTime: '5 phút',
    image: 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?auto=format&fit=crop&w=800&q=80',
    excerpt: 'Cán màng không chỉ tăng tính thẩm mỹ mà còn bảo vệ mực in chống trầy xước và kháng nước. Khi nào nên cán mờ sang trọng, khi nào nên cán bóng bắt sáng?'
  }
]

const apiPosts = ref([])

onMounted(async () => {
  try {
    const res = await api.getPosts({ per_page: 3 })
    const list = res?.data?.data || res?.data || []
    if (Array.isArray(list) && list.length > 0) {
      apiPosts.value = list.map(p => ({
        id: p.id,
        slug: p.slug,
        title: p.title,
        category: p.category || 'Cẩm nang in',
        date: p.published_at ? new Date(p.published_at).toLocaleDateString('vi-VN') : 'Mới cập nhật',
        readTime: `${Math.max(3, Math.ceil((p.content?.length || 500) / 400))} phút`,
        image: p.thumbnail || 'https://images.unsplash.com/photo-1541701494587-cb58502866ab?auto=format&fit=crop&w=800&q=80',
        excerpt: p.excerpt || p.title
      }))
    }
  } catch {
    // fallback to defaultPosts
  }
})

const displayPosts = computed(() => {
  return apiPosts.value.length > 0 ? apiPosts.value.slice(0, 3) : defaultPosts
})
</script>
