<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
          Sổ Quỹ Thu Chi Nội Bộ
        </h1>
        <p class="text-xs sm:text-sm text-slate-400 mt-1">
          Ghi nhận dòng tiền thực tế, phiếu thu, phiếu chi và liên kết khấu trừ lợi nhuận theo đơn hàng
        </p>
      </div>

      <div class="flex items-center gap-2.5">
        <button
          @click="openCreateModal('receipt')"
          class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-md shadow-emerald-500/20 transition-all cursor-pointer"
        >
          <Plus class="w-4 h-4" />
          <span>Lập Phiếu Thu</span>
        </button>

        <button
          @click="openCreateModal('payment')"
          class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-rose-500 hover:bg-rose-400 text-white shadow-md shadow-rose-500/20 transition-all cursor-pointer"
        >
          <Minus class="w-4 h-4" />
          <span>Lập Phiếu Chi</span>
        </button>
      </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="p-5 rounded-2xl border" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
        <div class="flex items-center justify-between text-xs text-slate-400 font-bold uppercase">
          <span>Tổng Thu Trong Kỳ</span>
          <ArrowDownLeft class="w-4 h-4 text-emerald-400" />
        </div>
        <div class="text-2xl font-black text-emerald-400 mt-2">
          {{ formatMoney(stats.total_receipts) }}
        </div>
      </div>

      <div class="p-5 rounded-2xl border" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
        <div class="flex items-center justify-between text-xs text-slate-400 font-bold uppercase">
          <span>Tổng Chi Trong Kỳ</span>
          <ArrowUpRight class="w-4 h-4 text-rose-400" />
        </div>
        <div class="text-2xl font-black text-rose-400 mt-2">
          {{ formatMoney(stats.total_payments) }}
        </div>
      </div>

      <div class="p-5 rounded-2xl border" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
        <div class="flex items-center justify-between text-xs text-slate-400 font-bold uppercase">
          <span>Dòng Tiền Tồn Ròng</span>
          <Wallet class="w-4 h-4 text-brand-gold" />
        </div>
        <div class="text-2xl font-black mt-2" :class="stats.net_balance >= 0 ? 'text-brand-gold' : 'text-red-400'">
          {{ formatMoney(stats.net_balance) }}
        </div>
      </div>
    </div>

    <!-- Filters Row -->
    <div class="p-4 rounded-2xl border grid grid-cols-1 sm:grid-cols-12 gap-3" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
      <div class="sm:col-span-3">
        <select
          v-model="typeFilter"
          @change="fetchEntries"
          class="w-full text-xs rounded-xl px-3 py-2 border"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
        >
          <option value="">Tất cả loại phiếu</option>
          <option value="receipt">Phiếu Thu (Tiền vào)</option>
          <option value="payment">Phiếu Chi (Tiền ra)</option>
        </select>
      </div>

      <div class="sm:col-span-3">
        <select
          v-model="categoryFilter"
          @change="fetchEntries"
          class="w-full text-xs rounded-xl px-3 py-2 border"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
        >
          <option value="">Tất cả danh mục chi</option>
          <option value="shipping">Tiền Ship / Vận chuyển</option>
          <option value="materials">Vật tư & Giấy mực xưởng</option>
          <option value="commission">Hoa hồng môi giới</option>
          <option value="office">Mặt bằng, Điện nước, Văn phòng</option>
          <option value="meal">Tiếp khách & Cơm trưa</option>
          <option value="advance">Rút lợi nhuận / Tạm ứng</option>
          <option value="other">Khoản chi khác</option>
        </select>
      </div>

      <div class="sm:col-span-4 relative">
        <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
        <input
          v-model="searchQuery"
          @keydown.enter="fetchEntries"
          type="text"
          placeholder="Tìm mã phiếu, người nhận, nội dung..."
          class="w-full text-xs rounded-xl pl-9 pr-3 py-2 border"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
        />
      </div>

      <div class="sm:col-span-2 flex justify-end">
        <button
          @click="fetchEntries"
          class="w-full px-4 py-2 rounded-xl text-xs font-bold bg-brand-gold text-slate-950 hover:bg-amber-400"
        >
          Lọc Dữ Liệu
        </button>
      </div>
    </div>

    <!-- Cashbook Entries Table -->
    <div class="rounded-2xl border overflow-hidden" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
      <div v-if="isLoading" class="p-12 text-center text-slate-400 text-xs">
        Đang tải sổ quỹ thu chi...
      </div>

      <div v-else-if="entries.length === 0" class="p-12 text-center text-slate-400 text-xs">
        Chưa có phiếu thu chi nào trong kỳ.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="uppercase font-bold tracking-wider border-b" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-400' : 'bg-slate-50 border-slate-200 text-slate-500'">
            <tr>
              <th class="py-3 px-4">Mã Phiếu & Ngày</th>
              <th class="py-3 px-4">Loại Phiếu</th>
              <th class="py-3 px-4">Nội Dung Chi Tiết</th>
              <th class="py-3 px-4">Người Nộp / Nhận</th>
              <th class="py-3 px-4">Tài Khoản / Quỹ</th>
              <th class="py-3 px-4 text-right">Số Tiền (VNĐ)</th>
              <th class="py-3 px-4 text-center">Thao Tác</th>
            </tr>
          </thead>
          <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
            <tr
              v-for="entry in entries"
              :key="entry.id"
              class="hover:bg-brand-gold/5 transition-colors"
              :class="isDarkMode ? 'text-slate-200' : 'text-slate-800'"
            >
              <td class="py-3.5 px-4 font-mono">
                <div class="font-bold text-brand-gold">{{ entry.code }}</div>
                <div class="text-[10px] text-slate-400">{{ formatDate(entry.entry_date) }}</div>
              </td>

              <td class="py-3.5 px-4">
                <span
                  class="px-2.5 py-1 rounded-full text-[10px] font-black border"
                  :class="entry.type === 'receipt' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border-rose-500/20'"
                >
                  {{ entry.type === 'receipt' ? 'Thu Tiền' : 'Chi Tiền' }}
                </span>
                <div class="text-[10px] text-slate-400 mt-1">{{ getCategoryLabel(entry.category) }}</div>
              </td>

              <td class="py-3.5 px-4 max-w-xs">
                <div class="font-semibold">{{ entry.description }}</div>
                <div v-if="entry.order" class="text-[10px] text-brand-gold mt-0.5">
                  Gắn đơn: {{ entry.order.code }}
                </div>
              </td>

              <td class="py-3.5 px-4">
                <div>{{ entry.payer_recipient || '-' }}</div>
                <div class="text-[10px] text-slate-400 font-bold">Nguồn: {{ getFundingOwnerLabel(entry.funding_owner) }}</div>
              </td>

              <td class="py-3.5 px-4">
                <span class="font-semibold">{{ getPaymentMethodLabel(entry.payment_method) }}</span>
              </td>

              <td class="py-3.5 px-4 text-right font-black" :class="entry.type === 'receipt' ? 'text-emerald-400' : 'text-rose-400'">
                {{ entry.type === 'receipt' ? '+' : '-' }}{{ formatMoney(entry.amount) }}
              </td>

              <td class="py-3.5 px-4 text-center">
                <button
                  @click="deleteEntry(entry)"
                  class="p-1.5 rounded-lg border text-red-400 hover:bg-red-500/10"
                  :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
                  title="Xóa phiếu"
                >
                  <Trash2 class="w-4 h-4" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- CREATE MODAL -->
    <div
      v-if="isModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
    >
      <div
        class="relative w-full max-w-lg rounded-3xl border shadow-2xl p-6 space-y-4"
        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
      >
        <button @click="isModalOpen = false" class="absolute top-4 right-4 p-1.5 text-slate-400 hover:text-white">
          <X class="w-5 h-5" />
        </button>

        <h3 class="text-base font-black" :class="entryForm.type === 'receipt' ? 'text-emerald-400' : 'text-rose-400'">
          {{ entryForm.type === 'receipt' ? 'Lập Phiếu Thu Tiền Mới' : 'Lập Phiếu Chi Tiền Mới' }}
        </h3>

        <form @submit.prevent="submitEntry" class="space-y-3">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Số Tiền (VNĐ) (*)</label>
              <input
                v-model.number="entryForm.amount"
                type="number"
                min="1"
                required
                class="w-full text-sm font-bold rounded-xl px-3 py-2 border text-right text-brand-gold"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
              />
            </div>
            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Ngày Lập Phiếu (*)</label>
              <input
                v-model="entryForm.entry_date"
                type="date"
                required
                class="w-full text-xs rounded-xl px-3 py-2 border"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Danh Mục Chi Tiêu (*)</label>
              <select
                v-model="entryForm.category"
                class="w-full text-xs rounded-xl px-3 py-2 border"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
              >
                <option value="shipping">Tiền Ship / Vận chuyển</option>
                <option value="materials">Vật tư & Giấy mực xưởng</option>
                <option value="commission">Hoa hồng môi giới</option>
                <option value="office">Mặt bằng, Điện nước, Văn phòng</option>
                <option value="meal">Tiếp khách & Cơm trưa</option>
                <option value="advance">Rút lợi nhuận / Tạm ứng</option>
                <option value="other">Khoản khác</option>
              </select>
            </div>

            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Tài Khoản / Phương Thức</label>
              <select
                v-model="entryForm.payment_method"
                class="w-full text-xs rounded-xl px-3 py-2 border"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
              >
                <option value="cash">Tiền mặt</option>
                <option value="bank_acb">ACB (51189927 - CTY IN ẤN NHẬT NGUYÊN)</option>
                <option value="bank_mb">MB Bank (679838699 - NGUYEN TRUONG HUNG)</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Người Nộp / Nhận Tiền</label>
              <input
                v-model="entryForm.payer_recipient"
                type="text"
                placeholder="Shipper, Thợ in, Anh Hưng..."
                class="w-full text-xs rounded-xl px-3 py-2 border"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
              />
            </div>

            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Nguồn Vốn / Người Chi</label>
              <select
                v-model="entryForm.funding_owner"
                class="w-full text-xs rounded-xl px-3 py-2 border"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
              >
                <option value="company">Tiền quỹ Công Ty</option>
                <option value="truong_hung">Trường Hưng (Tiền túi ứng)</option>
                <option value="minh_nguyen">Minh Nguyên (Tiền túi ứng)</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-[11px] text-slate-400 mb-1">Mô Tả Nội Dung Chi Tiết (*)</label>
            <textarea
              v-model="entryForm.description"
              rows="2"
              required
              placeholder="Ghi rõ mục đích chi tiêu hoặc nguồn thu..."
              class="w-full text-xs rounded-xl px-3 py-2 border"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
            ></textarea>
          </div>

          <div class="pt-3 flex justify-end gap-2 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <button
              type="button"
              @click="isModalOpen = false"
              class="px-4 py-2 rounded-xl text-xs border"
              :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
            >
              Hủy
            </button>
            <button
              type="submit"
              class="px-5 py-2 rounded-xl text-xs font-bold text-slate-950"
              :class="entryForm.type === 'receipt' ? 'bg-emerald-400 hover:bg-emerald-300' : 'bg-rose-400 hover:bg-rose-300 text-white'"
            >
              Lưu Phiếu
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import {
  Plus,
  Minus,
  Wallet,
  ArrowDownLeft,
  ArrowUpRight,
  Search,
  Trash2,
  X
} from 'lucide-vue-next'
import { api } from '../../../services/api'
import { useAdminTheme } from '../../../composables/useAdminTheme'

