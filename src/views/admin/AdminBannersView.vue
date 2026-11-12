<template>
  <div class="space-y-6">
    <!-- Top Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
          Quản Lý Banner & Slider
        </h1>
        <p class="text-xs text-slate-400 mt-1">
          Cập nhật banner slider trang chủ, banner thẻ quảng cáo (IN NHANH 24H, Tem nhãn...) và thứ tự hiển thị.
        </p>
      </div>

      <div class="flex items-center gap-3">
        <router-link
          to="/he-thong-admin/banners/create"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-gold to-amber-500 hover:from-amber-400 hover:to-brand-gold text-slate-950 font-black text-xs shadow-md transition-all transform hover:-translate-y-0.5 active:translate-y-0 cursor-pointer"
        >
          <Plus class="w-4 h-4" />
          <span>Thêm Mới Banner</span>
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

    <!-- Position Filter Tabs -->
    <div class="flex flex-wrap items-center gap-2 border-b pb-4" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
      <button
        v-for="tab in positionTabs"
        :key="tab.value"
        @click="selectedPosition = tab.value"
        class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer"
        :class="selectedPosition === tab.value
          ? (isDarkMode ? 'bg-brand-gold text-slate-950 shadow-sm' : 'bg-brand-blue text-white shadow-sm')
          : (isDarkMode ? 'bg-[#1C2434] text-slate-400 hover:text-white hover:bg-[#2E3A47]' : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200')"
      >
        <span>{{ tab.label }}</span>
        <span
          class="px-1.5 py-0.5 text-[10px] rounded-full font-black"
          :class="selectedPosition === tab.value
            ? (isDarkMode ? 'bg-slate-950/20 text-slate-950' : 'bg-white/20 text-white')
            : (isDarkMode ? 'bg-[#2E3A47] text-slate-300' : 'bg-slate-100 text-slate-600')"
        >
          {{ getBannerCountByPosition(tab.value) }}
        </span>
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="py-20 text-center space-y-3">
      <Loader2 class="w-8 h-8 animate-spin mx-auto text-brand-gold" />
      <p class="text-xs text-slate-400">Đang tải danh sách banner...</p>
    </div>

    <!-- Empty State -->
    <div
      v-else-if="filteredBanners.length === 0"
      class="p-12 text-center rounded-2xl border"
      :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
    >
      <ImageIcon class="w-12 h-12 mx-auto text-slate-500 mb-3" />
      <h3 class="text-sm font-bold" :class="isDarkMode ? 'text-white' : 'text-slate-800'">
        Chưa có banner nào ở mục này
      </h3>
      <p class="text-xs text-slate-400 mt-1 mb-4">
        Hãy bấm "Thêm Mới Banner" để tạo banner hiển thị đầu tiên trên trang chủ.
      </p>
      <router-link
        to="/he-thong-admin/banners/create"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-gold text-slate-950 font-bold text-xs cursor-pointer hover:bg-amber-400"
      >
        <Plus class="w-4 h-4" />
        <span>Thêm Mới Ngay</span>
      </router-link>
    </div>

    <!-- Banners Grid Display -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="banner in filteredBanners"
        :key="banner.id"
        class="rounded-2xl border overflow-hidden flex flex-col justify-between transition-all duration-200 hover:shadow-xl group"
        :class="[
          isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200',
          !banner.is_active ? 'opacity-60 grayscale-[40%]' : ''
        ]"
      >
        <!-- Banner Visual Card Area (Thuần Ảnh Đồ Họa, Không Chữ Đè) -->
        <div class="relative aspect-[16/9] overflow-hidden bg-slate-900">
          <!-- Background Image if present -->
          <img
            v-if="banner.image_url"
            :src="banner.image_url"
            :alt="banner.title || 'Banner'"
            class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500"
            @error="$event.target.style.display = 'none'"
          />
          <div v-else class="w-full h-full flex items-center justify-center text-slate-500 text-xs italic">
            (Chưa có hình ảnh)
          </div>

          <!-- Top Row: Position & Sort Order Badges -->
          <div class="absolute top-3 left-3 z-10">
            <span
              class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider backdrop-blur-md shadow-sm"
              :class="getPositionBadgeClass(banner.position)"
            >
              {{ getPositionLabel(banner.position) }}
            </span>
          </div>

          <div class="absolute top-3 right-3 z-10">
            <span class="px-2 py-0.5 rounded-lg bg-black/60 text-white font-mono text-[10px] font-bold backdrop-blur-md">
              #{{ banner.sort_order }}
            </span>
          </div>
        </div>

        <!-- Banner Details & Actions -->
        <div class="p-4 space-y-3 flex-1 flex flex-col justify-between text-xs">
          <div class="space-y-1.5">
            <h4 class="font-bold text-sm truncate" :class="isDarkMode ? 'text-white' : 'text-slate-800'">
              {{ banner.title || '(Chưa đặt tên banner)' }}
            </h4>

            <!-- Link Info -->
            <div class="flex items-center gap-2 text-slate-400">
              <ExternalLink class="w-3.5 h-3.5 shrink-0 text-brand-gold" />
              <span class="truncate font-mono text-[11px]" :class="isDarkMode ? 'text-slate-300' : 'text-slate-600'">
                {{ banner.link_url || '(Không có liên kết)' }}
              </span>
            </div>
          </div>

          <!-- Action & Toggle Footer -->
          <div class="pt-3 border-t flex items-center justify-between" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <!-- Quick Active Switch -->
            <button
              @click="toggleActiveStatus(banner)"
              class="flex items-center gap-2 cursor-pointer group/toggle"
              :title="banner.is_active ? 'Nhấn để ẩn banner' : 'Nhấn để kích hoạt banner'"
            >
              <div
                class="w-9 h-5 rounded-full transition-colors relative flex items-center p-0.5"
                :class="banner.is_active ? 'bg-emerald-500' : 'bg-slate-600'"
              >
                <div
                  class="w-4 h-4 rounded-full bg-white shadow-md transform transition-transform"
                  :class="banner.is_active ? 'translate-x-4' : 'translate-x-0'"
                ></div>
              </div>
              <span class="text-[11px] font-bold" :class="banner.is_active ? 'text-emerald-500' : 'text-slate-400'">
                {{ banner.is_active ? 'Hiển thị' : 'Đang ẩn' }}
              </span>
            </button>

            <!-- Edit & Delete Buttons -->
            <div class="flex items-center gap-1">
              <router-link
                :to="'/he-thong-admin/banners/' + banner.id + '/edit'"
                class="p-2 rounded-lg text-slate-400 hover:text-white transition-colors cursor-pointer"
                :class="isDarkMode ? 'hover:bg-[#2E3A47]' : 'hover:bg-slate-100 hover:text-slate-900'"
                title="Chỉnh sửa banner"
              >
                <SquarePen class="w-4 h-4 text-brand-gold" />
              </router-link>

              <button
                @click="confirmDeleteBanner(banner)"
                class="p-2 rounded-lg text-slate-400 hover:text-red-400 transition-colors cursor-pointer"
                :class="isDarkMode ? 'hover:bg-red-500/10' : 'hover:bg-red-50'"
                title="Xóa banner"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>



    <!-- Modal: Delete Confirmation -->
    <div
      v-if="bannerToDelete"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm"
      @click.self="bannerToDelete = null"
    >
      <div
        class="w-full max-w-md rounded-2xl border p-6 space-y-4 shadow-2xl"
        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-900'"
      >
        <div class="w-12 h-12 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center mx-auto">
          <Trash2 class="w-6 h-6" />
        </div>

        <div class="text-center space-y-1">
          <h3 class="text-base font-black">Xác nhận xóa banner?</h3>
          <p class="text-xs text-slate-400">
            Bạn có chắc chắn muốn xóa banner <strong class="text-white">"{{ bannerToDelete?.title || 'này' }}"</strong>? Hành động này không thể hoàn tác.
          </p>
        </div>

        <div class="flex items-center justify-center gap-3 pt-2">
          <button
            @click="bannerToDelete = null"
            class="px-4 py-2.5 rounded-xl border text-xs font-bold cursor-pointer"
            :class="isDarkMode ? 'border-[#2E3A47] text-slate-300 hover:bg-[#2E3A47]' : 'border-slate-200 text-slate-700 hover:bg-slate-100'"
          >
            Hủy Bỏ
          </button>
          <button
            @click="executeDeleteBanner"
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
  Image as ImageIcon,
  ExternalLink,
  SquarePen,
  Trash2,
  X,
  Loader2,
  CheckCircle2,
  AlertCircle,
  Sparkles
} from 'lucide-vue-next'
import { api } from '../../services/api'
import { useAdminTheme } from '../../composables/useAdminTheme'

