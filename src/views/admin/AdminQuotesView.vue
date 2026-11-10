<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
          Quản Lý Yêu Cầu Báo Giá
        </h1>
        <p class="text-xs mt-1" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
          Tiếp nhận đơn in từ khách hàng, kiểm tra file thiết kế, gửi báo giá và theo dõi tiến độ sản xuất.
        </p>
      </div>

      <button
        @click="loadQuotes"
        class="self-start sm:self-auto px-4 py-2.5 rounded-xl border text-xs font-bold flex items-center gap-2 transition-all cursor-pointer"
        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-300 hover:text-white' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50 shadow-sm'"
      >
        <RefreshCw class="w-3.5 h-3.5" :class="{ 'animate-spin': isLoading }" />
        <span>Làm mới danh sách</span>
      </button>
    </div>

    <!-- Filters & Search Bar (TailAdmin Card Style) -->
    <div
      class="rounded-2xl border p-4 sm:p-5 space-y-4 shadow-sm"
      :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
    >
      <!-- Status Tabs -->
      <div class="flex flex-wrap gap-2 border-b pb-4" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
        <button
          v-for="tab in statusTabs"
          :key="tab.value"
          @click="selectTab(tab.value)"
          class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer"
          :class="[
            selectedStatus === tab.value
              ? 'bg-brand-gold text-slate-950 shadow-sm'
              : (isDarkMode ? 'text-[#8A99AD] hover:bg-[#1C2434] hover:text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900')
          ]"
        >
          {{ tab.label }}
        </button>
      </div>

      <!-- Search Input -->
      <div class="relative">
        <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
        <input
          v-model="searchKeyword"
          @input="handleSearch"
          type="text"
          placeholder="Tìm kiếm theo mã đơn (NN-...), tên khách hàng, số điện thoại, tên ấn phẩm..."
          class="w-full text-xs rounded-xl pl-9 pr-4 py-2.5 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-slate-50 border-slate-200 text-slate-800 placeholder-slate-400'"
        />
      </div>
    </div>

    <!-- Quotes Table (TailAdmin Data Table Style) -->
    <div
      class="rounded-2xl border shadow-sm overflow-hidden"
      :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
    >
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr
              class="border-b text-[11px] font-bold uppercase tracking-wider"
              :class="isDarkMode ? 'bg-[#1C2434]/80 border-[#2E3A47] text-slate-400' : 'bg-slate-50 border-slate-100 text-slate-500'"
            >
              <th class="py-3.5 px-5">Mã Đơn / Ngày</th>
              <th class="py-3.5 px-5">Khách Hàng</th>
              <th class="py-3.5 px-5">Ấn Phẩm & SL</th>
              <th class="py-3.5 px-5">Quy Cách Khách Yêu Cầu</th>
              <th class="py-3.5 px-5">File Đính Kèm</th>
              <th class="py-3.5 px-5">Trạng Thái</th>
              <th class="py-3.5 px-5 text-right">Thao Tác</th>
            </tr>
          </thead>
          <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]/60' : 'divide-slate-100'">
            <tr v-if="isLoading">
              <td colspan="7" class="py-14 text-center text-slate-400">
                <Loader2 class="w-6 h-6 animate-spin mx-auto mb-2 text-brand-gold" />
                <span>Đang tải danh sách báo giá...</span>
              </td>
            </tr>
            <tr v-else-if="quotes.length === 0">
              <td colspan="7" class="py-14 text-center text-slate-400">
                Không tìm thấy yêu cầu báo giá nào phù hợp
              </td>
            </tr>
            <tr
              v-for="quote in quotes"
              :key="quote.id"
              class="transition-colors"
              :class="isDarkMode ? 'hover:bg-[#1C2434]/50' : 'hover:bg-slate-50/80'"
            >
              <!-- Code & Date -->
              <td class="py-4 px-5">
                <div class="font-mono font-bold text-brand-gold text-sm">{{ quote.code }}</div>
                <div class="text-[10px] text-slate-400 mt-0.5">{{ formatDate(quote.created_at) }}</div>
              </td>

              <!-- Customer Info with Avatar -->
              <td class="py-4 px-5">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-full bg-blue-500/10 text-blue-500 font-bold text-xs flex items-center justify-center shrink-0">
                    {{ getInitials(quote.customer_name) }}
                  </div>
                  <div>
                    <div class="font-bold text-sm" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                      {{ quote.customer_name }}
                    </div>
                    <div class="flex items-center gap-2 text-[11px] mt-0.5">
                      <span class="text-slate-400 font-medium">{{ quote.phone }}</span>
                      <a
                        :href="'https://zalo.me/' + cleanPhone(quote.phone)"
                        target="_blank"
                        class="text-[10px] text-blue-500 hover:underline font-bold bg-blue-500/10 px-1.5 py-0.2 rounded"
                      >
                        Zalo
                      </a>
                    </div>
                    <div v-if="quote.email" class="text-[10px] text-slate-400 truncate max-w-[140px]">{{ quote.email }}</div>
                    <div v-if="quote.company" class="text-[10px] text-slate-400">🏢 {{ quote.company }}</div>
                  </div>
                </div>
              </td>

              <!-- Product & Quantity -->
              <td class="py-4 px-5">
                <div class="font-bold max-w-[150px] truncate" :title="quote.product_name" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                  {{ quote.product_name || 'Yêu cầu quy cách riêng' }}
                </div>
                <div class="text-[11px] text-amber-500 font-semibold mt-0.5">
                  SL: {{ quote.quantity || 'Chưa xác định' }}
                </div>
                <div v-if="quote.quoted_price" class="text-[10px] text-emerald-500 font-mono font-bold mt-0.5">
                  Giá: {{ formatCurrency(quote.quoted_price) }}đ
                </div>
              </td>

              <!-- Specs Description -->
              <td class="py-4 px-5 max-w-[190px]">
                <p class="text-[11px] line-clamp-3 whitespace-pre-line" :class="isDarkMode ? 'text-slate-300' : 'text-slate-600'" :title="quote.specs_description">
                  {{ quote.specs_description || 'Không có mô tả thêm' }}
                </p>
                <div v-if="quote.admin_notes" class="mt-1 text-[10px] text-amber-400 bg-amber-500/10 p-1 rounded border border-amber-500/20">
                  <strong>Ghi chú:</strong> {{ quote.admin_notes }}
                </div>
              </td>

              <!-- File Download -->
              <td class="py-4 px-5">
                <div v-if="quote.file_path">
                  <a
                    :href="quote.file_path"
                    target="_blank"
                    download
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-blue-500/10 hover:bg-blue-500/20 text-blue-500 text-[11px] font-bold border border-blue-500/20 transition-colors"
                  >
                    <Download class="w-3.5 h-3.5" />
                    <span class="max-w-[90px] truncate">{{ quote.file_name || 'Tải file' }}</span>
                  </a>
                  <div v-if="quote.file_size" class="text-[10px] text-slate-400 mt-0.5">
                    {{ formatFileSize(quote.file_size) }}
                  </div>
                </div>
                <span v-else class="text-[11px] text-slate-400 italic">Không đính kèm</span>
              </td>

              <!-- Quick Status Change Select (TailAdmin Pill Style) -->
              <td class="py-4 px-5">
                <select
                  :value="quote.status"
                  @change="handleQuickStatusChange(quote, $event.target.value)"
                  class="text-xs rounded-lg px-2.5 py-1.5 font-bold border focus:outline-none focus:ring-1 focus:ring-brand-gold cursor-pointer"
                  :class="statusSelectClass(quote.status)"
                >
                  <option value="pending" class="bg-slate-900 text-amber-400">Chờ báo giá</option>
                  <option value="contacted" class="bg-slate-900 text-blue-400">Đang báo giá</option>
                  <option value="approved" class="bg-slate-900 text-emerald-400">Đã chốt đơn</option>
                  <option value="completed" class="bg-slate-900 text-purple-400">Hoàn thành</option>
                  <option value="cancelled" class="bg-slate-900 text-slate-400">Đã hủy</option>
                </select>
              </td>

              <!-- Actions -->
              <td class="py-4 px-5 text-right space-x-1.5 whitespace-nowrap">
                <button
                  @click="convertToOrder(quote)"
                  class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold bg-brand-gold text-slate-950 hover:bg-amber-400 transition-all shadow-sm inline-flex items-center gap-1 cursor-pointer"
                  title="Chuyển báo giá này thành đơn hàng sản xuất"
                >
                  <ShoppingBag class="w-3.5 h-3.5" />
                  <span>Lên Đơn</span>
                </button>
                <button
                  @click="openEditModal(quote)"
                  class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold border transition-colors cursor-pointer"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200 hover:text-white hover:bg-[#333A48]' : 'bg-slate-100 border-slate-200 text-slate-700 hover:bg-slate-200'"
                  title="Chi tiết & Báo giá"
                >
                  <Edit class="w-3.5 h-3.5 inline mr-1" />
                  <span>Chi tiết</span>
                </button>
                <button
                  @click="handleDelete(quote)"
                  class="p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-500/10 transition-colors cursor-pointer"
                  title="Xóa yêu cầu"
                >
                  <Trash2 class="w-3.5 h-3.5" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Edit & Quote Modal (TailAdmin Modal Style) -->
    <div
      v-if="editingQuote"
      class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4"
    >
      <div
        class="border rounded-2xl w-full max-w-xl p-6 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto"
        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-900'"
      >
        <div class="flex items-center justify-between border-b pb-3" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
          <div>
            <h3 class="text-base font-bold flex items-center gap-2">
              <span>Chi Tiết Báo Giá:</span>
              <span class="font-mono text-brand-gold">{{ editingQuote.code }}</span>
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Khách hàng: {{ editingQuote.customer_name }} - {{ editingQuote.phone }}</p>
          </div>
          <button @click="editingQuote = null" class="text-slate-400 hover:text-white p-1">
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Customer Summary Box -->
        <div
          class="p-4 rounded-xl border text-xs space-y-2"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
        >
          <div class="grid grid-cols-2 gap-2">
            <div><strong class="text-slate-400">Ấn phẩm:</strong> <span class="font-bold">{{ editingQuote.product_name || 'N/A' }}</span></div>
            <div><strong class="text-slate-400">Số lượng:</strong> <span class="text-amber-500 font-bold">{{ editingQuote.quantity || 'N/A' }}</span></div>
            <div><strong class="text-slate-400">Email:</strong> <span>{{ editingQuote.email || 'Không có' }}</span></div>
            <div><strong class="text-slate-400">Công ty:</strong> <span>{{ editingQuote.company || 'Cá nhân' }}</span></div>
          </div>
          <div class="pt-2 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
            <strong class="text-slate-400">Mô tả quy cách:</strong>
            <p class="mt-1 whitespace-pre-line">{{ editingQuote.specs_description || 'Không có' }}</p>
          </div>
          <div v-if="editingQuote.file_path" class="pt-2 border-t flex items-center justify-between" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
            <span class="text-slate-400">File thiết kế:</span>
            <a :href="editingQuote.file_path" target="_blank" download class="text-blue-500 hover:underline flex items-center gap-1 font-bold">
              <Download class="w-3.5 h-3.5" />
              <span>{{ editingQuote.file_name || 'Tải file in' }}</span>
            </a>
          </div>
        </div>

        <!-- Edit Form -->
        <div class="space-y-4 text-xs">
          <div>
            <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">Trạng Thái Tiến Độ</label>
            <select
              v-model="editForm.status"
              class="w-full rounded-xl px-3 py-2.5 border focus:outline-none focus:ring-1 focus:ring-brand-gold"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
            >
              <option value="pending">Chờ báo giá (Khách mới gửi)</option>
              <option value="contacted">Đang báo giá (Đã liên hệ Zalo / Điện thoại)</option>
              <option value="approved">Đã chốt đơn (Khách duyệt mẫu / cọc tiền)</option>
              <option value="completed">Hoàn thành (Đã in và giao thành công)</option>
              <option value="cancelled">Đã hủy</option>
            </select>
          </div>

          <div>
            <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">Số Tiền Báo Giá (VNĐ)</label>
            <input
              v-model.number="editForm.quoted_price"
              type="number"
              placeholder="Ví dụ: 1500000"
              class="w-full rounded-xl px-3 py-2.5 border focus:outline-none focus:ring-1 focus:ring-brand-gold"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
            />
          </div>

          <div>
            <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">Ghi Chú Nội Bộ (Xưởng)</label>
            <textarea
              v-model="editForm.admin_notes"
              rows="3"
              placeholder="Ví dụ: Đã báo giá 1.500.000đ qua Zalo. Khách hẹn thứ 5 chốt file in. Ghép bài offset đợt sáng."
              class="w-full rounded-xl px-3 py-2.5 border focus:outline-none focus:ring-1 focus:ring-brand-gold"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
            ></textarea>
          </div>
        </div>

        <!-- Modal Actions -->
        <div class="flex items-center justify-end gap-3 pt-3 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
          <button
            @click="editingQuote = null"
            class="px-4 py-2.5 rounded-xl border text-xs font-semibold transition-colors"
            :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-300 hover:text-white' : 'bg-slate-100 border-slate-200 text-slate-700 hover:bg-slate-200'"
          >
            Hủy Bỏ
          </button>
          <button
            @click="handleSaveQuote"
            :disabled="isSaving"
            class="px-5 py-2.5 rounded-xl bg-brand-gold hover:bg-amber-400 text-slate-950 text-xs font-black shadow-md shadow-brand-gold/20 flex items-center gap-2 transition-all cursor-pointer"
          >
            <Loader2 v-if="isSaving" class="w-3.5 h-3.5 animate-spin" />
            <span>{{ isSaving ? 'Đang lưu...' : 'Lưu Cập Nhật' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import {
  RefreshCw,
  Search,
  Download,
  Edit,
  Trash2,
  X,
  Loader2,
  ShoppingBag
} from 'lucide-vue-next'
import { api } from '../../services/api'
import { useAdminTheme } from '../../composables/useAdminTheme'

const router = useRouter()
const { isDarkMode } = useAdminTheme()

const quotes = ref([])
const isLoading = ref(false)
const selectedStatus = ref('all')
const searchKeyword = ref('')
const editingQuote = ref(null)
const isSaving = ref(false)

const editForm = ref({
  status: 'pending',
  quoted_price: null,
  admin_notes: ''
})

const statusTabs = [
  { value: 'all', label: 'Tất cả' },
  { value: 'pending', label: 'Chờ báo giá' },
  { value: 'contacted', label: 'Đang báo giá' },
  { value: 'approved', label: 'Đã chốt đơn' },
  { value: 'completed', label: 'Hoàn thành' },
  { value: 'cancelled', label: 'Đã hủy' }
]

function selectTab(val) {
  selectedStatus.value = val
  loadQuotes()
}

let searchTimer = null
function handleSearch() {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    loadQuotes()
  }, 350)
}