const { isDarkMode } = useAdminTheme()

const entries = ref([])
const stats = reactive({
  total_receipts: 0,
  total_payments: 0,
  net_balance: 0
})

const isLoading = ref(false)
const typeFilter = ref('')
const categoryFilter = ref('')
const searchQuery = ref('')

const isModalOpen = ref(false)
const entryForm = reactive({
  type: 'payment',
  category: 'shipping',
  amount: 0,
  payment_method: 'cash',
  funding_owner: 'company',
  payer_recipient: '',
  description: '',
  entry_date: new Date().toISOString().substring(0, 10)
})

async function fetchEntries() {
  isLoading.value = true
  try {
    const params = {}
    if (typeFilter.value) params.type = typeFilter.value
    if (categoryFilter.value) params.category = categoryFilter.value
    if (searchQuery.value.trim()) params.search = searchQuery.value.trim()

    const res = await api.getErpCashbook(params)
    if (res.data) {
      entries.value = res.data.data || []
    }
  } catch (err) {
    console.error('Failed to load cashbook', err)
  } finally {
    isLoading.value = false
  }
}

async function fetchStats() {
  try {
    const res = await api.getErpCashbookStats()
    if (res.data) {
      Object.assign(stats, res.data)
    }
  } catch (err) {
    console.error('Failed to load cashbook stats', err)
  }
}

