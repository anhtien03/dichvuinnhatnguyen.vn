<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
          Quản Lý Khách Hàng & CRM Zalo
        </h1>
        <p class="text-xs sm:text-sm text-slate-400 mt-1">
          Danh bạ đối tác, phân loại hạng khách và tự động nhắc nhở chăm sóc qua Zalo
        </p>
      </div>

      <div class="flex items-center gap-2.5">
        <button
          @click="openCreateModal"
          class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-brand-gold hover:bg-amber-400 text-slate-950 shadow-md shadow-brand-gold/20 transition-all cursor-pointer"
        >
          <UserPlus class="w-4 h-4" />
          <span>Thêm Khách Hàng Mới</span>
        </button>
      </div>
    </div>

    <!-- Mode Tabs: Danh bạ vs Chăm sóc tự động -->
    <div class="flex items-center gap-2 border-b pb-3" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
      <button
        @click="activeTab = 'directory'"
        class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer"
        :class="activeTab === 'directory' ? 'bg-brand-gold text-slate-950 shadow-sm' : (isDarkMode ? 'text-slate-400 hover:text-white' : 'text-slate-600 hover:text-slate-900')"
      >
        <Users class="w-4 h-4" />
        <span>Danh Bạ Khách Hàng</span>
      </button>

      <button
        @click="activeTab = 'care'"
        class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer"
        :class="activeTab === 'care' ? 'bg-brand-gold text-slate-950 shadow-sm' : (isDarkMode ? 'text-slate-400 hover:text-white' : 'text-slate-600 hover:text-slate-900')"
      >
        <HeartHandshake class="w-4 h-4" />
        <span>Chăm Sóc Khách Hàng Tự Động</span>
      </button>
    </div>

    <!-- TAB 1: DANH BẠ KHÁCH HÀNG -->
    <div v-if="activeTab === 'directory'" class="space-y-4">
      <!-- Search & Tag Filter -->
      <div class="p-4 rounded-2xl border flex flex-col sm:flex-row items-center gap-3" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
        <div class="relative flex-1 w-full">
          <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
          <input
            v-model="searchQuery"
            @keydown.enter="fetchCustomers"
            type="text"
            placeholder="Tìm theo tên khách, số điện thoại, công ty..."
            class="w-full text-xs rounded-xl pl-9 pr-3 py-2 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
            :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
          />
        </div>

        <div class="w-full sm:w-48">
          <select
            v-model="tagFilter"
            @change="fetchCustomers"
            class="w-full text-xs rounded-xl px-3 py-2 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
            :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
          >
            <option value="">Tất cả phân loại</option>
            <option value="vip">Khách VIP</option>
            <option value="bulk">Khách in nhiều</option>
            <option value="regular">Khách thông thường</option>
            <option value="demanding">Khách khó tính</option>
            <option value="care_needed">Cần chăm sóc gấp</option>
          </select>
        </div>

        <button
          @click="fetchCustomers"
          class="w-full sm:w-auto px-4 py-2 rounded-xl text-xs font-bold bg-brand-gold text-slate-950 hover:bg-amber-400"
        >
          Tìm
        </button>
      </div>

      <!-- Customers Table -->
      <div class="rounded-2xl border overflow-hidden" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
        <div v-if="isLoading" class="p-12 text-center text-slate-400 text-xs">
          Đang tải danh bạ khách hàng...
        </div>

        <div v-else-if="customers.length === 0" class="p-12 text-center text-slate-400 text-xs">
          Chưa có khách hàng nào.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="uppercase font-bold tracking-wider border-b" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-400' : 'bg-slate-50 border-slate-200 text-slate-500'">
              <tr>
                <th class="py-3 px-4">Tên Khách Hàng</th>
                <th class="py-3 px-4">Số Điện Thoại & Zalo</th>
                <th class="py-3 px-4">Công Ty / MST</th>
                <th class="py-3 px-4">Phân Loại</th>
                <th class="py-3 px-4 text-center">Tổng Đơn In</th>
                <th class="py-3 px-4 text-center">Thao Tác</th>
              </tr>
            </thead>
            <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
              <tr
                v-for="c in customers"
                :key="c.id"
                class="hover:bg-brand-gold/5 transition-colors"
                :class="isDarkMode ? 'text-slate-200' : 'text-slate-800'"
              >
                <td class="py-3.5 px-4 font-bold">
                  <div>{{ c.name }}</div>
                  <div class="text-[10px] text-slate-400 font-normal truncate max-w-xs">{{ c.address }}</div>
                </td>

                <td class="py-3.5 px-4 font-mono">
                  <div class="font-bold text-brand-gold">{{ c.phone }}</div>
                  <a
                    :href="`https://zalo.me/${cleanPhone(c.phone)}`"
                    target="_blank"
                    class="inline-flex items-center gap-1 text-[11px] text-blue-400 hover:underline mt-0.5"
                  >
                    <MessageSquare class="w-3 h-3" />
                    <span>Mở Zalo Chat</span>
                  </a>
                </td>

                <td class="py-3.5 px-4">
                  <div>{{ c.company_name || '-' }}</div>
                  <div class="text-[10px] text-slate-400">MST: {{ c.tax_code || '-' }}</div>
                </td>

                <td class="py-3.5 px-4">
                  <span
                    class="px-2.5 py-1 rounded-full text-[10px] font-bold border"
                    :class="getTagBadge(c.tag)"
                  >
                    {{ getTagLabel(c.tag) }}
                  </span>
                </td>

                <td class="py-3.5 px-4 text-center font-bold">
                  {{ c.orders_count || 0 }} đơn
                </td>

                <td class="py-3.5 px-4 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button
                      @click="editCustomer(c)"
                      class="p-1.5 rounded-lg border text-blue-400 hover:bg-blue-500/10"
                      :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
                      title="Sửa khách hàng"
                    >
                      <Edit3 class="w-4 h-4" />
                    </button>
                    <button
                      @click="deleteCustomer(c)"
                      class="p-1.5 rounded-lg border text-red-400 hover:bg-red-500/10"
                      :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
                      title="Xóa khách hàng"
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
    </div>

    <!-- TAB 2: CHĂM SÓC KHÁCH HÀNG TỰ ĐỘNG (DAILY CARE) -->
    <div v-else-if="activeTab === 'care'" class="space-y-4">
      <div class="p-4 rounded-2xl border flex items-center justify-between" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
        <div>
          <h2 class="text-sm font-bold">Danh sách khách hàng lâu chưa in ấn</h2>
          <p class="text-xs text-slate-400">Nhân viên kinh doanh chủ động nhắn tin Zalo chào thăm & báo giá ưu đãi</p>
        </div>

        <div class="flex items-center gap-2">
          <label class="text-xs text-slate-400">Thời gian chưa đặt:</label>
          <select
            v-model="careDays"
            @change="fetchCareList"
            class="text-xs rounded-xl px-3 py-1.5 border font-bold text-brand-gold"
            :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
          >
            <option :value="15">Trên 15 ngày</option>
            <option :value="30">Trên 30 ngày</option>
            <option :value="60">Trên 60 ngày</option>
          </select>
        </div>
      </div>

      <div class="rounded-2xl border overflow-hidden" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
        <div v-if="isCareLoading" class="p-12 text-center text-slate-400 text-xs">
          Đang quét dữ liệu chăm sóc khách hàng...
        </div>

        <div v-else-if="careList.length === 0" class="p-12 text-center text-slate-400 text-xs">
          Tuyệt vời! Tất cả khách hàng đều vừa mới đặt đơn trong vòng {{ careDays }} ngày qua.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="uppercase font-bold tracking-wider border-b" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-400' : 'bg-slate-50 border-slate-200 text-slate-500'">
              <tr>
                <th class="py-3 px-4">Khách Hàng</th>
                <th class="py-3 px-4">Số Điện Thoại</th>
                <th class="py-3 px-4">Đơn Cuối Cùng</th>
                <th class="py-3 px-4 text-right">Tổng Tiền Đã In</th>
                <th class="py-3 px-4 text-center">Hành Động Chăm Sóc</th>
              </tr>
            </thead>
            <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
              <tr
                v-for="item in careList"
                :key="item.id"
                class="hover:bg-brand-gold/5 transition-colors"
                :class="isDarkMode ? 'text-slate-200' : 'text-slate-800'"
              >
                <td class="py-3.5 px-4 font-bold">
                  {{ item.name }}
                  <div class="text-[10px] text-slate-400">{{ item.company_name }}</div>
                </td>

                <td class="py-3.5 px-4 font-mono font-bold text-brand-gold">
                  {{ item.phone }}
                </td>

                <td class="py-3.5 px-4">
                  <span v-if="item.last_order_date" class="text-amber-400 font-semibold">
                    {{ formatDate(item.last_order_date) }}
                  </span>
                  <span v-else class="text-slate-500 italic">
                    Chưa từng đặt đơn
                  </span>
                </td>

                <td class="py-3.5 px-4 text-right font-bold text-emerald-400">
                  {{ formatMoney(item.total_spent) }}
                </td>

                <td class="py-3.5 px-4 text-center">
                  <a
                    :href="`https://zalo.me/${cleanPhone(item.phone)}`"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black bg-blue-600 hover:bg-blue-500 text-white shadow-sm transition-all"
                  >
                    <MessageSquare class="w-3.5 h-3.5" />
                    <span>Nhắn Zalo Ngay</span>
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
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
          {{ isEditing ? 'Chỉnh Sửa Khách Hàng' : 'Thêm Mới Khách Hàng' }}
        </h3>

        <form @submit.prevent="submitCustomer" class="space-y-3">
          <div>
            <label class="block text-[11px] text-slate-400 mb-1">Tên Khách Hàng (*)</label>
            <input
              v-model="customerForm.name"
              type="text"
              required
              class="w-full text-xs rounded-xl px-3 py-2 border"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Số Điện Thoại (*)</label>
              <input
                v-model="customerForm.phone"
                type="text"
                required
                class="w-full text-xs rounded-xl px-3 py-2 border font-mono font-bold"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
              />
            </div>
            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Phân Loại</label>
              <select
                v-model="customerForm.tag"
                class="w-full text-xs rounded-xl px-3 py-2 border"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
              >
                <option value="vip">Khách VIP</option>
                <option value="bulk">Khách in nhiều</option>
                <option value="regular">Khách thông thường</option>
                <option value="demanding">Khách khó tính</option>
                <option value="care_needed">Cần chăm sóc gấp</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-[11px] text-slate-400 mb-1">Địa Chỉ</label>
            <input
              v-model="customerForm.address"
              type="text"
              class="w-full text-xs rounded-xl px-3 py-2 border"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Công Ty</label>
              <input
                v-model="customerForm.company_name"
                type="text"
                class="w-full text-xs rounded-xl px-3 py-2 border"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
              />
            </div>
            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Mã Số Thuế</label>
              <input
                v-model="customerForm.tax_code"
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
              Lưu Khách Hàng
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
  Users,
  UserPlus,
  HeartHandshake,
  Search,
  MessageSquare,
  Edit3,
  Trash2,
  X
} from 'lucide-vue-next'
import { api } from '../../../services/api'
import { useAdminTheme } from '../../../composables/useAdminTheme'

