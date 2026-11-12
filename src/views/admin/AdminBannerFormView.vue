<template>
  <div class="space-y-6">
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div class="flex items-center gap-3">
        <router-link
          to="/he-thong-admin/banners"
          class="p-2.5 rounded-xl border text-slate-400 hover:text-white transition-colors cursor-pointer"
          :class="isDarkMode ? 'border-[#2E3A47] hover:bg-[#2E3A47]' : 'border-slate-200 hover:bg-slate-100 hover:text-slate-900'"
          title="Quay lại danh sách banner"
        >
          <ArrowLeft class="w-4 h-4" />
        </router-link>
        <div>
          <div class="flex items-center gap-2">
            <h1 class="text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              {{ isEditMode ? 'Chỉnh Sửa Banner & Thẻ Quảng Cáo' : 'Thêm Mới Banner & Thẻ Quảng Cáo' }}
            </h1>
            <span
              class="px-2.5 py-0.5 rounded-full text-[11px] font-bold"
              :class="form.is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20'"
            >
              {{ form.is_active ? 'Đang Hiển Thị' : 'Tạm Ẩn' }}
            </span>
          </div>
          <p class="text-xs text-slate-400 mt-0.5">
            Tùy chỉnh thông tin hình ảnh, nhãn nổi bật, màu nền gradient và liên kết điều hướng trên website.
          </p>
        </div>
      </div>

      <!-- Header Action Buttons -->
      <div class="flex items-center gap-3">
        <router-link
          to="/he-thong-admin/banners"
          class="px-4 py-2.5 rounded-xl border text-xs font-bold transition-colors cursor-pointer"
          :class="isDarkMode ? 'border-[#2E3A47] text-slate-300 hover:bg-[#2E3A47]' : 'border-slate-200 text-slate-700 hover:bg-slate-100'"
        >
          Hủy Bỏ
        </router-link>

        <button
          type="button"
          @click="saveBanner"
          :disabled="isSaving"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-gold to-amber-500 hover:from-amber-400 hover:to-brand-gold text-slate-950 font-black text-xs shadow-md transition-all transform hover:-translate-y-0.5 active:translate-y-0 cursor-pointer disabled:opacity-60"
        >
          <Loader2 v-if="isSaving" class="w-4 h-4 animate-spin" />
          <Save v-else class="w-4 h-4" />
          <span>{{ isSaving ? 'Đang lưu...' : (isEditMode ? 'Cập Nhật Banner' : 'Tạo Banner Mới') }}</span>
        </button>
      </div>
    </div>

    <!-- Toast Notification -->
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

    <!-- Main Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
      <!-- Left Column: Form Fields (7 Cols) -->
      <div class="lg:col-span-7 space-y-6">
        <!-- 1. Tên Quản Lý Banner -->
        <div
          class="rounded-2xl border p-5 sm:p-6 space-y-3 shadow-sm"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <h2 class="text-sm font-bold uppercase tracking-wider text-brand-gold flex items-center gap-2">
            <Sparkles class="w-4 h-4 text-brand-gold" />
            <span>1. Tên Quản Lý Banner</span>
          </h2>

          <div>
            <label class="block font-bold text-xs mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
              Tên Gợi Nhớ (dùng quản lý trong Admin & thuộc tính alt của ảnh)
            </label>
            <input
              v-model="form.title"
              type="text"
              placeholder="Ví dụ: Banner Chính - Xưởng In Nhật Nguyên, Banner Phụ In Nhanh 24h..."
              class="w-full rounded-xl px-3.5 py-2.5 border text-xs font-bold transition-colors focus:outline-none focus:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-white border-slate-200 text-slate-900 placeholder-slate-400'"
            />
          </div>
        </div>

        <!-- 2. Hình Ảnh Banner / Ảnh Nền -->
        <div
          class="rounded-2xl border p-5 sm:p-6 space-y-4 shadow-sm"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold uppercase tracking-wider text-brand-gold flex items-center gap-2">
              <ImageIcon class="w-4 h-4 text-brand-gold" />
              <span>2. Hình Ảnh Banner / Ảnh Nền</span>
            </h2>

            <button
              type="button"
              @click="isMediaModalOpen = true"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-brand-gold/15 hover:bg-brand-gold/25 text-brand-gold font-bold text-xs transition-colors cursor-pointer"
            >
              <FolderOpen class="w-3.5 h-3.5" />
              <span>Chọn từ Thư Viện Media</span>
            </button>
          </div>

          <!-- Yêu Cầu Kích Thước Box -->
          <div
            class="p-3.5 rounded-xl border text-xs flex items-start gap-2.5 transition-colors"
            :class="isDarkMode ? 'bg-amber-500/10 border-amber-500/30 text-amber-200' : 'bg-amber-50 border-amber-200 text-amber-900'"
          >
            <Info class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
            <div class="space-y-1 flex-1">
              <div class="flex flex-wrap items-center gap-2">
                <span class="font-bold text-xs uppercase tracking-wide">
                  📐 Yêu Cầu Kích Thước Ảnh (Vị Trí Hiện Tại):
                </span>
                <span
                  class="px-2 py-0.5 rounded-md font-mono font-black text-xs border"
                  :class="isDarkMode ? 'bg-amber-500/20 text-brand-gold border-amber-500/30' : 'bg-white text-amber-800 border-amber-300 shadow-2xs'"
                >
                  {{ recommendedSizeDetail.size }}
                </span>
                <span class="text-[11px] font-semibold opacity-90">
                  ({{ recommendedSizeDetail.aspect }})
                </span>
              </div>
              <p class="text-[11px] leading-relaxed opacity-85">
                {{ recommendedSizeDetail.hint }}
              </p>
            </div>
          </div>

          <!-- Image Input & Preview -->
          <div class="flex items-center gap-2">
            <!-- Thumbnail preview if image selected -->
            <div
              v-if="form.image_url"
              class="w-14 h-11 rounded-xl overflow-hidden border shrink-0 bg-slate-100 relative shadow-2xs"
              :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
            >
              <img :src="form.image_url" alt="Banner thumb" class="w-full h-full object-cover" />
            </div>

            <input
              v-model="form.image_url"
              type="text"
              placeholder="Để trống nếu chỉ dùng nền gradient chữ, hoặc dán URL ảnh / chọn từ thư viện"
              class="flex-1 rounded-xl px-3.5 py-2.5 border text-xs font-mono transition-colors focus:outline-none focus:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-white border-slate-200 text-slate-900 placeholder-slate-400'"
            />
            <button
              v-if="form.image_url"
              type="button"
              @click="form.image_url = ''"
              class="p-2.5 rounded-xl border text-slate-400 hover:text-red-400 transition-colors cursor-pointer"
              :class="isDarkMode ? 'border-[#2E3A47] hover:bg-red-500/10' : 'border-slate-200 hover:bg-red-50'"
              title="Xóa link ảnh"
            >
              <Trash2 class="w-4 h-4" />
            </button>
          </div>

          <!-- Trợ lý kiểm tra kích thước ảnh tự động -->
          <div
            v-if="form.image_url && imageMeta.status !== 'none'"
            class="p-3.5 rounded-xl border text-xs flex items-start gap-2.5 transition-all"
            :class="imageMeta.status === 'good' ? (isDarkMode ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300' : 'bg-emerald-50 border-emerald-200 text-emerald-800') : (isDarkMode ? 'bg-amber-500/10 border-amber-500/30 text-amber-300' : 'bg-amber-50 border-amber-200 text-amber-900')"
          >
            <CheckCircle2 v-if="imageMeta.status === 'good'" class="w-4 h-4 shrink-0 text-emerald-500 mt-0.5" />
            <AlertCircle v-else class="w-4 h-4 shrink-0 text-amber-500 mt-0.5" />
            <div class="flex-1 space-y-1">
              <div class="font-bold flex flex-wrap items-center gap-2">
                <span>{{ imageMeta.status === 'good' ? '✓ Ảnh đạt chuẩn hiển thị' : '⚠️ Lưu ý kiểm tra ảnh' }}</span>
                <span class="font-mono text-[11px] px-2 py-0.5 rounded-md border font-bold" :class="isDarkMode ? 'bg-black/30 border-white/10' : 'bg-white border-slate-300'">
                  {{ imageMeta.width }} × {{ imageMeta.height }} px
                </span>
                <span v-if="imageMeta.fileSize" class="font-mono text-[11px] px-2 py-0.5 rounded-md border" :class="isDarkMode ? 'bg-black/30 border-white/10' : 'bg-white border-slate-300'">
                  {{ imageMeta.fileSize }}
                </span>
              </div>
              <p class="text-[11px] leading-relaxed opacity-90">{{ imageMeta.message }}</p>
            </div>
          </div>

          <!-- Direct Upload Quick Action -->
          <div class="flex flex-wrap items-center gap-3 pt-0.5">
            <label class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-dashed text-xs font-semibold text-slate-400 hover:text-brand-gold hover:border-brand-gold transition-colors cursor-pointer">
              <Upload class="w-4 h-4" />
              <span>{{ isUploading ? 'Đang tải lên...' : 'Tải file trực tiếp từ máy' }}</span>
              <input
                type="file"
                accept="image/*"
                class="hidden"
                :disabled="isUploading"
                @change="handleDirectUpload"
              />
            </label>
            <span class="text-[11px] text-slate-500">
              (Định dạng WebP, JPG, PNG • Tối ưu dưới 500 KB)
            </span>
          </div>

          <!-- Mobile Image (Tùy chọn) -->
          <div class="pt-3 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <label class="block font-bold text-xs mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
              Ảnh Riêng Cho Màn Hình Mobile (Tùy chọn)
            </label>
            <input
              v-model="form.mobile_image_url"
              type="text"
              placeholder="Để trống nếu dùng chung ảnh trên, hoặc dán URL ảnh tỉ lệ đứng/gọn riêng cho điện thoại"
              class="w-full rounded-xl px-3.5 py-2 border text-xs font-mono transition-colors focus:outline-none focus:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-white border-slate-200 text-slate-900 placeholder-slate-400'"
            />
          </div>
        </div>

        <!-- 3. Liên Kết Điều Hướng Khi Click -->
        <div
          class="rounded-2xl border p-5 sm:p-6 space-y-3 shadow-sm"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <label class="block font-bold text-xs" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
            Liên Kết Điều Hướng Khi Click (Link URL)
          </label>
          <input
            v-model="form.link_url"
            type="text"
            placeholder="Ví dụ: /yeu-cau-bao-gia hoặc /san-pham/in-hop-giay..."
            class="w-full rounded-xl px-3.5 py-2.5 border text-xs font-mono transition-colors focus:outline-none focus:border-brand-gold"
            :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-white border-slate-200 text-slate-900 placeholder-slate-400'"
          />

          <!-- Quick Link Chips -->
          <div class="flex flex-wrap items-center gap-2 pt-1">
            <span class="text-[11px] text-slate-400">Gợi ý liên kết nhanh:</span>
            <button
              v-for="sug in linkSuggestions"
              :key="sug.link"
              type="button"
              @click="form.link_url = sug.link"
              class="px-2.5 py-1 rounded-lg text-[11px] font-bold border transition-colors cursor-pointer"
              :class="isDarkMode ? 'border-[#2E3A47] text-slate-300 hover:border-brand-gold hover:text-brand-gold' : 'border-slate-200 text-slate-600 hover:border-brand-blue hover:text-brand-blue'"
            >
              {{ sug.label }}
            </button>
          </div>
        </div>
      </div>

      <!-- Right Column: Live Preview & Settings (5 Cols) -->
      <div class="lg:col-span-5 space-y-6">
        <!-- 1. Realtime Card Preview Box -->
        <div
          class="rounded-2xl border p-5 space-y-3 shadow-sm"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-wider text-brand-gold flex items-center gap-1.5">
              <Eye class="w-4 h-4 text-brand-gold" />
              <span>Xem Trước Hiển Thị Thực Tế</span>
            </h3>
            <span class="text-[10px] font-mono text-slate-400">{{ form.position }}</span>
          </div>

          <div
            class="relative rounded-2xl overflow-hidden shadow-lg transition-all bg-slate-900 border border-slate-700/50"
            :class="[
              form.position === 'home_showcase_1' || form.position === 'home_showcase_2'
                ? 'aspect-[2/3] max-w-[280px] mx-auto'
                : (form.position === 'popup' ? 'aspect-square max-w-[320px] mx-auto' : 'aspect-[16/9]')
            ]"
          >
            <!-- Background Image Preview (100% Thuần Ảnh Đồ Họa, Không Chữ Đè) -->
            <img
              v-if="form.image_url"
              :src="form.image_url"
              alt="Banner preview"
              class="w-full h-full object-cover transition-opacity duration-300"
            />
            <div v-else class="w-full h-full flex flex-col items-center justify-center text-slate-500 text-xs italic gap-2 p-4 text-center">
              <ImageIcon class="w-8 h-8 opacity-40" />
              <span>(Chưa có ảnh banner - Hãy tải ảnh hoặc chọn từ thư viện)</span>
            </div>

            <!-- Position Badge on Top -->
            <div class="absolute top-3 left-3 z-10">
              <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider backdrop-blur-md shadow-sm bg-black/60 text-white">
                {{ form.position }}
              </span>
            </div>

            <div v-if="form.image_url" class="absolute top-3 right-3 z-10">
              <span class="px-2 py-0.5 rounded-md bg-emerald-500/80 backdrop-blur-md text-[10px] font-mono text-white font-bold">
                ✓ Ảnh sắc nét 100%
              </span>
            </div>
          </div>

          <p class="text-[11px] text-slate-400 text-center italic">
            Hiển thị thực tế có thể co giãn tự động theo kích thước màn hình người dùng.
          </p>
        </div>

        <!-- 2. Vị Trí & Xuất Bản Settings (Sticky) -->
        <div
          class="rounded-2xl border p-5 space-y-4 shadow-sm sticky top-24"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <h3 class="text-xs font-bold uppercase tracking-wider text-brand-gold flex items-center gap-1.5">
            <CheckCircle2 class="w-4 h-4 text-brand-gold" />
            <span>Vị Trí & Cài Đặt Xuất Bản</span>
          </h3>

          <!-- Vị trí hiển thị -->
          <div>
            <label class="block font-bold text-xs mb-1.5" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">
              Vị Trí Hiển Thị Trên Website
            </label>
            <select
              v-model="form.position"
              class="w-full rounded-xl px-3.5 py-2.5 border text-xs transition-colors focus:outline-none focus:border-brand-gold cursor-pointer"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-900'"
            >
              <option value="home_main">Banner Chính Khối Giữa (Trang Chủ)</option>
              <option value="home_sub_1">Banner Phụ Góc Phải #1 (Ví dụ: IN NHANH online)</option>
              <option value="home_sub_2">Banner Phụ Góc Phải #2 (Ví dụ: Mẫu tem hot)</option>
              <option value="home_showcase_1">Banner Dọc - Ấn Phẩm Văn Phòng (Trang Chủ)</option>
              <option value="home_showcase_2">Banner Dọc - Tiếp Thị & Quảng Cáo (Trang Chủ)</option>
              <option value="popup">Banner Popup Khuyến Mãi</option>
            </select>
          </div>

          <!-- Kích hoạt hiển thị -->
          <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-colors" :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'">
            <input
              v-model="form.is_active"
              type="checkbox"
              class="w-4 h-4 text-brand-gold rounded focus:ring-brand-gold"
            />
            <div class="text-xs">
              <span class="font-bold block" :class="isDarkMode ? 'text-white' : 'text-slate-800'">
                Kích hoạt hiển thị
              </span>
              <span class="text-[11px] text-slate-400">
                Cho phép banner xuất hiện ngay trên trang
              </span>
            </div>
          </label>

          <!-- Thứ tự hiển thị -->
          <div>
            <label class="block font-bold text-xs mb-1.5" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">
              Thứ Tự Sắp Xếp (Số nhỏ đứng trước)
            </label>
            <input
              v-model.number="form.sort_order"
              type="number"
              min="0"
              step="1"
              class="w-full rounded-xl px-3.5 py-2 border text-xs font-mono font-bold transition-colors focus:outline-none focus:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-900'"
            />
          </div>

          <!-- Action Buttons -->
          <div class="pt-3 border-t space-y-2" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <button
              type="button"
              @click="saveBanner"
              :disabled="isSaving"
              class="w-full py-2.5 rounded-xl bg-gradient-to-r from-brand-gold to-amber-500 hover:from-amber-400 hover:to-brand-gold text-slate-950 font-black text-xs shadow-md shadow-brand-gold/25 flex items-center justify-center gap-2 transition-all cursor-pointer disabled:opacity-60"
            >
              <Loader2 v-if="isSaving" class="w-4 h-4 animate-spin" />
              <Save v-else class="w-4 h-4" />
              <span>{{ isSaving ? 'Đang lưu...' : (isEditMode ? 'Cập Nhật Banner' : 'Tạo Banner Mới') }}</span>
            </button>

            <router-link
              to="/he-thong-admin/banners"
              class="block w-full py-2 rounded-xl border text-center text-xs font-bold transition-colors cursor-pointer"
              :class="isDarkMode ? 'border-[#2E3A47] text-slate-300 hover:bg-[#1A222C]' : 'border-slate-200 text-slate-600 hover:bg-slate-100'"
            >
              Hủy Bỏ
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Integrated Media Library Modal -->
    <MediaLibraryModal
      v-if="isMediaModalOpen"
      :current-url="form.image_url"
      @select="onMediaSelected"
      @close="isMediaModalOpen = false"
    />
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
  ArrowLeft,
  Save,
  Loader2,
  CheckCircle2,
  AlertCircle,
  X,
  Sparkles,
  Image as ImageIcon,
  FolderOpen,
  Upload,
  Trash2,
  Info,
  Eye
} from 'lucide-vue-next'
import { api } from '../../services/api'
import { useAdminTheme } from '../../composables/useAdminTheme'
import MediaLibraryModal from '../../components/admin/MediaLibraryModal.vue'

