<template>
  <div class="space-y-6 max-w-7xl mx-auto py-2">
    <!-- Top Action Bar & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
      <div class="space-y-1">
        <router-link
          to="/he-thong-admin/orders"
          class="inline-flex items-center gap-1.5 text-xs font-bold transition-colors cursor-pointer"
          :class="isDarkMode ? 'text-slate-400 hover:text-white' : 'text-slate-500 hover:text-slate-900'"
        >
          <ArrowLeft class="w-4 h-4" />
          <span>Quay lại Danh sách Đơn hàng</span>
        </router-link>
        <div class="flex items-center gap-3">
          <h1 class="text-xl sm:text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
            {{ isEditing ? `Chỉnh Sửa Đơn Hàng: ${editingCode}` : 'Tạo Đơn Hàng In Ấn Mới' }}
          </h1>
          <span
            class="text-[11px] font-bold px-2.5 py-0.5 rounded-full border"
            :class="isEditing ? 'bg-amber-500/10 text-amber-400 border-amber-500/20' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'"
          >
            {{ isEditing ? 'Chế độ chỉnh sửa' : 'Đơn mới' }}
          </span>
        </div>
        <p class="text-xs text-slate-400">
          Tự động tính giá vốn, giá bán, chiết khấu, VAT và lợi nhuận gộp theo từng xưởng in
        </p>
      </div>

      <!-- Action buttons -->
      <div class="flex items-center gap-2.5">
        <button
          type="button"
          @click="copyZaloQuote"
          class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer border"
          :class="isCopied
            ? 'bg-emerald-500 text-white border-emerald-500'
            : (isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200 hover:text-white' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50')"
        >
          <Check v-if="isCopied" class="w-4 h-4" />
          <Copy v-else class="w-4 h-4 text-brand-gold" />
          <span>{{ isCopied ? 'Đã Chép Báo Giá Zalo!' : 'Sao Chép Báo Giá Zalo' }}</span>
        </button>

        <router-link
          to="/he-thong-admin/orders"
          class="px-4 py-2 rounded-xl text-xs font-semibold border transition-all cursor-pointer"
          :class="isDarkMode ? 'border-[#2E3A47] text-slate-300 hover:bg-[#24303F]' : 'border-slate-200 text-slate-700 hover:bg-slate-100'"
        >
          Hủy Bỏ
        </router-link>

        <button
          type="button"
          @click="submitOrder"
          :disabled="isSubmitting"
          class="flex items-center gap-1.5 px-5 py-2 rounded-xl text-xs font-black bg-brand-gold hover:bg-amber-400 text-slate-950 shadow-md shadow-brand-gold/20 transition-all cursor-pointer disabled:opacity-50"
        >
          <Loader2 v-if="isSubmitting" class="w-4 h-4 animate-spin" />
          <Save v-else class="w-4 h-4" />
          <span>{{ isSubmitting ? 'Đang lưu...' : (isEditing ? 'Cập Nhật Đơn' : 'Hoàn Tất & Tạo Đơn') }}</span>
        </button>
      </div>
    </div>

    <!-- MAIN FORM -->
    <form @submit.prevent="submitOrder" class="space-y-6">
      
      <!-- CARD 1: THÔNG TIN KHÁCH HÀNG (CRM) -->
      <div
        class="p-5 sm:p-6 rounded-2xl border shadow-sm space-y-4"
        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <div class="flex items-center justify-between border-b pb-3" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
          <div class="text-xs font-black uppercase tracking-wider text-brand-gold flex items-center gap-2">
            <UserCheck class="w-4 h-4" />
            <span>1. Thông Tin Khách Hàng (Tự động nhận diện khách quen)</span>
          </div>
          <span v-if="isLookingUp" class="text-[11px] text-brand-gold flex items-center gap-1">
            <Loader2 class="w-3 h-3 animate-spin" /> Đang tra cứu danh bạ...
          </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
          <div class="sm:col-span-4">
            <label class="block text-xs font-bold mb-1" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">
              Số Điện Thoại Khách <span class="text-red-500">*</span>
            </label>
            <input
              v-model="orderForm.customer_phone"
              @blur="lookupCustomerPhone"
              type="text"
              required
              placeholder="0901234567..."
              class="w-full text-xs rounded-xl px-3 py-2.5 border font-semibold transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            />
            <p class="text-[10px] text-slate-400 mt-1">
              Nhập SĐT xong bấm ra ngoài để tự điền tên, công ty, địa chỉ nếu là khách cũ.
            </p>
          </div>

          <div class="sm:col-span-4">
            <label class="block text-xs font-bold mb-1" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">
              Tên Khách Hàng / Zalo <span class="text-red-500">*</span>
            </label>
            <input
              v-model="orderForm.customer_name"
              type="text"
              required
              placeholder="Anh Tuấn / Chị Hạnh..."
              class="w-full text-xs rounded-xl px-3 py-2.5 border font-semibold transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            />
          </div>

          <div class="sm:col-span-4">
            <label class="block text-xs font-bold mb-1" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">
              Tên Công Ty / Doanh Nghiệp
            </label>
            <input
              v-model="orderForm.customer_company"
              type="text"
              placeholder="Công ty TNHH MTV..."
              class="w-full text-xs rounded-xl px-3 py-2.5 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            />
          </div>

          <div class="sm:col-span-6">
            <label class="block text-xs font-bold mb-1" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">
              Địa Chỉ Giao Hàng & Giao Chành Xe
            </label>
            <input
              v-model="orderForm.customer_address"
              type="text"
              placeholder="Số nhà, đường, phường, quận, chành xe..."
              class="w-full text-xs rounded-xl px-3 py-2.5 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            />
          </div>

          <div class="sm:col-span-3">
            <label class="block text-xs font-bold mb-1" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">
              Mã Số Thuế (nếu xuất hóa đơn)
            </label>
            <input
              v-model="orderForm.customer_tax_code"
              type="text"
              placeholder="0312345678"
              class="w-full text-xs rounded-xl px-3 py-2.5 border font-mono transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            />
          </div>

          <div class="sm:col-span-3">
            <label class="block text-xs font-bold mb-1" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">
              Kỹ Thuật / Thợ In Phụ Trách
            </label>
            <input
              v-model="orderForm.printer_name"
              type="text"
              placeholder="Thợ in, Designer..."
              class="w-full text-xs rounded-xl px-3 py-2.5 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            />
          </div>

          <div class="sm:col-span-12">
            <label class="block text-xs font-bold mb-1" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">
              Ghi Chú Tiến Độ / Lưu Ý Khách Hàng
            </label>
            <input
              v-model="orderForm.notes"
              type="text"
              placeholder="Giao hàng trước 15h thứ 6, kiểm tra kỹ màu in..."
              class="w-full text-xs rounded-xl px-3 py-2.5 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            />
          </div>
        </div>
      </div>

      <!-- CARD 2: DANH SÁCH MÓN IN & TÍNH GIÁ NHANH -->
      <div
        class="p-5 sm:p-6 rounded-2xl border shadow-sm space-y-4"
        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b pb-3" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
          <div>
            <div class="text-xs font-black uppercase tracking-wider text-brand-gold flex items-center gap-2">
              <Calculator class="w-4 h-4" />
              <span>2. Danh Sách Món In & Gán Xưởng Gia Công</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5">
              Hỗ trợ tự động đồng bộ 2 chiều giữa Đơn Giá ⟷ Tiền Trọn Gói và tính nợ từng xưởng
            </p>
          </div>

          <button
            type="button"
            @click="addItemRow"
            class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-brand-gold text-slate-950 hover:bg-amber-400 transition-all cursor-pointer w-fit shadow-sm"
          >
            <Plus class="w-4 h-4" />
            <span>Thêm Món In Mới</span>
          </button>
        </div>

        <!-- Preset Templates -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-2 custom-scrollbar">
          <span class="text-[11px] text-slate-400 font-bold whitespace-nowrap flex items-center gap-1">
            <Sparkles class="w-3.5 h-3.5 text-brand-gold" /> Mẫu nhanh:
          </span>
          <button
            v-for="tmpl in PRESET_TEMPLATES"
            :key="tmpl.name"
            type="button"
            @click="applyPreset(tmpl)"
            class="px-2.5 py-1 rounded-lg text-[11px] font-semibold border whitespace-nowrap transition-all hover:scale-105 cursor-pointer"
            :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] hover:border-brand-gold text-slate-300' : 'bg-slate-50 border-slate-200 hover:border-brand-gold text-slate-700'"
          >
            + {{ tmpl.name }}
          </button>
        </div>

        <!-- Items Loop -->
        <div class="space-y-4">
          <div
            v-for="(item, idx) in orderForm.items"
            :key="idx"
            class="p-4 rounded-xl border relative space-y-3.5 shadow-sm transition-all"
            :class="isDarkMode ? 'bg-[#1F2937]/50 border-[#2E3A47]' : 'bg-slate-50/70 border-slate-200'"
          >
            <!-- Item Header -->
            <div class="flex items-center justify-between border-b pb-2 text-xs font-bold" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
              <div class="flex items-center gap-2">
                <span class="w-5 h-5 rounded-full bg-brand-gold text-slate-950 flex items-center justify-center text-[10px] font-black">
                  {{ idx + 1 }}
                </span>
                <span class="text-brand-gold font-black">
                  {{ item.product_name || `Ấn phẩm in #${idx + 1}` }}
                </span>
              </div>

              <button
                v-if="orderForm.items.length > 1"
                type="button"
                @click="removeItemRow(idx)"
                class="text-red-400 hover:text-red-500 font-normal text-xs flex items-center gap-1 cursor-pointer"
              >
                <Trash2 class="w-3.5 h-3.5" />
                <span>Xóa món này</span>
              </button>
            </div>

            <!-- Row 1: Tên, Số lượng, Đơn vị, Đơn giá bán, Thành tiền trọn gói -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
              <div class="sm:col-span-4">
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Tên Sản Phẩm In (*)</label>
                <input
                  v-model="item.product_name"
                  type="text"
                  required
                  placeholder="Thẻ treo, Namecard, Tem decal, Hộp bánh..."
                  class="w-full text-xs rounded-lg px-3 py-2 border font-semibold"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                />
              </div>

              <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Số Lượng (*)</label>
                <input
                  v-model.number="item.quantity"
                  @input="onQtyChange(item)"
                  type="number"
                  step="any"
                  min="0.01"
                  required
                  class="w-full text-xs rounded-lg px-3 py-2 border font-bold"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                />
              </div>

              <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Đơn Vị</label>
                <input
                  v-model="item.unit"
                  type="text"
                  placeholder="Cái, Hộp, Tờ..."
                  class="w-full text-xs rounded-lg px-3 py-2 border"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                />
              </div>

              <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold text-amber-500 mb-1">Đơn Giá Bán (₫)</label>
                <input
                  v-model.number="item.unit_price"
                  @input="onUnitPriceChange(item)"
                  type="number"
                  min="0"
                  class="w-full text-xs rounded-lg px-3 py-2 border text-right font-bold text-amber-400"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
                />
              </div>

              <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold text-emerald-500 mb-1">Hoặc Tiền Trọn Gói</label>
                <input
                  v-model.number="item.line_total"
                  @input="onLineTotalChange(item)"
                  type="number"
                  min="0"
                  placeholder="Trọn gói"
                  class="w-full text-xs rounded-lg px-3 py-2 border text-right font-bold text-emerald-400"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
                />
              </div>
            </div>

            <!-- Row 2: Xưởng in, Đơn giá vốn, Tổng vốn xưởng, Thuế VAT xưởng -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-2.5 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
              <div class="sm:col-span-4">
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Xưởng In / Nhà Cung Cấp</label>
                <select
                  v-model="item.supplier_id"
                  @change="onSupplierSelect(item)"
                  class="w-full text-xs rounded-lg px-3 py-2 border font-medium"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                >
                  <option :value="null">-- Chọn xưởng in ấn / gia công --</option>
                  <option v-for="sup in suppliers" :key="sup.id" :value="sup.id">
                    {{ sup.name }}
                  </option>
                </select>
              </div>

              <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Đơn Giá Vốn Xưởng</label>
                <input
                  v-model.number="item.cost_price"
                  @input="onCostPriceChange(item)"
                  type="number"
                  min="0"
                  class="w-full text-xs rounded-lg px-3 py-2 border text-right"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                />
              </div>

              <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Tổng Vốn Xưởng (₫)</label>
                <input
                  v-model.number="item.cost_total"
                  @input="onCostTotalChange(item)"
                  type="number"
                  min="0"
                  placeholder="Trọn gói"
                  class="w-full text-xs rounded-lg px-3 py-2 border text-right font-semibold text-slate-300"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
                />
              </div>

              <div class="sm:col-span-4">
                <label class="block text-[11px] font-bold mb-1" :class="item.vat_input_rate > 0 ? 'text-amber-400 font-black' : 'text-slate-400'">
                  VAT Xưởng In Bắt Buộc Lấy
                </label>
                <select
                  v-model.number="item.vat_input_rate"
                  class="w-full text-xs rounded-lg px-3 py-2 border font-semibold"
                  :class="item.vat_input_rate > 0
                    ? (isDarkMode ? 'bg-amber-500/10 border-amber-500/40 text-amber-300' : 'bg-amber-50 border-amber-300 text-amber-800')
                    : (isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800')"
                >
                  <option :value="0">Xưởng KHÔNG tính VAT (0%)</option>
                  <option :value="8">Xưởng BẮT LẤY VAT 8%</option>
                  <option :value="10">Xưởng BẮT LẤY VAT 10%</option>
                </select>
              </div>

              <div class="sm:col-span-12">
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Quy Cách & Yêu Cầu Kỹ Thuật (Chất liệu, Kích thước, Cán màng, Bế nẹp...)</label>
                <input
                  v-model="item.specifications"
                  type="text"
                  placeholder="VD: Giấy C300 cán màng mờ 2 mặt, bế bo 4 góc, dán keo nắp sẵn..."
                  class="w-full text-xs rounded-lg px-3 py-2 border"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- CARD 3: CHIẾT KHẤU, VAT & TỔNG KẾT TÀI CHÍNH -->
      <div
        class="p-5 sm:p-6 rounded-2xl border shadow-sm space-y-5"
        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <div class="text-xs font-black uppercase tracking-wider text-brand-gold flex items-center gap-2 border-b pb-3" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
          <DollarSign class="w-4 h-4" />
          <span>3. Chiết Khấu, VAT Khách Hàng & Bảng Lợi Nhuận Gộp</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
          <!-- Chiết khấu -->
          <div class="sm:col-span-3">
            <label class="block text-xs font-bold text-slate-400 mb-1">Giảm Giá / Chiết Khấu (₫)</label>
            <input
              v-model.number="orderForm.discount"
              type="number"
              min="0"
              placeholder="0"
              class="w-full text-xs rounded-xl px-3 py-2.5 border text-right font-bold text-amber-400"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
            />
          </div>

          <!-- VAT Rate Pills -->
          <div class="sm:col-span-3">
            <label class="block text-xs font-bold text-slate-400 mb-1">Thuế VAT Khách Lấy</label>
            <div class="grid grid-cols-3 gap-1.5 pt-0.5">
              <button
                type="button"
                @click="setVatRate(0)"
                class="py-2 rounded-xl text-xs font-bold border transition-all cursor-pointer"
                :class="orderForm.vat_rate === 0
                  ? 'bg-brand-gold text-slate-950 border-brand-gold shadow-sm'
                  : (isDarkMode ? 'border-[#2E3A47] text-slate-300' : 'border-slate-200 text-slate-700')"
              >
                0%
              </button>
              <button
                type="button"
                @click="setVatRate(8)"
                class="py-2 rounded-xl text-xs font-bold border transition-all cursor-pointer"
                :class="orderForm.vat_rate === 8
                  ? 'bg-purple-600 text-white border-purple-600 shadow-sm'
                  : (isDarkMode ? 'border-[#2E3A47] text-slate-300' : 'border-slate-200 text-slate-700')"
              >
                8%
              </button>
              <button
                type="button"
                @click="setVatRate(10)"
                class="py-2 rounded-xl text-xs font-bold border transition-all cursor-pointer"
                :class="orderForm.vat_rate === 10
                  ? 'bg-purple-600 text-white border-purple-600 shadow-sm'
                  : (isDarkMode ? 'border-[#2E3A47] text-slate-300' : 'border-slate-200 text-slate-700')"
              >
                10%
              </button>
            </div>
          </div>

          <!-- Ship thu khách -->
          <div class="sm:col-span-3">
            <label class="block text-xs font-bold text-slate-400 mb-1">Phí Ship Thu Của Khách (₫)</label>
            <input
              v-model.number="orderForm.shipping_fee_customer"
              type="number"
              min="0"
              placeholder="0"
              class="w-full text-xs rounded-xl px-3 py-2.5 border text-right font-medium"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            />
          </div>

          <!-- Ship trả xưởng -->
          <div class="sm:col-span-3">
            <label class="block text-xs font-bold text-slate-400 mb-1">Phí Ship Trả Xưởng / Grab (₫)</label>
            <input
              v-model.number="orderForm.shipping_fee_shop"
              type="number"
              min="0"
              placeholder="0"
              class="w-full text-xs rounded-xl px-3 py-2.5 border text-right font-medium"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            />
          </div>

          <!-- Phụ thu -->
          <div class="sm:col-span-4">
            <label class="block text-xs font-bold text-slate-400 mb-1">Phụ Thu Thêm (Thiết kế, Giao gấp...)</label>
            <input
              v-model.number="orderForm.surcharge"
              type="number"
              min="0"
              placeholder="0"
              class="w-full text-xs rounded-xl px-3 py-2.5 border text-right font-medium"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            />
          </div>

          <!-- Phương thức cọc -->
          <div class="sm:col-span-4">
            <label class="block text-xs font-bold text-slate-400 mb-1">Tài Khoản / Phương Thức Cọc</label>
            <select
              v-model="orderForm.payment_method"
              class="w-full text-xs rounded-xl px-3 py-2.5 border font-semibold"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            >
              <option value="bank_acb">ACB (51189927 - CTY IN ẤN NHẬT NGUYÊN)</option>
              <option value="bank_mb">MB Bank (679838699 - HUYNH THANH TRUC)</option>
              <option value="bank_minh_nguyen">TK MINH NGUYEN</option>
              <option value="bank_truong_hung">TK TRUONG HUNG</option>
              <option value="cash">Tiền mặt tại cửa hàng</option>
            </select>
          </div>

          <!-- Tiền khách cọc -->
          <div class="sm:col-span-4">
            <label class="block text-xs font-bold text-emerald-400 mb-1">Khách Đã Đặt Cọc Trước (₫)</label>
            <input
              v-model.number="orderForm.paid_amount"
              type="number"
              min="0"
              placeholder="0"
              class="w-full text-xs rounded-xl px-3 py-2.5 border text-right font-bold text-emerald-400"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
            />
          </div>
        </div>

        <!-- 4-Column Live Financial Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 p-4 rounded-xl border bg-black/10" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
          <!-- 1. Tiền in -->
          <div class="p-3 rounded-lg border bg-black/15" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
            <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">1. Tiền In (Subtotal)</div>
            <div class="text-base sm:text-lg font-black mt-1">{{ formatMoney(calculated.subtotal) }}</div>
            <div v-if="orderForm.vat_rate > 0" class="text-[10px] text-purple-400 mt-0.5">
              +VAT {{ orderForm.vat_rate }}%: {{ formatMoney(calculated.vat_amount) }}
            </div>
          </div>

          <!-- 2. Tổng khách trả -->
          <div class="p-3 rounded-lg border bg-amber-500/5 border-amber-500/20">
            <div class="text-[10px] uppercase font-bold tracking-wider text-amber-500">2. Tổng Khách Trả (Bill)</div>
            <div class="text-lg sm:text-xl font-black text-amber-400 mt-1">{{ formatMoney(calculated.total_amount) }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">
              Đã cọc: <span class="text-emerald-400 font-bold">{{ formatMoney(orderForm.paid_amount) }}</span>
            </div>
          </div>

          <!-- 3. Nợ xưởng -->
          <div class="p-3 rounded-lg border bg-black/15" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
            <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">3. Tiền Nợ Xưởng In</div>
            <div class="text-base sm:text-lg font-black text-slate-300 mt-1">{{ formatMoney(calculated.total_supplier_debt) }}</div>
            <div v-if="calculated.supplier_vat_total > 0" class="text-[10px] text-amber-400 mt-0.5">
              (Gồm {{ formatMoney(calculated.supplier_vat_total) }} VAT xưởng)
            </div>
          </div>

          <!-- 4. Lợi nhuận gộp -->
          <div class="p-3 rounded-lg border bg-emerald-500/5 border-emerald-500/20">
            <div class="text-[10px] text-emerald-400 uppercase font-bold tracking-wider">4. Lợi Nhuận Gộp</div>
            <div class="text-lg sm:text-xl font-black text-emerald-400 mt-1">{{ formatMoney(calculated.profit_amount) }}</div>
            <div class="text-[10px] font-bold mt-0.5" :class="calculated.profit_margin >= 30 ? 'text-emerald-400' : 'text-amber-400'">
              Tỷ suất lời: {{ calculated.profit_margin }}%
            </div>
          </div>
        </div>

        <!-- VAT Notice -->
        <div v-if="calculated.vat_warning" class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-300 flex items-center gap-2">
          <span>⚠️ <strong>Lưu ý:</strong> {{ calculated.vat_warning }}</span>
        </div>
      </div>

      <!-- BOTTOM ACTION BUTTONS -->
      <div class="flex items-center justify-end gap-3 pt-4 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
        <router-link
          to="/he-thong-admin/orders"
          class="px-5 py-2.5 rounded-xl text-xs font-semibold border transition-all cursor-pointer"
          :class="isDarkMode ? 'border-[#2E3A47] text-slate-300 hover:bg-[#24303F]' : 'border-slate-200 text-slate-700 hover:bg-slate-100'"
        >
          Hủy Bỏ
        </router-link>

        <button
          type="submit"
          :disabled="isSubmitting"
          class="flex items-center gap-2 px-8 py-3 rounded-xl text-xs sm:text-sm font-black bg-brand-gold hover:bg-amber-400 text-slate-950 shadow-md shadow-brand-gold/20 transition-all cursor-pointer disabled:opacity-50"
        >
          <Loader2 v-if="isSubmitting" class="w-4 h-4 animate-spin" />
          <Save v-else class="w-4 h-4" />
          <span>{{ isSubmitting ? 'Đang lưu đơn hàng...' : (isEditing ? 'Cập Nhật Đơn Hàng' : 'Hoàn Tất & Tạo Đơn') }}</span>
        </button>
      </div>

    </form>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
  ArrowLeft,
  Plus,
  Trash2,
  Save,
  Loader2,
  Copy,
  Check,
  Sparkles,
  Calculator,
  UserCheck,
  DollarSign
} from 'lucide-vue-next'
import { api } from '../../../services/api'
import { useAdminTheme } from '../../../composables/useAdminTheme'

const { isDarkMode } = useAdminTheme()
const route = useRoute()
const router = useRouter()

const isEditing = ref(false)
const editingId = ref(null)
const editingCode = ref('')
const isSubmitting = ref(false)
const isLookingUp = ref(false)
const isCopied = ref(false)

const suppliers = ref([])

// Form State
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
  is_vat_required: false,
  shipping_fee_customer: 0,
  shipping_fee_shop: 0,
  surcharge: 0,
  paid_amount: 0,
  payment_method: 'bank_acb',
  items: [
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
})