const { isDarkMode } = useAdminTheme()

// State
const banners = ref([])
const isLoading = ref(true)
const isDeleting = ref(false)
const selectedPosition = ref('all')
const bannerToDelete = ref(null)

const toastMessage = ref('')
const toastType = ref('success')

// Filter tabs
const positionTabs = [
  { label: 'Tất Cả', value: 'all' },
  { label: 'Banner Chính Khối Giữa', value: 'home_main' },
  { label: 'Banner Phụ #1 (KTS 24h)', value: 'home_sub_1' },
  { label: 'Banner Phụ #2 (Tem Hot)', value: 'home_sub_2' },
  { label: 'Banner Dọc Văn Phòng', value: 'home_showcase_1' },
  { label: 'Banner Dọc Quảng Cáo', value: 'home_showcase_2' },
  { label: 'Popup Khuyến Mãi', value: 'popup' }
]

// Filtered banners
const filteredBanners = computed(() => {
  if (!Array.isArray(banners.value)) return []
  if (selectedPosition.value === 'all') {
    return banners.value
  }
  return banners.value.filter(b => b.position === selectedPosition.value)
})

function getBannerCountByPosition(pos) {
  if (!Array.isArray(banners.value)) return 0
  if (pos === 'all') return banners.value.length
  return banners.value.filter(b => b.position === pos).length
}

