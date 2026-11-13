<template>
  <div class="rounded-2xl border p-5 space-y-4" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200 shadow-sm'">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
      <div>
        <h2 class="text-sm sm:text-base font-black tracking-tight flex items-center gap-2" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
          <Landmark class="w-4 h-4 text-brand-gold" />
          <span>Thống Kê Thu / Chi Theo Từng Tài Khoản</span>
        </h2>
        <p class="text-[11px] text-slate-400 mt-0.5">
          Tổng hợp thực tế: Đơn hàng (+ Thu) + Phiếu thu (+) − Trả xưởng (− Chi) − Phiếu chi (−). Bấm vào tài khoản để xem sao kê chi tiết.
        </p>
      </div>
      <div v-if="fromDate && toDate" class="text-[11px] font-semibold px-3 py-1 rounded-lg border self-start sm:self-auto" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-600'">
        Kỳ: {{ formatDateShort(fromDate) }} → {{ formatDateShort(toDate) }}
      </div>
    </div>

    <!-- Account Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
      <button
        v-for="acc in visibleAccounts"
        :key="acc.payment_method"
        type="button"
        @click="openLedger(acc)"
        class="text-left p-4 rounded-xl border transition-all cursor-pointer group hover:border-brand-gold/60"
        :class="isDarkMode ? 'bg-[#24303F]/60 border-[#2E3A47] hover:bg-[#24303F]' : 'bg-slate-50/70 border-slate-200 hover:bg-amber-50/30'"
      >
        <div class="flex items-center justify-between gap-2 mb-3">
          <div class="flex items-center gap-2 min-w-0">
            <span
              class="w-2.5 h-2.5 rounded-full shrink-0"
              :class="dotClass(acc.payment_method)"
            ></span>
            <span class="text-xs font-black truncate" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              {{ acc.short_label }}
            </span>
          </div>
          <span class="text-[10px] font-bold text-slate-400 group-hover:text-brand-gold transition-colors shrink-0 flex items-center gap-0.5">
            Sao kê <ChevronRight class="w-3 h-3" />
          </span>
        </div>

        <div class="space-y-1.5 text-xs">
          <div class="flex items-center justify-between">
            <span class="text-slate-400">+ Tiền vào (Thu):</span>
            <span class="font-bold text-emerald-400">+{{ formatMoney(acc.total_in) }}</span>
          </div>
          <div v-if="acc.order_receipts > 0 || acc.cashbook_receipts > 0" class="flex items-center justify-between text-[10px] text-slate-400 pl-2">
            <span>Đơn hàng: {{ formatMoney(acc.order_receipts) }}</span>
            <span>Phiếu thu: {{ formatMoney(acc.cashbook_receipts) }}</span>
          </div>

          <div class="flex items-center justify-between pt-0.5">
            <span class="text-slate-400">− Tiền ra (Chi):</span>
            <span class="font-bold text-rose-400">−{{ formatMoney(acc.total_out) }}</span>
          </div>
          <div v-if="acc.supplier_payments > 0 || acc.cashbook_payments > 0" class="flex items-center justify-between text-[10px] text-slate-400 pl-2">
            <span>Trả xưởng: {{ formatMoney(acc.supplier_payments) }}</span>
            <span>Phiếu chi: {{ formatMoney(acc.cashbook_payments) }}</span>
          </div>

          <div class="flex items-center justify-between pt-2 mt-1.5 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
            <span class="font-bold" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">= Còn lại trong kỳ:</span>
            <span class="text-sm font-black" :class="acc.net_balance >= 0 ? 'text-brand-gold' : 'text-rose-400'">
              {{ acc.net_balance >= 0 ? '' : '−' }}{{ formatMoney(Math.abs(acc.net_balance)) }}
            </span>
          </div>
        </div>
      </button>
    </div>

    <!-- Account Ledger Modal -->
    <div v-if="ledgerModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
      <div class="w-full max-w-2xl rounded-2xl border overflow-hidden shadow-2xl flex flex-col max-h-[85vh]" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
        <div class="p-4 sm:p-5 border-b flex items-center justify-between" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
          <div>
            <h3 class="text-sm sm:text-base font-black" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              Sao kê tài khoản: {{ selectedAccount?.label }}
            </h3>
            <p class="text-[11px] text-slate-400 mt-0.5">
              Từ {{ formatDateShort(fromDate) }} đến {{ formatDateShort(toDate) }}
            </p>
          </div>
          <button @click="ledgerModalOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 cursor-pointer">
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Summary bar inside modal -->
        <div v-if="selectedAccount" class="grid grid-cols-3 gap-2 p-3 sm:px-5 border-b text-xs" :class="isDarkMode ? 'bg-[#24303F]/50 border-[#2E3A47]' : 'bg-slate-50 border-slate-100'">
          <div>
            <div class="text-[10px] text-slate-400 uppercase font-bold">Tổng Vào (+)</div>
            <div class="font-black text-emerald-400">+{{ formatMoney(selectedAccount.total_in) }}</div>
          </div>
          <div>
            <div class="text-[10px] text-slate-400 uppercase font-bold">Tổng Ra (−)</div>
            <div class="font-black text-rose-400">−{{ formatMoney(selectedAccount.total_out) }}</div>
          </div>
          <div>
            <div class="text-[10px] text-slate-400 uppercase font-bold">Còn Lại (=)</div>
            <div class="font-black" :class="selectedAccount.net_balance >= 0 ? 'text-brand-gold' : 'text-rose-400'">
              {{ formatMoney(selectedAccount.net_balance) }}
            </div>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 sm:p-5">
          <div v-if="ledgerLoading" class="py-10 text-center text-xs text-slate-400">
            Đang tải chi tiết giao dịch...
          </div>
          <div v-else-if="ledgerItems.length === 0" class="py-10 text-center text-xs text-slate-400">
            Chưa có giao dịch thu/chi nào trên tài khoản này trong kỳ đã chọn.
          </div>
          <div v-else class="space-y-2.5">
            <div
              v-for="item in ledgerItems"
              :key="item.id"
              class="p-3 rounded-xl border flex items-center justify-between gap-3"
              :class="isDarkMode ? 'bg-[#24303F]/40 border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
            >
              <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                  <span
                    class="px-2 py-0.5 rounded text-[10px] font-bold"
                    :class="item.direction === 'in' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-rose-500/15 text-rose-400'"
                  >
                    {{ item.source_label }}
                  </span>
                  <span class="text-[11px] text-slate-400">{{ item.date }}</span>
                </div>
                <div class="text-xs font-bold mt-1 truncate" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                  <router-link
                    v-if="item.order_id"
                    :to="`/he-thong-admin/orders/${item.order_id}`"
                    class="hover:text-brand-gold underline decoration-dotted"
                    @click="ledgerModalOpen = false"
                  >
                    {{ item.title }}
                  </router-link>
                  <span v-else>{{ item.title }}</span>
                </div>
                <div v-if="item.note" class="text-[11px] text-slate-400 truncate mt-0.5">{{ item.note }}</div>
              </div>
              <div class="text-right shrink-0">
                <div class="text-xs sm:text-sm font-black" :class="item.direction === 'in' ? 'text-emerald-400' : 'text-rose-400'">
                  {{ item.direction === 'in' ? '+' : '−' }}{{ formatMoney(item.amount) }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Landmark, ChevronRight, X } from 'lucide-vue-next'