const { isDarkMode } = useAdminTheme()
const route = useRoute()
const router = useRouter()

const bannerId = computed(() => route.params.id)
const isEditMode = computed(() => Boolean(bannerId.value))

const isSaving = ref(false)
const isUploading = ref(false)
const toastMessage = ref('')
const toastType = ref('success')
const isMediaModalOpen = ref(false)

// Trạng thái kiểm tra kích thước & tỉ lệ ảnh
const imageMeta = ref({
  width: 0,
  height: 0,
  ratio: 0,
  fileSize: null,
  status: 'none', // 'good' | 'warning' | 'none'
  message: ''
})

const form = reactive({
  title: '',
  subtitle: null,
  badge: null,
  image_url: '',
  mobile_image_url: '',
  link_url: '/yeu-cau-bao-gia',
  button_text: null,
  extra_text: null,
  position: 'home_main',
  bg_color: 'blue',
  sort_order: 1,
  is_active: true
})

const bgColors = [
  { label: 'Xanh KTS', value: 'blue', class: 'bg-gradient-to-r from-blue-700 to-blue-900 text-white' },
  { label: 'Vàng Kim', value: 'amber', class: 'bg-gradient-to-r from-amber-600 to-amber-700 text-white' },
  { label: 'Tím Hiện Đại', value: 'purple', class: 'bg-gradient-to-r from-purple-700 to-purple-900 text-white' },
  { label: 'Tối Quyền Lực', value: 'dark', class: 'bg-gradient-to-r from-slate-800 to-slate-950 text-white' }
]

