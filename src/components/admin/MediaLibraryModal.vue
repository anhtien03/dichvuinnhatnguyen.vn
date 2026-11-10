<template>
  <div class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-3 sm:p-5">
    <div
      class="border rounded-2xl w-full max-w-5xl shadow-2xl flex flex-col h-[88vh] max-h-[800px] overflow-hidden"
      :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-900'"
    >
      <!-- 1. MODAL HEADER -->
      <div
        class="px-5 py-4 border-b flex items-center justify-between shrink-0"
        :class="isDarkMode ? 'border-[#2E3A47] bg-[#24303F]/60' : 'border-slate-100 bg-slate-50'"
      >
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-brand-gold/15 text-brand-gold flex items-center justify-center font-bold shrink-0">
            <Images class="w-4 h-4" />
          </div>
          <div>
            <h2 class="text-sm sm:text-base font-black tracking-tight">
              Thư Viện Media 
            </h2>
            <p class="text-[11px] text-slate-400">
              Tạo thư mục phân loại ảnh (Hộp giấy, Túi giấy, Tem nhãn...) và tải ảnh từ máy tính
            </p>
          </div>
        </div>

        <button
          type="button"
          @click="$emit('close')"
          class="p-1.5 rounded-lg text-slate-400 hover:text-white cursor-pointer transition-colors"
          :class="isDarkMode ? 'hover:bg-[#2E3A47]' : 'hover:bg-slate-200 hover:text-slate-800'"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- 2. TABS & FOLDER ACTION BAR -->
      <div
        class="px-5 py-3 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0"
        :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'"
      >
        <!-- Tab Buttons -->
        <div class="flex items-center gap-2">
          <button
            type="button"
            @click="activeTab = 'library'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer"
            :class="activeTab === 'library'
              ? 'bg-brand-gold text-slate-950 font-black shadow-sm'
              : (isDarkMode ? 'text-slate-400 hover:text-white hover:bg-[#24303F]' : 'text-slate-600 hover:bg-slate-100')"
          >
            <Grid class="w-3.5 h-3.5" />
            <span>Thư Viện Ảnh ({{ mediaList.length }})</span>
          </button>

          <button
            type="button"
            @click="activeTab = 'upload'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer"
            :class="activeTab === 'upload'
              ? 'bg-brand-gold text-slate-950 font-black shadow-sm'
              : (isDarkMode ? 'text-slate-400 hover:text-white hover:bg-[#24303F]' : 'text-slate-600 hover:bg-slate-100')"
          >
            <UploadCloud class="w-3.5 h-3.5" />
            <span>Tải Ảnh Lên Thư Mục Này</span>
          </button>
        </div>

        <!-- Search in Library -->
        <div v-if="activeTab === 'library'" class="relative w-full sm:w-64">
          <Search class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Tìm theo tên file ảnh..."
            class="w-full text-xs rounded-xl pl-8 pr-3 py-1.5 border focus:outline-none focus:ring-1 focus:ring-brand-gold"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
          />
        </div>
      </div>

      <!-- 3. BREADCRUMB & CREATE FOLDER TOOLBAR -->
      <div
        v-if="activeTab === 'library'"
        class="px-5 py-2.5 border-b flex flex-wrap items-center justify-between gap-2 text-xs shrink-0"
        :class="isDarkMode ? 'bg-[#1C2434]/80 border-[#2E3A47]' : 'bg-slate-50/60 border-slate-100'"
      >
        <!-- Breadcrumb Navigation -->
        <div class="flex items-center gap-1.5 overflow-x-auto py-1">
          <button
            type="button"
            @click="navigateToFolder('products')"
            class="inline-flex items-center gap-1 font-bold text-xs hover:text-brand-gold transition-colors cursor-pointer"
            :class="currentFolder === 'products' ? (isDarkMode ? 'text-white' : 'text-slate-900 font-black') : 'text-slate-400'"
          >
            <Folder class="w-4 h-4 text-brand-gold" />
            <span>Thư viện gốc</span>
          </button>

          <template v-for="(part, idx) in breadcrumbParts" :key="idx">
            <ChevronRight class="w-3.5 h-3.5 text-slate-500 shrink-0" />
            <button
              type="button"
              @click="navigateToFolder(part.path)"
              class="font-bold text-xs hover:text-brand-gold transition-colors cursor-pointer whitespace-nowrap"
              :class="idx === breadcrumbParts.length - 1 ? (isDarkMode ? 'text-white' : 'text-slate-900 font-black') : 'text-slate-400'"
            >
              {{ part.name }}
            </button>
          </template>

          <!-- Back button if inside subfolder -->
          <button
            v-if="currentFolder !== 'products'"
            type="button"
            @click="goBackFolder"
            class="ml-3 px-2 py-0.5 rounded-lg border text-[11px] font-semibold flex items-center gap-1 cursor-pointer transition-colors"
            :class="isDarkMode ? 'border-[#2E3A47] hover:bg-[#24303F] text-slate-300' : 'border-slate-200 hover:bg-slate-100 text-slate-700'"
          >
            <ArrowLeft class="w-3 h-3" />
            <span>Lên một cấp</span>
          </button>
        </div>

        <!-- Action: New Folder Button -->
        <div class="flex items-center gap-2">
          <!-- Inline New Folder Form or Button -->
          <div v-if="isCreatingFolder" class="flex items-center gap-1.5">
            <input
              ref="newFolderInputRef"
              v-model="newFolderName"
              @keyup.enter="handleCreateFolder"
              @keyup.esc="isCreatingFolder = false"
              type="text"
              placeholder="Tên thư mục (VD: hop-giay)..."
              class="px-2.5 py-1 text-xs rounded-lg border focus:outline-none focus:ring-1 focus:ring-brand-gold font-medium"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-white border-slate-300 text-slate-800'"
            />
            <button
              type="button"
              @click="handleCreateFolder"
              class="px-2.5 py-1 rounded-lg bg-brand-gold text-slate-950 font-bold text-xs shadow-xs cursor-pointer hover:bg-amber-400"
            >
              Tạo
            </button>
            <button
              type="button"
              @click="isCreatingFolder = false"
              class="px-2 py-1 rounded-lg text-slate-400 hover:text-white text-xs cursor-pointer"
            >
              Hủy
            </button>
          </div>

          <button
            v-else
            type="button"
            @click="startCreatingFolder"
            class="px-3 py-1 rounded-xl border font-bold text-xs flex items-center gap-1.5 cursor-pointer transition-colors"
            :class="isDarkMode ? 'border-[#2E3A47] hover:bg-[#24303F] text-brand-gold' : 'border-slate-300 hover:bg-slate-100 text-brand-gold'"
          >
            <FolderPlus class="w-3.5 h-3.5" />
            <span>+ Tạo Thư Mục</span>
          </button>
        </div>
      </div>

      <!-- 4. MAIN CONTENT AREA -->
      <div class="flex-1 overflow-hidden flex flex-col md:flex-row">
        
        <!-- ======================================================= -->
        <!-- TAB 1: MEDIA LIBRARY (FOLDERS + IMAGES GRID) -->
        <!-- ======================================================= -->
        <div
          v-if="activeTab === 'library'"
          class="flex-1 overflow-y-auto p-4 sm:p-5 custom-scrollbar space-y-5"
        >
          <!-- Loading State -->
          <div v-if="isLoading" class="py-20 text-center text-slate-400">
            <Loader2 class="w-8 h-8 animate-spin mx-auto text-brand-gold mb-2" />
            <span class="text-xs">Đang tải thư mục & hình ảnh...</span>
          </div>

          <template v-else>
            <!-- 4.1 FOLDERS SECTION (IF ANY IN CURRENT DIRECTORY) -->
            <div v-if="folderList.length > 0" class="space-y-2">
              <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                <Folder class="w-3.5 h-3.5 text-brand-gold" />
                <span>Thư Mục ({{ folderList.length }})</span>
              </div>

              <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                <div
                  v-for="folder in folderList"
                  :key="folder.path"
                  @click="navigateToFolder(folder.path)"
                  class="group p-3 rounded-xl border flex items-center justify-between gap-2.5 cursor-pointer transition-all hover:scale-[1.02] select-none"
                  :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] hover:border-brand-gold' : 'bg-slate-50 border-slate-200 hover:border-brand-gold hover:bg-amber-500/5'"
                  :title="`Bấm để mở thư mục ${folder.name}`"
                >
                  <div class="flex items-center gap-2.5 overflow-hidden">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-brand-gold flex items-center justify-center shrink-0">
                      <Folder class="w-4 h-4 fill-amber-500/20" />
                    </div>
                    <div class="truncate">
                      <div class="font-bold text-xs truncate" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                        {{ folder.name }}
                      </div>
                      <div class="text-[10px] text-slate-400">
                        {{ folder.items_count }} tệp
                      </div>
                    </div>
                  </div>

                  <!-- Quick delete folder button -->
                  <button
                    type="button"
                    @click.stop="handleDeleteFolder(folder)"
                    class="opacity-0 group-hover:opacity-100 p-1 text-slate-400 hover:text-red-400 hover:bg-red-500/10 rounded-md transition-all cursor-pointer shrink-0"
                    title="Xóa thư mục này"
                  >
                    <Trash2 class="w-3.5 h-3.5" />
                  </button>
                </div>
              </div>
            </div>

            <!-- 4.2 IMAGES GRID IN CURRENT DIRECTORY -->
            <div class="space-y-2">
              <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-slate-400">
                <div class="flex items-center gap-1.5">
                  <Images class="w-3.5 h-3.5 text-brand-gold" />
                  <span>Hình Ảnh ({{ filteredMedia.length }})</span>
                </div>
                <span v-if="currentFolder !== 'products'" class="text-amber-500 lowercase font-mono">
                  trong /{{ currentFolder }}
                </span>
              </div>

              <!-- Empty State -->
              <div v-if="filteredMedia.length === 0" class="py-14 text-center border-2 border-dashed rounded-2xl" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
                <ImageIcon class="w-10 h-10 text-slate-400 mx-auto mb-2 opacity-50" />
                <p class="text-xs text-slate-400 font-medium">Chưa có ảnh nào trong thư mục này</p>
                <button
                  @click="activeTab = 'upload'"
                  class="mt-3 px-3.5 py-1.5 rounded-xl bg-brand-gold text-slate-950 font-bold text-xs inline-flex items-center gap-1.5 cursor-pointer shadow-sm hover:bg-amber-400"
                >
                  <UploadCloud class="w-3.5 h-3.5" />
                  <span>Tải ảnh vào thư mục này</span>
                </button>
              </div>

              <!-- Thumbnails Grid -->
              <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                <div
                  v-for="item in filteredMedia"
                  :key="item.id"
                  @click="selectItem(item)"
                  @dblclick="selectAndConfirm(item)"
                  title="Click để xem chi tiết, Nhấn đúp để chọn nhanh"
                  class="group relative aspect-square rounded-xl overflow-hidden border-2 cursor-pointer transition-all duration-200 bg-slate-100 flex items-center justify-center select-none"
                  :class="[
                    selectedItem?.id === item.id
                      ? 'border-brand-gold ring-2 ring-brand-gold/50 shadow-md scale-[0.98]'
                      : (isDarkMode ? 'border-[#2E3A47] hover:border-slate-500' : 'border-slate-200 hover:border-slate-400')
                  ]"
                >
                  <img
                    :src="item.url"
                    :alt="item.name"
                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                    loading="lazy"
                    @error="onImgError"
                  />

                  <!-- Selected Checkmark Badge -->
                  <div
                    v-if="selectedItem?.id === item.id"
                    class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-brand-gold text-slate-950 flex items-center justify-center shadow-md font-black"
                  >
                    <Check class="w-3.5 h-3.5 stroke-[3]" />
                  </div>

                  <!-- Stock Sample Badge -->
                  <div
                    v-if="item.is_stock"
                    class="absolute bottom-1 left-1 px-1.5 py-0.5 rounded bg-black/60 backdrop-blur-xs text-[9px] text-white font-medium"
                  >
                    Mẫu
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>

        <!-- 4.3 SELECTED ITEM DETAILS SIDEBAR -->
        <div
          v-if="activeTab === 'library' && selectedItem"
          class="w-full md:w-64 border-t md:border-t-0 md:border-l p-4 flex flex-col justify-between shrink-0 text-xs overflow-y-auto"
          :class="isDarkMode ? 'border-[#2E3A47] bg-[#24303F]/40' : 'border-slate-100 bg-slate-50/70'"
        >
          <div class="space-y-3">
            <div class="font-bold uppercase tracking-wider text-[10px] text-slate-400">
              Chi Tiết Hình Ảnh
            </div>

            <!-- Large Preview -->
            <div class="aspect-video rounded-xl overflow-hidden border bg-black/20 flex items-center justify-center" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
              <img :src="selectedItem.url" :alt="selectedItem.name" class="w-full h-full object-contain" />
            </div>

            <!-- Meta details -->
            <div class="space-y-1.5">
              <div class="font-bold truncate text-xs" :class="isDarkMode ? 'text-white' : 'text-slate-900'" :title="selectedItem.name">
                {{ selectedItem.name }}
              </div>
              <div class="text-[11px] text-slate-400">
                Thư mục: <strong class="font-mono text-amber-500">/{{ selectedItem.folder || currentFolder }}</strong>
              </div>
              <div class="text-[11px] text-slate-400">
                Dung lượng: <strong>{{ formatSize(selectedItem.size) }}</strong>
              </div>
              <div v-if="selectedItem.mime" class="text-[11px] text-slate-400">
                Định dạng: <span class="uppercase font-mono font-bold">{{ selectedItem.mime.replace('image/', '') }}</span>
              </div>
            </div>

            <!-- Copy URL -->
            <button
              type="button"
              @click="copyUrl(selectedItem.url)"
              class="w-full py-1.5 px-3 rounded-lg border text-[11px] font-semibold flex items-center justify-center gap-1.5 cursor-pointer transition-colors"
              :class="isDarkMode ? 'border-[#2E3A47] hover:bg-[#1C2434] text-slate-300' : 'border-slate-200 hover:bg-white text-slate-700'"
            >
              <Copy class="w-3 h-3" />
              <span>{{ copyStatus || 'Sao chép liên kết ảnh' }}</span>
            </button>
          </div>

          <!-- Delete action (if not stock sample) -->
          <div v-if="!selectedItem.is_stock" class="pt-4 mt-4 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
            <button
              type="button"
              @click="handleDeleteItem(selectedItem)"
              class="w-full py-1.5 px-2 text-[11px] font-bold text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg transition-colors flex items-center justify-center gap-1 cursor-pointer"
            >
              <Trash2 class="w-3.5 h-3.5" />
              <span>Xóa ảnh này khỏi kho</span>
            </button>
          </div>
        </div>

        <!-- ======================================================= -->
        <!-- TAB 2: DRAG & DROP UPLOAD INTO CURRENT FOLDER -->
        <!-- ======================================================= -->
        <div
          v-if="activeTab === 'upload'"
          class="flex-1 p-6 sm:p-10 flex flex-col items-center justify-center space-y-3"
        >
          <!-- Current Upload Target Folder indicator -->
          <div class="text-xs font-bold px-3 py-1.5 rounded-full border flex items-center gap-2 mb-2" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-300' : 'bg-slate-100 border-slate-200 text-slate-700'">
            <Folder class="w-3.5 h-3.5 text-brand-gold" />
            <span>Tải vào thư mục: <strong class="text-brand-gold font-mono">/{{ currentFolder }}</strong></span>
          </div>

          <div
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
            class="w-full max-w-lg border-2 border-dashed rounded-3xl p-8 sm:p-12 text-center transition-all cursor-pointer"
            :class="[
              isDragging
                ? 'border-brand-gold bg-brand-gold/10 scale-[1.01]'
                : (isDarkMode ? 'border-[#2E3A47] hover:border-brand-gold bg-[#24303F]/40' : 'border-slate-300 hover:border-brand-gold bg-slate-50')
            ]"
            @click="triggerFileInput"
          >
            <input
              ref="fileInputRef"
              type="file"
              multiple
              accept="image/jpeg,image/png,image/webp,image/svg+xml,image/gif,image/avif,image/jfif"
              class="hidden"
              @change="handleFileSelect"
            />

            <div class="w-16 h-16 rounded-2xl bg-brand-gold/15 text-brand-gold mx-auto flex items-center justify-center mb-4">
              <UploadCloud v-if="!isUploading" class="w-8 h-8" />
              <Loader2 v-else class="w-8 h-8 animate-spin" />
            </div>

            <h3 class="text-sm sm:text-base font-bold mb-1" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              {{ isUploading ? 'Đang tải ảnh lên máy chủ...' : 'Kéo thả ảnh vào đây hoặc bấm để chọn từ máy tính' }}
            </h3>
            <p class="text-xs text-slate-400 max-w-sm mx-auto mb-4">
              Hỗ trợ JPG, PNG, WEBP, SVG tối đa 10MB. File sẽ được lưu vào thư mục <span class="font-mono font-bold">/{{ currentFolder }}</span>.
            </p>

            <button
              type="button"
              :disabled="isUploading"
              class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-gold to-amber-500 hover:from-amber-400 hover:to-brand-gold text-slate-950 font-black text-xs shadow-md shadow-brand-gold/25 inline-flex items-center gap-2 cursor-pointer disabled:opacity-50"
            >
              <UploadCloud class="w-4 h-4" />
              <span>Duyệt Ảnh Từ Máy Tính</span>
            </button>
          </div>

          <!-- Upload Status / Error -->
          <div v-if="uploadError" class="mt-4 p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs">
            {{ uploadError }}
          </div>
        </div>

      </div>

      <!-- 5. MODAL FOOTER -->
      <div
        class="px-5 py-3.5 border-t flex items-center justify-between shrink-0"
        :class="isDarkMode ? 'border-[#2E3A47] bg-[#24303F]/60' : 'border-slate-100 bg-slate-50'"
      >
        <div class="text-xs text-slate-400 truncate max-w-md">
          <span v-if="selectedItem">
            Đã chọn: <strong :class="isDarkMode ? 'text-white' : 'text-slate-900'">{{ selectedItem.name }}</strong>
          </span>
          <span v-else>Chưa chọn ảnh nào</span>
        </div>

        <div class="flex items-center gap-3">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 rounded-xl border text-xs font-bold cursor-pointer transition-colors"
            :class="isDarkMode ? 'border-[#2E3A47] text-slate-300 hover:bg-[#1C2434]' : 'border-slate-300 text-slate-700 hover:bg-slate-100'"
          >
            Đóng
          </button>

          <button
            type="button"
            @click="handleConfirmSelection"
            :disabled="!selectedItem"
            class="px-6 py-2 rounded-xl bg-gradient-to-r from-brand-gold to-amber-500 hover:from-amber-400 hover:to-brand-gold text-slate-950 font-black text-xs shadow-md shadow-brand-gold/25 flex items-center gap-2 cursor-pointer transition-all disabled:opacity-40 disabled:cursor-not-allowed"
          >
            <Check class="w-4 h-4 stroke-[3]" />
            <span>Chọn Hình Ảnh Này</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import {
  Images,
  X,
  Grid,
  UploadCloud,
  Search,
  Check,
  Trash2,
  Copy,
  Loader2,
  Image as ImageIcon,
  Folder,
  FolderPlus,
  ChevronRight,
  ArrowLeft
} from 'lucide-vue-next'
import { api } from '../../services/api'
import { useAdminTheme } from '../../composables/useAdminTheme'