// Preset Templates
const PRESET_TEMPLATES = [
  { name: 'Thẻ treo / Tag', quantity: 1000, unit: 'Cái', unit_price: 150, cost_price: 80, specifications: 'C300, cán mờ 2 mặt, đục lỗ 3mm' },
  { name: 'Namecard', quantity: 5, unit: 'Hộp', unit_price: 45000, cost_price: 25000, specifications: 'C300, cán màng mờ 2 mặt' },
  { name: 'Tem Decal Giấy', quantity: 1000, unit: 'Con', unit_price: 220, cost_price: 120, specifications: 'Decal giấy đế vàng, bế demi tròn D4cm' },
  { name: 'Hộp Giấy Kraft', quantity: 500, unit: 'Cái', unit_price: 4500, cost_price: 2800, specifications: 'Kraft 280gsm, bế dán hoàn thiện' },
  { name: 'Túi Giấy Ivory', quantity: 500, unit: 'Cái', unit_price: 8500, cost_price: 5200, specifications: 'Ivory 250gsm, xỏ quai dù bóng' },
  { name: 'Folder / Kẹp File', quantity: 500, unit: 'Cái', unit_price: 7500, cost_price: 4500, specifications: 'C300 cán màng mờ 1 mặt, tai gấp 7cm' }
]