function cleanPhone(phone) {
  if (!phone) return ''
  return phone.replace(/[^0-9]/g, '')
}

function getInitials(name) {
  if (!name) return 'KH'
  const parts = name.trim().split(' ')
  if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase()
  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
}

function formatDate(iso) {
  if (!iso) return ''
  const d = new Date(iso)
  return `${d.getDate().toString().padStart(2, '0')}/${(d.getMonth() + 1).toString().padStart(2, '0')} ${d.getHours().toString().padStart(2, '0')}:${d.getMinutes().toString().padStart(2, '0')}`
}

function formatCurrency(val) {
  if (!val) return '0'
  return Number(val).toLocaleString('vi-VN')
}

function formatFileSize(bytes) {
  if (!bytes) return ''
  if (bytes < 1024) return bytes + ' B'
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB'
  return (bytes / (1024 * 1024)).toFixed(1) + ' MB'
}

function statusSelectClass(status) {
  switch (status) {
    case 'pending': return 'bg-amber-500/10 text-amber-500 border-amber-500/30'
    case 'contacted': return 'bg-blue-500/10 text-blue-500 border-blue-500/30'
    case 'approved': return 'bg-emerald-500/10 text-emerald-500 border-emerald-500/30'
    case 'completed': return 'bg-purple-500/10 text-purple-500 border-purple-500/30'
    default: return 'bg-slate-500/10 text-slate-400 border-slate-500/30'
  }
}