const emit = defineEmits(['close', 'select'])
const props = defineProps({
  currentUrl: {
    type: String,
    default: ''
  }
})

const { isDarkMode } = useAdminTheme()

const activeTab = ref('library') // 'library' | 'upload'
const currentFolder = ref('products')
const folderList = ref([])
const mediaList = ref([])
const isLoading = ref(true)
const searchQuery = ref('')
const selectedItem = ref(null)

const isCreatingFolder = ref(false)
const newFolderName = ref('')
const newFolderInputRef = ref(null)

const isDragging = ref(false)
const isUploading = ref(false)
const uploadError = ref('')
const fileInputRef = ref(null)
const copyStatus = ref('')

// Compute breadcrumb segments (excluding root 'products')
const breadcrumbParts = computed(() => {
  if (currentFolder.value === 'products') return []
  const parts = currentFolder.value.replace(/^products\/?/, '').split('/').filter(Boolean)
  let accumPath = 'products'
  return parts.map(part => {
    accumPath += '/' + part
    return {
      name: part,
      path: accumPath
    }
  })
})

const filteredMedia = computed(() => {
  if (!searchQuery.value.trim()) return mediaList.value
  const q = searchQuery.value.toLowerCase().trim()
  return mediaList.value.filter(m => m.name.toLowerCase().includes(q))
})

