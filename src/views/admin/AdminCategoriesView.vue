<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
          Quản Lý Danh Mục Nhóm In
        </h1>
        <p class="text-xs mt-1" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
          Quản lý các nhóm danh mục ấn phẩm, hình ảnh đại diện và SEO hiển thị trên Mega Menu, trang chủ và catalog.
        </p>
      </div>

      <router-link
        to="/he-thong-admin/categories/create"
        class="self-start sm:self-auto px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-gold to-amber-500 hover:from-amber-400 hover:to-brand-gold text-slate-950 font-black text-xs shadow-md shadow-brand-gold/25 flex items-center gap-2 transition-all cursor-pointer transform hover:-translate-y-0.5 active:translate-y-0"
      >
        <Plus class="w-4 h-4" />
        <span>+ Thêm Danh Mục Mới</span>
      </router-link>
    </div>

    <!-- Alert Thông báo -->
    <div v-if="alertMessage" class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs flex items-center justify-between font-bold">
      <div class="flex items-center gap-2">
        <CheckCircle2 class="w-4 h-4 shrink-0" />
        <span>{{ alertMessage }}</span>
      </div>
      <button @click="alertMessage = ''" class="text-slate-400 hover:text-white text-xs cursor-pointer">✕</button>
    </div>

    <!-- Categories Table -->
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
              <th class="py-3.5 px-4">Tên Danh Mục</th>
              <th class="py-3.5 px-4">Đường Dẫn SEO (Slug)</th>
              <th class="py-3.5 px-4">Mô Tả Danh Mục</th>
              <th class="py-3.5 px-4">Huy Hiệu (Badge)</th>
              <th class="py-3.5 px-4 text-center">Số Ấn Phẩm</th>
              <th class="py-3.5 px-4 text-right">Hành Động</th>
            </tr>
          </thead>
          <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
            <tr v-if="isLoading">
              <td colspan="6" class="py-12 text-center text-slate-400">
                <Loader2 class="w-6 h-6 animate-spin mx-auto text-brand-gold mb-2" />
                <span>Đang tải danh mục...</span>
              </td>
            </tr>

            <tr v-else-if="categories.length === 0">
              <td colspan="6" class="py-12 text-center text-slate-400">
                Chưa có danh mục nào. Bấm nút Thêm Danh Mục Mới để tạo.
              </td>
            </tr>

            <tr
              v-for="cat in categories"
              :key="cat.id"
              class="transition-colors"
              :class="isDarkMode ? 'hover:bg-[#1C2434]/50' : 'hover:bg-slate-50/80'"
            >
              <!-- Cột 1: Tên & Hình ảnh (nếu có) -->
              <td class="py-3.5 px-4">
                <div class="flex items-center gap-3">
                  <!-- Image Thumbnail chỉ hiển thị nếu danh mục đã được cài ảnh -->
                  <div
                    v-if="cat.image"
                    class="w-11 h-11 rounded-xl overflow-hidden border shrink-0 bg-slate-100 relative shadow-2xs group"
                    :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
                  >
                    <img :src="cat.image" :alt="cat.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform" />
                  </div>

                  <div>
                    <router-link
                      :to="'/he-thong-admin/categories/' + cat.id + '/edit'"
                      class="font-bold text-sm hover:text-brand-gold transition-colors block"
                      :class="isDarkMode ? 'text-white' : 'text-slate-900'"
                    >
                      {{ cat.name }}
                    </router-link>
                    <div v-if="cat.meta_title" class="text-[10px] text-emerald-400 font-mono mt-0.5">
                      ✓ Đã cấu hình SEO
                    </div>
                  </div>
                </div>
              </td>

              <!-- Cột 2: Đường dẫn -->
              <td class="py-3.5 px-4">
                <a
                  :href="'/danh-muc/' + cat.slug"
                  target="_blank"
                  class="font-mono text-slate-400 hover:text-brand-gold flex items-center gap-1"
                >
                  <span>/danh-muc/{{ cat.slug }}</span>
                  <ExternalLink class="w-3 h-3 opacity-60" />
                </a>
              </td>

              <!-- Cột 3: Mô tả -->
              <td class="py-3.5 px-4 max-w-[240px]">
                <p class="text-slate-400 line-clamp-2">
                  {{ cat.description || 'Chưa có mô tả' }}
                </p>
              </td>

              <!-- Cột 4: Huy hiệu -->
              <td class="py-3.5 px-4">
                <span v-if="cat.badge" class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-500 border border-amber-500/20">
                  {{ cat.badge }}
                </span>
                <span v-else class="text-slate-500">-</span>
              </td>

              <!-- Cột 5: Số ấn phẩm -->
              <td class="py-3.5 px-4 text-center font-bold font-mono text-sm" :class="isDarkMode ? 'text-white' : 'text-slate-800'">
                {{ cat.products_count ?? 0 }}
              </td>

              <!-- Cột 6: Hành động -->
              <td class="py-3.5 px-4 text-right space-x-1.5 whitespace-nowrap">
                <router-link
                  :to="'/he-thong-admin/categories/' + cat.id + '/edit'"
                  class="px-2.5 py-1.5 rounded-lg border font-bold text-xs cursor-pointer transition-colors inline-flex items-center"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200 hover:text-white hover:bg-[#333A48]' : 'bg-slate-100 border-slate-200 text-slate-700 hover:bg-slate-200'"
                >
                  <Edit class="w-3.5 h-3.5 mr-1 text-brand-gold" />
                  <span>Sửa</span>
                </router-link>
                <button
                  type="button"
                  @click="handleDelete(cat)"
                  class="p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-500/10 cursor-pointer"
                  title="Xóa danh mục"
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
import { ref, onMounted } from 'vue'
import {
  Plus,
  Edit,
  Trash2,
  ExternalLink,
  CheckCircle2,
  Loader2
} from 'lucide-vue-next'
import { api } from '../../services/api'
import { useAdminTheme } from '../../composables/useAdminTheme'

const { isDarkMode } = useAdminTheme()

const categories = ref([])
const isLoading = ref(true)
const alertMessage = ref('')

async function loadCategories() {
  isLoading.value = true
  try {
    const res = await api.getCategories()
    categories.value = res.data || []
  } catch (err) {
    console.error('Lỗi tải danh mục', err)
  } finally {
    isLoading.value = false
  }
}

async function handleDelete(cat) {
  if (!confirm(`Bạn có chắc muốn xóa danh mục "${cat.name}"?`)) return
  try {
    await api.deleteAdminCategory(cat.id)
    alertMessage.value = `Đã xóa danh mục "${cat.name}" thành công!`
    await loadCategories()
  } catch (err) {
    alert('Lỗi xóa danh mục: ' + err.message)
  }
}

onMounted(() => {
  loadCategories()
})
</script>