const linkSuggestions = [
  { label: 'Nhận Báo Giá', link: '/yeu-cau-bao-gia' },
  { label: 'Tất Cả Sản Phẩm', link: '/san-pham' },
  { label: 'In Hộp Giấy', link: '/danh-muc/bao-bi-tui-giay' },
  { label: 'In Tem Nhãn Decal', link: '/danh-muc/tem-nhan-decal' },
  { label: 'In Túi Giấy', link: '/danh-muc/bao-bi-tui-giay' }
]

const recommendedSizeDetail = computed(() => {
  switch (form.position) {
    case 'home_main':
      return {
        size: '1280 × 720 px (hoặc 1600 × 900 px)',
        aspect: 'Tỉ lệ ngang 16:9',
        hint: 'Banner chính khối giữa trang chủ (kế bên menu danh mục). Thiết kế hoàn chỉnh logo/chữ trên ảnh và chừa lề an toàn ~40-60px.'
      }
    case 'home_sub_1':
    case 'home_sub_2':
      return {
        size: '800 × 450 px (hoặc 640 × 360 px)',
        aspect: 'Tỉ lệ ngang 16:9',
        hint: 'Banner phụ góc phải trang chủ. Thiết kế ảnh đồ họa kích thước 800 × 450 px, chừa lề an toàn ~25-30px.'
      }
    case 'home_showcase_1':
    case 'home_showcase_2':
      return {
        size: '600 × 900 px (hoặc 800 × 1200 px)',
        aspect: 'Tỉ lệ dọc 2:3',
        hint: 'Banner đứng cột trái trong khối danh mục sản phẩm nổi bật trang chủ.'
      }
    case 'popup':
      return {
        size: '800 × 800 px (Vuông) hoặc 800 × 1000 px',
        aspect: 'Tỉ lệ 1:1 hoặc 4:5',
        hint: 'Banner popup bật lên khi khách vừa vào website.'
      }
    default:
      return {
        size: '1280 × 720 px',
        aspect: 'Tỉ lệ 16:9',
        hint: 'Ảnh định dạng WebP hoặc JPG chất lượng cao, dung lượng dưới 500 KB.'
      }
  }
})