const { isDarkMode } = useAdminTheme()

const activeTab = ref('directory')
const customers = ref([])
const careList = ref([])
const isLoading = ref(false)
const isCareLoading = ref(false)
const searchQuery = ref('')
const tagFilter = ref('')
const careDays = ref(15)

// Modal State
const isModalOpen = ref(false)
const isEditing = ref(false)
const editingId = ref(null)
const customerForm = reactive({
  name: '',
  phone: '',
  address: '',
  company_name: '',
  tax_code: '',
  tag: 'regular'
})

async function fetchCustomers() {
  isLoading.value = true
  try {
    const params = {}
    if (searchQuery.value.trim()) params.search = searchQuery.value.trim()
    if (tagFilter.value) params.tag = tagFilter.value

    const res = await api.getErpCustomers(params)
    if (res.data) {
      customers.value = res.data.data || []
    }
  } catch (err) {
    console.error('Failed to load customers', err)
  } finally {
    isLoading.value = false
  }
}

async function fetchCareList() {
  isCareLoading.value = true
  try {
    const res = await api.getErpDailyCare(careDays.value)
    if (res.data) {
      careList.value = res.data
    }
  } catch (err) {
    console.error('Failed to load care list', err)
  } finally {
    isCareLoading.value = false
  }
}

function openCreateModal() {
  isEditing.value = false
  editingId.value = null
  customerForm.name = ''
  customerForm.phone = ''
  customerForm.address = ''
  customerForm.company_name = ''
  customerForm.tax_code = ''
  customerForm.tag = 'regular'
  isModalOpen.value = true
}

