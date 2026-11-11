<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
          Quản Lý Xưởng In & Công Nợ Nhà Cung Cấp
        </h1>
        <p class="text-xs sm:text-sm text-slate-400 mt-1">
          Danh bạ đối tác gia công, quản lý công nợ gối đầu và lịch sử thanh toán tiền xưởng
        </p>
      </div>

      <div class="flex items-center gap-2.5">
        <button
          @click="openCreateModal"
          class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-brand-gold hover:bg-amber-400 text-slate-950 shadow-md shadow-brand-gold/20 transition-all cursor-pointer"
        >
          <Building2 class="w-4 h-4" />
          <span>Thêm Xưởng / Đối Tác Mới</span>
        </button>
      </div>
    </div>

    <!-- Search Bar -->
    <div class="p-4 rounded-2xl border flex items-center gap-3" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
      <div class="relative flex-1">
        <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
        <input
          v-model="searchQuery"
          @keydown.enter="fetchSuppliers"
          type="text"
          placeholder="Tìm tên xưởng in, số điện thoại, dịch vụ (in bạt, offset, cán màng)..."
          class="w-full text-xs rounded-xl pl-9 pr-3 py-2 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
        />
      </div>

      <button
        @click="fetchSuppliers"
        class="px-4 py-2 rounded-xl text-xs font-bold bg-brand-gold text-slate-950 hover:bg-amber-400"
      >
        Tìm
      </button>
    </div>

    <!-- Suppliers Table -->
    <div class="rounded-2xl border overflow-hidden" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
      <div v-if="isLoading" class="p-12 text-center text-slate-400 text-xs">
        Đang tải danh sách xưởng in...
      </div>

      <div v-else-if="suppliers.length === 0" class="p-12 text-center text-slate-400 text-xs">
        Chưa có xưởng in / nhà cung cấp nào.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="uppercase font-bold tracking-wider border-b" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-400' : 'bg-slate-50 border-slate-200 text-slate-500'">
            <tr>
              <th class="py-3 px-4">Tên Xưởng / Đối Tác</th>
              <th class="py-3 px-4">Số Điện Thoại</th>
              <th class="py-3 px-4">Dịch Vụ Năng Lực</th>
              <th class="py-3 px-4">Tài Khoản Ngân Hàng</th>
              <th class="py-3 px-4 text-right">Tổng Tiền Đặt</th>
              <th class="py-3 px-4 text-right">Đã Trả Xưởng</th>
              <th class="py-3 px-4 text-right">Công Nợ Tồn</th>
              <th class="py-3 px-4 text-center">Thao Tác</th>
            </tr>
          </thead>
          <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
            <tr
              v-for="s in suppliers"
              :key="s.id"
              class="hover:bg-brand-gold/5 transition-colors"
              :class="isDarkMode ? 'text-slate-200' : 'text-slate-800'"
            >
              <td class="py-3.5 px-4 font-bold">
                <div>{{ s.name }}</div>
                <div class="text-[10px] text-slate-400 font-normal truncate max-w-xs">{{ s.address }}</div>
              </td>

              <td class="py-3.5 px-4 font-mono font-bold text-brand-gold">
                {{ s.phone || '-' }}
              </td>

              <td class="py-3.5 px-4 max-w-xs">
                <span class="px-2 py-0.5 rounded text-[11px] bg-blue-500/10 text-blue-400 border border-blue-500/20">
                  {{ s.services || 'In ấn tổng hợp' }}
                </span>
              </td>

              <td class="py-3.5 px-4">
                <div class="font-bold">{{ s.bank_name || '-' }}</div>
                <div class="text-[10px] text-slate-400 font-mono">{{ s.bank_account }} ({{ s.bank_owner }})</div>
              </td>

              <td class="py-3.5 px-4 text-right font-semibold">
                {{ formatMoney(s.total_cost) }}
              </td>

              <td class="py-3.5 px-4 text-right text-emerald-400 font-semibold">
                {{ formatMoney(s.total_paid) }}
              </td>

              <td class="py-3.5 px-4 text-right font-black" :class="(s.balance || 0) > 0 ? 'text-red-400' : 'text-slate-400'">
                {{ formatMoney(s.balance) }}
              </td>

              <td class="py-3.5 px-4 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button
                    @click="openPaymentModal(s)"
                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500 hover:text-slate-950 transition-colors"
                    title="Trả tiền xưởng"
                  >
                    Trả Nợ
                  </button>

                  <button
                    @click="editSupplier(s)"
                    class="p-1.5 rounded-lg border text-blue-400 hover:bg-blue-500/10"
                    :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
                    title="Chỉnh sửa xưởng"
                  >
                    <Edit3 class="w-4 h-4" />
                  </button>

                  <button
                    @click="deleteSupplier(s)"
                    class="p-1.5 rounded-lg border text-red-400 hover:bg-red-500/10"
                    :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
                    title="Xóa xưởng"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- CREATE / EDIT MODAL -->
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

        <h3 class="text-base font-black text-brand-gold">
          {{ isEditing ? 'Chỉnh Sửa Xưởng In' : 'Thêm Xưởng In / Đối Tác Mới' }}
        </h3>

        <form @submit.prevent="submitSupplier" class="space-y-3">
          <div>
            <label class="block text-[11px] text-slate-400 mb-1">Tên Xưởng In / Đối Tác (*)</label>
            <input
              v-model="supplierForm.name"
              type="text"
              required
              placeholder="Xưởng in bạt Tiến Phát, Xưởng bế hộp Hưng Thịnh..."
              class="w-full text-xs rounded-xl px-3 py-2 border"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Số Điện Thoại</label>
              <input
                v-model="supplierForm.phone"
                type="text"
                class="w-full text-xs rounded-xl px-3 py-2 border"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
              />
            </div>
            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Dịch Vụ Gia Công Cung Cấp</label>
              <input
                v-model="supplierForm.services"
                type="text"
                placeholder="In bạt, in offset, cán màng..."
                class="w-full text-xs rounded-xl px-3 py-2 border"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
              />
            </div>
          </div>

          <div>
            <label class="block text-[11px] text-slate-400 mb-1">Địa Chỉ Xưởng</label>
            <input
              v-model="supplierForm.address"
              type="text"
              class="w-full text-xs rounded-xl px-3 py-2 border"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
            />
          </div>

          <div class="grid grid-cols-3 gap-3">
            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Ngân Hàng</label>
              <input
                v-model="supplierForm.bank_name"
                type="text"
                placeholder="Vietcombank, MB..."
                class="w-full text-xs rounded-xl px-3 py-2 border"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
              />
            </div>
            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Số Tài Khoản</label>
              <input
                v-model="supplierForm.bank_account"
                type="text"
                class="w-full text-xs rounded-xl px-3 py-2 border font-mono"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
              />
            </div>
            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Chủ Tài Khoản</label>
              <input
                v-model="supplierForm.bank_owner"
                type="text"
                class="w-full text-xs rounded-xl px-3 py-2 border"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
              />
            </div>
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
              class="px-5 py-2 rounded-xl text-xs font-bold bg-brand-gold text-slate-950 hover:bg-amber-400"
            >
              Lưu Xưởng In
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- PAY SUPPLIER MODAL -->
    <div
      v-if="isPaymentModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
    >
      <div
        class="relative w-full max-w-md rounded-3xl border shadow-2xl p-6 space-y-4"
        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
      >
        <button @click="isPaymentModalOpen = false" class="absolute top-4 right-4 p-1.5 text-slate-400 hover:text-white">
          <X class="w-5 h-5" />
        </button>

        <h3 class="text-base font-black text-brand-gold">
          Thanh Toán Tiền Xưởng
        </h3>
        <p class="text-xs text-slate-400">
          Đối tác: <span class="font-bold text-white">{{ currentSupplier?.name }}</span> - Nợ hiện tại: <span class="font-bold text-red-400">{{ formatMoney(currentSupplier?.balance) }}</span>
        </p>

        <form @submit.prevent="submitPayment" class="space-y-3">
          <div>
            <label class="block text-[11px] text-slate-400 mb-1">Số Tiền Trả Xưởng (VNĐ) (*)</label>
            <input
              v-model.number="paymentForm.amount"
              type="number"
              min="1"
              required
              class="w-full text-sm font-bold rounded-xl px-3 py-2 border text-right text-emerald-400"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
            />
          </div>

          <div>
            <label class="block text-[11px] text-slate-400 mb-1">Tài Khoản Chi Tiền</label>
            <select
              v-model="paymentForm.payment_method"
              class="w-full text-xs rounded-xl px-3 py-2 border"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
            >
              <option value="bank_acb">ACB (51189927 - CTY IN ẤN NHẬT NGUYÊN)</option>
              <option value="bank_mb">MB Bank (679838699 - HUYNH THANH TRUC)</option>
              <option value="bank_minh_nguyen">TK MINH NGUYEN</option>
              <option value="bank_truong_hung">TK TRUONG HUNG</option>
              <option value="cash">Tiền mặt</option>
            </select>
          </div>

          <div>
            <label class="block text-[11px] text-slate-400 mb-1">Ghi Chú Đợt Trả Nợ</label>
            <input
              v-model="paymentForm.note"
              type="text"
              placeholder="Thanh toán đợt in tháng này..."
              class="w-full text-xs rounded-xl px-3 py-2 border"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
            />
          </div>

          <div class="pt-2 flex justify-end gap-2">
            <button
              type="button"
              @click="isPaymentModalOpen = false"
              class="px-4 py-2 rounded-xl text-xs border"
              :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
            >
              Hủy
            </button>
            <button
              type="submit"
              class="px-5 py-2 rounded-xl text-xs font-bold bg-brand-gold text-slate-950 hover:bg-amber-400"
            >
              Xác Nhận Đã Chuyển Tiền
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
  Building2,
  Search,
  Edit3,
  Trash2,
  X
} from 'lucide-vue-next'
import { api } from '../../../services/api'
import { useAdminTheme } from '../../../composables/useAdminTheme'