function formatSize(bytes) {
  if (!bytes) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i]
}

function selectItem(item) {
  selectedItem.value = item
}

function onImgError(e) {
  e.target.src = 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=400&q=80'
}

function copyUrl(url) {
  navigator.clipboard.writeText(url)
  copyStatus.value = '✓ Đã sao chép!'
  setTimeout(() => { copyStatus.value = '' }, 2000)
}

function triggerFileInput() {
  fileInputRef.value?.click()
}

async function loadFolderContent(folder = currentFolder.value) {
  isLoading.value = true
  try {
    const res = await api.getAdminMedia({ folder })
    currentFolder.value = res.current_folder || folder
    folderList.value = res.folders || []
    mediaList.value = res.data || []
    
    // Auto-select current image if matches
    if (props.currentUrl) {
      const match = mediaList.value.find(m => m.url === props.currentUrl)
      if (match) selectedItem.value = match
    }
  } catch (err) {
    console.error('Lỗi tải media', err)
  } finally {
    isLoading.value = false
  }
}

function navigateToFolder(folderPath) {
  loadFolderContent(folderPath)
}

function goBackFolder() {
  if (currentFolder.value === 'products') return
  const parent = currentFolder.value.substring(0, currentFolder.value.lastIndexOf('/')) || 'products'
  loadFolderContent(parent)
}

