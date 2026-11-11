<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
          Quản Lý Ấn Phẩm In
        </h1>
        <p class="text-xs mt-1" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
          Danh sách toàn bộ ấn phẩm in ấn. Bấm nút Thêm Ấn Phẩm Mới để mở trang tạo dạng form dọc truyền thống.
        </p>
      </div>

      <!-- Action Button -> Chuyển sang trang Create Form dọc -->
      <router-link
        to="/he-thong-admin/products/create"
        class="self-start sm:self-auto px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-gold to-amber-500 hover:from-amber-400 hover:to-brand-gold text-slate-950 font-black text-xs shadow-md shadow-brand-gold/25 flex items-center gap-2 transition-all cursor-pointer"
      >
        <Plus class="w-4 h-4" />
        <span>+ Thêm Mới Ấn Phẩm</span>
      </router-link>
    </div>

    <!-- Filter & Search Bar -->
    <div
      class="rounded-2xl border p-4 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-sm"
      :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
    >
      <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
        <select
          v-model="selectedCategory"
          @change="loadProducts"
          class="w-full sm:w-56 rounded-xl px-3 py-2 text-xs font-semibold border cursor-pointer focus:outline-none focus:ring-1 focus:ring-brand-gold"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
        >
          <option value="">Tất cả danh mục in ({{ products.length }})</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.slug">
            {{ cat.name }}
          </option>
        </select>

        <span class="text-xs text-slate-400 font-semibold hidden md:inline">
          Tổng cộng: <strong :class="isDarkMode ? 'text-white' : 'text-slate-900'">{{ filteredProducts.length }}</strong> ấn phẩm
        </span>
      </div>

      <div class="relative w-full sm:w-72">
        <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Tìm theo tên ấn phẩm..."
          class="w-full text-xs rounded-xl pl-9 pr-3 py-2 border focus:outline-none focus:ring-1 focus:ring-brand-gold"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
        />
      </div>
    </div>

    <!-- Alert Thông báo -->
    <div v-if="alertMessage" class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs flex items-center justify-between font-bold">
      <div class="flex items-center gap-2">
        <CheckCircle2 class="w-4 h-4 shrink-0" />
        <span>{{ alertMessage }}</span>
      </div>
      <button @click="alertMessage = ''" class="text-slate-400 hover:text-white text-xs cursor-pointer">✕</button>
    </div>

    <!-- BẢNG DANH SÁCH TOÀN BỘ ẤN PHẨM (FULL-WIDTH, KHÔNG CHIA CỘT) -->
    <div
      class="rounded-2xl border shadow-sm overflow-hidden"
      :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
    >
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr
              class="border-b font-bold uppercase tracking-wider text-[11px]"
              :class="isDarkMode ? 'border-[#2E3A47] bg-[#1C2434]/40 text-slate-400' : 'border-slate-100 bg-slate-50/50 text-slate-500'"
            >
              <th class="py-3.5 px-4">Ảnh & Ấn Phẩm</th>
              <th class="py-3.5 px-4">Danh Mục</th>
              <th class="py-3.5 px-4">Giá Cơ Sở / ĐVT</th>
              <th class="py-3.5 px-4">Thời Gian & Tối Thiểu</th>
              <th class="py-3.5 px-4">Trạng Thái</th>
              <th class="py-3.5 px-4 text-right">Hành Động</th>
            </tr>
          </thead>
          <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
            <tr v-if="isLoading">
              <td colspan="6" class="py-12 text-center text-slate-400">
                <Loader2 class="w-6 h-6 animate-spin mx-auto text-brand-gold mb-2" />
                <span>Đang tải danh sách ấn phẩm...</span>
              </td>
            </tr>

            <tr v-else-if="filteredProducts.length === 0">
              <td colspan="6" class="py-14 text-center">
                <Package class="w-10 h-10 text-slate-400 mx-auto mb-2 opacity-50" />
                <p class="text-slate-400 font-medium">Không tìm thấy ấn phẩm nào phù hợp</p>
                <router-link
                  to="/he-thong-admin/products/create"
                  class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-gold text-slate-950 font-bold text-xs"
                >
                  <Plus class="w-4 h-4" />
                  <span>+ Thêm Ấn Phẩm Mới</span>
                </router-link>
              </td>
            </tr>

            <tr
              v-for="prod in filteredProducts"
              :key="prod.id"
              class="transition-colors"
              :class="isDarkMode ? 'hover:bg-[#1C2434]/50' : 'hover:bg-slate-50/80'"
            >
              <!-- Cột 1: Ảnh & Tên -->
              <td class="py-3 px-4">
                <div class="flex items-center gap-3">
                  <div
                    class="w-12 h-12 rounded-xl overflow-hidden border shrink-0 bg-slate-100 flex items-center justify-center"
                    :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
                  >
                    <img
                      v-if="prod.image"
                      :src="prod.image"
                      :alt="prod.name"
                      class="w-full h-full object-cover"
                      @error="e => e.target.style.display = 'none'"
                    />
                    <Package v-else class="w-5 h-5 text-slate-400" />
                  </div>
                  <div>
                    <div class="font-bold text-sm" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                      {{ prod.name }}
                    </div>
                    <div class="flex items-center gap-1.5 mt-1">
                      <span v-if="prod.tag" class="text-[10px] px-2 py-0.5 rounded-full bg-brand-gold/15 text-brand-gold font-bold">
                        {{ prod.tag }}
                      </span>
                      <span v-if="prod.is_popular" class="text-[10px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-500 font-bold">
                        ★ Bán Chạy
                      </span>
                    </div>
                  </div>
                </div>
              </td>

              <!-- Cột 2: Danh Mục -->
              <td class="py-3 px-4">
                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold" :class="isDarkMode ? 'bg-[#1C2434] text-slate-300' : 'bg-slate-100 text-slate-700'">
                  {{ prod.category?.name || 'Chưa phân loại' }}
                </span>
              </td>

              <!-- Cột 3: Giá cơ sở -->
              <td class="py-3 px-4">
                <div class="font-mono font-black text-brand-gold text-sm">
                  {{ formatCurrency(prod.base_price) }}đ
                </div>
                <div class="text-[11px] text-slate-400 mt-0.5">
                  / {{ prod.unit || 'cái' }}
                </div>
              </td>

              <!-- Cột 4: Thời gian & SL tối thiểu -->
              <td class="py-3 px-4">
                <div class="font-semibold" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">
                  ⏱ {{ prod.turnaround || '24h - 48h' }}
                </div>
                <div class="text-[11px] text-slate-400 mt-0.5">
                  Tối thiểu: {{ prod.min_quantity || 1 }} {{ prod.unit || 'cái' }}
                </div>
              </td>

              <!-- Cột 5: Trạng thái hiển thị -->
              <td class="py-3 px-4">
                <button
                  type="button"
                  @click="toggleActiveStatus(prod)"
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border cursor-pointer transition-colors"
                  :class="prod.is_active
                    ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 hover:bg-emerald-500/20'
                    : 'bg-slate-500/10 text-slate-400 border-slate-500/20 hover:bg-slate-500/20'"
                  :title="prod.is_active ? 'Bấm để ẩn khỏi website' : 'Bấm để hiển thị trên website'"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="prod.is_active ? 'bg-emerald-400' : 'bg-slate-400'"></span>
                  <span>{{ prod.is_active ? 'Đang hiển thị' : 'Đang ẩn' }}</span>
                </button>
              </td>

              <!-- Cột 6: Hành động [Sửa] [Xóa] -->
              <td class="py-3 px-4 text-right space-x-2 whitespace-nowrap">
                <!-- Nút Bắn Google Index -->
                <button
                  type="button"
                  @click="handleGoogleIndex(prod)"
                  :disabled="indexingLoadingId === prod.id"
                  class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold border transition-colors cursor-pointer disabled:opacity-40"
                  :class="isDarkMode
                    ? 'bg-amber-500/10 border-amber-500/20 text-amber-400 hover:bg-amber-500/20'
                    : 'bg-amber-50 border-amber-200 text-amber-700 hover:bg-amber-100'"
                  title="Gửi tín hiệu Google Indexing API (hoặc mở GSC)"
                >
                  <Loader2 v-if="indexingLoadingId === prod.id" class="w-3.5 h-3.5 animate-spin text-amber-500" />
                  <Zap v-else class="w-3.5 h-3.5 text-amber-500" />
                  <span class="hidden xl:inline">Index Google</span>
                </button>

                <!-- Nút Cấu Hình Bảng Giá -->
                <router-link
                  :to="`/he-thong-admin/pricing?product_id=${prod.id}`"
                  class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold border transition-colors cursor-pointer"
                  :class="isDarkMode
                    ? 'bg-blue-500/10 border-blue-500/20 text-blue-400 hover:bg-blue-500/20'
                    : 'bg-blue-50 border-blue-200 text-blue-700 hover:bg-blue-100'"
                  title="Cấu hình bảng giá bậc thang theo quy cách"
                >
                  <Calculator class="w-3.5 h-3.5 text-blue-400" />
                  <span class="hidden xl:inline">Bảng Giá</span>
                </router-link>

                <!-- Nút Sửa -> Chuyển sang form chỉnh sửa -->
                <router-link
                  :to="`/he-thong-admin/products/${prod.id}/edit`"
                  class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold border transition-colors cursor-pointer"
                  :class="isDarkMode
                    ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200 hover:text-white hover:bg-[#333A48]'
                    : 'bg-slate-100 border-slate-200 text-slate-700 hover:bg-slate-200'"
                >
                  <Edit class="w-3.5 h-3.5 text-brand-gold" />
                  <span>Sửa</span>
                </router-link>

                <!-- Nút Xóa -->
                <button
                  type="button"
                  @click="handleDelete(prod)"
                  class="inline-flex items-center gap-1 p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 cursor-pointer transition-colors"
                  title="Xóa ấn phẩm này"
                >
                  <Trash2 class="w-3.5 h-3.5" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  Plus,
  Search,
  Edit,
  Trash2,
  Package,
  Loader2,
  CheckCircle2,
  Zap,
  Calculator
} from 'lucide-vue-next'
import { api } from '../../services/api'
import { useAdminTheme } from '../../composables/useAdminTheme'

