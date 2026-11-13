<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
          Tài Chính & Phân Chia Lợi Nhuận 50/50
        </h1>
        <p class="text-xs sm:text-sm text-slate-400 mt-1">
          Quyết toán lợi nhuận ròng giữa Trường Hưng & Minh Nguyên và theo dõi doanh thu - chi phí
        </p>
      </div>

      <!-- Date Filter -->
      <div class="flex items-center gap-2">
        <input
          v-model="fromDate"
          type="date"
          class="text-xs rounded-xl px-3 py-2 border"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-white border-slate-200'"
        />
        <span class="text-xs text-slate-400">đến</span>
        <input
          v-model="toDate"
          type="date"
          class="text-xs rounded-xl px-3 py-2 border"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-white border-slate-200'"
        />
        <button
          @click="fetchProfitSharing"
          class="px-4 py-2 rounded-xl text-xs font-bold bg-brand-gold text-slate-950 hover:bg-amber-400 transition-all cursor-pointer"
        >
          Quyết Toán
        </button>
      </div>
    </div>

    <!-- Overall Financial KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
      <div class="p-5 rounded-2xl border" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
        <div class="text-[11px] font-bold uppercase text-slate-400">Tổng Doanh Thu Đơn Hàng</div>
        <div class="text-xl sm:text-2xl font-black text-amber-400 mt-2">{{ formatMoney(financeData.total_revenue) }}</div>
      </div>

      <div class="p-5 rounded-2xl border" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
        <div class="text-[11px] font-bold uppercase text-slate-400">Tổng Vốn Xưởng In</div>
        <div class="text-xl sm:text-2xl font-black text-slate-300 mt-2">{{ formatMoney(financeData.total_cost) }}</div>
      </div>

      <div class="p-5 rounded-2xl border" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
        <div class="text-[11px] font-bold uppercase text-slate-400">Chi Phí Chung Ngoài Đơn</div>
        <div class="text-xl sm:text-2xl font-black text-rose-400 mt-2">{{ formatMoney(financeData.unlinked_expenses) }}</div>
      </div>

      <div class="p-5 rounded-2xl border" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
        <div class="text-[11px] font-bold uppercase text-slate-400">Lợi Nhuận Ròng Thực Tế</div>
        <div class="text-xl sm:text-2xl font-black text-emerald-400 mt-2">{{ formatMoney(financeData.net_profit) }}</div>
      </div>
    </div>

    <!-- 50/50 Profit Split Cards for Both Founders -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Partner 1: Trường Hưng -->
      <div class="p-6 rounded-3xl border space-y-4 relative overflow-hidden" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200 shadow-sm'">
        <div class="flex items-center justify-between pb-3 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-cyan-400 text-white font-black text-lg flex items-center justify-center shadow-lg shadow-blue-500/20">
              TH
            </div>
            <div>
              <h2 class="text-base font-black">Trường Hưng</h2>
              <div class="text-xs text-slate-400">Sáng Lập Viên - Tỷ Lệ 50%</div>
            </div>
          </div>
          <span class="px-3 py-1 rounded-full text-xs font-black bg-blue-500/10 text-blue-400 border border-blue-500/20">
            50% Lợi Nhuận
          </span>
        </div>

        <div class="space-y-2.5 text-xs">
          <div class="flex justify-between py-1 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <span class="text-slate-400">1. Phần chia 50% lợi nhuận ròng:</span>
            <span class="font-bold text-emerald-400">+{{ formatMoney(partners.truong_hung?.share_50) }}</span>
          </div>

          <div class="flex justify-between py-1 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <span class="text-slate-400">2. Tiền túi tự ứng trước chi cho cty:</span>
            <span class="font-bold text-blue-400">+{{ formatMoney(partners.truong_hung?.advances) }}</span>
          </div>

          <div class="flex justify-between py-1 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <span class="text-slate-400">3. Tiền đã tạm ứng / rút trước trong kỳ:</span>
            <span class="font-bold text-rose-400">-{{ formatMoney(partners.truong_hung?.withdrawals) }}</span>
          </div>
        </div>

        <div class="pt-3 p-4 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-between">
          <div class="text-xs font-bold uppercase tracking-wider text-blue-300">
            Thực Nhận Kỳ Này:
          </div>
          <div class="text-xl sm:text-2xl font-black text-brand-gold">
            {{ formatMoney(partners.truong_hung?.final_payout) }}
          </div>
        </div>
      </div>

      <!-- Partner 2: Minh Nguyên -->
      <div class="p-6 rounded-3xl border space-y-4 relative overflow-hidden" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200 shadow-sm'">
        <div class="flex items-center justify-between pb-3 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-gold to-amber-500 text-slate-950 font-black text-lg flex items-center justify-center shadow-lg shadow-brand-gold/20">
              MN
            </div>
            <div>
              <h2 class="text-base font-black">Minh Nguyên</h2>
              <div class="text-xs text-slate-400">Sáng Lập Viên - Tỷ Lệ 50%</div>
            </div>
          </div>
          <span class="px-3 py-1 rounded-full text-xs font-black bg-brand-gold/10 text-brand-gold border border-brand-gold/20">
            50% Lợi Nhuận
          </span>
        </div>

        <div class="space-y-2.5 text-xs">
          <div class="flex justify-between py-1 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <span class="text-slate-400">1. Phần chia 50% lợi nhuận ròng:</span>
            <span class="font-bold text-emerald-400">+{{ formatMoney(partners.minh_nguyen?.share_50) }}</span>
          </div>

          <div class="flex justify-between py-1 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <span class="text-slate-400">2. Tiền túi tự ứng trước chi cho cty:</span>
            <span class="font-bold text-blue-400">+{{ formatMoney(partners.minh_nguyen?.advances) }}</span>
          </div>

          <div class="flex justify-between py-1 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <span class="text-slate-400">3. Tiền đã tạm ứng / rút trước trong kỳ:</span>
            <span class="font-bold text-rose-400">-{{ formatMoney(partners.minh_nguyen?.withdrawals) }}</span>
          </div>
        </div>

        <div class="pt-3 p-4 rounded-2xl bg-brand-gold/10 border border-brand-gold/20 flex items-center justify-between">
          <div class="text-xs font-bold uppercase tracking-wider text-amber-300">
            Thực Nhận Kỳ Này:
          </div>
          <div class="text-xl sm:text-2xl font-black text-brand-gold">
            {{ formatMoney(partners.minh_nguyen?.final_payout) }}
          </div>
        </div>
      </div>
    </div>

    <!-- Per-account Breakdown -->
    <AccountBreakdownPanel
      :accounts="accountStats"
      :from-date="fromDate"
      :to-date="toDate"
    />

    <!-- 12-Month Performance Chart Table -->
    <div class="p-6 rounded-3xl border space-y-4" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-sm sm:text-base font-black">Lịch Sử Doanh Thu & Lợi Nhuận 12 Tháng Gần Nhất</h2>
          <p class="text-xs text-slate-400">Số liệu tăng trưởng từ các đơn hàng thực tế</p>
        </div>
        <TrendingUp class="w-5 h-5 text-brand-gold" />
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="uppercase font-bold tracking-wider border-b" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-400' : 'bg-slate-50 border-slate-200 text-slate-500'">
            <tr>
              <th class="py-3 px-4">Tháng</th>
              <th class="py-3 px-4 text-right">Doanh Thu</th>
              <th class="py-3 px-4 text-right">Giá Vốn Xưởng</th>
              <th class="py-3 px-4 text-right">Lợi Nhuận Gộp</th>
              <th class="py-3 px-4 text-right">Tỷ Suất Lợi Nhuận</th>
            </tr>
          </thead>
          <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
            <tr
              v-for="(row, idx) in chartData"
              :key="idx"
              class="hover:bg-brand-gold/5 transition-colors"
              :class="isDarkMode ? 'text-slate-200' : 'text-slate-800'"
            >
              <td class="py-3.5 px-4 font-bold text-brand-gold">{{ row.month }}</td>
              <td class="py-3.5 px-4 text-right font-semibold">{{ formatMoney(row.revenue) }}</td>
              <td class="py-3.5 px-4 text-right text-slate-400">{{ formatMoney(row.cost) }}</td>
              <td class="py-3.5 px-4 text-right font-black text-emerald-400">{{ formatMoney(row.profit) }}</td>
              <td class="py-3.5 px-4 text-right font-bold text-amber-400">
                {{ row.revenue > 0 ? ((row.profit / row.revenue) * 100).toFixed(1) + '%' : '0%' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import {
  TrendingUp,
  DollarSign
} from 'lucide-vue-next'
import { api } from '../../../services/api'
import { useAdminTheme } from '../../../composables/useAdminTheme'
import AccountBreakdownPanel from '../../../components/admin/erp/AccountBreakdownPanel.vue'

const { isDarkMode } = useAdminTheme()

const now = new Date()
const fromDate = ref(new Date(now.getFullYear(), now.getMonth(), 1).toISOString().substring(0, 10))
const toDate = ref(new Date(now.getFullYear(), now.getMonth() + 1, 0).toISOString().substring(0, 10))

const financeData = reactive({
  total_revenue: 0,
  total_cost: 0,
  order_profit: 0,
  unlinked_expenses: 0,
  net_profit: 0
})

const partners = reactive({
  truong_hung: null,
  minh_nguyen: null
})

const chartData = ref([])
const accountStats = ref([])

async function fetchProfitSharing() {
  try {
    const [profitRes, cashbookRes] = await Promise.all([
      api.getErpProfitSharing({
        from_date: fromDate.value,
        to_date: toDate.value
      }),
      api.getErpCashbookStats({
        from_date: fromDate.value,
        to_date: toDate.value
      })
    ])
    if (profitRes.data) {
      Object.assign(financeData, profitRes.data)
      if (profitRes.data.partners) {
        partners.truong_hung = profitRes.data.partners.truong_hung
        partners.minh_nguyen = profitRes.data.partners.minh_nguyen
      }
    }
    if (cashbookRes.data?.accounts) {
      accountStats.value = cashbookRes.data.accounts
    }
  } catch (err) {
    console.error('Failed to load profit sharing', err)
  }
}

async function fetchDashboard() {
  try {
    const res = await api.getErpDashboard()
    if (res.data && res.data.chart_data) {
      chartData.value = res.data.chart_data
    }
  } catch (err) {
    console.error('Failed to load dashboard overview', err)
  }
}

function formatMoney(num) {
  if (!num) return '0 ₫'
  return new Intl.NumberFormat('vi-VN').format(Math.round(num)) + ' ₫'
}

onMounted(() => {
  fetchProfitSharing()
  fetchDashboard()
})
</script>

