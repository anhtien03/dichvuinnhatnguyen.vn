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

    <!-- 4 Financial Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
      <!-- 1. Vốn Gia Công Gốc -->
      <div
        class="p-4 rounded-2xl border flex flex-col justify-between transition-all ring-1"
        :class="isDarkMode ? 'bg-blue-950/20 border-blue-500/30 ring-blue-500/20' : 'bg-blue-50/70 border-blue-200 ring-blue-200/50 shadow-xs'"
      >
        <div>
          <div class="text-[11px] uppercase font-black tracking-wider" :class="isDarkMode ? 'text-blue-400' : 'text-blue-700'">
            1. Vốn Gia Công Gốc
          </div>
          <div class="text-xl sm:text-2xl font-black mt-1.5" :class="isDarkMode ? 'text-blue-300' : 'text-blue-900'">
            {{ formatMoney(totals.baseCost) }}
          </div>
        </div>
        <div class="mt-2.5 pt-2 border-t text-[10px] font-medium" :class="isDarkMode ? 'border-blue-500/20 text-slate-400' : 'border-blue-200/60 text-slate-500'">
          (Chưa tính thuế VAT xưởng)
        </div>
      </div>

      <!-- 2. Thuế VAT Xưởng Lấy -->
      <div
        class="p-4 rounded-2xl border flex flex-col justify-between transition-all ring-1"
        :class="isDarkMode ? 'bg-amber-500/10 border-amber-500/30 ring-amber-500/20' : 'bg-amber-50/80 border-amber-300 ring-amber-300/50 shadow-xs'"
      >
        <div>
          <div class="text-[11px] uppercase font-black tracking-wider" :class="isDarkMode ? 'text-amber-400' : 'text-amber-800'">
            2. Thuế VAT Xưởng Lấy
          </div>
          <div class="text-xl sm:text-2xl font-black mt-1.5" :class="isDarkMode ? 'text-amber-400' : 'text-amber-600'">
            +{{ formatMoney(totals.vat) }}
          </div>
        </div>
        <div class="mt-2.5 pt-2 border-t text-[10px] font-semibold" :class="isDarkMode ? 'border-amber-500/20 text-amber-400/90' : 'border-amber-200/60 text-amber-800'">
          Tự động cộng từ các đơn có VAT
        </div>
      </div>

      <!-- 3. Tổng Nợ Xưởng (Vốn+VAT) -->
      <div
        class="p-4 rounded-2xl border flex flex-col justify-between transition-all ring-1"
        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] ring-white/5' : 'bg-white border-slate-200 ring-slate-200/50 shadow-xs'"
      >
        <div>
          <div class="text-[11px] uppercase font-black tracking-wider" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">
            3. Tổng Nợ Xưởng (Vốn+VAT)
          </div>
          <div class="text-xl sm:text-2xl font-black mt-1.5" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
            {{ formatMoney(totals.totalCost) }}
          </div>
        </div>
        <div class="mt-2.5 pt-2 border-t flex items-center justify-between text-[10px]" :class="isDarkMode ? 'border-[#2E3A47] text-slate-400' : 'border-slate-100 text-slate-600'">
          <span>Đã trả:</span>
          <strong class="text-emerald-500 font-bold">{{ formatMoney(totals.totalPaid) }}</strong>
        </div>
      </div>

      <!-- 4. Tổng Công Nợ Tồn Hiện Tại -->
      <div
        class="p-4 rounded-2xl border flex flex-col justify-between transition-all ring-1"
        :class="isDarkMode ? 'bg-rose-950/20 border-rose-500/30 ring-rose-500/20' : 'bg-rose-50/80 border-rose-300 ring-rose-300/50 shadow-xs'"
      >
        <div>
          <div class="text-[11px] uppercase font-black tracking-wider" :class="isDarkMode ? 'text-rose-400' : 'text-rose-700'">
            4. Tổng Công Nợ Tồn Còn Nợ
          </div>
          <div class="text-xl sm:text-2xl font-black mt-1.5" :class="isDarkMode ? 'text-rose-400' : 'text-rose-600'">
            {{ formatMoney(totals.balance) }}
          </div>
        </div>
        <div class="mt-2.5 pt-2 border-t text-[10px] font-semibold" :class="isDarkMode ? 'border-rose-500/20 text-rose-300' : 'border-rose-200/60 text-rose-700'">
          Tùy theo đơn có VAT và không VAT
        </div>
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
              <th class="py-3 px-4 text-right">
                <div>Tổng Tiền Đặt</div>
                <div class="text-[10px] font-bold text-amber-500 normal-case tracking-normal">Đã gồm VAT xưởng</div>
              </th>
              <th class="py-3 px-4 text-right">Đã Trả Xưởng</th>
              <th class="py-3 px-4 text-right">
                <div>Công Nợ Tồn</div>
                <div class="text-[10px] font-semibold text-rose-400 normal-case tracking-normal">Tùy đơn có/k VAT</div>
              </th>
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
                <div class="cursor-pointer hover:text-brand-gold transition-colors" @click="openDetailModal(s)">{{ s.name }}</div>
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

              <td class="py-3.5 px-4 text-right">
                <div class="font-bold text-sm" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                  {{ formatMoney(s.total_cost) }}
                </div>
                <div v-if="s.total_vat > 0" class="text-[10px] text-amber-500 font-semibold mt-0.5">
                  (Vốn: {{ formatMoney(s.total_base_cost) }} + VAT: {{ formatMoney(s.total_vat) }})
                </div>
                <div v-else class="text-[10px] text-slate-400 mt-0.5">
                  (Đơn không VAT)
                </div>
              </td>

              <td class="py-3.5 px-4 text-right text-emerald-500 font-bold">
                {{ formatMoney(s.total_paid) }}
              </td>

              <td class="py-3.5 px-4 text-right font-black">
                <template v-if="(s.balance || 0) > 0">
                  <div class="text-sm" :class="isDarkMode ? 'text-rose-400' : 'text-rose-600'">
                    {{ formatMoney(s.balance) }}
                  </div>
                  <div v-if="s.total_vat > 0" class="text-[10px] text-amber-500 font-semibold mt-0.5">
                    Gồm {{ formatMoney(s.total_vat) }} VAT
                  </div>
                  <div v-else class="text-[10px] text-slate-400 mt-0.5">
                    Đơn không VAT
                  </div>
                </template>
                <template v-else>
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                    ✓ 0 ₫ (Hết nợ)
                  </span>
                </template>
              </td>

              <td class="py-3.5 px-4 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button
                    @click="openDetailModal(s)"
                    class="px-2 py-1 rounded-lg text-[11px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20 hover:bg-blue-500 hover:text-white transition-colors flex items-center gap-1 cursor-pointer"
                    title="Đối soát chi tiết đơn gia công có/không VAT"
                  >
                    <Receipt class="w-3.5 h-3.5" />
                    <span>Đối Soát</span>
                  </button>

                  <button
                    @click="openPaymentModal(s)"
                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500 hover:text-slate-950 transition-colors cursor-pointer"
                    title="Trả tiền xưởng"
                  >
                    Trả Nợ
                  </button>

                  <button
                    @click="editSupplier(s)"
                    class="p-1.5 rounded-lg border text-blue-400 hover:bg-blue-500/10 cursor-pointer"
                    :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
                    title="Chỉnh sửa xưởng"
                  >
                    <Edit3 class="w-4 h-4" />
                  </button>

                  <button
                    @click="deleteSupplier(s)"
                    class="p-1.5 rounded-lg border text-red-400 hover:bg-red-500/10 cursor-pointer"
                    :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
                    title="Xóa xưởng"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
          <tfoot class="font-bold border-t-2" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-100 border-slate-300 text-slate-900'">
            <tr>
              <td colspan="4" class="py-3.5 px-4 uppercase text-xs tracking-wider">
                Tổng cộng tất cả ({{ suppliers.length }} xưởng in)
              </td>
              <td class="py-3.5 px-4 text-right font-black">
                <div class="text-sm">{{ formatMoney(totals.totalCost) }}</div>
                <div v-if="totals.vat > 0" class="text-[10px] text-amber-500 font-semibold">
                  (Vốn: {{ formatMoney(totals.baseCost) }} + VAT: {{ formatMoney(totals.vat) }})
                </div>
              </td>
              <td class="py-3.5 px-4 text-right font-black text-emerald-500 text-sm">
                {{ formatMoney(totals.totalPaid) }}
              </td>
              <td class="py-3.5 px-4 text-right font-black text-rose-500 text-sm">
                <div>{{ formatMoney(totals.balance) }}</div>
                <div class="text-[10px] text-slate-400 font-normal">Tùy đơn có/k VAT</div>
              </td>
              <td class="py-3.5 px-4"></td>
            </tr>
          </tfoot>
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
    <!-- DETAIL / RECONCILIATION MODAL -->
    <div
      v-if="isDetailModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
    >
      <div
        class="relative w-full max-w-4xl max-h-[90vh] flex flex-col rounded-3xl border shadow-2xl overflow-hidden"
        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
      >
        <!-- Modal Header -->
        <div class="p-5 border-b flex items-center justify-between" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
          <div>
            <h3 class="text-base font-black text-brand-gold flex items-center gap-2">
              <Receipt class="w-5 h-5" />
              <span>Đối Soát Công Nợ Xưởng In: {{ detailSupplier?.name }}</span>
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">
              SĐT: {{ detailSupplier?.phone || '-' }} | Ngân hàng: {{ detailSupplier?.bank_name || '-' }} - {{ detailSupplier?.bank_account }} ({{ detailSupplier?.bank_owner }})
            </p>
          </div>
          <button @click="isDetailModalOpen = false" class="p-1.5 text-slate-400 hover:text-white cursor-pointer">
            <X class="w-5 h-5" />
          </button>
        </div>

        <div v-if="isLoadingDetail" class="p-12 text-center text-slate-400 text-xs">
          Đang tải dữ liệu đối soát xưởng in...
        </div>

        <template v-else>
          <!-- KPI 4 Cards -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-5 border-b" :class="isDarkMode ? 'bg-[#161D2A] border-[#2E3A47]' : 'bg-slate-50 border-slate-100'">
            <div class="p-3 rounded-xl border" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
              <div class="text-[10px] uppercase font-bold text-slate-400">1. Vốn Gia Công Gốc</div>
              <div class="text-base font-black mt-1">{{ formatMoney(detailSupplier?.total_base_cost) }}</div>
              <div class="text-[10px] text-slate-400 mt-0.5">Chưa tính VAT</div>
            </div>

            <div class="p-3 rounded-xl border bg-amber-500/10 border-amber-500/30">
              <div class="text-[10px] uppercase font-bold text-amber-500">2. Thuế VAT Xưởng Lấy</div>
              <div class="text-base font-black text-amber-500 mt-1">+{{ formatMoney(detailSupplier?.total_vat) }}</div>
              <div class="text-[10px] text-amber-400/80 mt-0.5">Cộng từ các đơn có VAT</div>
            </div>

            <div class="p-3 rounded-xl border bg-blue-500/10 border-blue-500/30">
              <div class="text-[10px] uppercase font-bold text-blue-400">3. Tổng Nợ Xưởng (Vốn+VAT)</div>
              <div class="text-base font-black text-blue-400 mt-1">{{ formatMoney(detailSupplier?.total_cost) }}</div>
              <div class="text-[10px] text-emerald-400 mt-0.5">Đã trả: {{ formatMoney(detailSupplier?.total_paid) }}</div>
            </div>

            <div class="p-3 rounded-xl border bg-red-500/10 border-red-500/30">
              <div class="text-[10px] uppercase font-bold text-red-400">4. Công Nợ Còn Tồn</div>
              <div class="text-base font-black text-red-400 mt-1">{{ formatMoney(detailSupplier?.balance) }}</div>
              <div class="text-[10px] text-red-300/80 mt-0.5">Còn nợ xưởng</div>
            </div>
          </div>

          <!-- Table of Order Items -->
          <div class="flex-1 overflow-y-auto p-5 custom-scrollbar space-y-3">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
              <span>Danh Sách Đơn Hàng Gia Công ({{ detailSupplier?.order_items?.length || 0 }} món)</span>
              <span class="text-[11px] font-normal normal-case text-amber-400">Tự động tính thuế VAT theo từng đơn/món có hoặc không có VAT</span>
            </div>

            <div class="rounded-xl border overflow-hidden" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
              <table class="w-full text-left text-xs">
                <thead class="uppercase font-bold tracking-wider border-b" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-400' : 'bg-slate-100 border-slate-200 text-slate-600'">
                  <tr>
                    <th class="py-2.5 px-3">Mã Đơn & Ngày</th>
                    <th class="py-2.5 px-3">Sản Phẩm & Quy Cách</th>
                    <th class="py-2.5 px-3 text-center">Số Lượng</th>
                    <th class="py-2.5 px-3 text-right">Tiền Vốn Gốc</th>
                    <th class="py-2.5 px-3 text-center">Thuế VAT Xưởng</th>
                    <th class="py-2.5 px-3 text-right">Tiền Thuế</th>
                    <th class="py-2.5 px-3 text-right font-black text-brand-gold">Tổng Tiền (Vốn+VAT)</th>
                  </tr>
                </thead>
                <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
                  <tr v-for="it in (detailSupplier?.order_items || [])" :key="it.id" class="hover:bg-brand-gold/5">
                    <td class="py-2.5 px-3 font-mono font-bold">
                      <div>{{ it.order?.code || ('Đơn #' + it.order_id) }}</div>
                      <div class="text-[10px] text-slate-400 font-normal">{{ it.order?.created_at?.substring(0, 10) }}</div>
                    </td>
                    <td class="py-2.5 px-3">
                      <div class="font-bold">{{ it.product_name }}</div>
                      <div v-if="it.specifications" class="text-[10px] text-slate-400 max-w-xs truncate">{{ it.specifications }}</div>
                    </td>
                    <td class="py-2.5 px-3 text-center font-bold">
                      {{ formatNumber(it.quantity) }} {{ it.unit || 'Cái' }}
                    </td>
                    <td class="py-2.5 px-3 text-right font-semibold">
                      {{ formatMoney(it.cost_total) }}
                    </td>
                    <td class="py-2.5 px-3 text-center">
                      <span v-if="it.vat_input_rate > 0" class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        {{ it.vat_input_rate }}%
                      </span>
                      <span v-else class="text-[10px] text-slate-400">
                        0% (K.VAT)
                      </span>
                    </td>
                    <td class="py-2.5 px-3 text-right font-medium" :class="it.vat_input_amount > 0 ? 'text-amber-400' : 'text-slate-400'">
                      {{ formatMoney(it.vat_input_amount || (it.cost_total * (it.vat_input_rate || 0) / 100)) }}
                    </td>
                    <td class="py-2.5 px-3 text-right font-black" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                      {{ formatMoney(it.cost_total + (it.vat_input_amount || (it.cost_total * (it.vat_input_rate || 0) / 100))) }}
                    </td>
                  </tr>
                  <tr v-if="!detailSupplier?.order_items || detailSupplier.order_items.length === 0">
                    <td colspan="7" class="py-8 text-center text-slate-400 italic">
                      Chưa có đơn hàng nào gán cho xưởng này.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Footer -->
          <div class="p-4 border-t flex items-center justify-between" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <div class="text-xs text-slate-400">
              Tổng tiền nợ xưởng: <strong class="text-brand-gold">{{ formatMoney(detailSupplier?.total_cost) }}</strong> (Đã cộng VAT từ các đơn có VAT)
            </div>
            <div class="flex items-center gap-2">
              <button
                @click="isDetailModalOpen = false; openPaymentModal(detailSupplier)"
                class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-500 text-slate-950 hover:bg-emerald-400 cursor-pointer"
              >
                Thanh Toán Trả Nợ Cho Xưởng Này
              </button>
              <button
                @click="isDetailModalOpen = false"
                class="px-4 py-2 rounded-xl text-xs border cursor-pointer"
                :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
              >
                Đóng
              </button>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import {
  Building2,
  Search,
  Edit3,
  Trash2,
  X,
  Receipt
} from 'lucide-vue-next'
import { api } from '../../../services/api'
import { useAdminTheme } from '../../../composables/useAdminTheme'
import { swalToast, swalError, swalDelete } from '../../../utils/swal'