const { isDarkMode } = useAdminTheme()

const suppliers = ref([])
const isLoading = ref(false)
const searchQuery = ref('')

const isModalOpen = ref(false)
const isEditing = ref(false)
const editingId = ref(null)
const supplierForm = reactive({
  name: '',
  phone: '',
  services: '',
  address: '',
  bank_name: '',
  bank_account: '',
  bank_owner: ''
})

const isPaymentModalOpen = ref(false)
const currentSupplier = ref(null)
const paymentForm = reactive({
  amount: 0,
  payment_method: 'bank_acb',
  note: ''
})

async function fetchSuppliers() {
  isLoading.value = true
  try {
    const params = {}
    if (searchQuery.value.trim()) params.search = searchQuery.value.trim()

    const res = await api.getErpSuppliers(params)
    if (res.data) {
      suppliers.value = res.data
    }
  } catch (err) {
    console.error('Failed to load suppliers', err)
  } finally {
    isLoading.value = false
  }
}

function openCreateModal() {
  isEditing.value = false
  editingId.value = null
  supplierForm.name = ''
  supplierForm.phone = ''
  supplierForm.services = ''
  supplierForm.address = ''
  supplierForm.bank_name = ''
  supplierForm.bank_account = ''
  supplierForm.bank_owner = ''
  isModalOpen.value = true
}