function startCreatingFolder() {
  isCreatingFolder.value = true
  newFolderName.value = ''
  nextTick(() => {
    newFolderInputRef.value?.focus()
  })
}

async function handleCreateFolder() {
  if (!newFolderName.value.trim()) return
  try {
    await api.createAdminMediaFolder({
      name: newFolderName.value.trim(),
      parent_folder: currentFolder.value
    })
    isCreatingFolder.value = false
    newFolderName.value = ''
    await loadFolderContent()
  } catch (err) {
    alert('Lỗi tạo thư mục: ' + err.message)
  }
}

async function handleDeleteFolder(folder) {
  if (!confirm(`Bạn có chắc muốn xóa thư mục "${folder.name}" và toàn bộ tệp bên trong?`)) return
  try {
    await api.deleteAdminMediaFolder(folder.path)
    await loadFolderContent()
  } catch (err) {
    alert('Lỗi xóa thư mục: ' + err.message)
  }
}

async function uploadFiles(files) {
  if (!files || files.length === 0) return
  uploadError.value = ''
  isUploading.value = true

  let lastUploaded = null
  let successCount = 0

  for (const file of files) {
    try {
      const formData = new FormData()
      formData.append('file', file)
      formData.append('folder', currentFolder.value)

      const res = await api.uploadAdminMedia(formData)
      if (res && res.data) {
        mediaList.value.unshift(res.data)
        lastUploaded = res.data
        successCount++
      }
    } catch (err) {
      console.error('Lỗi tải file', file.name, err)
      uploadError.value = err.data?.message || err.message || 'Lỗi tải ảnh lên'
    }
  }

  if (lastUploaded) {
    selectedItem.value = lastUploaded
    activeTab.value = 'library'
  }
  isUploading.value = false
}