function getCardBgClass(color) {
  switch (color) {
    case 'amber':
      return 'bg-gradient-to-br from-amber-600 via-amber-500 to-amber-700'
    case 'purple':
      return 'bg-gradient-to-br from-purple-700 via-purple-600 to-purple-900'
    case 'dark':
      return 'bg-gradient-to-br from-slate-900 via-slate-800 to-black'
    case 'blue':
    default:
      return 'bg-gradient-to-br from-blue-700 via-blue-600 to-blue-900'
  }
}

function showToast(msg, type = 'success') {
  toastMessage.value = msg
  toastType.value = type
  setTimeout(() => {
    toastMessage.value = ''
  }, 4000)
}

function inspectImage(url, file = null) {
  if (!url) {
    imageMeta.value = { width: 0, height: 0, ratio: 0, fileSize: null, status: 'none', message: '' }
    return
  }
  const img = new Image()
  img.onload = () => {
    const w = img.naturalWidth
    const h = img.naturalHeight
    const ratio = h > 0 ? (w / h) : 1
    const sizeStr = file ? (file.size < 1024 * 1024 ? Math.round(file.size / 1024) + ' KB' : (file.size / (1024 * 1024)).toFixed(1) + ' MB') : null

    let targetRatio = 16 / 9
    let ratioLabel = '16:9 (Ngang)'
    let isVertical = false

    if (form.position === 'home_showcase_1' || form.position === 'home_showcase_2') {
      targetRatio = 2 / 3
      ratioLabel = '2:3 (Dọc)'
      isVertical = true
    } else if (form.position === 'popup') {
      targetRatio = 1
      ratioLabel = '1:1 (Vuông)'
    }

    let status = 'good'
    let msg = `Tỉ lệ ảnh (${(w / h).toFixed(2)}:1) khớp chuẩn với vị trí ${ratioLabel}.`
    if (sizeStr) msg += ` Dung lượng: ${sizeStr}.`

    if (isVertical && ratio > 1.1) {
      status = 'warning'
      msg = `Lưu ý: Vị trí này là Banner Dọc (${ratioLabel}), bạn đang chọn ảnh ngang (${w}×${h} px). Ảnh sẽ bị cắt 2 mép bên khi hiển thị.`
    } else if (!isVertical && ratio < 0.9) {
      status = 'warning'
      msg = `Lưu ý: Vị trí này là Banner Ngang (${ratioLabel}), bạn đang chọn ảnh đứng (${w}×${h} px). Ảnh sẽ bị cắt trên dưới khi hiển thị.`
    } else if (Math.abs(ratio - targetRatio) / targetRatio > 0.28) {
      status = 'warning'
      msg = `Tỉ lệ ảnh thực tế (${(w / h).toFixed(2)}:1) hơi lệch so với tỉ lệ chuẩn ${ratioLabel}. Hãy lưu ý chừa lề an toàn cho chữ/logo.`
    }

    imageMeta.value = {
      width: w,
      height: h,
      ratio,
      fileSize: sizeStr || imageMeta.value.fileSize,
      status,
      message: msg
    }
  }
  img.onerror = () => {
    imageMeta.value = {
      width: 0,
      height: 0,
      ratio: 0,
      fileSize: null,
      status: 'warning',
      message: 'Không thể tải thử ảnh từ đường dẫn này. Vui lòng kiểm tra lại URL ảnh.'
    }
  }
  img.src = url
}