function editCustomer(c) {
  isEditing.value = true
  editingId.value = c.id
  customerForm.name = c.name
  customerForm.phone = c.phone
  customerForm.address = c.address || ''
  customerForm.company_name = c.company_name || ''
  customerForm.tax_code = c.tax_code || ''
  customerForm.tag = c.tag || 'regular'
  isModalOpen.value = true
}

async function submitCustomer() {
  try {
    if (isEditing.value) {
      await api.updateErpCustomer(editingId.value, customerForm)
    } else {
      await api.createErpCustomer(customerForm)
    }
    isModalOpen.value = false
    fetchCustomers()
  } catch (err) {
    alert(err.message || 'Lỗi khi lưu khách hàng')
  }
}

async function deleteCustomer(c) {
  if (confirm(`Bạn có chắc muốn xóa khách hàng [${c.name}]?`)) {
    try {
      await api.deleteErpCustomer(c.id)
      fetchCustomers()
    } catch (err) {
      alert(err.message || 'Lỗi khi xóa')
    }
  }
}

function cleanPhone(p) {
  return (p || '').replace(/\D/g, '')
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

function getTagLabel(tag) {
  switch (tag) {
    case 'vip': return 'Khách VIP'
    case 'bulk': return 'In nhiều'
    case 'demanding': return 'Khó tính'
    case 'care_needed': return 'Cần chăm sóc'
    default: return 'Thông thường'
  }
}

function getTagBadge(tag) {
  switch (tag) {
    case 'vip': return 'bg-amber-500/10 text-amber-400 border-amber-500/20'
    case 'bulk': return 'bg-blue-500/10 text-blue-400 border-blue-500/20'
    case 'demanding': return 'bg-red-500/10 text-red-400 border-red-500/20'
    case 'care_needed': return 'bg-purple-500/10 text-purple-400 border-purple-500/20'
    default: return 'bg-slate-500/10 text-slate-400 border-slate-500/20'
  }
}

onMounted(() => {
  fetchCustomers()
  fetchCareList()
})
</script>