const { isDarkMode } = useAdminTheme()

const products = ref([])
const categories = ref([])
const isLoading = ref(true)
const selectedCategory = ref('')
const searchQuery = ref('')
const alertMessage = ref('')
const indexingLoadingId = ref(null)

async function handleGoogleIndex(prod) {
  indexingLoadingId.value = prod.id
  const url = `https://dichvuinnhatnguyen.vn/san-pham/${prod.slug}`
  try {
    const res = await api.publishGoogleIndexingUrl(url, 'URL_UPDATED')
    alert(`✅ ${res.message || 'Đã gửi yêu cầu Index tới Google thành công!'}\nURL: ${url}`)
  } catch (err) {
    const msg = err.response?.data?.message || err.message
    if (confirm(`⚠️ ${msg}\n\nBạn có muốn mở ngay Google Search Console để kiểm tra URL này không?`)) {
      window.open(`https://search.google.com/search-console/inspect?resource_id=https%3A%2F%2Fdichvuinnhatnguyen.vn%2F&id=${encodeURIComponent(url)}`, '_blank')
    }
  } finally {
    indexingLoadingId.value = null
  }
}

const filteredProducts = computed(() => {
  return products.value.filter(p => {
    const matchCat = !selectedCategory.value || (p.category && p.category.slug === selectedCategory.value)
    const matchSearch = !searchQuery.value.trim() ||
      p.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      (p.description && p.description.toLowerCase().includes(searchQuery.value.toLowerCase()))
    return matchCat && matchSearch
  })
})