// Real-time Financial Calculations
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
  const vatAmount = Math.max(0, (subtotal - discount)) * (vatRate / 100)
  const shipCustomer = Number(orderForm.shipping_fee_customer) || 0
  const shipShop = Number(orderForm.shipping_fee_shop) || 0
  const surcharge = Number(orderForm.surcharge) || 0

  const totalAmount = Math.max(0, (subtotal - discount) + vatAmount + shipCustomer + surcharge)
  const totalSupplierDebt = baseCost + supplierVatTotal + shipShop

  let profitAmount = 0
  let vatWarning = ''

  if (vatRate > 0) {
    const taxPayable = Math.max(0, vatAmount - supplierVatTotal)
    profitAmount = totalAmount - totalSupplierDebt - taxPayable
  } else {
    profitAmount = (subtotal - discount + surcharge) - totalSupplierDebt
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
  item.supplier_name = found ? found.name : ''
}

// Auto Customer Lookup by Phone
async function lookupCustomerPhone() {
  const p = orderForm.customer_phone?.trim()
  if (!p) return

  isLookingUp.value = true
  try {
    const res = await api.searchErpCustomerPhone(p)
    if (res?.data) {
      if (!orderForm.customer_name) orderForm.customer_name = res.data.name || ''
      if (!orderForm.customer_address) orderForm.customer_address = res.data.address || ''
      if (!orderForm.customer_company) orderForm.customer_company = res.data.company_name || ''
      if (!orderForm.customer_tax_code) orderForm.customer_tax_code = res.data.tax_code || ''
    }
  } catch (err) {
    console.error('Customer lookup error', err)
  } finally {
    isLookingUp.value = false
  }
}