async function loadQuotes() {
  isLoading.value = true
  try {
    const params = {}
    if (selectedStatus.value !== 'all') {
      params.status = selectedStatus.value
    }
    if (searchKeyword.value.trim()) {
      params.search = searchKeyword.value.trim()
    }
    const res = await api.getAdminQuotes(params)
    quotes.value = res.data?.data || res.data || []
  } catch (err) {
    console.error('Failed to load quotes', err)
  } finally {
    isLoading.value = false
  }
}

async function handleQuickStatusChange(quote, newStatus) {
  try {
    await api.updateAdminQuote(quote.id, { status: newStatus })
    quote.status = newStatus
  } catch (err) {
    alert('Lỗi cập nhật trạng thái: ' + err.message)
    loadQuotes()
  }
}

function convertToOrder(quote) {
  sessionStorage.setItem('pending_order_from_quote', JSON.stringify({
    customer_name: quote.customer_name,
    customer_phone: quote.phone,
    customer_company: quote.company || '',
    product_name: quote.product_name,
    quantity: quote.quantity,
    unit_price: quote.quoted_price || 0,
    specifications: quote.specs_description,
    notes: `Khách gửi báo giá từ web mã ${quote.code}. ${quote.admin_notes || ''}`
  }))
  router.push('/he-thong-admin/orders/create?from_quote=1')
}

function openEditModal(quote) {
  editingQuote.value = quote
  editForm.value = {
    status: quote.status,
    quoted_price: quote.quoted_price,
    admin_notes: quote.admin_notes || ''
  }
}

async function handleSaveQuote() {
  if (!editingQuote.value) return
  isSaving.value = true
  try {
    const res = await api.updateAdminQuote(editingQuote.value.id, editForm.value)
    Object.assign(editingQuote.value, res.data)
    editingQuote.value = null
    loadQuotes()
  } catch (err) {
    alert('Lỗi khi lưu thông tin: ' + err.message)
  } finally {
    isSaving.value = false
  }
}

async function handleDelete(quote) {
  if (!confirm(`Bạn có chắc chắn muốn xóa đơn báo giá [${quote.code}] của khách ${quote.customer_name}?`)) {
    return
  }
  try {
    await api.deleteAdminQuote(quote.id)
    loadQuotes()
  } catch (err) {
    alert('Lỗi khi xóa: ' + err.message)
  }
}

onMounted(() => {
  loadQuotes()
})
</script>