const { isDarkMode } = useAdminTheme()

const suppliers = ref([])
const isLoading = ref(false)
const searchQuery = ref('')

const totals = computed(() => {
  let baseCost = 0
  let vat = 0
  let totalCost = 0
  let totalPaid = 0
  let balance = 0

  suppliers.value.forEach(s => {
    baseCost += Number(s.total_base_cost || 0)
    vat += Number(s.total_vat || 0)
    totalCost += Number(s.total_cost || 0)
    totalPaid += Number(s.total_paid || 0)
    balance += Number(s.balance || 0)
  })

  return {
    baseCost,
    vat,
    totalCost,
    totalPaid,
    balance
  }
})

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

const isDetailModalOpen = ref(false)
const detailSupplier = ref(null)
const isLoadingDetail = ref(false)

async function openDetailModal(s) {
  detailSupplier.value = s
  isDetailModalOpen.value = true
  isLoadingDetail.value = true
  try {
    const res = await api.getErpSupplier(s.id)
    if (res.data) {
      detailSupplier.value = res.data
    }
  } catch (err) {
    console.error('Failed to load supplier detail', err)
  } finally {
    isLoadingDetail.value = false
  }
}

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
    swalToast('Đã lưu thông tin xưởng in')
    fetchSuppliers()
  } catch (err) {
    swalError('Lỗi khi lưu xưởng', err.message || 'Không thể lưu')
  }
}

async function deleteSupplier(s) {
  const confirmed = await swalDelete(`xưởng in [${s.name}]`)
  if (confirmed) {
    try {
      await api.deleteErpSupplier(s.id)
      swalToast(`Đã xóa xưởng [${s.name}]`)
      fetchSuppliers()
    } catch (err) {
      swalError('Lỗi khi xóa', err.message || 'Không thể xóa xưởng')
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
    swalToast('Đã ghi nhận thanh toán công nợ')
    fetchSuppliers()
  } catch (err) {
    swalError('Lỗi khi thanh toán', err.message || 'Không thể thanh toán')
  }
}

function formatMoney(num) {
  if (!num) return '0 ₫'
  return new Intl.NumberFormat('vi-VN').format(Math.round(num)) + ' ₫'
}

function formatNumber(num) {
  if (!num) return '0'
  return new Intl.NumberFormat('vi-VN').format(num)
}

onMounted(() => {
  fetchSuppliers()
})
</script>

