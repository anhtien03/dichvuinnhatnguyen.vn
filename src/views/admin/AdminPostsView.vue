<template>
  <div class="space-y-6">
    <!-- Top Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
          Quản Lý Bài Viết & Cẩm Nang
        </h1>
        <p class="text-xs text-slate-400 mt-1">
          Đăng bài viết hướng dẫn chọn chất liệu giấy, quy chuẩn xuất file in CMYK và tin tức chuyên ngành.
        </p>
      </div>

      <div class="flex items-center gap-3">
        <router-link
          to="/he-thong-admin/posts/create"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-gold to-amber-500 hover:from-amber-400 hover:to-brand-gold text-slate-950 font-black text-xs shadow-md transition-all transform hover:-translate-y-0.5 active:translate-y-0 cursor-pointer"
        >
          <Plus class="w-4 h-4" />
          <span>Viết Bài Mới</span>
        </router-link>
      </div>
    </div>

    <!-- Notification Toast -->
    <div
      v-if="toastMessage"
      class="p-4 rounded-xl text-xs font-semibold flex items-center justify-between border shadow-sm transition-all"
      :class="toastType === 'success' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-red-500/10 border-red-500/30 text-red-400'"
    >
      <div class="flex items-center gap-2">
        <CheckCircle2 v-if="toastType === 'success'" class="w-4 h-4 shrink-0" />
        <AlertCircle v-else class="w-4 h-4 shrink-0" />
        <span>{{ toastMessage }}</span>
      </div>
      <button @click="toastMessage = ''" class="text-slate-400 hover:text-white">
        <X class="w-4 h-4" />
      </button>
    </div>

    <!-- Search & Filters Bar -->
    <div
      class="p-4 rounded-2xl border flex flex-col md:flex-row md:items-center justify-between gap-3"
      :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
    >
      <!-- Search Box -->
      <div class="relative flex-1 max-w-md">
        <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Tìm bài viết theo tiêu đề, tóm tắt..."
          class="w-full pl-10 pr-4 py-2 rounded-xl border text-xs focus:outline-none focus:border-brand-gold"
          :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-slate-50 border-slate-200 text-slate-900 placeholder-slate-400'"
        />
      </div>

      <!-- Filters Row -->
      <div class="flex flex-wrap items-center gap-2.5">
        <!-- Category Filter -->
        <select
          v-model="selectedCategory"
          class="px-3 py-2 rounded-xl border text-xs font-semibold focus:outline-none focus:border-brand-gold cursor-pointer"
          :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
        >
          <option value="all">Tất Cả Chuyên Mục</option>
          <option v-for="cat in uniqueCategories" :key="cat" :value="cat">{{ cat }}</option>
        </select>

        <!-- Status Filter -->
        <select
          v-model="selectedStatus"
          class="px-3 py-2 rounded-xl border text-xs font-semibold focus:outline-none focus:border-brand-gold cursor-pointer"
          :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
        >
          <option value="all">Tất Cả Trạng Thái</option>
          <option value="published">Đã Xuất Bản</option>
          <option value="draft">Bản Nháp</option>
        </select>

        <!-- Reset Button -->
        <button
          v-if="searchQuery || selectedCategory !== 'all' || selectedStatus !== 'all'"
          @click="resetFilters"
          class="p-2 rounded-xl text-slate-400 hover:text-white transition-colors"
          :class="isDarkMode ? 'hover:bg-[#2E3A47]' : 'hover:bg-slate-100 hover:text-slate-900'"
          title="Đặt lại bộ lọc"
        >
          <RotateCcw class="w-4 h-4" />
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="py-20 text-center space-y-3">
      <Loader2 class="w-8 h-8 animate-spin mx-auto text-brand-gold" />
      <p class="text-xs text-slate-400">Đang tải danh sách bài viết...</p>
    </div>

    <!-- Empty State -->
    <div
      v-else-if="filteredPosts.length === 0"
      class="p-12 text-center rounded-2xl border"
      :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
    >
      <Newspaper class="w-12 h-12 mx-auto text-slate-500 mb-3" />
      <h3 class="text-sm font-bold" :class="isDarkMode ? 'text-white' : 'text-slate-800'">
        Không tìm thấy bài viết nào
      </h3>
      <p class="text-xs text-slate-400 mt-1 mb-4">
        Hãy bắt đầu đăng bài viết hướng dẫn hoặc cẩm nang đầu tiên để thu hút khách hàng từ Google.
      </p>
      <router-link
        to="/he-thong-admin/posts/create"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-gold text-slate-950 font-bold text-xs"
      >
        <Plus class="w-4 h-4" />
        <span>Viết Bài Ngay</span>
      </router-link>
    </div>

    <!-- Posts Table -->
    <div
      v-else
      class="rounded-2xl border overflow-hidden shadow-sm"
      :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
    >
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
          <thead>
            <tr
              class="border-b text-[11px] font-bold uppercase tracking-wider"
              :class="isDarkMode ? 'border-[#2E3A47] text-slate-400 bg-[#1A222C]' : 'border-slate-100 text-slate-500 bg-slate-50/50'"
            >
              <th class="py-3.5 px-4 w-16 text-center">Ảnh</th>
              <th class="py-3.5 px-4">Tiêu Đề & Slug</th>
              <th class="py-3.5 px-4">Chuyên Mục</th>
              <th class="py-3.5 px-4">Ngày Đăng</th>
              <th class="py-3.5 px-4 text-center">Trạng Thái</th>
              <th class="py-3.5 px-4 text-right">Thao Tác</th>
            </tr>
          </thead>
          <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
            <tr
              v-for="post in filteredPosts"
              :key="post.id"
              class="transition-colors group"
              :class="isDarkMode ? 'hover:bg-[#24303F]/60' : 'hover:bg-slate-50/80'"
            >
              <!-- Thumbnail -->
              <td class="py-3 px-4 text-center">
                <div class="w-14 h-10 rounded-lg overflow-hidden bg-slate-800 mx-auto border border-slate-200/50 dark:border-slate-700">
                  <img
                    v-if="post.thumbnail"
                    :src="post.thumbnail"
                    :alt="post.title"
                    class="w-full h-full object-cover"
                    @error="(e) => e.target.style.display = 'none'"
                  />
                  <div v-else class="w-full h-full flex items-center justify-center text-slate-500">
                    <ImageIcon class="w-4 h-4" />
                  </div>
                </div>
              </td>

              <!-- Title & Slug -->
              <td class="py-3 px-4 max-w-md">
                <router-link
                  :to="`/he-thong-admin/posts/${post.id}/edit`"
                  class="font-bold text-sm leading-snug hover:text-brand-gold transition-colors line-clamp-1"
                  :class="isDarkMode ? 'text-white' : 'text-slate-900'"
                >
                  {{ post.title }}
                </router-link>
                <div class="flex items-center gap-2 mt-1">
                  <span class="text-[11px] font-mono text-slate-400 truncate max-w-xs">
                    /tin-tuc/{{ post.slug }}
                  </span>
                  <a
                    :href="`/tin-tuc/${post.slug}`"
                    target="_blank"
                    class="text-brand-gold hover:underline text-[10px] inline-flex items-center gap-0.5"
                    title="Xem bài viết trên website"
                  >
                    <span>Xem</span>
                    <ExternalLink class="w-3 h-3" />
                  </a>
                </div>
              </td>

              <!-- Category -->
              <td class="py-3 px-4 whitespace-nowrap">
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-brand-blue/10 text-brand-blue">
                  {{ post.category || 'Cẩm nang in ấn' }}
                </span>
              </td>

              <!-- Date -->
              <td class="py-3 px-4 text-slate-400 whitespace-nowrap text-[11px]">
                <div class="flex items-center gap-1.5">
                  <Calendar class="w-3.5 h-3.5 text-slate-400" />
                  <span>{{ formatDate(post.published_at || post.created_at) }}</span>
                </div>
              </td>

              <!-- Status Toggle -->
              <td class="py-3 px-4 text-center whitespace-nowrap">
                <button
                  @click="togglePublishStatus(post)"
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold transition-all cursor-pointer"
                  :class="post.is_published
                    ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/20'
                    : 'bg-slate-500/10 text-slate-400 border border-slate-500/30 hover:bg-slate-500/20'"
                  :title="post.is_published ? 'Bấm để chuyển về bản nháp' : 'Bấm để xuất bản ngay'"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="post.is_published ? 'bg-emerald-400' : 'bg-slate-400'"></span>
                  <span>{{ post.is_published ? 'Đã xuất bản' : 'Bản nháp' }}</span>
                </button>
              </td>

              <!-- Action Buttons -->
              <td class="py-3 px-4 text-right whitespace-nowrap">
                <div class="flex items-center justify-end gap-1">
                  <router-link
                    :to="`/he-thong-admin/posts/${post.id}/edit`"
                    class="p-2 rounded-lg text-slate-400 hover:text-white transition-colors"
                    :class="isDarkMode ? 'hover:bg-[#2E3A47]' : 'hover:bg-slate-100 hover:text-slate-900'"
                    title="Chỉnh sửa bài viết"
                  >
                    <SquarePen class="w-4 h-4 text-brand-gold" />
                  </router-link>

                  <button
                    @click="confirmDeletePost(post)"
                    class="p-2 rounded-lg text-slate-400 hover:text-red-400 transition-colors cursor-pointer"
                    :class="isDarkMode ? 'hover:bg-red-500/10' : 'hover:bg-red-50'"
                    title="Xóa bài viết"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer Info Bar -->
      <div
        class="p-4 border-t flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-2"
        :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'"
      >
        <span>Hiển thị {{ filteredPosts.length }} / {{ posts.length }} bài viết</span>
        <span>SEO Cẩm Nang & Xuất Bản • In Nhật Nguyên</span>
      </div>
    </div>

    <!-- Modal: Delete Confirmation -->
    <div
      v-if="postToDelete"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm"
      @click.self="postToDelete = null"
    >
      <div
        class="w-full max-w-md rounded-2xl border p-6 space-y-4 shadow-2xl"
        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-900'"
      >
        <div class="w-12 h-12 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center mx-auto">
          <Trash2 class="w-6 h-6" />
        </div>

        <div class="text-center space-y-1">
          <h3 class="text-base font-black">Xác nhận xóa bài viết?</h3>
          <p class="text-xs text-slate-400">
            Bạn có chắc chắn muốn xóa bài viết <strong class="text-white">"{{ postToDelete.title }}"</strong>? Bài viết sẽ bị gỡ bỏ hoàn toàn khỏi website.
          </p>
        </div>

        <div class="flex items-center justify-center gap-3 pt-2">
          <button
            @click="postToDelete = null"
            class="px-4 py-2.5 rounded-xl border text-xs font-bold cursor-pointer"
            :class="isDarkMode ? 'border-[#2E3A47] text-slate-300 hover:bg-[#2E3A47]' : 'border-slate-200 text-slate-700 hover:bg-slate-100'"
          >
            Hủy Bỏ
          </button>
          <button
            @click="executeDeletePost"
            :disabled="isDeleting"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-xs shadow-md cursor-pointer disabled:opacity-60"
          >
            <Loader2 v-if="isDeleting" class="w-4 h-4 animate-spin" />
            <span>{{ isDeleting ? 'Đang xóa...' : 'Xác Nhận Xóa' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  Plus,
  Search,
  RotateCcw,
  SquarePen,
  Trash2,
  ExternalLink,
  Calendar,
  Image as ImageIcon,
  Newspaper,
  CheckCircle2,
  AlertCircle,
  X,
  Loader2
} from 'lucide-vue-next'
import { api } from '../../services/api'
import { useAdminTheme } from '../../composables/useAdminTheme'

const { isDarkMode } = useAdminTheme()

const posts = ref([])
const isLoading = ref(true)
const isDeleting = ref(false)
const searchQuery = ref('')
const selectedCategory = ref('all')
const selectedStatus = ref('all')
const postToDelete = ref(null)

const toastMessage = ref('')
const toastType = ref('success')

function showToast(msg, type = 'success') {
  toastMessage.value = msg
  toastType.value = type
  setTimeout(() => {
    if (toastMessage.value === msg) {
      toastMessage.value = ''
    }
  }, 4000)
}

function resetFilters() {
  searchQuery.value = ''
  selectedCategory.value = 'all'
  selectedStatus.value = 'all'
}

const uniqueCategories = computed(() => {
  const set = new Set(posts.value.map(p => p.category).filter(Boolean))
  return Array.from(set)
})

const filteredPosts = computed(() => {
  return posts.value.filter(p => {
    // Search filter
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.toLowerCase()
      const titleMatch = p.title?.toLowerCase().includes(q)
      const excerptMatch = p.excerpt?.toLowerCase().includes(q)
      if (!titleMatch && !excerptMatch) return false
    }

    // Category filter
    if (selectedCategory.value !== 'all' && p.category !== selectedCategory.value) {
      return false
    }

    // Status filter
    if (selectedStatus.value === 'published' && !p.is_published) return false
    if (selectedStatus.value === 'draft' && p.is_published) return false

    return true
  })
})

