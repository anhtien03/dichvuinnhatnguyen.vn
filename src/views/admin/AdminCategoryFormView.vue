<template>
  <div class="space-y-6">
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div class="flex items-center gap-3">
        <router-link
          to="/he-thong-admin/categories"
          class="p-2.5 rounded-xl border text-slate-400 hover:text-white transition-colors cursor-pointer"
          :class="isDarkMode ? 'border-[#2E3A47] hover:bg-[#2E3A47]' : 'border-slate-200 hover:bg-slate-100 hover:text-slate-900'"
          title="Quay lại danh sách danh mục"
        >
          <ArrowLeft class="w-4 h-4" />
        </router-link>
        <div>
          <div class="flex items-center gap-2">
            <h1 class="text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              {{ isEditMode ? 'Chỉnh Sửa Danh Mục' : 'Thêm Mới Danh Mục Nhóm In' }}
            </h1>
            <span
              class="px-2.5 py-0.5 rounded-full text-[11px] font-bold"
              :class="form.is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20'"
            >
              {{ form.is_active ? 'Đang Hiển Thị' : 'Tạm Ẩn' }}
            </span>
          </div>
          <p class="text-xs text-slate-400 mt-0.5">
            Quản lý thông tin nhóm ấn phẩm, hình ảnh đại diện, đường dẫn slug và tối ưu thứ hạng tìm kiếm Google.
          </p>
        </div>
      </div>

      <!-- Header Action Buttons -->
      <div class="flex items-center gap-3">
        <router-link
          to="/he-thong-admin/categories"
          class="px-4 py-2.5 rounded-xl border text-xs font-bold transition-colors cursor-pointer"
          :class="isDarkMode ? 'border-[#2E3A47] text-slate-300 hover:bg-[#2E3A47]' : 'border-slate-200 text-slate-700 hover:bg-slate-100'"
        >
          Hủy Bỏ
        </router-link>

        <button
          type="button"
          @click="saveCategory"
          :disabled="isSaving"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-gold to-amber-500 hover:from-amber-400 hover:to-brand-gold text-slate-950 font-black text-xs shadow-md transition-all transform hover:-translate-y-0.5 active:translate-y-0 cursor-pointer disabled:opacity-60"
        >
          <Loader2 v-if="isSaving" class="w-4 h-4 animate-spin" />
          <Save v-else class="w-4 h-4" />
          <span>{{ isSaving ? 'Đang lưu...' : (isEditMode ? 'Cập Nhật Danh Mục' : 'Tạo Danh Mục Mới') }}</span>
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

    <!-- Main Form Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
      <!-- Left Column: Primary Content (8 Cols) -->
      <div class="lg:col-span-8 space-y-6">
        <!-- 1. Thông Tin Cơ Bản -->
        <div
          class="rounded-2xl border p-5 sm:p-6 space-y-4 shadow-sm"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <h2 class="text-sm font-bold uppercase tracking-wider text-brand-gold flex items-center gap-2">
            <Tags class="w-4 h-4 text-brand-gold" />
            <span>1. Thông Tin Nhóm Ấn Phẩm</span>
          </h2>

          <!-- Tên Danh Mục -->
          <div>
            <label class="block font-bold text-xs mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
              Tên Danh Mục In <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.name"
              @input="handleNameInput"
              type="text"
              required
              placeholder="Ví dụ: In Hộp Giấy & Bao Bì"
              class="w-full rounded-xl px-4 py-2.5 border text-sm font-bold transition-colors focus:outline-none focus:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-white border-slate-200 text-slate-900 placeholder-slate-400'"
            />
          </div>

          <!-- Đường Dẫn SEO (Slug) -->
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block font-bold text-xs" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                Đường Dẫn SEO (Slug) <span class="text-red-500">*</span>
              </label>
              <button
                type="button"
                @click="form.slug = slugify(form.name)"
                class="text-[11px] font-bold text-brand-gold hover:underline cursor-pointer"
              >
                Tự động tạo từ tên
              </button>
            </div>
            <div class="relative flex items-center">
              <span
                class="px-3 py-2.5 text-xs font-mono rounded-l-xl border border-r-0 select-none"
                :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-slate-400' : 'bg-slate-100 border-slate-200 text-slate-500'"
              >
                /danh-muc/
              </span>
              <input
                v-model="form.slug"
                type="text"
                required
                placeholder="in-hop-giay-bao-bi"
                class="w-full rounded-r-xl px-3.5 py-2.5 border text-xs font-mono transition-colors focus:outline-none focus:border-brand-gold"
                :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-white border-slate-200 text-slate-900 placeholder-slate-400'"
              />
            </div>
          </div>

          <!-- Huy Hiệu Tag (Badge) -->
          <div>
            <label class="block font-bold text-xs mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
              Huy Hiệu Nổi Bật (Badge Tag - Tùy chọn)
            </label>
            <input
              v-model="form.badge"
              type="text"
              placeholder="Ví dụ: Bán chạy, In nhanh 24h, Giá xưởng, Chống nước..."
              class="w-full rounded-xl px-3.5 py-2 border text-xs transition-colors focus:outline-none focus:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-white border-slate-200 text-slate-900 placeholder-slate-400'"
            />
          </div>

          <!-- Mô Tả Danh Mục -->
          <div>
            <label class="block font-bold text-xs mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
              Mô Tả Danh Mục
            </label>
            <textarea
              v-model="form.description"
              rows="3"
              placeholder="Mô tả nhóm ấn phẩm hiển thị trên đầu trang catalog sản phẩm hoặc mega menu..."
              class="w-full rounded-xl p-3.5 border text-xs leading-relaxed transition-colors focus:outline-none focus:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-white border-slate-200 text-slate-900 placeholder-slate-400'"
            ></textarea>
          </div>
        </div>

        <!-- 2. Hình Ảnh Đại Diện Danh Mục -->
        <div
          class="rounded-2xl border p-5 sm:p-6 space-y-4 shadow-sm"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold uppercase tracking-wider text-brand-gold flex items-center gap-2">
              <ImageIcon class="w-4 h-4 text-brand-gold" />
              <span>2. Hình Ảnh Đại Diện / Mockup Danh Mục</span>
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

          <!-- Image Preview & File Actions -->
          <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
            <!-- Large Image Preview Box -->
            <div class="sm:col-span-5">
              <div
                v-if="form.image"
                class="aspect-[4/3] rounded-2xl overflow-hidden border relative group shadow-md"
                :class="isDarkMode ? 'border-[#2E3A47] bg-[#1A222C]' : 'border-slate-200 bg-slate-50'"
              >
                <img :src="form.image" alt="Preview danh mục" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                <button
                  type="button"
                  @click="form.image = ''"
                  class="absolute top-2 right-2 p-1.5 rounded-xl bg-black/70 hover:bg-red-600 text-white transition-colors cursor-pointer shadow-md"
                  title="Xóa ảnh"
                >
                  <Trash2 class="w-3.5 h-3.5" />
                </button>
              </div>
              <div
                v-else
                class="aspect-[4/3] rounded-2xl border-2 border-dashed flex flex-col items-center justify-center text-center p-4"
                :class="isDarkMode ? 'border-[#2E3A47] bg-[#1A222C] text-slate-500' : 'border-slate-300 bg-slate-50 text-slate-400'"
              >
                <ImageIcon class="w-10 h-10 mb-2 opacity-50" />
                <span class="text-xs font-semibold">Chưa có ảnh đại diện</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Khuyên dùng ảnh vuông hoặc 4:3 sắc nét</span>
              </div>
            </div>

            <!-- Image URL Input & Direct Upload -->
            <div class="sm:col-span-7 space-y-3">
              <div>
                <label class="block font-bold text-xs mb-1" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">
                  Đường dẫn URL Hình Ảnh
                </label>
                <input
                  v-model="form.image"
                  type="text"
                  placeholder="Dán link ảnh hoặc chọn từ Thư viện Media"
                  class="w-full rounded-xl px-3.5 py-2.5 border text-xs font-mono transition-colors focus:outline-none focus:border-brand-gold"
                  :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-white border-slate-200 text-slate-900 placeholder-slate-400'"
                />
              </div>

              <!-- Upload Button -->
              <div class="flex flex-wrap items-center gap-3">
                <label class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-dashed text-xs font-semibold text-slate-400 hover:text-brand-gold hover:border-brand-gold cursor-pointer transition-colors">
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
                <span class="text-[11px] text-slate-400">Định dạng JPG, PNG, WebP</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 3. Tối Ưu Hóa SEO & Google Preview -->
        <div
          class="rounded-2xl border p-5 sm:p-6 space-y-4 shadow-sm"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold uppercase tracking-wider text-brand-gold flex items-center gap-2">
              <Globe class="w-4 h-4 text-brand-gold" />
              <span>3. Cấu Hình Tối Ưu SEO Google</span>
            </h2>
            <button
              type="button"
              @click="autoFillSEO"
              class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-500 hover:text-amber-400 cursor-pointer"
            >
              <Sparkles class="w-3.5 h-3.5" />
              <span>Gợi ý SEO tự động</span>
            </button>
          </div>

          <!-- Meta Title -->
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block font-bold text-xs" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                Tiêu Đề Trang SEO (Meta Title)
              </label>
              <span class="text-[10px] font-mono text-slate-400">
                {{ form.meta_title ? form.meta_title.length : 0 }}/65 ký tự
              </span>
            </div>
            <input
              v-model="form.meta_title"
              type="text"
              placeholder="In Hộp Giấy Giá Xưởng TPHCM - Thiết Kế Miễn Phí | Xưởng In Nhật Nguyên"
              class="w-full rounded-xl px-3.5 py-2.5 border text-xs transition-colors focus:outline-none focus:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-white border-slate-200 text-slate-900 placeholder-slate-400'"
            />
          </div>

          <!-- Meta Description -->
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block font-bold text-xs" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                Thẻ Mô Tả Tìm Kiếm (Meta Description)
              </label>
              <span class="text-[10px] font-mono text-slate-400">
                {{ form.meta_description ? form.meta_description.length : 0 }}/160 ký tự
              </span>
            </div>
            <textarea
              v-model="form.meta_description"
              rows="3"
              placeholder="Xưởng in Nhật Nguyên chuyên in ấn chất lượng cao, chuẩn màu, hỗ trợ kiểm tra file thiết kế và giao hàng toàn quốc."
              class="w-full rounded-xl p-3.5 border text-xs leading-relaxed transition-colors focus:outline-none focus:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-white border-slate-200 text-slate-900 placeholder-slate-400'"
            ></textarea>
          </div>

          <!-- Live Google SERP Snippet Preview -->
          <div class="p-4 rounded-xl border bg-white text-left space-y-1 shadow-inner border-slate-200">
            <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">
              Xem trước kết quả trên Google Search (SERP Preview):
            </div>
            <div class="flex items-center gap-1.5 text-xs text-slate-700 truncate">
              <span class="text-emerald-700 font-semibold font-mono">https://innhatnguyen.vn</span>
              <span class="text-slate-400">› danh-muc ›</span>
              <span class="text-slate-600 font-mono">{{ form.slug || 'slug-danh-muc' }}</span>
            </div>
            <div class="text-base text-[#1a0dab] hover:underline font-medium cursor-pointer line-clamp-1 leading-snug">
              {{ form.meta_title || form.name || 'Tiêu Đề Danh Mục In Nhật Nguyên' }}
            </div>
            <div class="text-xs text-[#4d5156] line-clamp-2 leading-relaxed">
              {{ form.meta_description || form.description || 'Mô tả tóm tắt nội dung danh mục ấn phẩm in ấn phục vụ doanh nghiệp, cửa hàng và sự kiện.' }}
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Sidebar Settings (4 Cols) -->
      <div class="lg:col-span-4 space-y-6">
        <!-- Sticky Save Card -->
        <div
          class="rounded-2xl border p-5 space-y-4 shadow-sm sticky top-24"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <h3 class="text-xs font-bold uppercase tracking-wider text-brand-gold flex items-center gap-1.5">
            <CheckCircle2 class="w-4 h-4 text-brand-gold" />
            <span>Xuất Bản & Hiển Thị</span>
          </h3>

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
                Cho phép khách xem trên Mega Menu và catalog
              </span>
            </div>
          </label>

          <!-- Thứ tự hiển thị -->
          <div>
            <label class="block font-bold text-xs mb-1" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">
              Thứ Tự Sắp Xếp (Số nhỏ đứng trước)
            </label>
            <input
              v-model.number="form.sort_order"
              type="number"
              min="0"
              class="w-full rounded-xl px-3.5 py-2 border text-xs font-mono font-bold transition-colors focus:outline-none focus:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-900'"
            />
          </div>

          <!-- Thông tin tóm tắt -->
          <div class="pt-3 border-t text-xs space-y-2 text-slate-400" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <div class="flex items-center justify-between">
              <span>Đường dẫn xem thử:</span>
              <a
                v-if="form.slug"
                :href="'/danh-muc/' + form.slug"
                target="_blank"
                class="text-brand-gold hover:underline font-mono text-[11px] flex items-center gap-1"
              >
                <span>Xem trang</span>
                <ExternalLink class="w-3 h-3" />
              </a>
              <span v-else class="text-[11px] italic">Chưa có slug</span>
            </div>

            <div v-if="isEditMode && categoryDetail" class="flex items-center justify-between">
              <span>Số ấn phẩm liên kết:</span>
              <span class="font-bold font-mono text-brand-gold">{{ categoryDetail.products_count ?? 0 }} ấn phẩm</span>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="pt-3 border-t space-y-2" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <button
              type="button"
              @click="saveCategory"
              :disabled="isSaving"
              class="w-full py-2.5 rounded-xl bg-gradient-to-r from-brand-gold to-amber-500 hover:from-amber-400 hover:to-brand-gold text-slate-950 font-black text-xs shadow-md shadow-brand-gold/25 flex items-center justify-center gap-2 transition-all cursor-pointer disabled:opacity-60"
            >
              <Loader2 v-if="isSaving" class="w-4 h-4 animate-spin" />
              <Save v-else class="w-4 h-4" />
              <span>{{ isSaving ? 'Đang lưu...' : (isEditMode ? 'Cập Nhật Danh Mục' : 'Tạo Danh Mục Mới') }}</span>
            </button>

            <router-link
              to="/he-thong-admin/categories"
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
      :current-url="form.image"
      @select="onMediaSelected"
      @close="isMediaModalOpen = false"
    />
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
  ArrowLeft,
  Save,
  Loader2,
  CheckCircle2,
  AlertCircle,
  X,
  Tags,
  Globe,
  Sparkles,
  Image as ImageIcon,
  FolderOpen,
  Upload,
  Trash2,
  ExternalLink
} from 'lucide-vue-next'
import { api } from '../../services/api'
import { useAdminTheme } from '../../composables/useAdminTheme'
import { slugify } from '../../composables/useSEO'
import MediaLibraryModal from '../../components/admin/MediaLibraryModal.vue'