function formatCurrency(val) {
  return new Intl.NumberFormat('vi-VN').format(val || 0)
}

async function loadCategories() {
  try {
    const res = await api.getCategories()
    categories.value = res.data || []
  } catch (err) {
    console.error('Lỗi tải danh mục', err)
  }
}

async function loadProducts() {
  isLoading.value = true
  try {
    const params = { all: 1 }
    if (selectedCategory.value) {
      params.category = selectedCategory.value
    }
    const res = await api.getProducts(params)
    products.value = res.data || []
  } catch (err) {
    console.error('Lỗi tải sản phẩm', err)
  } finally {
    isLoading.value = false
  }
}

async function toggleActiveStatus(product) {
  try {
    const newStatus = !product.is_active
    await api.updateAdminProduct(product.id, {
      ...product,
      is_active: newStatus
    })
    product.is_active = newStatus
    alertMessage.value = `Đã ${newStatus ? 'hiển thị' : 'ẩn'} ấn phẩm "${product.name}".`
  } catch (err) {
    alert('Lỗi cập nhật trạng thái: ' + err.message)
  }
}

async function handleDelete(product) {
  if (!confirm(`Bạn có chắc muốn xóa ấn phẩm "${product.name}"? Thao tác này không thể hoàn tác.`)) {
    return
  }

  try {
    await api.deleteAdminProduct(product.id)
    products.value = products.value.filter(p => p.id !== product.id)
    alertMessage.value = `Đã xóa ấn phẩm "${product.name}" thành công!`
  } catch (err) {
    alert('Không thể xóa ấn phẩm: ' + err.message)
  }
}

onMounted(async () => {
  await Promise.all([loadCategories(), loadProducts()])
})
</script>