function openCreateModal(t) {
  entryForm.type = t
  entryForm.amount = 0
  entryForm.category = t === 'receipt' ? 'other' : 'shipping'
  entryForm.payment_method = 'cash'
  entryForm.funding_owner = 'company'
  entryForm.payer_recipient = ''
  entryForm.description = ''
  entryForm.entry_date = new Date().toISOString().substring(0, 10)
  isModalOpen.value = true
}

async function submitEntry() {
  try {
    await api.createErpCashbook(entryForm)
    isModalOpen.value = false
    fetchEntries()
    fetchStats()
  } catch (err) {
    alert(err.message || 'Lỗi khi lưu phiếu')
  }
}

async function deleteEntry(entry) {
  if (confirm(`Bạn có chắc muốn xóa phiếu [${entry.code}]?`)) {
    try {
      await api.deleteErpCashbook(entry.id)
      fetchEntries()
      fetchStats()
    } catch (err) {
      alert(err.message || 'Lỗi khi xóa')
    }
  }
}

function formatMoney(num) {
  if (!num) return '0 ₫'
  return new Intl.NumberFormat('vi-VN').format(Math.round(num)) + ' ₫'
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return `${d.getDate().toString().padStart(2, '0')}/${(d.getMonth() + 1).toString().padStart(2, '0')}/${d.getFullYear()}`
}

function getCategoryLabel(cat) {
  switch (cat) {
    case 'shipping': return 'Vận chuyển'
    case 'materials': return 'Vật tư xưởng'
    case 'commission': return 'Hoa hồng'
    case 'office': return 'Mặt bằng / Điện nước'
    case 'meal': return 'Tiếp khách / Cơm'
    case 'advance': return 'Tạm ứng / Rút lãi'
    default: return 'Khác'
  }
}

function getPaymentMethodLabel(m) {
  switch (m) {
    case 'bank_acb': return 'ACB (51189927)'
    case 'bank_mb': return 'MB Bank (679838699)'
    default: return 'Tiền mặt'
  }
}

function getFundingOwnerLabel(owner) {
  switch (owner) {
    case 'truong_hung': return 'Trường Hưng ứng'
    case 'minh_nguyen': return 'Minh Nguyên ứng'
    default: return 'Công Ty'
  }
}

onMounted(() => {
  fetchEntries()
  fetchStats()
})
</script>