watch(() => [form.image_url, form.position], ([newUrl]) => {
  if (newUrl) {
    inspectImage(newUrl)
  } else {
    imageMeta.value = { width: 0, height: 0, ratio: 0, fileSize: null, status: 'none', message: '' }
  }
})

function onMediaSelected(mediaItem) {
  const url = typeof mediaItem === 'string' ? mediaItem : (mediaItem?.url || '')
  if (url) {
    form.image_url = url
    inspectImage(url)
    showToast('Đã chọn hình ảnh banner từ thư viện!')
  }
  isMediaModalOpen.value = false
}

async function handleDirectUpload(e) {
  const file = e.target.files?.[0]
  if (!file) return

  try {
    isUploading.value = true
    const formData = new FormData()
    formData.append('file', file)
    formData.append('folder', 'banners')

    const res = await api.uploadAdminMedia(formData)
    if (res.data?.url) {
      form.image_url = res.data.url
      inspectImage(res.data.url, file)
      showToast('Tải ảnh banner lên thành công!')
    }
  } catch (err) {
    showToast('Lỗi tải ảnh: ' + (err.data?.message || err.message || 'Thất bại'), 'error')
  } finally {
    isUploading.value = false
    if (e.target) e.target.value = ''
  }
}

async function fetchBanner() {
  if (!isEditMode.value) return

  try {
    const res = await api.getAdminBanner(bannerId.value)
    const b = res.data || res

    form.title = b.title || ''
    form.subtitle = b.subtitle || ''
    form.badge = b.badge || ''
    form.image_url = b.image_url || ''
    form.mobile_image_url = b.mobile_image_url || ''
    form.link_url = b.link_url || ''
    form.button_text = b.button_text || ''
    form.extra_text = b.extra_text || ''
    form.position = b.position || 'home_main'
    form.bg_color = b.bg_color || 'blue'
    form.sort_order = b.sort_order ?? 0
    form.is_active = b.is_active ?? true

    if (form.image_url) {
      inspectImage(form.image_url)
    }
  } catch (err) {
    console.error('Failed to load banner', err)
    showToast('Không thể tải thông tin banner này', 'error')
  }
}

async function saveBanner() {
  try {
    isSaving.value = true
    const payload = {
      title: form.title?.trim() || '',
      subtitle: null,
      badge: null,
      image_url: form.image_url?.trim() || null,
      mobile_image_url: form.mobile_image_url?.trim() || null,
      link_url: form.link_url?.trim() || null,
      button_text: null,
      extra_text: null,
      position: form.position,
      bg_color: 'blue',
      sort_order: Number(form.sort_order) || 0,
      is_active: Boolean(form.is_active)
    }

    if (isEditMode.value) {
      await api.updateAdminBanner(bannerId.value, payload)
      showToast('Cập nhật banner thành công!')
    } else {
      await api.createAdminBanner(payload)
      showToast('Thêm mới banner thành công!')
    }

    setTimeout(() => {
      router.push('/he-thong-admin/banners')
    }, 800)
  } catch (err) {
    console.error('Failed to save banner', err)
    showToast('Lỗi: ' + (err.message || 'Không thể lưu banner'), 'error')
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  fetchBanner()
})
</script>