function handleFileSelect(e) {
  const files = Array.from(e.target.files || [])
  if (files.length > 0) {
    uploadFiles(files)
  }
  if (e.target) e.target.value = ''
}

function handleDrop(e) {
  isDragging.value = false
  const files = Array.from(e.dataTransfer.files || []).filter(f => f.type.startsWith('image/'))
  if (files.length > 0) {
    uploadFiles(files)
  }
}

async function handleDeleteItem(item) {
  if (!confirm(`Bạn có chắc muốn xóa ảnh "${item.name}" khỏi thư viện?`)) return
  try {
    await api.deleteAdminMedia(item.path || item.url)
    mediaList.value = mediaList.value.filter(m => m.id !== item.id)
    if (selectedItem.value?.id === item.id) {
      selectedItem.value = null
    }
  } catch (err) {
    alert('Lỗi xóa ảnh: ' + err.message)
  }
}

function selectAndConfirm(item) {
  selectedItem.value = item
  handleConfirmSelection()
}

function handleConfirmSelection() {
  if (selectedItem.value) {
    emit('select', selectedItem.value.url, selectedItem.value)
    emit('close')
  }
}

onMounted(() => {
  loadFolderContent('products')
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(148, 163, 184, 0.2);
  border-radius: 4px;
}
</style>