// Copy Zalo Quote
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

  let discountText = ''
  if (orderForm.discount > 0) {
    discountText = `• Giảm giá: -${formatMoney(orderForm.discount)}\n`
  }
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
${discountText}${vatText}${shipText}${surchargeText}👉 TỔNG THANH TOÁN: ${formatMoney(calculated.value.total_amount)}
---------------------------------------
CÔNG TY THIẾT KẾ IN ẤN NHẬT NGUYÊN
Hotline / Zalo: 0359 005 469 - 076 759 8368
Địa chỉ: Đông Hưng Thuận 40, P. Đông Hưng Thuận, Q.12, TP.HCM
Cam kết in sắc nét, đúng tiến độ và hỗ trợ kiểm tra file tận tình!`

  navigator.clipboard.writeText(quoteMessage).then(() => {
    isCopied.value = true
    setTimeout(() => { isCopied.value = false }, 3000)
  }).catch(() => {
    prompt('Hãy copy báo giá sau:', quoteMessage)
  })
}

// Submit Order Form
async function submitOrder() {
  if (!orderForm.customer_name || !orderForm.customer_phone) {
    alert('Vui lòng điền đầy đủ Tên và SĐT khách hàng!')
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
      discount: orderForm.discount || 0,
      vat_rate: orderForm.vat_rate || 0,
      is_vat_required: orderForm.vat_rate > 0,
      shipping_fee_customer: orderForm.shipping_fee_customer || 0,
      shipping_fee_shop: orderForm.shipping_fee_shop || 0,
      surcharge: orderForm.surcharge || 0,
      items: orderForm.items.map(it => ({
        product_name: it.product_name,
        quantity: it.quantity,
        unit: it.unit || 'Cái',
        unit_price: it.unit_price || 0,
        line_total: it.line_total || (it.quantity * it.unit_price),
        supplier_id: it.supplier_id || null,
        supplier_name: it.supplier_name || '',
        cost_price: it.cost_price || 0,
        cost_total: it.cost_total || (it.quantity * it.cost_price),
        vat_input_rate: it.vat_input_rate || 0,
        specifications: it.specifications || ''
      }))
    }

    if (!isEditing.value && orderForm.paid_amount > 0) {
      payload.initial_payment = {
        amount: orderForm.paid_amount,
        payment_method: orderForm.payment_method || 'bank_acb',
        note: 'Tiền cọc khi tạo đơn'
      }
    }

    if (isEditing.value) {
      await api.updateErpOrder(editingId.value, payload)
      alert('✅ Cập nhật đơn hàng thành công!')
    } else {
      await api.createErpOrder(payload)
      alert('✅ Tạo đơn hàng in ấn mới thành công!')
    }

    router.push('/he-thong-admin/orders')
  } catch (err) {
    alert('Lỗi: ' + (err.response?.data?.message || err.message))
  } finally {
    isSubmitting.value = false
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

// Initial Data Load
async function loadOrderData(id) {
  try {
    const res = await api.getErpOrder(id)
    if (res?.data) {
      const order = res.data
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

      if (order.items && order.items.length > 0) {
        orderForm.items = order.items.map(it => ({
          product_name: it.product_name,
          quantity: it.quantity,
          unit: it.unit || 'Cái',
          unit_price: it.unit_price,
          line_total: it.line_total,
          supplier_id: it.supplier_id,
          supplier_name: it.supplier_name || '',
          cost_price: it.cost_price,
          cost_total: it.cost_total,
          vat_input_rate: it.vat_input_rate || 0,
          specifications: it.specifications || ''
        }))
      }
    }
  } catch (err) {
    alert('Không thể tải thông tin đơn hàng: ' + err.message)
    router.push('/he-thong-admin/orders')
  }
}

async function loadSuppliers() {
  try {
    const res = await api.getErpSuppliers()
    if (res?.data) {
      suppliers.value = res.data
    }
  } catch (err) {
    console.error('Failed to load suppliers', err)
  }
}

onMounted(() => {
  loadSuppliers()

  // If edit mode
  if (route.params.id) {
    loadOrderData(route.params.id)
  } else if (route.query.from_quote) {
    // Transfer from Web Quote Request
    const raw = sessionStorage.getItem('pending_order_from_quote')
    if (raw) {
      try {
        const qData = JSON.parse(raw)
        sessionStorage.removeItem('pending_order_from_quote')
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
      } catch (err) {}
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
</style>



