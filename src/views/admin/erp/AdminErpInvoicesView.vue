<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
          Quản Lý Hóa Đơn VAT (HĐĐT)
        </h1>
        <p class="text-xs sm:text-sm text-slate-400 mt-1">
          Theo dõi danh sách các đơn hàng có yêu cầu xuất hóa đơn GTGT 8% / 10% và cập nhật số HĐĐT
        </p>
      </div>

      <div class="flex items-center gap-2.5">
        <button
          @click="exportCSV"
          class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm border transition-all cursor-pointer"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-200 hover:bg-[#333A48]' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-100'"
        >
          <FileSpreadsheet class="w-4 h-4 text-emerald-400" />
          <span>Xuất File Cho Kế Toán</span>
        </button>
      </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center gap-2 border-b pb-3" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
      <button
        @click="selectStatus('')"
        class="px-4 py-2 rounded-xl text-xs font-bold transition-all"
        :class="statusFilter === '' ? 'bg-brand-gold text-slate-950 shadow-sm' : (isDarkMode ? 'text-slate-400 hover:text-white' : 'text-slate-600 hover:text-slate-900')"
      >
        Tất Cả Đơn VAT
      </button>

      <button
        @click="selectStatus('pending')"
        class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition-all"
        :class="statusFilter === 'pending' ? 'bg-amber-400 text-slate-950 shadow-sm' : (isDarkMode ? 'text-slate-400 hover:text-white' : 'text-slate-600 hover:text-slate-900')"
      >
        <Clock class="w-3.5 h-3.5" />
        <span>Chưa Xuất Hóa Đơn</span>
      </button>

      <button
        @click="selectStatus('issued')"
        class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition-all"
        :class="statusFilter === 'issued' ? 'bg-emerald-500 text-slate-950 shadow-sm' : (isDarkMode ? 'text-slate-400 hover:text-white' : 'text-slate-600 hover:text-slate-900')"
      >
        <CheckCircle2 class="w-3.5 h-3.5" />
        <span>Đã Xuất Hóa Đơn</span>
      </button>
    </div>

    <!-- Table -->
    <div class="rounded-2xl border overflow-hidden" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
      <div v-if="isLoading" class="p-12 text-center text-slate-400 text-xs">
        Đang tải danh sách hóa đơn...
      </div>

      <div v-else-if="orders.length === 0" class="p-12 text-center text-slate-400 text-xs">
        Không có đơn hàng nào yêu cầu xuất hóa đơn VAT trong mục này.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="uppercase font-bold tracking-wider border-b" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-400' : 'bg-slate-50 border-slate-200 text-slate-500'">
            <tr>
              <th class="py-3 px-4">Mã Đơn & Ngày</th>
              <th class="py-3 px-4">Đơn Vị Mua Hàng</th>
              <th class="py-3 px-4">Mã Số Thuế</th>
              <th class="py-3 px-4 text-right">Tiền Hàng</th>
              <th class="py-3 px-4 text-right">Tiền Thuế VAT</th>
              <th class="py-3 px-4 text-right">Tổng Thanh Toán</th>
              <th class="py-3 px-4">Trạng Thái & Số HĐĐT</th>
              <th class="py-3 px-4 text-center">Thao Tác</th>
            </tr>
          </thead>
          <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
            <tr
              v-for="order in orders"
              :key="order.id"
              class="hover:bg-brand-gold/5 transition-colors"
              :class="isDarkMode ? 'text-slate-200' : 'text-slate-800'"
            >
              <td class="py-3.5 px-4 font-mono">
                <div class="font-bold text-brand-gold">{{ order.code }}</div>
                <div class="text-[10px] text-slate-400">{{ formatDate(order.created_at) }}</div>
              </td>

              <td class="py-3.5 px-4 font-bold">
                <div>{{ order.customer_company || order.customer_name }}</div>
                <div class="text-[10px] text-slate-400 font-normal truncate max-w-xs">{{ order.customer_address }}</div>
              </td>

              <td class="py-3.5 px-4 font-mono font-bold">
                {{ order.customer_tax_code || 'Chưa cung cấp' }}
              </td>

              <td class="py-3.5 px-4 text-right font-semibold">
                {{ formatMoney(order.subtotal) }}
              </td>

              <td class="py-3.5 px-4 text-right font-semibold text-purple-400">
                {{ formatMoney(order.vat_amount) }} ({{ order.vat_rate }}%)
              </td>

              <td class="py-3.5 px-4 text-right font-black text-amber-400">
                {{ formatMoney(order.total_amount) }}
              </td>

              <td class="py-3.5 px-4">
                <div v-if="order.invoice_number" class="space-y-0.5">
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    Đã xuất
                  </span>
                  <div class="font-mono font-bold text-brand-gold text-[11px]">Số: {{ order.invoice_number }}</div>
                </div>
                <div v-else>
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    Chờ xuất HĐ
                  </span>
                </div>
              </td>

              <td class="py-3.5 px-4 text-center">
                <button
                  @click="openUpdateModal(order)"
                  class="px-3 py-1 rounded-lg text-[11px] font-bold bg-brand-gold/10 text-brand-gold border border-brand-gold/20 hover:bg-brand-gold hover:text-slate-950 transition-colors"
                >
                  Cập Nhật HĐĐT
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- UPDATE INVOICE MODAL -->
    <div
      v-if="isModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
    >
      <div
        class="relative w-full max-w-md rounded-3xl border shadow-2xl p-6 space-y-4"
        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
      >
        <button @click="isModalOpen = false" class="absolute top-4 right-4 p-1.5 text-slate-400 hover:text-white">
          <X class="w-5 h-5" />
        </button>

        <h3 class="text-base font-black text-brand-gold">
          Cập Nhật Số Hóa Đơn Điện Tử
        </h3>
        <p class="text-xs text-slate-400">
          Đơn hàng: <span class="font-bold text-white">{{ currentOrder?.code }}</span> - Cty: {{ currentOrder?.customer_company || currentOrder?.customer_name }}
        </p>

        <form @submit.prevent="submitInvoiceUpdate" class="space-y-3">
          <div>
            <label class="block text-[11px] text-slate-400 mb-1">Mã Số Thuế (*)</label>
            <input
              v-model="invoiceForm.customer_tax_code"
              type="text"
              class="w-full text-xs rounded-xl px-3 py-2 border font-mono"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
            />
          </div>

          <div>
            <label class="block text-[11px] text-slate-400 mb-1">Số Hóa Đơn Điện Tử (HĐĐT)</label>
            <input
              v-model="invoiceForm.invoice_number"
              type="text"
              placeholder="VD: 0001234..."
              class="w-full text-xs rounded-xl px-3 py-2 border font-mono font-bold text-brand-gold"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
            />
          </div>

          <div>
            <label class="block text-[11px] text-slate-400 mb-1">Ngày Ký Hóa Đơn</label>
            <input
              v-model="invoiceForm.invoice_date"
              type="date"
              class="w-full text-xs rounded-xl px-3 py-2 border"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
            />
          </div>

          <div class="pt-2 flex justify-end gap-2">
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
              class="px-5 py-2 rounded-xl text-xs font-bold bg-brand-gold text-slate-950 hover:bg-amber-400"
            >
              Lưu Thông Tin
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
  FileSpreadsheet,
  Clock,
  CheckCircle2,
  X
} from 'lucide-vue-next'
import { api } from '../../../services/api'
import { useAdminTheme } from '../../../composables/useAdminTheme'