const { isDarkMode } = useAdminTheme()
const route = useRoute()
const router = useRouter()

const categoryId = computed(() => route.params.id)
const isEditMode = computed(() => Boolean(categoryId.value))

const isSaving = ref(false)
const isUploading = ref(false)
const toastMessage = ref('')
const toastType = ref('success')
const isMediaModalOpen = ref(false)
const categoryDetail = ref(null)

const form = reactive({
  name: '',
  slug: '',
  image: '',
  badge: '',
  description: '',
  meta_title: '',
  meta_description: '',
  sort_order: 0,
  is_active: true
})

function showToast(msg, type = 'success') {
  toastMessage.value = msg
  toastType.value = type
  setTimeout(() => {
    toastMessage.value = ''
  }, 4000)
}

function handleNameInput() {
  if (!isEditMode.value && !form.slug) {
    form.slug = slugify(form.name)
  }
}

function autoFillSEO() {
  if (!form.name) {
    showToast('Vui lòng nhập tên danh mục trước', 'error')
    return
  }
  form.meta_title = `${form.name} - Giá Gốc Trực Tiếp Tại Xưởng TPHCM | Nhật Nguyên`
  form.meta_description = `Xưởng in Nhật Nguyên chuyên sản xuất ${form.name.toLowerCase()} theo yêu cầu chất lượng cao, giá gốc trực tiếp tận xưởng không qua trung gian.`
  showToast('Đã tạo gợi ý tiêu đề & mô tả SEO tự động!')
}