function formatDate(dateStr) {
  if (!dateStr) return '—'
  const d = new Date(dateStr)
  return d.toLocaleDateString('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

async function fetchPosts() {
  try {
    isLoading.value = true
    const res = await api.getAdminPosts()
    posts.value = res.data || []
  } catch (err) {
    console.error('Failed to load posts', err)
    showToast('Lỗi khi tải danh sách bài viết', 'error')
  } finally {
    isLoading.value = false
  }
}

async function togglePublishStatus(post) {
  try {
    const original = post.is_published
    post.is_published = !original
    await api.toggleAdminPost(post.id)
    showToast(post.is_published ? 'Đã xuất bản bài viết thành công!' : 'Đã chuyển về bản nháp')
  } catch (err) {
    console.error('Failed to toggle post publish', err)
    showToast('Lỗi khi đổi trạng thái bài viết', 'error')
    post.is_published = !post.is_published
  }
}

function confirmDeletePost(post) {
  postToDelete.value = post
}

async function executeDeletePost() {
  if (!postToDelete.value) return
  try {
    isDeleting.value = true
    await api.deleteAdminPost(postToDelete.value.id)
    showToast('Đã xóa bài viết thành công')
    postToDelete.value = null
    await fetchPosts()
  } catch (err) {
    console.error('Failed to delete post', err)
    showToast('Lỗi khi xóa bài viết', 'error')
  } finally {
    isDeleting.value = false
  }
}

onMounted(() => {
  fetchPosts()
})
</script>