const { isDarkMode } = useAdminTheme()

const orders = ref([])
const isLoading = ref(false)
const statusFilter = ref('')

const isModalOpen = ref(false)
const currentOrder = ref(null)
const invoiceForm = reactive({
  customer_tax_code: '',
  invoice_number: '',
  invoice_date: ''
})

async function fetchInvoices() {
  isLoading.value = true
  try {
    const params = { is_vat_required: 1 }
    if (statusFilter.value) params.invoice_status = statusFilter.value

    const res = await api.getErpOrders(params)
    if (res.data) {
      orders.value = res.data.data || []
    }
  } catch (err) {
    console.error('Failed to load invoices', err)
  } finally {
    isLoading.value = false
  }
}

function selectStatus(stg) {
  statusFilter.value = stg
  fetchInvoices()
}

function openUpdateModal(order) {
  currentOrder.value = order
  invoiceForm.customer_tax_code = order.customer_tax_code || ''
  invoiceForm.invoice_number = order.invoice_number || ''
  invoiceForm.invoice_date = order.invoice_date ? order.invoice_date.substring(0, 10) : new Date().toISOString().substring(0, 10)
  isModalOpen.value = true
}

async function submitInvoiceUpdate() {
  if (!currentOrder.value) return
  try {
    const payload = {
      customer_tax_code: invoiceForm.customer_tax_code,
      invoice_number: invoiceForm.invoice_number,
      invoice_date: invoiceForm.invoice_date,
      invoice_status: invoiceForm.invoice_number ? 'issued' : 'pending'
    }
    await api.updateErpOrder(currentOrder.value.id, payload)
    isModalOpen.value = false
    fetchInvoices()
  } catch (err) {
    alert(err.message || 'Lỗi khi cập nhật')
  }
}

function exportCSV() {
  if (orders.value.length === 0) {
    alert('Không có dữ liệu để xuất')
    return
  }
  const headers = ['Mã Đơn', 'Khách Hàng', 'MST', 'Tiền Hàng', 'Thuế VAT', 'Tổng Tiền', 'Số HĐĐT', 'Ngày Xuất']
  const rows = orders.value.map(o => [
    o.code,
    `"${(o.customer_company || o.customer_name || '').replace(/"/g, '""')}"`,
    `"${o.customer_tax_code || ''}"`,
    o.subtotal,
    o.vat_amount,
    o.total_amount,
    `"${o.invoice_number || ''}"`,
    o.invoice_date || ''
  ])

  const csvContent = '\uFEFF' + [headers.join(','), ...rows.map(r => r.join(','))].join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `danh_sach_hoa_don_vat_${new Date().toISOString().substring(0, 10)}.csv`
  a.click()
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

onMounted(() => {
  fetchInvoices()
})
</script>