// Media Selection callback
function onMediaSelected(mediaItem) {
  const url = typeof mediaItem === 'string' ? mediaItem : (mediaItem?.url || '')
  if (url) {
    form.image = url
    showToast('Đã chọn hình ảnh danh mục từ thư viện!')
  }
  isMediaModalOpen.value = false
}

// Direct Upload callback
async function handleDirectUpload(e) {
  const file = e.target.files?.[0]
  if (!file) return

  try {
    isUploading.value = true
    const formData = new FormData()
    formData.append('file', file)
    formData.append('folder', 'categories')

    const res = await api.uploadAdminMedia(formData)
    if (res.data?.url) {
      form.image = res.data.url
      showToast('Tải ảnh lên thành công!')
    }
  } catch (err) {
    showToast('Lỗi tải ảnh: ' + (err.data?.message || err.message || 'Thất bại'), 'error')
  } finally {
    isUploading.value = false
    if (e.target) e.target.value = ''
  }
}

// Load category for edit
async function fetchCategory() {
  if (!isEditMode.value) return

  try {
    const res = await api.getCategoryBySlug(categoryId.value)
    const cat = res.data || res
    categoryDetail.value = cat

    form.name = cat.name || ''
    form.slug = cat.slug || ''
    form.image = cat.image || ''
    form.badge = cat.badge || ''
    form.description = cat.description || ''
    form.meta_title = cat.meta_title || ''
    form.meta_description = cat.meta_description || ''
    form.sort_order = cat.sort_order ?? 0
    form.is_active = cat.is_active ?? true
  } catch (err) {
    console.error('Failed to load category', err)
    showToast('Không thể tải thông tin danh mục này', 'error')
  }
}

// Save Category (Create or Update)
async function saveCategory() {
  if (!form.name.trim()) {
    showToast('Vui lòng nhập tên danh mục', 'error')
    return
  }

  try {
    isSaving.value = true
    const payload = {
      name: form.name.trim(),
      slug: form.slug?.trim() || slugify(form.name),
      image: form.image?.trim() || null,
      badge: form.badge?.trim() || null,
      description: form.description?.trim() || null,
      meta_title: form.meta_title?.trim() || null,
      meta_description: form.meta_description?.trim() || null,
      sort_order: Number(form.sort_order) || 0,
      is_active: Boolean(form.is_active)
    }

    if (isEditMode.value) {
      await api.updateAdminCategory(categoryId.value, payload)
      showToast('Cập nhật danh mục thành công!')
    } else {
      await api.createAdminCategory(payload)
      showToast('Tạo danh mục mới thành công!')
    }

    // Redirect back to categories list
    setTimeout(() => {
      router.push('/he-thong-admin/categories')
    }, 800)
  } catch (err) {
    console.error('Failed to save category', err)
    showToast('Lỗi: ' + (err.message || 'Không thể lưu danh mục'), 'error')
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  fetchCategory()
})
</script>