function editSupplier(s) {
  isEditing.value = true
  editingId.value = s.id
  supplierForm.name = s.name
  supplierForm.phone = s.phone || ''
  supplierForm.services = s.services || ''
  supplierForm.address = s.address || ''
  supplierForm.bank_name = s.bank_name || ''
  supplierForm.bank_account = s.bank_account || ''
  supplierForm.bank_owner = s.bank_owner || ''
  isModalOpen.value = true
}

async function submitSupplier() {
  try {
    if (isEditing.value) {
      await api.updateErpSupplier(editingId.value, supplierForm)
    } else {
      await api.createErpSupplier(supplierForm)
    }
    isModalOpen.value = false
    fetchSuppliers()
  } catch (err) {
    alert(err.message || 'Lỗi khi lưu xưởng')
  }
}

async function deleteSupplier(s) {
  if (confirm(`Bạn có chắc muốn xóa xưởng [${s.name}]?`)) {
    try {
      await api.deleteErpSupplier(s.id)
      fetchSuppliers()
    } catch (err) {
      alert(err.message || 'Lỗi khi xóa')
    }
  }
}

function openPaymentModal(s) {
  currentSupplier.value = s
  paymentForm.amount = Math.max(0, s.balance || 0)
  paymentForm.payment_method = 'bank_acb'
  paymentForm.note = 'Thanh toán tiền xưởng'
  isPaymentModalOpen.value = true
}

async function submitPayment() {
  if (!currentSupplier.value || paymentForm.amount <= 0) return
  try {
    await api.payErpSupplier(currentSupplier.value.id, paymentForm)
    isPaymentModalOpen.value = false
    fetchSuppliers()
  } catch (err) {
    alert(err.message || 'Lỗi khi thanh toán')
  }
}

function formatMoney(num) {
  if (!num) return '0 ₫'
  return new Intl.NumberFormat('vi-VN').format(Math.round(num)) + ' ₫'
}

onMounted(() => {
  fetchSuppliers()
})
</script>