function getPositionLabel(pos) {
  switch (pos) {
    case 'home_main': return 'Banner Chính Khối Giữa'
    case 'home_sub_1': return 'Banner Phụ #1'
    case 'home_sub_2': return 'Banner Phụ #2'
    case 'home_showcase_1': return 'Banner Dọc Văn Phòng'
    case 'home_showcase_2': return 'Banner Dọc Quảng Cáo'
    case 'popup': return 'Popup'
    default: return pos
  }
}

function getPositionBadgeClass(pos) {
  switch (pos) {
    case 'home_main':
      return 'bg-amber-500/90 text-slate-950 font-black'
    case 'home_sub_1':
      return 'bg-blue-600/90 text-white'
    case 'home_sub_2':
      return 'bg-purple-600/90 text-white'
    case 'home_showcase_1':
      return 'bg-indigo-600/90 text-white'
    case 'home_showcase_2':
      return 'bg-teal-600/90 text-white'
    case 'popup':
      return 'bg-rose-600/90 text-white'
    default:
      return 'bg-slate-700 text-white'
  }
}

function getCardBgClass(color) {
  switch (color) {
    case 'amber':
      return 'bg-gradient-to-br from-amber-600 via-amber-500 to-amber-700 text-white'
    case 'purple':
      return 'bg-gradient-to-br from-purple-700 via-purple-600 to-purple-900 text-white'
    case 'dark':
      return 'bg-gradient-to-br from-slate-800 via-slate-900 to-black text-white'
    case 'blue':
    default:
      return 'bg-gradient-to-br from-blue-700 via-brand-blue to-blue-900 text-white'
  }
}

function showToast(msg, type = 'success') {
  toastMessage.value = msg
  toastType.value = type
  setTimeout(() => {
    if (toastMessage.value === msg) {
      toastMessage.value = ''
    }
  }, 4000)
}

// Fetch banners
async function fetchBanners() {
  try {
    isLoading.value = true
    const res = await api.getAdminBanners()
    banners.value = Array.isArray(res?.data) ? res.data : (Array.isArray(res) ? res : [])
  } catch (err) {
    console.error('Failed to load banners', err)
    showToast('Lỗi khi tải danh sách banner. Vui lòng kiểm tra kết nối API server.', 'error')
  } finally {
    isLoading.value = false
  }
}

// Toggle Active Quick Status
async function toggleActiveStatus(banner) {
  try {
    const originalState = banner.is_active
    banner.is_active = !originalState
    await api.toggleAdminBanner(banner.id)
    showToast(banner.is_active ? 'Đã kích hoạt banner' : 'Đã ẩn banner khỏi website')
  } catch (err) {
    console.error('Failed to toggle banner status', err)
    showToast('Lỗi khi đổi trạng thái banner', 'error')
    banner.is_active = !banner.is_active
  }
}

// Delete Banner
function confirmDeleteBanner(banner) {
  bannerToDelete.value = banner
}

async function executeDeleteBanner() {
  if (!bannerToDelete.value) return
  try {
    isDeleting.value = true
    await api.deleteAdminBanner(bannerToDelete.value.id)
    showToast('Đã xóa banner thành công')
    bannerToDelete.value = null
    await fetchBanners()
  } catch (err) {
    console.error('Failed to delete banner', err)
    showToast('Lỗi khi xóa banner', 'error')
  } finally {
    isDeleting.value = false
  }
}

onMounted(() => {
  fetchBanners()
})
</script>