import { api } from '../../../services/api'
import { useAdminTheme } from '../../../composables/useAdminTheme'

const props = defineProps({
  accounts: {
    type: Array,
    default: () => []
  },
  fromDate: {
    type: String,
    default: ''
  },
  toDate: {
    type: String,
    default: ''
  }
})

const { isDarkMode } = useAdminTheme()

const visibleAccounts = computed(() => {
  return (props.accounts || []).filter(acc => {
    if (acc.payment_method === 'other') {
      return acc.total_in > 0 || acc.total_out > 0
    }
    return true
  })
})

const ledgerModalOpen = ref(false)
const ledgerLoading = ref(false)
const selectedAccount = ref(null)
const ledgerItems = ref([])

async function openLedger(acc) {
  selectedAccount.value = acc
  ledgerModalOpen.value = true
  ledgerLoading.value = true
  ledgerItems.value = []
  try {
    const params = { payment_method: acc.payment_method }
    if (props.fromDate) params.from_date = props.fromDate
    if (props.toDate) params.to_date = props.toDate
    const res = await api.getErpCashbookAccountLedger(params)
    ledgerItems.value = res.data?.items || []
  } catch (err) {
    console.error('Failed to load account ledger', err)
  } finally {
    ledgerLoading.value = false
  }
}

function dotClass(method) {
  const map = {
    bank_acb: 'bg-sky-400',
    bank_mb: 'bg-indigo-400',
    bank_minh_nguyen: 'bg-amber-400',
    bank_truong_hung: 'bg-cyan-400',
    cash: 'bg-emerald-400',
    other: 'bg-slate-400'
  }
  return map[method] || 'bg-slate-400'
}

function formatMoney(val) {
  return new Intl.NumberFormat('vi-VN').format(Math.round(val || 0)) + ' đ'
}

function formatDateShort(dateStr) {
  if (!dateStr) return ''
  const parts = dateStr.split('-')
  if (parts.length !== 3) return dateStr
  return `${parts[2]}/${parts[1]}/${parts[0]}`
}
</script>
