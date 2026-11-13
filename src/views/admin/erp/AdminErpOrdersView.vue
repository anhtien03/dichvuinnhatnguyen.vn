<template>
  <div class="space-y-6">
    <!-- Top Bar: Title & Quick Stats & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
          Quản Lý Đơn Hàng & Sản Xuất
        </h1>
        <p class="text-xs sm:text-sm text-slate-400 mt-1">
          Theo dõi tiến độ in ấn 8 giai đoạn, giá vốn, lợi nhuận gộp và thanh toán VietQR
        </p>
      </div>

      <div class="flex items-center gap-2.5">
        <router-link
          to="/he-thong-admin/orders/create"
          class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-brand-gold hover:bg-amber-400 text-slate-950 shadow-md shadow-brand-gold/20 transition-all cursor-pointer"
        >
          <Plus class="w-4 h-4" />
          <span>Tạo Đơn Hàng Mới</span>
        </router-link>
      </div>
    </div>

    <!-- Filter & Stage Tabs (8 Production Stages) -->
    <div class="p-4 rounded-2xl border" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
      <!-- Stage Filter Pills -->
      <div class="flex items-center gap-2 overflow-x-auto pb-3 custom-scrollbar">
        <button
          @click="selectStage('')"
          class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer"
          :class="currentStage === '' ? 'bg-brand-gold text-slate-950 shadow-sm' : (isDarkMode ? 'bg-[#24303F] text-slate-300 hover:text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200')"
        >
          Tất Cả Đơn
        </button>

        <button
          v-for="stg in STAGES"
          :key="stg.id"
          @click="selectStage(stg.id)"
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap cursor-pointer"
          :class="currentStage === stg.id ? 'bg-brand-gold text-slate-950 font-bold shadow-sm' : (isDarkMode ? 'bg-[#24303F] text-slate-300 hover:text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200')"
        >
          <span class="w-2 h-2 rounded-full" :class="stg.dotColor"></span>
          <span>{{ stg.label }}</span>
        </button>
      </div>

      <!-- Search & Status Filter Row -->
      <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-3 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
        <div class="sm:col-span-5 relative">
          <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
          <input
            v-model="searchQuery"
            @keydown.enter="fetchOrders"
            type="text"
            placeholder="Tìm theo Mã đơn, Tên khách, SĐT..."
            class="w-full text-xs rounded-xl pl-9 pr-3 py-2 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
            :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
          />
        </div>

        <div class="sm:col-span-3">
          <select
            v-model="paymentStatusFilter"
            @change="fetchOrders"
            class="w-full text-xs rounded-xl px-3 py-2 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
            :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
          >
            <option value="">Tất cả trạng thái tiền</option>
            <option value="unpaid">Chưa thanh toán</option>
            <option value="partial">Đã cọc 1 phần</option>
            <option value="paid">Đã thanh toán đủ</option>
          </select>
        </div>

        <div class="sm:col-span-4 flex items-center justify-end gap-2">
          <button
            @click="fetchOrders"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all border"
            :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white hover:bg-[#333A48]' : 'bg-slate-100 border-slate-200 text-slate-700 hover:bg-slate-200'"
          >
            Tìm kiếm
          </button>
          <button
            @click="resetFilter"
            class="px-3 py-2 rounded-xl text-xs text-slate-400 hover:text-white transition-all"
          >
            Làm mới
          </button>
        </div>
      </div>
    </div>

    <!-- Orders Table List -->
    <div class="rounded-2xl border overflow-hidden" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
      <div v-if="isLoading" class="p-12 text-center text-slate-400 text-xs">
        Đang tải danh sách đơn hàng...
      </div>

      <div v-else-if="orders.length === 0" class="p-12 text-center text-slate-400 text-xs">
        Chưa có đơn hàng nào phù hợp với bộ lọc.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="uppercase font-bold tracking-wider border-b" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-400' : 'bg-slate-50 border-slate-200 text-slate-500'">
            <tr>
              <th class="py-3 px-4">Mã đơn</th>
              <th class="py-3 px-4">Khách hàng</th>
              <th class="py-3 px-4">Sản phẩm</th>
              <th class="py-3 px-4">Giai đoạn</th>
              <th class="py-3 px-4 text-right">Tổng tiền</th>
              <th class="py-3 px-4 text-right">Đã trả</th>
              <th class="py-3 px-4 text-right">Còn nợ</th>
              <th class="py-3 px-4 text-right">Lãi gộp</th>
              <th class="py-3 px-4 text-center">Thao tác</th>
            </tr>
          </thead>
          <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
            <tr
              v-for="order in orders"
              :key="order.id"
              class="transition-colors hover:bg-brand-gold/5"
              :class="isDarkMode ? 'text-slate-200' : 'text-slate-800'"
            >
              <!-- Code & Date -->
              <td class="py-3.5 px-4 font-bold">
                <div class="flex items-center gap-1.5">
                  <span class="text-brand-gold cursor-pointer hover:underline" @click="viewDetail(order)">
                    {{ order.code }}
                  </span>
                  <span
                    v-if="order.is_vat_required"
                    class="px-1.5 py-0.5 rounded text-[10px] bg-purple-500/10 text-purple-400 font-bold border border-purple-500/20"
                    title="Yêu cầu xuất VAT"
                  >
                    VAT
                  </span>
                </div>
                <div class="text-[10px] text-slate-400 font-normal">
                  {{ formatDate(order.created_at) }}
                </div>
              </td>

              <!-- Customer Info -->
              <td class="py-3.5 px-4">
                <div class="font-bold flex items-center gap-1">
                  <span>{{ order.customer_name }}</span>
                </div>
                <div class="flex items-center gap-2 mt-0.5">
                  <span class="text-[11px] text-slate-400">{{ order.customer_phone }}</span>
                  <a
                    :href="`https://zalo.me/${cleanPhone(order.customer_phone)}`"
                    target="_blank"
                    class="text-[10px] font-bold text-blue-400 hover:underline"
                    title="Mở Zalo"
                  >
                    Zalo
                  </a>
                </div>
              </td>

              <!-- Items Summary -->
              <td class="py-3.5 px-4 max-w-[200px] truncate">
                <div v-for="(it, idx) in order.items" :key="idx" class="truncate">
                  • {{ it.product_name }} (SL: {{ formatNumber(it.quantity) }} {{ it.unit }})
                </div>
                <div v-if="order.items?.length === 0" class="text-slate-400 italic">
                  Chưa có sản phẩm
                </div>
              </td>

              <!-- Production Stage Dropdown -->
              <td class="py-3.5 px-4">
                <select
                  :value="order.production_stage"
                  @change="handleStageChange(order, $event.target.value)"
                  class="text-[11px] font-bold rounded-lg px-2 py-1 border transition-all cursor-pointer focus:outline-none"
                  :class="getStageBadgeClass(order.production_stage)"
                >
                  <option v-for="stg in STAGES" :key="stg.id" :value="stg.id">
                    {{ stg.label }}
                  </option>
                </select>
              </td>

              <!-- Financials -->
              <td class="py-3.5 px-4 text-right">
                <div class="font-bold" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                  {{ formatMoney(order.total_amount) }}
                </div>
                <div v-if="order.vat_rate > 0" class="text-[10px] font-semibold text-purple-400">
                  +VAT {{ order.vat_rate }}%: {{ formatMoney(order.vat_amount) }}
                </div>
                <div v-else class="text-[10px] text-slate-400">
                  (K.VAT)
                </div>
              </td>

              <td class="py-3.5 px-4 text-right text-emerald-400 font-semibold">
                {{ formatMoney(order.paid_amount) }}
              </td>

              <td class="py-3.5 px-4 text-right font-bold" :class="(order.total_amount - order.paid_amount) > 0 ? 'text-red-400' : 'text-slate-400'">
                <div>{{ formatMoney(order.total_amount - order.paid_amount) }}</div>
                <div v-if="(order.total_amount - order.paid_amount) > 0" class="text-[10px] font-normal" :class="order.vat_rate > 0 ? 'text-purple-400' : 'text-slate-400'">
                  {{ order.vat_rate > 0 ? 'Gồm VAT ' + order.vat_rate + '%' : 'Đơn k.VAT' }}
                </div>
              </td>

              <td class="py-3.5 px-4 text-right font-black text-brand-gold">
                {{ formatMoney(order.profit_amount) }}
              </td>

              <!-- Actions -->
              <td class="py-3.5 px-4 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <!-- VietQR -->
                  <button
                    @click="openQRModal(order)"
                    class="p-1.5 rounded-lg border text-brand-gold hover:bg-brand-gold/10 transition-colors"
                    :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
                    title="Mã VietQR Chuyển Khoản"
                  >
                    <QrCode class="w-4 h-4" />
                  </button>

                  <!-- View & Pay -->
                  <button
                    @click="openPaymentModal(order)"
                    class="p-1.5 rounded-lg border text-emerald-400 hover:bg-emerald-500/10 transition-colors"
                    :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
                    title="Ghi nhận đặt cọc / thanh toán"
                  >
                    <DollarSign class="w-4 h-4" />
                  </button>

                  <!-- Edit -->
                  <router-link
                    :to="`/he-thong-admin/orders/${order.id}/edit`"
                    class="p-1.5 rounded-lg border text-blue-400 hover:bg-blue-500/10 transition-colors inline-flex items-center"
                    :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
                    title="Chỉnh sửa đơn"
                  >
                    <Edit3 class="w-4 h-4" />
                  </router-link>

                  <!-- Delete -->
                  <button
                    @click="confirmDelete(order)"
                    class="p-1.5 rounded-lg border text-red-400 hover:bg-red-500/10 transition-colors"
                    :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
                    title="Xóa đơn hàng"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="p-4 flex items-center justify-between border-t text-xs" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
        <span class="text-slate-400">Trang {{ currentPage }} / {{ totalPages }} (Tổng {{ totalItems }} đơn)</span>
        <div class="flex items-center gap-1">
          <button
            @click="changePage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-2.5 py-1 rounded-lg border disabled:opacity-30"
            :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
          >
            Trước
          </button>
          <button
            @click="changePage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="px-2.5 py-1 rounded-lg border disabled:opacity-30"
            :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
          >
            Sau
          </button>
        </div>
      </div>
    </div>

    <!-- CREATE / EDIT ORDER MODAL -->
    <Transition name="modal-fade">
      <div
        v-show="isModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-950/80 overflow-y-auto"
      >
        <div
          class="modal-content relative w-full max-w-5xl rounded-3xl border shadow-2xl p-5 sm:p-7 max-h-[92vh] overflow-y-auto custom-scrollbar"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
        >
          <!-- Modal Header -->
          <div class="flex items-center justify-between pb-4 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <div>
              <h2 class="text-lg sm:text-xl font-black">
                {{ isEditing ? `Chỉnh Sửa Đơn Hàng: ${editingCode}` : 'Tạo Đơn Hàng In Ấn Mới' }}
              </h2>
              <p class="text-xs text-slate-400 mt-0.5">
                Tự động tính giá vốn, giá bán, chiết khấu, VAT và lợi nhuận gộp
              </p>
            </div>
            <button @click="isModalOpen = false" class="p-2 rounded-xl text-slate-400 hover:text-white">
              <X class="w-5 h-5" />
            </button>
          </div>

        <!-- Form Body -->
        <form @submit.prevent="submitOrderForm" class="space-y-6 pt-5">
          <!-- Customer Information Section -->
          <div class="p-4 rounded-2xl border space-y-3" :class="isDarkMode ? 'bg-[#24303F]/60 border-[#2E3A47]' : 'bg-slate-50 border-slate-200'">
            <div class="text-xs font-bold uppercase tracking-wider text-brand-gold">
              1. Thông Tin Khách Hàng
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div>
                <label class="block text-[11px] font-semibold mb-1 text-slate-400">Số Điện Thoại (*)</label>
                <input
                  v-model="orderForm.customer_phone"
                  @blur="lookupCustomerPhone"
                  type="text"
                  required
                  placeholder="0901234567..."
                  class="w-full text-xs rounded-xl px-3 py-2 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                />
              </div>

              <div>
                <label class="block text-[11px] font-semibold mb-1 text-slate-400">Tên Khách Hàng (*)</label>
                <input
                  v-model="orderForm.customer_name"
                  type="text"
                  required
                  placeholder="Anh Tuấn / Chị Hạnh..."
                  class="w-full text-xs rounded-xl px-3 py-2 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                />
              </div>

              <div>
                <label class="block text-[11px] font-semibold mb-1 text-slate-400">Tên Công Ty / Đơn Vị</label>
                <input
                  v-model="orderForm.customer_company"
                  type="text"
                  placeholder="Công ty TNHH..."
                  class="w-full text-xs rounded-xl px-3 py-2 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                />
              </div>

              <div class="sm:col-span-2">
                <label class="block text-[11px] font-semibold mb-1 text-slate-400">Địa Chỉ Giao Hàng</label>
                <input
                  v-model="orderForm.customer_address"
                  type="text"
                  placeholder="Số nhà, đường, phường, quận..."
                  class="w-full text-xs rounded-xl px-3 py-2 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                />
              </div>

              <div>
                <label class="block text-[11px] font-semibold mb-1 text-slate-400">Mã Số Thuế (nếu có)</label>
                <input
                  v-model="orderForm.customer_tax_code"
                  type="text"
                  placeholder="0312345678"
                  class="w-full text-xs rounded-xl px-3 py-2 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                />
              </div>
            </div>
          </div>

          <!-- Items Table Section -->
          <div class="p-4 rounded-2xl border space-y-3" :class="isDarkMode ? 'bg-[#24303F]/60 border-[#2E3A47]' : 'bg-slate-50 border-slate-200'">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
              <div class="text-xs font-bold uppercase tracking-wider text-brand-gold flex items-center gap-1.5">
                <Calculator class="w-4 h-4" />
                <span>2. Danh Sách Sản Phẩm & Tính Giá Nhanh</span>
              </div>
              <button
                type="button"
                @click="addItemRow"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-gold text-slate-950 hover:bg-amber-400 transition-all cursor-pointer w-fit"
              >
                <Plus class="w-3.5 h-3.5" />
                <span>Thêm Món In Mới</span>
              </button>
            </div>

            <!-- Quick Preset Template Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 custom-scrollbar pt-1">
              <span class="text-[11px] text-slate-400 font-semibold whitespace-nowrap flex items-center gap-1">
                <Sparkles class="w-3.5 h-3.5 text-brand-gold" /> Mẫu nhanh:
              </span>
              <button
                v-for="tmpl in PRESET_TEMPLATES"
                :key="tmpl.name"
                type="button"
                @click="applyPreset(tmpl)"
                class="px-2.5 py-1 rounded-lg text-[11px] font-semibold border whitespace-nowrap transition-all hover:scale-105 cursor-pointer"
                :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] hover:border-brand-gold text-slate-300' : 'bg-white border-slate-200 hover:border-brand-gold text-slate-700'"
              >
                + {{ tmpl.name }}
              </button>
            </div>

            <!-- Items Loop -->
            <div class="space-y-3">
              <div
                v-for="(item, idx) in orderForm.items"
                :key="idx"
                class="p-3.5 rounded-xl border relative space-y-3 shadow-sm"
                :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
              >
                <div class="flex items-center justify-between border-b pb-2 text-xs font-bold" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
                  <span class="text-brand-gold">Món in #{{ idx + 1 }}</span>
                  <button
                    v-if="orderForm.items.length > 1"
                    type="button"
                    @click="removeItemRow(idx)"
                    class="text-red-400 hover:text-red-500 font-normal text-[11px] cursor-pointer"
                  >
                    Xóa dòng này
                  </button>
                </div>

                <!-- Row 1: Tên, Số lượng, Đơn vị, Đơn giá bán, Thành tiền trọn gói -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5">
                  <div class="sm:col-span-4">
                    <label class="block text-[10px] text-slate-400 mb-0.5">Tên Sản Phẩm (*)</label>
                    <input
                      v-model="item.product_name"
                      type="text"
                      required
                      placeholder="Namecard, Tem decal, Menu..."
                      class="w-full text-xs rounded-lg px-2.5 py-1.5 border font-semibold"
                      :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
                    />
                  </div>

                  <div class="sm:col-span-2">
                    <label class="block text-[10px] text-slate-400 mb-0.5">Số Lượng (*)</label>
                    <input
                      v-model.number="item.quantity"
                      @input="onQtyChange(item)"
                      type="number"
                      step="any"
                      min="0.01"
                      required
                      class="w-full text-xs rounded-lg px-2.5 py-1.5 border font-bold"
                      :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
                    />
                  </div>

                  <div class="sm:col-span-2">
                    <label class="block text-[10px] text-slate-400 mb-0.5">Đơn Vị</label>
                    <input
                      v-model="item.unit"
                      type="text"
                      placeholder="Cái, Hộp..."
                      class="w-full text-xs rounded-lg px-2.5 py-1.5 border"
                      :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
                    />
                  </div>

                  <div class="sm:col-span-2">
                    <label class="block text-[10px] text-slate-400 mb-0.5">Đơn Giá Bán</label>
                    <input
                      v-model.number="item.unit_price"
                      @input="onUnitPriceChange(item)"
                      type="number"
                      min="0"
                      class="w-full text-xs rounded-lg px-2.5 py-1.5 border text-right font-bold text-amber-400"
                      :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
                    />
                  </div>

                  <div class="sm:col-span-2">
                    <label class="block text-[10px] text-slate-400 mb-0.5">Hoặc Tiền Trọn Gói</label>
                    <input
                      v-model.number="item.line_total"
                      @input="onLineTotalChange(item)"
                      type="number"
                      min="0"
                      placeholder="Trọn gói"
                      class="w-full text-xs rounded-lg px-2.5 py-1.5 border text-right font-bold text-emerald-400"
                      :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
                    />
                  </div>
                </div>

                <!-- Row 2: Xưởng in, Đơn giá vốn, Tổng vốn xưởng, Thuế VAT xưởng -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 pt-2 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
                  <div class="sm:col-span-4">
                    <label class="block text-[10px] text-slate-400 mb-0.5">Xưởng In / Nhà Cung Cấp</label>
                    <select
                      v-model="item.supplier_id"
                      @change="onSupplierSelect(item)"
                      class="w-full text-xs rounded-lg px-2.5 py-1.5 border"
                      :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
                    >
                      <option :value="null">-- Chọn xưởng gia công --</option>
                      <option v-for="sup in suppliers" :key="sup.id" :value="sup.id">
                        {{ sup.name }}
                      </option>
                    </select>
                  </div>

                  <div class="sm:col-span-2">
                    <label class="block text-[10px] text-slate-400 mb-0.5">Đơn Giá Vốn</label>
                    <input
                      v-model.number="item.cost_price"
                      @input="onCostPriceChange(item)"
                      type="number"
                      min="0"
                      class="w-full text-xs rounded-lg px-2.5 py-1.5 border text-right"
                      :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
                    />
                  </div>

                  <div class="sm:col-span-2">
                    <label class="block text-[10px] text-slate-400 mb-0.5">Tổng Vốn Xưởng</label>
                    <input
                      v-model.number="item.cost_total"
                      @input="onCostTotalChange(item)"
                      type="number"
                      min="0"
                      placeholder="Trọn gói"
                      class="w-full text-xs rounded-lg px-2.5 py-1.5 border text-right font-semibold text-slate-300"
                      :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
                    />
                  </div>

                  <div class="sm:col-span-4">
                    <label class="block text-[10px] text-slate-400 mb-0.5">VAT Nhà In Bắt Buộc Lấy</label>
                    <select
                      v-model.number="item.vat_input_rate"
                      @change="recalculate"
                      class="w-full text-xs rounded-lg px-2.5 py-1.5 border font-semibold"
                      :class="item.vat_input_rate > 0 ? (isDarkMode ? 'bg-amber-500/10 border-amber-500/40 text-amber-300' : 'bg-amber-50 border-amber-300 text-amber-800') : (isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200')"
                    >
                      <option :value="0">Xưởng KHÔNG tính VAT (0%)</option>
                      <option :value="8">Xưởng BẮT LẤY VAT 8%</option>
                      <option :value="10">Xưởng BẮT LẤY VAT 10%</option>
                    </select>
                  </div>

                  <div class="sm:col-span-12">
                    <label class="block text-[10px] text-slate-400 mb-0.5">Quy Cách & Yêu Cầu Kỹ Thuật (Kích thước, Loại giấy, Cán màng, Bế...)</label>
                    <input
                      v-model="item.specifications"
                      type="text"
                      placeholder="VD: C300 cán màng mờ 2 mặt, bo 4 góc, dán keo nắp sẵn..."
                      class="w-full text-xs rounded-lg px-2.5 py-1.5 border"
                      :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200'"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Financial Calculation & Surcharges Section -->
          <div class="p-4 rounded-2xl border space-y-4" :class="isDarkMode ? 'bg-[#24303F]/60 border-[#2E3A47]' : 'bg-slate-50 border-slate-200'">
            <div class="flex items-center justify-between">
              <div class="text-xs font-bold uppercase tracking-wider text-brand-gold flex items-center gap-1.5">
                <DollarSign class="w-4 h-4" />
                <span>3. Chiết Khấu, VAT & Tổng Kết Báo Giá</span>
              </div>

              <!-- Quick Zalo Quote Copy Button -->
              <button
                type="button"
                @click="copyZaloQuote"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer"
                :class="isCopied ? 'bg-emerald-500 text-white' : 'bg-brand-gold hover:bg-amber-400 text-slate-950'"
              >
                <Check v-if="isCopied" class="w-3.5 h-3.5" />
                <Copy v-else class="w-3.5 h-3.5" />
                <span>{{ isCopied ? 'Đã Chép Báo Giá Zalo!' : 'Sao Chép Báo Giá Gửi Zalo' }}</span>
              </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
              <div>
                <div class="flex items-center justify-between mb-1">
                  <label class="block text-[11px] text-slate-400">Giảm Giá / Chiết Khấu (₫)</label>
                  <span class="text-[10px] text-emerald-400 font-semibold">Trừ vào lợi nhuận</span>
                </div>
                <input
                  v-model.number="orderForm.discount"
                  @input="recalculate"
                  type="number"
                  min="0"
                  class="w-full text-xs rounded-xl px-3 py-2 border text-right font-bold text-amber-400"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
                />
                <p class="text-[10px] text-slate-400 mt-1">Trừ thẳng vào lợi nhuận xưởng, giữ nguyên bill khách trả.</p>
              </div>

              <div>
                <label class="block text-[11px] text-slate-400 mb-1">Thuế VAT Khách Lấy</label>
                <div class="flex rounded-xl overflow-hidden border p-0.5" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
                  <button
                    type="button"
                    @click="setVatRate(0)"
                    class="flex-1 py-1 text-xs font-bold rounded-lg transition-all"
                    :class="orderForm.vat_rate === 0 ? 'bg-brand-gold text-slate-950' : 'text-slate-400'"
                  >
                    0%
                  </button>
                  <button
                    type="button"
                    @click="setVatRate(8)"
                    class="flex-1 py-1 text-xs font-bold rounded-lg transition-all"
                    :class="orderForm.vat_rate === 8 ? 'bg-purple-600 text-white' : 'text-slate-400'"
                  >
                    8%
                  </button>
                  <button
                    type="button"
                    @click="setVatRate(10)"
                    class="flex-1 py-1 text-xs font-bold rounded-lg transition-all"
                    :class="orderForm.vat_rate === 10 ? 'bg-purple-600 text-white' : 'text-slate-400'"
                  >
                    10%
                  </button>
                </div>
              </div>

              <div>
                <label class="block text-[11px] text-slate-400 mb-1">Phí Ship Khách Trả (VNĐ)</label>
                <input
                  v-model.number="orderForm.shipping_fee_customer"
                  @input="recalculate"
                  type="number"
                  min="0"
                  class="w-full text-xs rounded-xl px-3 py-2 border text-right font-semibold"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200'"
                />
              </div>

              <div>
                <label class="block text-[11px] text-slate-400 mb-1">Phụ Thu (Thiết kế / In gấp)</label>
                <input
                  v-model.number="orderForm.surcharge"
                  @input="recalculate"
                  type="number"
                  min="0"
                  class="w-full text-xs rounded-xl px-3 py-2 border text-right font-semibold"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200'"
                />
              </div>

              <div>
                <label class="block text-[11px] text-slate-400 mb-1">Tiền Ship Xưởng (Cửa hàng chịu)</label>
                <input
                  v-model.number="orderForm.shipping_fee_shop"
                  @input="recalculate"
                  type="number"
                  min="0"
                  class="w-full text-xs rounded-xl px-3 py-2 border text-right text-rose-300"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
                />
              </div>

              <div>
                <label class="block text-[11px] text-slate-400 mb-1">Thợ In / Xưởng Phụ Trách</label>
                <input
                  v-model="orderForm.printer_name"
                  type="text"
                  placeholder="Thợ máy / Xưởng in"
                  class="w-full text-xs rounded-xl px-3 py-2 border"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200'"
                />
              </div>

              <div>
                <label class="block text-[11px] text-slate-400 mb-1">Khách Đặt Cọc Trước</label>
                <input
                  v-model.number="orderForm.paid_amount"
                  type="number"
                  min="0"
                  class="w-full text-xs rounded-xl px-3 py-2 border text-right font-bold text-emerald-400"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
                />
              </div>

              <div>
                <label class="block text-[11px] text-slate-400 mb-1">Tài Khoản Nhận Cọc</label>
                <select
                  v-model="orderForm.payment_method"
                  class="w-full text-xs rounded-xl px-3 py-2 border"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200'"
                >
                  <option value="bank_acb">ACB (51189927 - CTY IN ẤN NHẬT NGUYÊN)</option>
                  <option value="bank_mb">MB Bank (679838699 - HUYNH THANH TRUC)</option>
                  <option value="bank_minh_nguyen">TK MINH NGUYEN</option>
                  <option value="bank_truong_hung">TK TRUONG HUNG</option>
                  <option value="cash">Tiền mặt tại xưởng</option>
                </select>
              </div>
            </div>

            <!-- Totals Highlight Summary Card -->
            <div
              class="p-4 sm:p-5 rounded-2xl border transition-all"
              :class="isDarkMode ? 'bg-[#151D2A] border-[#2E3A47]' : 'bg-slate-50 border-slate-200 shadow-sm'"
            >
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                <!-- 1. Tiền in -->
                <div
                  class="p-4 rounded-xl border flex flex-col justify-between transition-all ring-1"
                  :class="isDarkMode ? 'bg-blue-950/20 border-blue-500/30 ring-blue-500/20 shadow-xs' : 'bg-blue-50/70 border-blue-200 ring-blue-200/50 shadow-xs'"
                >
                  <div>
                    <div class="text-[11px] uppercase font-black tracking-wider" :class="isDarkMode ? 'text-blue-400' : 'text-blue-700'">
                      1. Tổng Tiền Hàng (Subtotal)
                    </div>
                    <div class="text-xl sm:text-2xl font-black mt-1.5" :class="isDarkMode ? 'text-blue-300' : 'text-blue-900'">
                      {{ formatMoney(calculated.subtotal) }}
                    </div>
                  </div>
                  <div class="mt-2.5 pt-2 border-t" :class="isDarkMode ? 'border-blue-500/20' : 'border-blue-200/60'">
                    <div v-if="orderForm.vat_rate > 0" class="text-[11px] font-semibold flex items-center gap-1.5" :class="isDarkMode ? 'text-purple-300' : 'text-purple-800'">
                      <span class="px-1.5 py-0.5 rounded text-[10px] font-black" :class="isDarkMode ? 'bg-purple-500/30 text-purple-300' : 'bg-purple-100 text-purple-800'">+VAT {{ orderForm.vat_rate }}%</span>
                      <span>{{ formatMoney(calculated.vat_amount) }}</span>
                    </div>
                    <div v-else class="text-[10px] font-medium" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
                      (Chưa tính thuế VAT)
                    </div>
                  </div>
                </div>

                <!-- 2. Tổng khách trả -->
                <div
                  class="p-4 rounded-xl border flex flex-col justify-between transition-all ring-1"
                  :class="isDarkMode ? 'bg-amber-500/10 border-amber-500/30 ring-amber-500/20 shadow-xs' : 'bg-amber-50/80 border-amber-300 ring-amber-300/50 shadow-xs'"
                >
                  <div>
                    <div class="text-[11px] uppercase font-black tracking-wider" :class="isDarkMode ? 'text-amber-400' : 'text-amber-800'">
                      2. Tổng Khách Trả (Bill)
                    </div>
                    <div class="text-xl sm:text-2xl font-black mt-1.5" :class="isDarkMode ? 'text-amber-400' : 'text-amber-600'">
                      {{ formatMoney(calculated.total_amount) }}
                    </div>
                  </div>
                  <div class="mt-2.5 pt-2 border-t flex items-center justify-between text-[11px]" :class="isDarkMode ? 'border-amber-500/20 text-slate-300' : 'border-amber-200/60 text-slate-700'">
                    <div>
                      Đã cọc: <strong :class="isDarkMode ? 'text-emerald-400' : 'text-emerald-700'">{{ formatMoney(orderForm.paid_amount) }}</strong>
                    </div>
                    <div v-if="calculated.total_amount - orderForm.paid_amount > 0" class="font-bold text-[10px]" :class="isDarkMode ? 'text-rose-400' : 'text-rose-600'">
                      Còn: {{ formatMoney(calculated.total_amount - orderForm.paid_amount) }}
                    </div>
                    <div v-else-if="calculated.total_amount > 0 && orderForm.paid_amount >= calculated.total_amount" class="font-bold text-[10px]" :class="isDarkMode ? 'text-emerald-400' : 'text-emerald-700'">
                      ✓ Đã đủ
                    </div>
                  </div>
                </div>

                <!-- 3. Nợ xưởng in -->
                <div
                  class="p-4 rounded-xl border flex flex-col justify-between transition-all ring-1"
                  :class="isDarkMode ? 'bg-rose-950/20 border-rose-500/30 ring-rose-500/20 shadow-xs' : 'bg-rose-50/70 border-rose-200 ring-rose-200/50 shadow-xs'"
                >
                  <div>
                    <div class="text-[11px] uppercase font-black tracking-wider" :class="isDarkMode ? 'text-rose-400' : 'text-rose-700'">
                      3. Tiền Nợ Nhà In / Xưởng
                    </div>
                    <div class="text-xl sm:text-2xl font-black mt-1.5" :class="isDarkMode ? 'text-rose-300' : 'text-rose-900'">
                      {{ formatMoney(calculated.total_supplier_debt) }}
                    </div>
                  </div>
                  <div class="mt-2.5 pt-2 border-t space-y-1" :class="isDarkMode ? 'border-rose-500/20' : 'border-rose-200/60'">
                    <div v-if="calculated.supplier_vat_total > 0" class="text-[10px] font-bold flex items-center gap-1" :class="isDarkMode ? 'text-amber-300' : 'text-amber-800'">
                      <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                      <span>Gồm {{ formatMoney(calculated.supplier_vat_total) }} VAT xưởng</span>
                    </div>
                    <div v-if="orderForm.shipping_fee_shop > 0" class="text-[10px] font-medium flex items-center gap-1" :class="isDarkMode ? 'text-sky-300' : 'text-sky-800'">
                      <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                      <span>Ship Grab {{ formatMoney(orderForm.shipping_fee_shop) }}</span>
                    </div>
                    <div v-if="!calculated.supplier_vat_total && !orderForm.shipping_fee_shop" class="text-[10px] font-medium" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
                      (Tiền gia công xưởng in)
                    </div>
                  </div>
                </div>

                <!-- 4. Lợi nhuận gộp -->
                <div
                  class="p-4 rounded-xl border flex flex-col justify-between transition-all ring-1"
                  :class="isDarkMode ? 'bg-emerald-500/10 border-emerald-500/30 ring-emerald-500/20 shadow-xs' : 'bg-emerald-50/80 border-emerald-300 ring-emerald-300/40 shadow-xs'"
                >
                  <div>
                    <div class="text-[11px] uppercase font-black tracking-wider" :class="isDarkMode ? 'text-emerald-400' : 'text-emerald-800'">
                      4. Lợi Nhuận Gộp Thực Nhận
                    </div>
                    <div class="text-xl sm:text-2xl font-black mt-1.5" :class="isDarkMode ? 'text-emerald-400' : 'text-emerald-700'">
                      {{ formatMoney(calculated.profit_amount) }}
                    </div>
                  </div>
                  <div class="mt-2.5 pt-2 border-t flex items-center justify-between" :class="isDarkMode ? 'border-emerald-500/20' : 'border-emerald-200/60'">
                    <span
                      class="px-2 py-0.5 rounded-full text-[10px] font-bold border"
                      :class="calculated.profit_margin >= 30
                        ? (isDarkMode ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : 'bg-emerald-100 text-emerald-800 border-emerald-300')
                        : (calculated.profit_amount < 0
                      ? (isDarkMode ? 'bg-rose-500/20 text-rose-300 border-rose-500/40' : 'bg-rose-100 text-rose-800 border-rose-300')
                      : (isDarkMode ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-amber-100 text-amber-800 border-amber-300'))"
                    >
                      Tỷ suất lời: {{ calculated.profit_margin }}%
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- VAT Notice if applicable -->
            <div v-if="calculated.vat_warning" class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-300 flex items-center gap-2">
              <span>⚠️ <strong>Lưu ý:</strong> {{ calculated.vat_warning }}</span>
            </div>
          </div>

          <!-- Footer Buttons -->
          <div class="flex items-center justify-end gap-3 pt-3 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <button
              type="button"
              @click="isModalOpen = false"
              class="px-5 py-2.5 rounded-xl text-xs font-semibold border cursor-pointer"
              :class="isDarkMode ? 'border-[#2E3A47] text-slate-300 hover:bg-[#24303F]' : 'border-slate-200 text-slate-700 hover:bg-slate-100'"
            >
              Hủy bỏ
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="px-6 py-2.5 rounded-xl text-xs font-black bg-brand-gold hover:bg-amber-400 text-slate-950 shadow-md shadow-brand-gold/20 disabled:opacity-50 cursor-pointer"
            >
              {{ isSubmitting ? 'Đang lưu...' : (isEditing ? 'Cập Nhật Đơn Hàng' : 'Hoàn Tất & Tạo Đơn') }}
            </button>
          </div>
        </form>
      </div>
    </div>
    </Transition>

    <!-- VIETQR POPUP MODAL -->
    <Transition name="modal-fade">
      <div
        v-if="isQRModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80"
      >
        <div
          class="modal-content relative w-full max-w-md rounded-3xl border shadow-2xl p-6 text-center space-y-4"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
        >
          <button @click="isQRModalOpen = false" class="absolute top-4 right-4 p-1.5 text-slate-400 hover:text-white">
            <X class="w-5 h-5" />
          </button>

          <h3 class="text-lg font-black text-brand-gold">
            Mã VietQR Thanh Toán Nhanh
          </h3>
          <p class="text-xs text-slate-400">
            Đơn hàng: <span class="font-bold text-white">{{ qrData.order_code }}</span>
          </p>

          <!-- Bank Toggle -->
          <div class="flex justify-center gap-2">
            <button
              @click="switchQRBank('acb')"
              class="px-3 py-1.5 rounded-xl text-xs font-bold border transition-all cursor-pointer"
              :class="qrBank === 'acb' ? 'bg-blue-600 text-white border-blue-600 shadow' : 'border-slate-600 text-slate-400'"
            >
              ACB (Công Ty - 51189927)
            </button>
            <button
              @click="switchQRBank('mb')"
              class="px-3 py-1.5 rounded-xl text-xs font-bold border transition-all cursor-pointer"
              :class="qrBank === 'mb' ? 'bg-red-600 text-white border-red-600 shadow' : 'border-slate-600 text-slate-400'"
            >
              MB Bank (679838699)
            </button>
          </div>

          <!-- QR Image Display -->
          <div class="p-3 bg-white rounded-2xl inline-block shadow-inner mx-auto">
            <img
              v-if="qrData.qr_url"
              :src="qrData.qr_url"
              alt="VietQR"
              class="w-64 h-64 object-contain mx-auto"
            />
            <div v-else class="w-64 h-64 flex items-center justify-center text-xs text-slate-400">
              Đang tạo mã VietQR...
            </div>
          </div>

          <div class="text-xs space-y-1">
            <div>Số tiền cần thanh toán: <span class="font-bold text-emerald-400 text-sm">{{ formatMoney(qrData.amount) }}</span></div>
            <div class="text-[11px] text-slate-400">Nội dung chuyển khoản: <span class="font-mono font-bold text-brand-gold">{{ qrData.memo }}</span></div>
          </div>

          <a
            :href="qrData.qr_url"
            download="vietqr.png"
            target="_blank"
            class="block w-full py-2.5 rounded-xl text-xs font-bold bg-brand-gold text-slate-950 hover:bg-amber-400 transition-all text-center"
          >
            Tải Ảnh Mã QR Về Máy
          </a>
        </div>
      </div>
    </Transition>

    <!-- QUICK PAYMENT / DEPOSIT MODAL -->
    <Transition name="modal-fade">
      <div
        v-if="isPaymentModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80"
      >
        <div
          class="modal-content relative w-full max-w-md rounded-3xl border shadow-2xl p-6 space-y-4"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
        >
          <button @click="isPaymentModalOpen = false" class="absolute top-4 right-4 p-1.5 text-slate-400 hover:text-white">
            <X class="w-5 h-5" />
          </button>

          <h3 class="text-base font-black text-brand-gold">
            Ghi Nhận Đặt Cọc / Thanh Toán
          </h3>
          <p class="text-xs text-slate-400">
            Đơn: <span class="font-bold text-white">{{ currentPaymentOrder?.code }}</span> - Còn thiếu: <span class="font-bold text-red-400">{{ formatMoney(currentPaymentOrder ? (currentPaymentOrder.total_amount - currentPaymentOrder.paid_amount) : 0) }}</span>
          </p>

          <form @submit.prevent="submitPayment" class="space-y-3">
            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Số Tiền Thanh Toán (VNĐ) (*)</label>
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
              <label class="block text-[11px] text-slate-400 mb-1">Tài Khoản / Phương Thức</label>
              <select
                v-model="paymentForm.payment_method"
                class="w-full text-xs rounded-xl px-3 py-2 border"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
              >
                <option value="bank_acb">ACB (51189927 - CTY IN ẤN NHẬT NGUYÊN)</option>
                <option value="bank_mb">MB Bank (679838699 - HUYNH THANH TRUC)</option>
                <option value="bank_minh_nguyen">TK MINH NGUYEN</option>
                <option value="bank_truong_hung">TK TRUONG HUNG</option>
                <option value="cash">Tiền mặt tại cửa hàng</option>
              </select>
            </div>

            <div>
              <label class="block text-[11px] text-slate-400 mb-1">Ghi Chú Đợt Thanh Toán</label>
              <input
                v-model="paymentForm.note"
                type="text"
                placeholder="Khách chuyển khoản đợt 2, nhận hàng trả nốt..."
                class="w-full text-xs rounded-xl px-3 py-2 border"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
              />
            </div>

            <div class="pt-2 flex justify-end gap-2">
              <button
                type="button"
                @click="isPaymentModalOpen = false"
                class="px-4 py-2 rounded-xl text-xs border cursor-pointer"
                :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
              >
                Hủy
              </button>
              <button
                type="submit"
                class="px-5 py-2 rounded-xl text-xs font-bold bg-brand-gold text-slate-950 hover:bg-amber-400 cursor-pointer"
              >
                Xác Nhận Thu Tiền
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import {
  Plus,
  Search,
  QrCode,
  DollarSign,
  Edit3,
  Trash2,
  X,
  Printer,
  ChevronDown,
  Copy,
  Check,
  Sparkles,
  Calculator
} from 'lucide-vue-next'
import { api } from '../../../services/api'
import { useAdminTheme } from '../../../composables/useAdminTheme'
import { swalToast, swalError, swalWarning, swalDelete } from '../../../utils/swal'

const { isDarkMode } = useAdminTheme()

// 8 Stages definition as in hj.html
const STAGES = [
  { id: 'cho_duyet_file', label: '1. Chờ duyệt file', dotColor: 'bg-amber-400' },
  { id: 'thiet_ke', label: '2. Đang thiết kế', dotColor: 'bg-blue-400' },
  { id: 'gui_file_in', label: '3. Đã gửi in', dotColor: 'bg-indigo-400' },
  { id: 'dang_in', label: '4. Đang in', dotColor: 'bg-cyan-400' },
  { id: 'gia_cong', label: '5. Đang gia công', dotColor: 'bg-yellow-400' },
  { id: 'da_in_xong', label: '6. Đã in xong', dotColor: 'bg-emerald-400' },
  { id: 'dang_giao', label: '7. Đang giao hàng', dotColor: 'bg-orange-400' },
  { id: 'hoan_thanh', label: '8. Hoàn thành', dotColor: 'bg-green-500' }
]

// Preset Templates for Fast Quote creation
const PRESET_TEMPLATES = [
  { name: 'Card visit (Namecard)', unit: 'Hộp', quantity: 5, unit_price: 35000, cost_price: 18000, specifications: 'C300, cán màng mờ 2 mặt, hộp mica' },
  { name: 'Tem decal giấy cán bóng', unit: 'Cái', quantity: 1000, unit_price: 350, cost_price: 180, specifications: 'Khổ 5x5cm, cán màng bóng, bế demi' },
  { name: 'Tem decal nhựa chống nước', unit: 'Cái', quantity: 1000, unit_price: 550, cost_price: 300, specifications: 'Decal nhựa đục / trong, cán màng bóng, bế demi tròn' },
  { name: 'Tờ rơi A5 C150', unit: 'Tờ', quantity: 1000, unit_price: 650, cost_price: 380, specifications: 'Khổ A5 (14.8x21cm), C150 in 2 mặt 4 màu' },
  { name: 'Bạt Hiflex / Standee', unit: 'Cái', quantity: 1, unit_price: 150000, cost_price: 70000, specifications: 'Khổ 0.6x1.6m hoặc 0.8x1.8m, đóng khoen 4 góc' },
  { name: 'Bao thư nhỏ 12x22cm', unit: 'Cái', quantity: 500, unit_price: 1800, cost_price: 1100, specifications: 'Giấy F120 in offset, có sẵn keo nắp' },
  { name: 'Túi giấy Kraft', unit: 'Cái', quantity: 500, unit_price: 7500, cost_price: 4800, specifications: 'Kraft Nhật 180gsm, quai dù tròn, đáy lót cứng' }
]

const isCopied = ref(false)

// State
const orders = ref([])
const suppliers = ref([])
const isLoading = ref(false)
const currentStage = ref('')
const searchQuery = ref('')
const paymentStatusFilter = ref('')
const currentPage = ref(1)
const totalPages = ref(1)
const totalItems = ref(0)

// Modal State
const isModalOpen = ref(false)
const isEditing = ref(false)
const editingId = ref(null)
const editingCode = ref('')
const isSubmitting = ref(false)

// Order Form
const orderForm = reactive({
  customer_phone: '',
  customer_name: '',
  customer_company: '',
  customer_address: '',
  customer_tax_code: '',
  printer_name: '',
  notes: '',
  discount: 0,
  vat_rate: 0,
  shipping_fee_customer: 0,
  shipping_fee_shop: 0,
  surcharge: 0,
  paid_amount: 0,
  payment_method: 'bank_acb',
  is_vat_required: false,
  items: []
})

// QR Modal State
const isQRModalOpen = ref(false)
const qrBank = ref('acb')
const qrCurrentOrderId = ref(null)
const qrData = reactive({
  order_code: '',
  amount: 0,
  memo: '',
  qr_url: ''
})

// Payment Modal State
const isPaymentModalOpen = ref(false)
const currentPaymentOrder = ref(null)
const paymentForm = reactive({
  amount: 0,
  payment_method: 'bank_acb',
  note: ''
})

// Calculations in Order Form
const calculated = computed(() => {
  let subtotal = 0
  let baseCost = 0
  let supplierVatTotal = 0

  orderForm.items.forEach(it => {
    const qty = Number(it.quantity) || 0
    const price = Number(it.unit_price) || 0
    const rowSub = (it.line_total !== undefined && it.line_total !== null && it.line_total !== '') 
      ? Number(it.line_total) 
      : (qty * price)

    const cPrice = Number(it.cost_price) || 0
    const rowCost = (it.cost_total !== undefined && it.cost_total !== null && it.cost_total !== '') 
      ? Number(it.cost_total) 
      : (qty * cPrice)

    const rowVatInput = rowCost * ((Number(it.vat_input_rate) || 0) / 100)

    subtotal += rowSub
    baseCost += rowCost
    supplierVatTotal += rowVatInput
  })

  const discount = Number(orderForm.discount) || 0
  const vatRate = Number(orderForm.vat_rate) || 0
  const vatAmount = subtotal * (vatRate / 100)
  const shipCustomer = Number(orderForm.shipping_fee_customer) || 0
  const shipShop = Number(orderForm.shipping_fee_shop) || 0
  const surcharge = Number(orderForm.surcharge) || 0

  // 2. Tổng khách trả (Bill): Tiền in + VAT + Ship thu khách + Phụ thu (KHÔNG trừ chiết khấu)
  const totalAmount = Math.max(0, subtotal + vatAmount + shipCustomer + surcharge)

  // 3. Tiền nợ xưởng in: Chỉ gồm tiền gia công xưởng in + VAT xưởng (KHÔNG cộng ship Grab)
  const totalSupplierDebt = baseCost + supplierVatTotal

  // Net Profit calculation: Chiết khấu và ship Grab trừ thẳng vào lợi nhuận
  let profitAmount = 0
  let vatWarning = ''

  if (vatRate > 0) {
    // Customer takes VAT: VAT to pay Tax Agency = max(0, vatAmount - supplierVatTotal)
    const taxPayable = Math.max(0, vatAmount - supplierVatTotal)
    // Lợi nhuận = Doanh thu - Nợ xưởng - Phí ship trả xưởng/Grab - Chiết khấu - Thuế nộp
    profitAmount = totalAmount - totalSupplierDebt - shipShop - discount - taxPayable
  } else {
    // Customer does NOT take VAT: Profit = (Tiền in + Phụ thu + Ship thu khách) - Nợ xưởng - Phí ship Grab - Chiết khấu
    profitAmount = (subtotal + shipCustomer + surcharge) - totalSupplierDebt - shipShop - discount
    if (supplierVatTotal > 0) {
      vatWarning = `Khách không lấy VAT nhưng xưởng in bắt lấy VAT (${formatMoney(supplierVatTotal)}). Bạn đang chịu thay khoản thuế này, lợi nhuận bị trừ tương ứng.`
    }
  }

  const profitMargin = totalAmount > 0 ? Math.round((profitAmount / totalAmount) * 100) : 0

  return {
    subtotal,
    cost_amount: baseCost,
    supplier_vat_total: supplierVatTotal,
    total_supplier_debt: totalSupplierDebt,
    vat_amount: vatAmount,
    total_amount: totalAmount,
    profit_amount: profitAmount,
    profit_margin: profitMargin,
    vat_warning: vatWarning
  }
})

function recalculate() {
  // Triggers reactivity
}

// 2-Way Sync between Unit Price and Line Total
function onQtyChange(item) {
  const qty = Number(item.quantity) || 0
  if (qty > 0) {
    if (item.unit_price > 0) {
      item.line_total = Math.round(qty * Number(item.unit_price))
    } else if (item.line_total > 0) {
      item.unit_price = Math.round(Number(item.line_total) / qty)
    }

    if (item.cost_price > 0) {
      item.cost_total = Math.round(qty * Number(item.cost_price))
    } else if (item.cost_total > 0) {
      item.cost_price = Math.round(Number(item.cost_total) / qty)
    }
  }
}

function onUnitPriceChange(item) {
  const qty = Number(item.quantity) || 0
  const price = Number(item.unit_price) || 0
  item.line_total = Math.round(qty * price)
}

function onLineTotalChange(item) {
  const qty = Number(item.quantity) || 0
  const lineTotal = Number(item.line_total) || 0
  if (qty > 0) {
    item.unit_price = Math.round(lineTotal / qty)
  }
}

function onCostPriceChange(item) {
  const qty = Number(item.quantity) || 0
  const cPrice = Number(item.cost_price) || 0
  item.cost_total = Math.round(qty * cPrice)
}

function onCostTotalChange(item) {
  const qty = Number(item.quantity) || 0
  const cTotal = Number(item.cost_total) || 0
  if (qty > 0) {
    item.cost_price = Math.round(cTotal / qty)
  }
}

function setVatRate(rate) {
  orderForm.vat_rate = rate
  orderForm.is_vat_required = rate > 0
}

function applyPreset(tmpl) {
  // If first item is empty, replace it, else push
  if (orderForm.items.length === 1 && !orderForm.items[0].product_name) {
    Object.assign(orderForm.items[0], {
      product_name: tmpl.name,
      quantity: tmpl.quantity,
      unit: tmpl.unit,
      unit_price: tmpl.unit_price,
      line_total: tmpl.quantity * tmpl.unit_price,
      cost_price: tmpl.cost_price,
      cost_total: tmpl.quantity * tmpl.cost_price,
      vat_input_rate: 0,
      specifications: tmpl.specifications
    })
  } else {
    orderForm.items.push({
      product_name: tmpl.name,
      quantity: tmpl.quantity,
      unit: tmpl.unit,
      unit_price: tmpl.unit_price,
      line_total: tmpl.quantity * tmpl.unit_price,
      supplier_id: null,
      supplier_name: '',
      cost_price: tmpl.cost_price,
      cost_total: tmpl.quantity * tmpl.cost_price,
      vat_input_rate: 0,
      specifications: tmpl.specifications
    })
  }
}

function copyZaloQuote() {
  const customerName = orderForm.customer_name || 'Quý khách'
  const itemsText = orderForm.items.map((it, idx) => {
    const qty = formatNumber(it.quantity || 1)
    const unit = it.unit || 'cái'
    const lineTotal = formatMoney(it.line_total || (it.quantity * it.unit_price))
    return `${idx + 1}. ${it.product_name || 'Sản phẩm in'} (${qty} ${unit})
   • Quy cách: ${it.specifications || 'Tiêu chuẩn xưởng in'}
   • Thành tiền: ${lineTotal}`
  }).join('\n')

  let vatText = ''
  if (orderForm.vat_rate > 0) {
    vatText = `• Thuế VAT (${orderForm.vat_rate}%): +${formatMoney(calculated.value.vat_amount)}\n`
  }
  let shipText = ''
  if (orderForm.shipping_fee_customer > 0) {
    shipText = `• Phí ship: +${formatMoney(orderForm.shipping_fee_customer)}\n`
  }
  let surchargeText = ''
  if (orderForm.surcharge > 0) {
    surchargeText = `• Phụ thu: +${formatMoney(orderForm.surcharge)}\n`
  }

  const quoteMessage = `✨ BÁO GIÁ IN ẤN NHẬT NGUYÊN ✨
Kính gửi: ${customerName} ${orderForm.customer_phone ? '(' + orderForm.customer_phone + ')' : ''}
---------------------------------------
${itemsText}
---------------------------------------
• Tiền in: ${formatMoney(calculated.value.subtotal)}
${vatText}${shipText}${surchargeText}👉 TỔNG THANH TOÁN: ${formatMoney(calculated.value.total_amount)}
---------------------------------------
CÔNG TY THIẾT KẾ IN ẤN NHẬT NGUYÊN
Hotline / Zalo: 0359 005 469 - 076 759 8368
Địa chỉ: Đông Hưng Thuận 40, P. Đông Hưng Thuận, Q.12, TP.HCM
Cam kết in sắc nét, đúng tiến độ và hỗ trợ kiểm tra file tận tình!`

  navigator.clipboard.writeText(quoteMessage).then(() => {
    isCopied.value = true
    setTimeout(() => { isCopied.value = false }, 3000)
  }).catch(() => {
    alert('Vui lòng cấp quyền clipboard để sao chép báo giá!')
  })
}

function addItemRow() {
  orderForm.items.push({
    product_name: '',
    quantity: 1,
    unit: 'Cái',
    unit_price: 0,
    line_total: 0,
    supplier_id: null,
    supplier_name: '',
    cost_price: 0,
    cost_total: 0,
    vat_input_rate: 0,
    specifications: ''
  })
}

function removeItemRow(idx) {
  orderForm.items.splice(idx, 1)
}

function onSupplierSelect(item) {
  const found = suppliers.value.find(s => s.id === item.supplier_id)
  if (found) {
    item.supplier_name = found.name
  } else {
    item.supplier_name = ''
  }
}

// Phone lookup for auto fill
async function lookupCustomerPhone() {
  const p = orderForm.customer_phone?.trim()
  if (!p) return

  try {
    const res = await api.searchErpCustomerPhone(p)
    if (res.data) {
      if (!orderForm.customer_name) orderForm.customer_name = res.data.name || ''
      if (!orderForm.customer_address) orderForm.customer_address = res.data.address || ''
      if (!orderForm.customer_company) orderForm.customer_company = res.data.company_name || ''
      if (!orderForm.customer_tax_code) orderForm.customer_tax_code = res.data.tax_code || ''
    }
  } catch (err) {
    console.error('Customer lookup error', err)
  }
}

// Fetch orders list
async function fetchOrders() {
  isLoading.value = true
  try {
    const params = {
      page: currentPage.value,
      per_page: 25
    }
    if (currentStage.value) params.production_stage = currentStage.value
    if (searchQuery.value.trim()) params.search = searchQuery.value.trim()
    if (paymentStatusFilter.value) params.payment_status = paymentStatusFilter.value

    const res = await api.getErpOrders(params)
    if (res.data) {
      orders.value = res.data.data || []
      currentPage.value = res.data.current_page || 1
      totalPages.value = res.data.last_page || 1
      totalItems.value = res.data.total || 0
    }
  } catch (err) {
    console.error('Failed to fetch orders', err)
  } finally {
    isLoading.value = false
  }
}

async function fetchSuppliers() {
  try {
    const res = await api.getErpSuppliers()
    if (res.data) {
      suppliers.value = res.data
    }
  } catch (err) {
    console.error('Failed to load suppliers', err)
  }
}

function selectStage(stg) {
  currentStage.value = stg
  currentPage.value = 1
  fetchOrders()
}

function resetFilter() {
  currentStage.value = ''
  searchQuery.value = ''
  paymentStatusFilter.value = ''
  currentPage.value = 1
  fetchOrders()
}

function changePage(p) {
  currentPage.value = p
  fetchOrders()
}

// Open Create Modal
function openCreateModal() {
  isEditing.value = false
  editingId.value = null
  editingCode.value = ''
  orderForm.customer_phone = ''
  orderForm.customer_name = ''
  orderForm.customer_company = ''
  orderForm.customer_address = ''
  orderForm.customer_tax_code = ''
  orderForm.printer_name = ''
  orderForm.notes = ''
  orderForm.discount = 0
  orderForm.vat_rate = 0
  orderForm.shipping_fee_customer = 0
  orderForm.shipping_fee_shop = 0
  orderForm.surcharge = 0
  orderForm.paid_amount = 0
  orderForm.payment_method = 'bank_acb'
  orderForm.items = [
    {
      product_name: '',
      quantity: 1,
      unit: 'Cái',
      unit_price: 0,
      line_total: 0,
      supplier_id: null,
      supplier_name: '',
      cost_price: 0,
      cost_total: 0,
      vat_input_rate: 0,
      specifications: ''
    }
  ]
  isModalOpen.value = true
}

// Edit Order
function editOrder(order) {
  isEditing.value = true
  editingId.value = order.id
  editingCode.value = order.code
  orderForm.customer_phone = order.customer_phone
  orderForm.customer_name = order.customer_name
  orderForm.customer_company = order.customer_company || ''
  orderForm.customer_address = order.customer_address || ''
  orderForm.customer_tax_code = order.customer_tax_code || ''
  orderForm.printer_name = order.printer_name || ''
  orderForm.notes = order.notes || ''
  orderForm.discount = order.discount || 0
  orderForm.vat_rate = order.vat_rate || 0
  orderForm.shipping_fee_customer = order.shipping_fee_customer || 0
  orderForm.shipping_fee_shop = order.shipping_fee_shop || 0
  orderForm.surcharge = order.surcharge || 0
  orderForm.paid_amount = order.paid_amount || 0
  orderForm.items = (order.items || []).map(i => ({
    product_name: i.product_name,
    quantity: i.quantity,
    unit: i.unit,
    unit_price: i.unit_price,
    line_total: Math.round(i.quantity * i.unit_price),
    supplier_id: i.supplier_id,
    supplier_name: i.supplier_name,
    cost_price: i.cost_price,
    cost_total: Math.round(i.quantity * i.cost_price),
    vat_input_rate: i.vat_input_rate || 0,
    specifications: i.specifications
  }))
  if (orderForm.items.length === 0) {
    addItemRow()
  }
  isModalOpen.value = true
}

// Submit Order Form
async function submitOrderForm() {
  if (orderForm.items.length === 0) {
    alert('Vui lòng thêm ít nhất 1 sản phẩm in!')
    return
  }

  isSubmitting.value = true
  try {
    const payload = {
      customer_name: orderForm.customer_name,
      customer_phone: orderForm.customer_phone,
      customer_company: orderForm.customer_company,
      customer_address: orderForm.customer_address,
      customer_tax_code: orderForm.customer_tax_code,
      printer_name: orderForm.printer_name,
      notes: orderForm.notes,
      discount: orderForm.discount,
      vat_rate: orderForm.vat_rate,
      shipping_fee_customer: orderForm.shipping_fee_customer,
      shipping_fee_shop: orderForm.shipping_fee_shop,
      surcharge: orderForm.surcharge,
      paid_amount: orderForm.paid_amount,
      payment_method: orderForm.payment_method,
      items: orderForm.items.map(it => {
        const qty = Number(it.quantity) || 1
        let uPrice = Number(it.unit_price) || 0
        if (!uPrice && it.line_total) {
          uPrice = Number(it.line_total) / qty
        }
        let cPrice = Number(it.cost_price) || 0
        if (!cPrice && it.cost_total) {
          cPrice = Number(it.cost_total) / qty
        }
        return {
          product_name: it.product_name,
          quantity: qty,
          unit: it.unit || 'Cái',
          unit_price: uPrice,
          supplier_id: it.supplier_id,
          supplier_name: it.supplier_name,
          cost_price: cPrice,
          vat_input_rate: Number(it.vat_input_rate) || 0,
          specifications: it.specifications
        }
      })
    }

    if (isEditing.value) {
      await api.updateErpOrder(editingId.value, payload)
    } else {
      await api.createErpOrder(payload)
    }

    isModalOpen.value = false
    swalToast('Đã lưu đơn hàng thành công')
    fetchOrders()
  } catch (err) {
    swalError('Lỗi lưu đơn hàng', err.message || 'Có lỗi xảy ra khi lưu đơn hàng')
  } finally {
    isSubmitting.value = false
  }
}

// Stage change
async function handleStageChange(order, newStage) {
  try {
    await api.updateErpOrderStage(order.id, newStage)
    order.production_stage = newStage
    swalToast('Đã chuyển trạng thái sản xuất')
  } catch (err) {
    swalError('Không thể chuyển trạng thái', err.message)
  }
}

// Delete
async function confirmDelete(order) {
  const confirmed = await swalDelete(`đơn hàng [${order.code}]`)
  if (confirmed) {
    try {
      await api.deleteErpOrder(order.id)
      swalToast(`Đã xóa đơn hàng [${order.code}]`)
      fetchOrders()
    } catch (err) {
      swalError('Không thể xóa', err.message)
    }
  }
}

// VietQR
async function openQRModal(order) {
  qrCurrentOrderId.value = order.id
  qrBank.value = 'acb'
  loadVietQR()
  isQRModalOpen.value = true
}

async function switchQRBank(bank) {
  qrBank.value = bank
  loadVietQR()
}

async function loadVietQR() {
  if (!qrCurrentOrderId.value) return
  qrData.qr_url = ''
  try {
    const res = await api.getErpOrderVietQR(qrCurrentOrderId.value, qrBank.value)
    if (res.data) {
      Object.assign(qrData, res.data)
    }
  } catch (err) {
    console.error('Failed to load VietQR', err)
  }
}

// Payment modal
function openPaymentModal(order) {
  currentPaymentOrder.value = order
  paymentForm.amount = Math.max(0, order.total_amount - order.paid_amount)
  paymentForm.payment_method = 'bank_acb'
  paymentForm.note = 'Thanh toán tiền hàng'
  isPaymentModalOpen.value = true
}

async function submitPayment() {
  if (!currentPaymentOrder.value || paymentForm.amount <= 0) return
  try {
    await api.addErpOrderPayment(currentPaymentOrder.value.id, paymentForm)
    isPaymentModalOpen.value = false
    swalToast('Đã ghi nhận thanh toán')
    fetchOrders()
  } catch (err) {
    swalError('Không thể ghi nhận', err.message)
  }
}

// Helpers
function formatMoney(num) {
  if (!num) return '0 ₫'
  return new Intl.NumberFormat('vi-VN').format(Math.round(num)) + ' ₫'
}

function formatNumber(num) {
  if (!num) return '0'
  return new Intl.NumberFormat('vi-VN').format(num)
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return `${d.getDate().toString().padStart(2, '0')}/${(d.getMonth() + 1).toString().padStart(2, '0')}/${d.getFullYear()} ${d.getHours().toString().padStart(2, '0')}:${d.getMinutes().toString().padStart(2, '0')}`
}

function cleanPhone(phone) {
  return (phone || '').replace(/\D/g, '')
}

function getStageBadgeClass(stg) {
  switch (stg) {
    case 'cho_duyet_file':
      return isDarkMode.value ? 'bg-amber-500/10 text-amber-400 border-amber-500/30' : 'bg-amber-50 text-amber-700 border-amber-200'
    case 'thiet_ke':
      return isDarkMode.value ? 'bg-blue-500/10 text-blue-400 border-blue-500/30' : 'bg-blue-50 text-blue-700 border-blue-200'
    case 'gui_file_in':
      return isDarkMode.value ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500/30' : 'bg-indigo-50 text-indigo-700 border-indigo-200'
    case 'dang_in':
      return isDarkMode.value ? 'bg-cyan-500/10 text-cyan-400 border-cyan-500/30' : 'bg-cyan-50 text-cyan-700 border-cyan-200'
    case 'gia_cong':
      return isDarkMode.value ? 'bg-yellow-500/10 text-yellow-400 border-yellow-500/30' : 'bg-yellow-50 text-yellow-700 border-yellow-200'
    case 'da_in_xong':
      return isDarkMode.value ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-emerald-50 text-emerald-700 border-emerald-200'
    case 'dang_giao':
      return isDarkMode.value ? 'bg-orange-500/10 text-orange-400 border-orange-500/30' : 'bg-orange-50 text-orange-700 border-orange-200'
    case 'hoan_thanh':
      return isDarkMode.value ? 'bg-green-500/10 text-green-400 border-green-500/30' : 'bg-green-50 text-green-700 border-green-200'
    default:
      return isDarkMode.value ? 'bg-[#24303F] text-slate-300 border-[#2E3A47]' : 'bg-slate-100 text-slate-700 border-slate-200'
  }
}

const route = useRoute()

onMounted(() => {
  fetchOrders()
  fetchSuppliers()

  // Handle auto conversion from Web Quote Request
  if (route.query.from_quote) {
    const raw = sessionStorage.getItem('pending_order_from_quote')
    if (raw) {
      try {
        const qData = JSON.parse(raw)
        sessionStorage.removeItem('pending_order_from_quote')
        openCreateModal()
        orderForm.customer_name = qData.customer_name || ''
        orderForm.customer_phone = qData.customer_phone || ''
        orderForm.customer_company = qData.customer_company || ''
        orderForm.notes = qData.notes || ''

        let qty = 1
        if (qData.quantity) {
          const matchNum = qData.quantity.toString().match(/\d+/)
          if (matchNum) qty = Number(matchNum[0])
        }

        const price = Number(qData.unit_price) || 0

        orderForm.items = [
          {
            product_name: qData.product_name || 'Ấn phẩm in',
            quantity: qty,
            unit: 'Cái',
            unit_price: price,
            line_total: price > 0 ? (qty * price) : 0,
            supplier_id: null,
            supplier_name: '',
            cost_price: 0,
            cost_total: 0,
            vat_input_rate: 0,
            specifications: qData.specifications || ''
          }
        ]
      } catch (err) {
        console.error('Failed to parse quote transfer data', err)
      }
    }
  }
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  height: 5px;
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #333a48;
  border-radius: 9999px;
}

/* 60fps hardware-accelerated modal transitions */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.15s ease-out;
}
.modal-fade-enter-active .modal-content,
.modal-fade-leave-active .modal-content {
  transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.15s ease-out;
  will-change: transform, opacity;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
.modal-fade-enter-from .modal-content,
.modal-fade-leave-to .modal-content {
  opacity: 0;
  transform: scale(0.97) translateY(-6px);
}
</style>



