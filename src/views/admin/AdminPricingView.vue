<template>
  <div class="space-y-6 max-w-7xl mx-auto py-2">
    <!-- Top Bar Header & Quick Stats -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
      <div>
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-brand-gold/15 text-brand-gold flex items-center justify-center font-bold">
            <Calculator class="w-5 h-5" />
          </div>
          <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              Cấu Hình Bảng Giá Theo Từng Sản Phẩm
            </h1>
            <p class="text-xs text-slate-400 mt-0.5">
              Thiết lập bảng giá bậc thang theo số lượng, phụ phí kích thước, chất liệu giấy và gia công riêng cho từng ấn phẩm in
            </p>
          </div>
        </div>
      </div>

      <!-- Quick Stats Pills -->
      <div class="flex items-center gap-2 flex-wrap">
        <span
          class="px-3 py-1.5 rounded-xl text-xs font-bold border"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-300' : 'bg-white border-slate-200 text-slate-700'"
        >
          Tổng SP: <strong class="text-brand-gold">{{ products.length }}</strong>
        </span>
        <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
          ⚡ Tự động tính giá: {{ autoPricingCount }}
        </span>
        <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
          📞 Để liên hệ báo giá: {{ contactOnlyCount }}
        </span>
        <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-500/10 text-slate-400 border border-slate-500/20">
          Chưa cấu hình: {{ unconfiguredCount }}
        </span>
      </div>
    </div>

    <!-- Alert Banner (Success / Error) -->
    <div v-if="alertMessage" class="p-4 rounded-xl text-xs font-bold flex items-center justify-between border" :class="alertType === 'success' ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400' : 'bg-red-500/10 border-red-500/20 text-red-400'">
      <div class="flex items-center gap-2">
        <CheckCircle2 v-if="alertType === 'success'" class="w-4 h-4 shrink-0" />
        <AlertCircle v-else class="w-4 h-4 shrink-0" />
        <span>{{ alertMessage }}</span>
      </div>
      <button @click="alertMessage = ''" class="text-slate-400 hover:text-white">✕</button>
    </div>

    <!-- Main 2-Column Split Studio -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
      
      <!-- =================================================================== -->
      <!-- LEFT COLUMN: DANH SÁCH SẢN PHẨM (lg:col-span-4) -->
      <!-- =================================================================== -->
      <div
        class="lg:col-span-4 rounded-2xl border p-4 space-y-3.5 shadow-sm"
        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <div class="flex items-center justify-between border-b pb-2.5" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
          <span class="text-xs font-black uppercase tracking-wider text-brand-gold flex items-center gap-1.5">
            <Package class="w-4 h-4" />
            <span>Chọn Ấn Phẩm Cần Set Giá</span>
          </span>
          <span class="text-[11px] text-slate-400">{{ filteredProducts.length }} ấn phẩm</span>
        </div>

        <!-- Search & Category Filters -->
        <div class="space-y-2">
          <div class="relative">
            <Search class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Tìm theo tên hoặc slug..."
              class="w-full text-xs rounded-xl pl-8 pr-3 py-2 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            />
          </div>

          <select
            v-model="selectedCategory"
            class="w-full text-xs rounded-xl px-3 py-2 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
            :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
          >
            <option value="">Tất cả danh mục nhóm in</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
        </div>

        <!-- Scrollable Product List -->
        <div v-if="isLoading" class="p-8 text-center text-xs text-slate-400">
          <Loader2 class="w-5 h-5 animate-spin mx-auto mb-2 text-brand-gold" />
          <span>Đang tải danh sách ấn phẩm...</span>
        </div>

        <div v-else class="space-y-2 max-h-[70vh] overflow-y-auto custom-scrollbar pr-1">
          <div
            v-for="prod in filteredProducts"
            :key="prod.id"
            @click="selectProduct(prod)"
            class="p-3 rounded-xl border text-left transition-all cursor-pointer group flex items-start gap-3"
            :class="[
              selectedProduct?.id === prod.id
                ? 'border-brand-gold bg-brand-gold/10 ring-1 ring-brand-gold shadow-sm'
                : (isDarkMode ? 'bg-[#24303F]/60 border-[#2E3A47] hover:border-slate-500' : 'bg-slate-50 border-slate-200 hover:border-slate-300')
            ]"
          >
            <!-- Thumbnail -->
            <img
              :src="prod.image || '/logo-icon.png'"
              :alt="prod.name"
              class="w-12 h-12 rounded-lg object-cover shrink-0 border"
              :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
              @error="(e) => e.target.src = '/logo-icon.png'"
            />

            <!-- Info -->
            <div class="flex-1 min-w-0">
              <div class="font-bold text-xs truncate" :class="selectedProduct?.id === prod.id ? 'text-brand-gold' : (isDarkMode ? 'text-white' : 'text-slate-900')">
                {{ prod.name }}
              </div>
              <div class="text-[10px] text-slate-400 truncate mt-0.5">
                {{ prod.category?.name || 'Chưa phân danh mục' }} • {{ prod.unit || 'cái' }}
              </div>
              
              <!-- Status Pill -->
              <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                <span
                  v-if="prod.specs?.hide_pricing"
                  class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-400 border border-amber-500/30"
                >
                  📞 Liên hệ báo giá
                </span>
                <span
                  v-else-if="hasPricingSpecs(prod)"
                  class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/30"
                >
                  ✓ {{ prod.specs.quantities?.length || 0 }} mốc giá
                </span>
                <span
                  v-else
                  class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-slate-500/15 text-slate-400 border border-slate-500/30"
                >
                  Chưa cấu hình
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- =================================================================== -->
      <!-- RIGHT COLUMN: STUDIO CẤU HÌNH BẢNG GIÁ CHI TIẾT (lg:col-span-8) -->
      <!-- =================================================================== -->
      <div class="lg:col-span-8 space-y-6">
        
        <!-- Empty State When No Product Selected -->
        <div
          v-if="!selectedProduct"
          class="rounded-2xl border p-12 text-center space-y-3"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <div class="w-16 h-16 rounded-3xl bg-brand-gold/15 text-brand-gold flex items-center justify-center mx-auto">
            <Calculator class="w-8 h-8" />
          </div>
          <h3 class="text-base font-bold" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
            Chưa Chọn Ấn Phẩm Nào
          </h3>
          <p class="text-xs text-slate-400 max-w-md mx-auto">
            Vui lòng bấm chọn một sản phẩm ở danh sách bên trái để bắt đầu thiết lập bảng giá bậc thang và quy cách in ấn.
          </p>
        </div>

        <!-- Active Pricing Studio Editor -->
        <div v-else class="space-y-6">
          
          <!-- Product Header & Save Bar -->
          <div
            class="rounded-2xl border p-5 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
          >
            <div class="flex items-center gap-3">
              <img
                :src="selectedProduct.image || '/logo-icon.png'"
                class="w-12 h-12 rounded-xl object-cover shrink-0 border"
                :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
              />
              <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-gold">
                  {{ selectedProduct.category?.name || 'Ấn phẩm in' }}
                </span>
                <h2 class="text-base font-black truncate max-w-md" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                  {{ selectedProduct.name }}
                </h2>
                <div class="text-[11px] text-slate-400">
                  Đơn vị tính: <strong class="text-white">{{ selectedProduct.unit }}</strong> • Giá gốc sàn: <strong class="text-brand-gold">{{ formatCurrency(selectedProduct.base_price) }}₫</strong>
                </div>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <a
                :href="`/san-pham/${selectedProduct.slug}`"
                target="_blank"
                class="px-3 py-2 rounded-xl text-xs font-semibold border flex items-center gap-1.5 transition-colors"
                :class="isDarkMode ? 'border-[#2E3A47] text-slate-300 hover:text-white hover:bg-[#24303F]' : 'border-slate-200 text-slate-700 hover:bg-slate-100'"
                title="Xem trang sản phẩm ngoài website"
              >
                <span>Xem Ngoài Web</span>
                <ExternalLink class="w-3.5 h-3.5" />
              </a>

              <button
                type="button"
                @click="savePricingConfig"
                :disabled="isSaving"
                class="flex items-center gap-2 px-5 py-2 rounded-xl text-xs font-black bg-brand-gold hover:bg-amber-400 text-slate-950 shadow-md shadow-brand-gold/20 transition-all cursor-pointer disabled:opacity-50"
              >
                <Loader2 v-if="isSaving" class="w-4 h-4 animate-spin" />
                <Save v-else class="w-4 h-4" />
                <span>{{ isSaving ? 'Đang lưu...' : 'Lưu Bảng Giá' }}</span>
              </button>
            </div>
          </div>

          <!-- Display Mode Toggle Banner (Bật Tính Giá Tự Động vs Ẩn Chuyển Sang Liên Hệ Báo Giá) -->
          <div
            class="rounded-2xl border p-4.5 transition-all shadow-sm"
            :class="[
              pricingSpecs.hide_pricing
                ? (isDarkMode ? 'bg-amber-950/20 border-amber-500/40' : 'bg-amber-50/80 border-amber-300')
                : (isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200')
            ]"
          >
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div class="flex items-start sm:items-center gap-3">
                <div
                  class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 font-bold shadow-xs"
                  :class="pricingSpecs.hide_pricing ? 'bg-amber-500 text-slate-950' : 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30'"
                >
                  <PhoneCall v-if="pricingSpecs.hide_pricing" class="w-5 h-5" />
                  <Zap v-else class="w-5 h-5" />
                </div>

                <div>
                  <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs font-black" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                      Chế Độ Hiển Thị Ngoài Website:
                    </span>
                    <span
                      v-if="pricingSpecs.hide_pricing"
                      class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-500 text-slate-950 shadow-xs"
                    >
                      📞 Đang Ẩn Bảng Giá • Hiện "Liên Hệ Báo Giá"
                    </span>
                    <span
                      v-else
                      class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/40"
                    >
                      ⚡ Đang Bật Bảng Tính Giá Tự Động
                    </span>
                  </div>
                  <p class="text-[11px] mt-1" :class="pricingSpecs.hide_pricing ? (isDarkMode ? 'text-amber-300' : 'text-amber-800') : 'text-slate-400'">
                    {{
                      pricingSpecs.hide_pricing
                        ? 'Khách vào trang chi tiết ấn phẩm sẽ thấy giao diện "Liên hệ nhận báo giá trực tiếp", gọi Hotline hoặc chat Zalo 5 phút thay vì bảng tính tiền tự động.'
                        : 'Khách hàng có thể tự chọn số lượng, kích thước, giấy và xem giá tiền ước tính trực tiếp.'
                    }}
                  </p>
                </div>
              </div>

              <!-- Action Button to Toggle -->
              <div class="flex items-center gap-2 shrink-0">
                <button
                  type="button"
                  @click="toggleHidePricing"
                  class="px-4 py-2.5 rounded-xl text-xs font-black transition-all cursor-pointer flex items-center gap-2 shadow-sm"
                  :class="[
                    pricingSpecs.hide_pricing
                      ? 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-emerald-600/20'
                      : 'bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-amber-500/20'
                  ]"
                >
                  <Eye v-if="pricingSpecs.hide_pricing" class="w-4 h-4" />
                  <EyeOff v-else class="w-4 h-4" />
                  <span>{{ pricingSpecs.hide_pricing ? 'Bật Lại Tính Giá Tự Động' : 'Ẩn Bảng Giá (Để Báo Giá)' }}</span>
                </button>
              </div>
            </div>
          </div>

          <!-- 1-Click Fast Template Bar -->
          <div
            class="rounded-2xl border p-4 space-y-2.5"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
          >
            <div class="flex items-center justify-between text-xs font-bold text-brand-gold">
              <span class="flex items-center gap-1.5">
                <Sparkles class="w-4 h-4" />
                <span>Nạp Mẫu Bảng Giá Chuẩn (1 Cú Click):</span>
              </span>
              <span class="text-[11px] text-slate-400 font-normal">
                Bấm vào mẫu để tự động điền các mốc giá phổ biến, sau đó bạn có thể sửa lại giá tùy ý
              </span>
            </div>

            <div class="flex items-center gap-2 overflow-x-auto pb-1 custom-scrollbar">
              <button
                v-for="tmpl in FORM_PRESETS"
                :key="tmpl.id"
                type="button"
                @click="applyFormPreset(tmpl)"
                class="px-3 py-1.5 rounded-xl text-xs font-bold border transition-all whitespace-nowrap hover:scale-105 cursor-pointer flex items-center gap-1.5"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] hover:border-brand-gold text-slate-200' : 'bg-slate-50 border-slate-200 hover:border-brand-gold text-slate-700'"
              >
                <span>{{ tmpl.icon }}</span>
                <span>{{ tmpl.name }}</span>
              </button>
            </div>
          </div>

          <!-- SECTION 1: BẢNG GIÁ BẬC THANG THEO SỐ LƯỢNG (QUANTITIES) -->
          <div
            class="rounded-2xl border p-5 space-y-4 shadow-sm"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
          >
            <div class="flex items-center justify-between border-b pb-3" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
              <div>
                <h3 class="text-xs font-black uppercase tracking-wider text-brand-gold flex items-center gap-1.5">
                  <Coins class="w-4 h-4" />
                  <span>1. Bảng Giá Bậc Thang Theo Số Lượng (In Càng Nhiều Giá Càng Rẻ)</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">
                  Đây là các mốc số lượng hiển thị cho khách bấm chọn ngoài website
                </p>
              </div>

              <button
                type="button"
                @click="addQuantityRow"
                class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-gold text-slate-950 hover:bg-amber-400 cursor-pointer"
              >
                <Plus class="w-3.5 h-3.5" />
                <span>Thêm Mốc Số Lượng</span>
              </button>
            </div>

            <!-- Quantities Table -->
            <div class="overflow-x-auto">
              <table class="w-full text-xs text-left">
                <thead>
                  <tr class="border-b text-slate-400 text-[10px] uppercase font-bold" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
                    <th class="py-2 px-2">Nhãn Hiển Thị Ngoài Web</th>
                    <th class="py-2 px-2 w-28">Số Lượng</th>
                    <th class="py-2 px-2 w-36">Đơn Giá / {{ selectedProduct.unit }} (₫)</th>
                    <th class="py-2 px-2 w-36 text-right">Tổng Tiền Ước Tính</th>
                    <th class="py-2 px-2 w-12 text-center">Xóa</th>
                  </tr>
                </thead>
                <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
                  <tr v-if="!pricingSpecs.quantities || pricingSpecs.quantities.length === 0">
                    <td colspan="5" class="py-4 text-center text-slate-400 italic">
                      Chưa có mốc số lượng nào. Hãy bấm "Thêm Mốc Số Lượng" hoặc chọn mẫu nhanh phía trên.
                    </td>
                  </tr>

                  <tr v-for="(qty, idx) in pricingSpecs.quantities" :key="idx" class="hover:bg-white/5 transition-colors">
                    <td class="py-2 px-2">
                      <input
                        v-model="qty.label"
                        type="text"
                        placeholder="VD: 5 hộp (500 cái) - Khuyên dùng"
                        class="w-full rounded-lg px-2.5 py-1.5 border text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-brand-gold"
                        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                      />
                    </td>
                    <td class="py-2 px-2">
                      <input
                        v-model.number="qty.count"
                        type="number"
                        min="1"
                        class="w-full rounded-lg px-2.5 py-1.5 border text-xs font-bold text-center focus:outline-none focus:ring-1 focus:ring-brand-gold"
                        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                      />
                    </td>
                    <td class="py-2 px-2">
                      <input
                        v-model.number="qty.pricePerUnit"
                        type="number"
                        min="0"
                        class="w-full rounded-lg px-2.5 py-1.5 border text-xs font-black text-right text-amber-400 focus:outline-none focus:ring-1 focus:ring-brand-gold"
                        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
                      />
                    </td>
                    <td class="py-2 px-2 text-right font-black text-emerald-400">
                      {{ formatCurrency(qty.count * qty.pricePerUnit) }}₫
                    </td>
                    <td class="py-2 px-2 text-center">
                      <button
                        type="button"
                        @click="removeQuantityRow(idx)"
                        class="p-1 rounded text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-colors"
                      >
                        <Trash2 class="w-3.5 h-3.5" />
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- SECTION 2: KÍCH THƯỚC & PHỤ PHÍ (SIZES) -->
          <div
            class="rounded-2xl border p-5 space-y-4 shadow-sm"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
          >
            <div class="flex items-center justify-between border-b pb-3" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
              <div>
                <h3 class="text-xs font-black uppercase tracking-wider text-brand-gold flex items-center gap-1.5">
                  <Ruler class="w-4 h-4" />
                  <span>2. Kích Thước Ấn Phẩm & Phụ Phí (Sizes)</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">
                  Quy cách kích thước chuẩn (phụ phí 0đ) hoặc kích thước lớn hơn (+ phụ phí)
                </p>
              </div>

              <button
                type="button"
                @click="addSizeRow"
                class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold border hover:border-brand-gold transition-colors cursor-pointer"
                :class="isDarkMode ? 'border-[#2E3A47] text-slate-200' : 'border-slate-200 text-slate-700'"
              >
                <Plus class="w-3.5 h-3.5 text-brand-gold" />
                <span>Thêm Kích Thước</span>
              </button>
            </div>

            <!-- Sizes Table -->
            <div class="overflow-x-auto">
              <table class="w-full text-xs text-left">
                <thead>
                  <tr class="border-b text-slate-400 text-[10px] uppercase font-bold" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
                    <th class="py-2 px-2">Tên / Kích Thước Cụ Thể</th>
                    <th class="py-2 px-2 w-36">Mã (Value)</th>
                    <th class="py-2 px-2 w-36 text-right">Phụ Phí Thêm (₫)</th>
                    <th class="py-2 px-2 w-12 text-center">Xóa</th>
                  </tr>
                </thead>
                <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
                  <tr v-if="!pricingSpecs.sizes || pricingSpecs.sizes.length === 0">
                    <td colspan="4" class="py-3 text-center text-slate-400 italic">
                      Không có tùy chọn kích thước (sản phẩm cố định 1 khổ in).
                    </td>
                  </tr>

                  <tr v-for="(sz, idx) in pricingSpecs.sizes" :key="idx" class="hover:bg-white/5 transition-colors">
                    <td class="py-2 px-2">
                      <input
                        v-model="sz.label"
                        type="text"
                        placeholder="VD: Khổ A5 (14.8 x 21 cm)"
                        class="w-full rounded-lg px-2.5 py-1.5 border text-xs font-medium focus:outline-none focus:ring-1 focus:ring-brand-gold"
                        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                      />
                    </td>
                    <td class="py-2 px-2">
                      <input
                        v-model="sz.value"
                        type="text"
                        placeholder="a5, a4..."
                        class="w-full rounded-lg px-2.5 py-1.5 border text-xs font-mono focus:outline-none"
                        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                      />
                    </td>
                    <td class="py-2 px-2">
                      <input
                        v-model.number="sz.extra"
                        type="number"
                        min="0"
                        placeholder="0"
                        class="w-full rounded-lg px-2.5 py-1.5 border text-xs font-bold text-right text-amber-400 focus:outline-none"
                        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
                      />
                    </td>
                    <td class="py-2 px-2 text-center">
                      <button
                        type="button"
                        @click="removeSizeRow(idx)"
                        class="p-1 rounded text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-colors"
                      >
                        <Trash2 class="w-3.5 h-3.5" />
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- SECTION 3: CHẤT LIỆU GIẤY / BẠT & PHỤ PHÍ (MATERIALS) -->
          <div
            class="rounded-2xl border p-5 space-y-4 shadow-sm"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
          >
            <div class="flex items-center justify-between border-b pb-3" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
              <div>
                <h3 class="text-xs font-black uppercase tracking-wider text-brand-gold flex items-center gap-1.5">
                  <Layers class="w-4 h-4" />
                  <span>3. Chất Liệu Giấy / Decal / Bạt & Phụ Phí (Materials)</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">
                  Định lượng giấy thông dụng (C300, Kraft, Ivory, Decal nhựa sữa, Decal trong...)
                </p>
              </div>

              <button
                type="button"
                @click="addMaterialRow"
                class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold border hover:border-brand-gold transition-colors cursor-pointer"
                :class="isDarkMode ? 'border-[#2E3A47] text-slate-200' : 'border-slate-200 text-slate-700'"
              >
                <Plus class="w-3.5 h-3.5 text-brand-gold" />
                <span>Thêm Chất Liệu</span>
              </button>
            </div>

            <!-- Materials Table -->
            <div class="overflow-x-auto">
              <table class="w-full text-xs text-left">
                <thead>
                  <tr class="border-b text-slate-400 text-[10px] uppercase font-bold" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
                    <th class="py-2 px-2">Tên Chất Liệu</th>
                    <th class="py-2 px-2 w-36">Mã (Value)</th>
                    <th class="py-2 px-2 w-36 text-right">Phụ Phí Thêm (₫)</th>
                    <th class="py-2 px-2 w-12 text-center">Xóa</th>
                  </tr>
                </thead>
                <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
                  <tr v-if="!pricingSpecs.materials || pricingSpecs.materials.length === 0">
                    <td colspan="4" class="py-3 text-center text-slate-400 italic">
                      Không có tùy chọn chất liệu (mặc định 1 loại giấy).
                    </td>
                  </tr>

                  <tr v-for="(mat, idx) in pricingSpecs.materials" :key="idx" class="hover:bg-white/5 transition-colors">
                    <td class="py-2 px-2">
                      <input
                        v-model="mat.label"
                        type="text"
                        placeholder="VD: Giấy Couche 300gsm (Chuẩn xưởng)"
                        class="w-full rounded-lg px-2.5 py-1.5 border text-xs font-medium focus:outline-none focus:ring-1 focus:ring-brand-gold"
                        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                      />
                    </td>
                    <td class="py-2 px-2">
                      <input
                        v-model="mat.value"
                        type="text"
                        placeholder="c300, kraft..."
                        class="w-full rounded-lg px-2.5 py-1.5 border text-xs font-mono focus:outline-none"
                        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                      />
                    </td>
                    <td class="py-2 px-2">
                      <input
                        v-model.number="mat.extra"
                        type="number"
                        min="0"
                        placeholder="0"
                        class="w-full rounded-lg px-2.5 py-1.5 border text-xs font-bold text-right text-amber-400 focus:outline-none"
                        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
                      />
                    </td>
                    <td class="py-2 px-2 text-center">
                      <button
                        type="button"
                        @click="removeMaterialRow(idx)"
                        class="p-1 rounded text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-colors"
                      >
                        <Trash2 class="w-3.5 h-3.5" />
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- SECTION 4: QUY CÁCH GIA CÔNG SAU IN (FINISHINGS) -->
          <div
            class="rounded-2xl border p-5 space-y-4 shadow-sm"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
          >
            <div class="flex items-center justify-between border-b pb-3" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
              <div>
                <h3 class="text-xs font-black uppercase tracking-wider text-brand-gold flex items-center gap-1.5">
                  <Sparkles class="w-4 h-4" />
                  <span>4. Quy Cách Gia Công Sau In & Phụ Phí (Finishings)</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">
                  Cán màng mờ / bóng, ép kim vàng, dập nổi, bế demi, cấn đường gấp...
                </p>
              </div>

              <button
                type="button"
                @click="addFinishingRow"
                class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold border hover:border-brand-gold transition-colors cursor-pointer"
                :class="isDarkMode ? 'border-[#2E3A47] text-slate-200' : 'border-slate-200 text-slate-700'"
              >
                <Plus class="w-3.5 h-3.5 text-brand-gold" />
                <span>Thêm Gia Công</span>
              </button>
            </div>

            <!-- Finishings Table -->
            <div class="overflow-x-auto">
              <table class="w-full text-xs text-left">
                <thead>
                  <tr class="border-b text-slate-400 text-[10px] uppercase font-bold" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
                    <th class="py-2 px-2">Tên Quy Cách Gia Công</th>
                    <th class="py-2 px-2 w-36">Mã (Value)</th>
                    <th class="py-2 px-2 w-44 text-right">Phụ Phí Thêm / 1 {{ selectedProduct.unit || 'cái' }} (₫)</th>
                    <th class="py-2 px-2 w-12 text-center">Xóa</th>
                  </tr>
                </thead>
                <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
                  <tr v-if="!pricingSpecs.finishings || pricingSpecs.finishings.length === 0">
                    <td colspan="4" class="py-3 text-center text-slate-400 italic">
                      Không có tùy chọn gia công thêm.
                    </td>
                  </tr>

                  <tr v-for="(fin, idx) in pricingSpecs.finishings" :key="idx" class="hover:bg-white/5 transition-colors">
                    <td class="py-2 px-2">
                      <input
                        v-model="fin.label"
                        type="text"
                        placeholder="VD: Ép kim Logo vàng (+120k)"
                        class="w-full rounded-lg px-2.5 py-1.5 border text-xs font-medium focus:outline-none focus:ring-1 focus:ring-brand-gold"
                        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                      />
                    </td>
                    <td class="py-2 px-2">
                      <input
                        v-model="fin.value"
                        type="text"
                        placeholder="matte, foil, emboss..."
                        class="w-full rounded-lg px-2.5 py-1.5 border text-xs font-mono focus:outline-none"
                        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                      />
                    </td>
                    <td class="py-2 px-2">
                      <input
                        v-model.number="fin.extra"
                        type="number"
                        min="0"
                        placeholder="0"
                        class="w-full rounded-lg px-2.5 py-1.5 border text-xs font-bold text-right text-amber-400 focus:outline-none"
                        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
                      />
                    </td>
                    <td class="py-2 px-2 text-center">
                      <button
                        type="button"
                        @click="removeFinishingRow(idx)"
                        class="p-1 rounded text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-colors"
                      >
                        <Trash2 class="w-3.5 h-3.5" />
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- SECTION 5: THÔNG SỐ QUY CÁCH IN CHI TIẾT (CUSTOM SPECIFICATIONS) -->
          <div
            class="rounded-2xl border p-5 space-y-4 shadow-sm"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
          >
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b pb-3" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
              <div>
                <h3 class="text-xs font-black uppercase tracking-wider text-brand-gold flex items-center gap-1.5">
                  <Sliders class="w-4 h-4" />
                  <span>5. Thông Số Quy Cách In Ấn Chi Tiết (Màu sắc, Loại giấy, Cán màng...)</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">
                  Các thông số này sẽ hiển thị trực tiếp tại mục "QUY CÁCH IN" ngoài website (ví dụ: Màu sắc - 2 màu, Loại giấy - Decal lưới...).
                </p>
              </div>

              <button
                type="button"
                @click="addCustomSpecRow('', '')"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-brand-gold text-slate-950 hover:bg-amber-400 transition-all cursor-pointer shadow-xs self-start sm:self-auto"
              >
                <Plus class="w-3.5 h-3.5" />
                <span>Thêm Dòng Quy Cách</span>
              </button>
            </div>

            <!-- Gợi ý nạp nhanh các thông số thông dụng -->
            <div class="flex flex-wrap items-center gap-1.5 text-xs">
              <span class="text-[11px] text-slate-400 font-semibold mr-1">Thêm nhanh:</span>
              <button
                type="button"
                @click="quickAddCustomSpecRow('Màu sắc', 'In 4 màu CMYK chuẩn nét')"
                class="px-2.5 py-1 rounded-lg border text-[11px] font-semibold transition-all hover:border-brand-gold cursor-pointer"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-700'"
              >
                + Màu sắc
              </button>
              <button
                type="button"
                @click="quickAddCustomSpecRow('Loại giấy / Chất liệu', 'Decal lưới dán kính chống nắng (nhìn 1 chiều)')"
                class="px-2.5 py-1 rounded-lg border text-[11px] font-semibold transition-all hover:border-brand-gold cursor-pointer"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-700'"
              >
                + Loại giấy / Decal
              </button>
              <button
                type="button"
                @click="quickAddCustomSpecRow('Kỹ thuật in', 'In kỹ thuật số UV / Mực dầu cao cấp ngoài trời')"
                class="px-2.5 py-1 rounded-lg border text-[11px] font-semibold transition-all hover:border-brand-gold cursor-pointer"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-700'"
              >
                + Kỹ thuật in
              </button>
              <button
                type="button"
                @click="quickAddCustomSpecRow('Cán màng / Gia công', 'Màng mờ / Màng bóng bảo vệ chống nước')"
                class="px-2.5 py-1 rounded-lg border text-[11px] font-semibold transition-all hover:border-brand-gold cursor-pointer"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-700'"
              >
                + Cán màng
              </button>
              <button
                type="button"
                @click="quickAddCustomSpecRow('Kích thước chuẩn', 'Theo kích thước thực tế hoặc theo file đặt in')"
                class="px-2.5 py-1 rounded-lg border text-[11px] font-semibold transition-all hover:border-brand-gold cursor-pointer"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-700'"
              >
                + Kích thước
              </button>
              <button
                type="button"
                @click="quickAddCustomSpecRow('Độ bền ngoài trời', 'Kháng nước, chống nắng 1 - 3 năm không phai màu')"
                class="px-2.5 py-1 rounded-lg border text-[11px] font-semibold transition-all hover:border-brand-gold cursor-pointer"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-700'"
              >
                + Độ bền
              </button>
              <button
                type="button"
                @click="quickAddCustomSpecRow('Thành phẩm', 'Cắt xén thẳng / Bế demi theo viền')"
                class="px-2.5 py-1 rounded-lg border text-[11px] font-semibold transition-all hover:border-brand-gold cursor-pointer"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-700'"
              >
                + Thành phẩm
              </button>
            </div>

            <!-- Bảng danh sách thông số quy cách in -->
            <div class="rounded-xl border overflow-hidden" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
              <div v-if="!pricingSpecs.custom_specs || pricingSpecs.custom_specs.length === 0" class="p-6 text-center text-slate-400 space-y-2">
                <Sliders class="w-8 h-8 mx-auto opacity-30" />
                <p class="text-xs">Chưa có thông số quy cách riêng cho sản phẩm này.</p>
                <p class="text-[11px] text-slate-500">Bấm các nút thêm nhanh ở trên hoặc "Thêm Dòng Quy Cách" để tùy chỉnh (Ví dụ: Màu sắc - 2 màu, Loại giấy - Decal lưới...).</p>
              </div>

              <div v-else class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
                <div
                  v-for="(spec, idx) in pricingSpecs.custom_specs"
                  :key="idx"
                  class="p-3 flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 hover:bg-black/5 transition-colors"
                >
                  <!-- Tên quy cách -->
                  <div class="sm:w-1/3">
                    <input
                      v-model="spec.name"
                      type="text"
                      placeholder="Tên thông số (Vd: Màu sắc, Loại giấy...)"
                      class="w-full rounded-lg px-3 py-1.5 text-xs font-bold border focus:outline-none focus:ring-1 focus:ring-brand-gold"
                      :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-900'"
                    />
                  </div>

                  <!-- Giá trị quy cách -->
                  <div class="flex-1">
                    <input
                      v-model="spec.value"
                      type="text"
                      placeholder="Giá trị quy cách (Vd: 2 màu, Decal lưới chống nắng...)"
                      class="w-full rounded-lg px-3 py-1.5 text-xs font-medium border focus:outline-none focus:ring-1 focus:ring-brand-gold"
                      :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                    />
                  </div>

                  <!-- Nút xóa -->
                  <button
                    type="button"
                    @click="removeCustomSpecRow(idx)"
                    class="self-end sm:self-auto p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-colors cursor-pointer"
                    title="Xóa dòng này"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- SECTION 6: BẢNG GIẢ LẬP TÍNH GIÁ TRỰC TIẾP (LIVE SIMULATOR) -->
          <div
            class="rounded-2xl border p-5 space-y-4 shadow-sm"
            :class="isDarkMode ? 'bg-[#1F2937]/50 border-brand-gold/30' : 'bg-amber-50/50 border-amber-200'"
          >
            <div class="flex items-center justify-between border-b pb-2" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-amber-200/60'">
              <span class="text-xs font-black uppercase tracking-wider text-brand-gold flex items-center gap-1.5">
                <Sliders class="w-4 h-4" />
                <span>Thử Nghiệm Tính Giá Trực Tiếp (Khách Xem Ngoài Web Sẽ Thấy Như Thế Này)</span>
              </span>
              <span class="text-[11px] text-slate-400">Kiểm tra công thức trước khi Lưu</span>
            </div>

            <!-- Simulator Controls -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
              <!-- Select Quantity -->
              <div>
                <label class="block text-[10px] font-bold text-slate-400 mb-1">Mốc Số Lượng:</label>
                <select
                  v-model="simQuantityIndex"
                  class="w-full rounded-lg px-2.5 py-1.5 border font-bold text-xs"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                >
                  <option v-for="(q, idx) in pricingSpecs.quantities" :key="idx" :value="idx">
                    {{ q.label }} ({{ formatCurrency(q.pricePerUnit) }}₫)
                  </option>
                </select>
              </div>

              <!-- Select Size -->
              <div>
                <label class="block text-[10px] font-bold text-slate-400 mb-1">Kích Thước:</label>
                <select
                  v-model="simSizeIndex"
                  class="w-full rounded-lg px-2.5 py-1.5 border text-xs"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                >
                  <option v-for="(sz, idx) in pricingSpecs.sizes" :key="idx" :value="idx">
                    {{ sz.label }} (+{{ formatCurrency(sz.extra) }}₫)
                  </option>
                </select>
              </div>

              <!-- Select Material -->
              <div>
                <label class="block text-[10px] font-bold text-slate-400 mb-1">Chất Liệu:</label>
                <select
                  v-model="simMaterialIndex"
                  class="w-full rounded-lg px-2.5 py-1.5 border text-xs"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                >
                  <option v-for="(mat, idx) in pricingSpecs.materials" :key="idx" :value="idx">
                    {{ mat.label }} (+{{ formatCurrency(mat.extra) }}₫)
                  </option>
                </select>
              </div>

              <!-- Select Finishing -->
              <div>
                <label class="block text-[10px] font-bold text-slate-400 mb-1">Gia Công:</label>
                <select
                  v-model="simFinishingIndex"
                  class="w-full rounded-lg px-2.5 py-1.5 border text-xs"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                >
                  <option v-for="(fin, idx) in pricingSpecs.finishings" :key="idx" :value="idx">
                    {{ fin.label }} (+{{ formatCurrency(fin.extra) }}₫)
                  </option>
                </select>
              </div>
            </div>

            <!-- Simulated Total Box -->
            <div class="p-3.5 rounded-xl bg-slate-950 text-white flex items-center justify-between shadow-inner">
              <div>
                <span class="text-[10px] text-slate-400 uppercase tracking-wider block">Tổng Tiền Dự Kiến (Khách Trả Chưa VAT):</span>
                <div class="text-2xl font-black text-brand-gold mt-0.5">
                  {{ formatCurrency(simulatedCalculation.total) }} ₫
                </div>
              </div>
              <div class="text-right">
                <span class="text-[10px] text-slate-400 block">Đơn giá chia bình quân:</span>
                <div class="text-sm font-bold text-white mt-0.5">
                  {{ formatCurrency(simulatedCalculation.unitPrice) }} ₫ / {{ selectedProduct.unit }}
                </div>
              </div>
            </div>
          </div>

          <!-- Bottom Action Buttons -->
          <div class="flex items-center justify-end gap-3 pt-4 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
            <button
              type="button"
              @click="savePricingConfig"
              :disabled="isSaving"
              class="flex items-center gap-2 px-8 py-3 rounded-xl text-xs sm:text-sm font-black bg-brand-gold hover:bg-amber-400 text-slate-950 shadow-md shadow-brand-gold/20 transition-all cursor-pointer disabled:opacity-50"
            >
              <Loader2 v-if="isSaving" class="w-4 h-4 animate-spin" />
              <Save v-else class="w-4 h-4" />
              <span>{{ isSaving ? 'Đang lưu cấu hình...' : 'Lưu Bảng Giá Cho Sản Phẩm Này' }}</span>
            </button>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import {
  Calculator,
  Package,
  Search,
  Plus,
  Trash2,
  Save,
  Loader2,
  ExternalLink,
  Sparkles,
  Coins,
  Ruler,
  Layers,
  Sliders,
  CheckCircle2,
  AlertCircle,
  Eye,
  EyeOff,
  Zap,
  PhoneCall
} from 'lucide-vue-next'
import { api } from '../../services/api'
import { useAdminTheme } from '../../composables/useAdminTheme'
import { swalToast, swalConfirm } from '../../utils/swal'

const { isDarkMode } = useAdminTheme()
const route = useRoute()

const products = ref([])
const categories = ref([])
const isLoading = ref(true)
const isSaving = ref(false)
const selectedCategory = ref('')
const searchQuery = ref('')
const alertMessage = ref('')
const alertType = ref('success')

const selectedProduct = ref(null)

// Working Pricing Matrix State
const pricingSpecs = reactive({
  hide_pricing: false,
  quantities: [],
  sizes: [],
  materials: [],
  finishings: [],
  custom_specs: []
})

// Simulator indices
const simQuantityIndex = ref(0)
const simSizeIndex = ref(0)
const simMaterialIndex = ref(0)
const simFinishingIndex = ref(0)

// Quick Preset Templates for Printing Industry
const FORM_PRESETS = [
  {
    id: 'namecard',
    icon: '📇',
    name: 'Mẫu Namecard / Danh Thiếp',
    specs: {
      quantities: [
        { label: '2 hộp (200 cái) - In nhanh', count: 2, pricePerUnit: 50000 },
        { label: '5 hộp (500 cái) - Khuyên dùng', count: 5, pricePerUnit: 35000 },
        { label: '10 hộp (1.000 cái) - Giá xưởng', count: 10, pricePerUnit: 25000 },
        { label: '20 hộp (2.000 cái) - Đại lý', count: 20, pricePerUnit: 20000 }
      ],
      sizes: [
        { label: 'Tiêu chuẩn (9.0 x 5.4 cm)', value: 'standard', extra: 0 },
        { label: 'Khổ nhỏ (9.0 x 5.0 cm)', value: 'small', extra: 0 },
        { label: 'Bo tròn 4 góc (+30k/hộp)', value: 'rounded', extra: 30000 }
      ],
      materials: [
        { label: 'Giấy Couche 300gsm (Chuẩn xưởng dày dặn)', value: 'c300', extra: 0 },
        { label: 'Giấy Mỹ Thuật vân sần cao cấp', value: 'art_paper', extra: 60000 },
        { label: 'Nhựa Plastic trong suốt xé không rách', value: 'plastic', extra: 120000 },
        { label: 'Giấy Kraft tái chế vintage', value: 'kraft', extra: 25000 }
      ],
      finishings: [
        { label: 'Cán màng mờ 2 mặt (Khuyên dùng)', value: 'matte', extra: 0 },
        { label: 'Cán màng bóng 2 mặt', value: 'glossy', extra: 0 },
        { label: 'Ép kim vàng / bạc Logo (+120k)', value: 'foil', extra: 120000 },
        { label: 'Dập nổi định hình Logo (+100k)', value: 'emboss', extra: 100000 }
      ]
    }
  },
  {
    id: 'decal',
    icon: '🏷️',
    name: 'Mẫu Tem Nhãn Decal',
    specs: {
      quantities: [
        { label: '500 con tem (In nhanh)', count: 500, pricePerUnit: 400 },
        { label: '1.000 con tem (Phổ biến)', count: 1000, pricePerUnit: 220 },
        { label: '2.000 con tem (Tiết kiệm)', count: 2000, pricePerUnit: 170 },
        { label: '5.000 con tem (Giá xưởng)', count: 5000, pricePerUnit: 110 }
      ],
      sizes: [
        { label: 'Tròn đường kính D3cm', value: 'd3', extra: 0 },
        { label: 'Tròn đường kính D4cm', value: 'd4', extra: 50 },
        { label: 'Tròn đường kính D5cm', value: 'd5', extra: 100 },
        { label: 'Chữ nhật 4 x 6 cm', value: 'rect_4x6', extra: 80 }
      ],
      materials: [
        { label: 'Decal Giấy đế vàng', value: 'paper_decal', extra: 0 },
        { label: 'Decal Nhựa Sữa (Chống nước 100%)', value: 'pvc_white', extra: 50 },
        { label: 'Decal Nhựa Trong Suốt', value: 'pvc_clear', extra: 60 },
        { label: 'Decal Xi Bạc ánh kim loại', value: 'silver', extra: 150 },
        { label: 'Decal Vỡ (Niêm phong, bảo hành)', value: 'fragile', extra: 120 }
      ],
      finishings: [
        { label: 'Cán màng bóng bảo vệ', value: 'glossy', extra: 0 },
        { label: 'Cán màng mờ sang trọng', value: 'matte', extra: 0 },
        { label: 'Bế demi thành phẩm từng tờ', value: 'sheet', extra: 0 },
        { label: 'Cắt rời từng con tem riêng', value: 'single', extra: 30 }
      ]
    }
  },
  {
    id: 'box',
    icon: '📦',
    name: 'Mẫu Hộp Giấy Bao Bì',
    specs: {
      quantities: [
        { label: '300 hộp (Số lượng ít)', count: 300, pricePerUnit: 6500 },
        { label: '500 hộp (Tiêu chuẩn)', count: 500, pricePerUnit: 4800 },
        { label: '1.000 hộp (Giá gốc xưởng)', count: 1000, pricePerUnit: 3400 },
        { label: '2.000 hộp (Đại lý)', count: 2000, pricePerUnit: 2600 }
      ],
      sizes: [
        { label: 'Hộp nhỏ (10 x 6 x 6 cm)', value: 'small_box', extra: 0 },
        { label: 'Hộp vừa (15 x 10 x 8 cm)', value: 'medium_box', extra: 800 },
        { label: 'Hộp lớn (22 x 15 x 10 cm)', value: 'large_box', extra: 1600 }
      ],
      materials: [
        { label: 'Giấy Ivory 300gsm (Trắng mịn cao cấp)', value: 'ivory_300', extra: 0 },
        { label: 'Giấy Ivory 350gsm (Dày cứng cao cấp)', value: 'ivory_350', extra: 400 },
        { label: 'Giấy Kraft 280gsm (Vintage mộc mạc)', value: 'kraft_280', extra: 0 },
        { label: 'Carton sóng E bồi Duplex (Hộp ship hàng)', value: 'corrugated_e', extra: 1200 }
      ],
      finishings: [
        { label: 'Cán màng mờ + Bế dán hoàn thiện', value: 'matte_glued', extra: 0 },
        { label: 'Cán màng bóng + Bế dán hoàn thiện', value: 'glossy_glued', extra: 0 },
        { label: 'Ép kim Logo vàng / bạc (+150k)', value: 'foil_logo', extra: 150000 },
        { label: 'Cửa sổ dán màng bóng kính (PVC)', value: 'window', extra: 300 }
      ]
    }
  },
  {
    id: 'bag',
    icon: '🛍️',
    name: 'Mẫu Túi Giấy Quà Tặng',
    specs: {
      quantities: [
        { label: '200 túi (Số lượng ít)', count: 200, pricePerUnit: 11000 },
        { label: '500 túi (Khuyên dùng)', count: 500, pricePerUnit: 8200 },
        { label: '1.000 túi (Giá xưởng)', count: 1000, pricePerUnit: 6400 },
        { label: '2.000 túi (Tiết kiệm)', count: 2000, pricePerUnit: 5200 }
      ],
      sizes: [
        { label: 'Túi nhỏ (20 x 15 x 8 cm)', value: 'small_bag', extra: 0 },
        { label: 'Túi đứng vừa (30 x 20 x 10 cm)', value: 'medium_bag', extra: 1200 },
        { label: 'Túi lớn (35 x 25 x 12 cm)', value: 'large_bag', extra: 2400 }
      ],
      materials: [
        { label: 'Ivory 250gsm (Trắng cứng cáp)', value: 'ivory_250', extra: 0 },
        { label: 'Kraft Nâu 170gsm (Thân thiện môi trường)', value: 'kraft_brown', extra: 0 },
        { label: 'Couche 250gsm cán màng', value: 'c250_matte', extra: 300 }
      ],
      finishings: [
        { label: 'Xỏ quai dù bóng (Đủ màu)', value: 'rope_poly', extra: 0 },
        { label: 'Quai ruy băng lụa cao cấp', value: 'ribbon', extra: 800 },
        { label: 'Quai giấy xoắn', value: 'twisted_paper', extra: 0 },
        { label: 'Ép kim vàng 2 mặt túi', value: 'foil_gold', extra: 250000 }
      ]
    }
  },
  {
    id: 'flyer',
    icon: '📄',
    name: 'Mẫu Tờ Rơi / Brochure',
    specs: {
      quantities: [
        { label: '500 tờ (In nhanh kỹ thuật số)', count: 500, pricePerUnit: 850 },
        { label: '1.000 tờ (In Offset chuẩn màu)', count: 1000, pricePerUnit: 550 },
        { label: '2.000 tờ (Tiết kiệm)', count: 2000, pricePerUnit: 420 },
        { label: '5.000 tờ (Giá đại lý)', count: 5000, pricePerUnit: 310 }
      ],
      sizes: [
        { label: 'Khổ A5 (14.8 x 21.0 cm)', value: 'a5', extra: 0 },
        { label: 'Khổ A4 (21.0 x 29.7 cm)', value: 'a4', extra: 450 },
        { label: 'Khổ A3 gấp đôi (42.0 x 29.7 cm)', value: 'a3', extra: 1100 }
      ],
      materials: [
        { label: 'Couche 150gsm (Mỏng nhẹ phát đại trà)', value: 'c150', extra: 0 },
        { label: 'Couche 200gsm (Dày vừa sang trọng)', value: 'c200', extra: 200 },
        { label: 'Couche 250gsm (Dày cứng cao cấp)', value: 'c250', extra: 350 }
      ],
      finishings: [
        { label: 'Không cán màng (Tiết kiệm)', value: 'none', extra: 0 },
        { label: 'Cán màng mờ 2 mặt', value: 'matte', extra: 250 },
        { label: 'Cấn 1 đường gấp đôi', value: 'fold_1', extra: 50 },
        { label: 'Cấn 2 đường gấp 3 (Z-fold / C-fold)', value: 'fold_2', extra: 100 }
      ]
    }
  },
  {
    id: 'tag',
    icon: '🏷️',
    name: 'Mẫu Thẻ Treo / Tag Quần Áo',
    specs: {
      quantities: [
        { label: '500 cái (In nhanh)', count: 500, pricePerUnit: 350 },
        { label: '1.000 cái (Phổ biến)', count: 1000, pricePerUnit: 180 },
        { label: '2.000 cái (Tiết kiệm)', count: 2000, pricePerUnit: 130 },
        { label: '5.000 cái (Giá xưởng)', count: 5000, pricePerUnit: 90 }
      ],
      sizes: [
        { label: 'Chữ nhật (4.5 x 9 cm)', value: 'rect_45x90', extra: 0 },
        { label: 'Vuông (5 x 5 cm)', value: 'square_50x50', extra: 0 },
        { label: 'Tròn hoặc hình dáng bế theo yêu cầu', value: 'custom_shape', extra: 100000 }
      ],
      materials: [
        { label: 'Couche 300gsm cán màng mờ', value: 'c300', extra: 0 },
        { label: 'Giấy Kraft 300gsm phong cách mộc', value: 'kraft', extra: 30 },
        { label: 'Giấy Bồi 2 lớp siêu dày (600gsm)', value: 'bonded_600', extra: 150 }
      ],
      finishings: [
        { label: 'Đục lỗ 3mm chuẩn', value: 'hole_3mm', extra: 0 },
        { label: 'Đục lỗ 5mm', value: 'hole_5mm', extra: 0 },
        { label: 'Kèm dây chỉ xỏ tag sẵn', value: 'string', extra: 50 },
        { label: 'Ép kim vàng Logo nhãn hiệu', value: 'foil', extra: 120000 }
      ]
    }
  }
]

// Filtered products list
const filteredProducts = computed(() => {
  return products.value.filter(p => {
    const matchCat = !selectedCategory.value || p.category_id === Number(selectedCategory.value) || p.category?.id === Number(selectedCategory.value)
    const matchSearch = !searchQuery.value.trim() ||
      p.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      p.slug.toLowerCase().includes(searchQuery.value.toLowerCase())
    return matchCat && matchSearch
  })
})

const contactOnlyCount = computed(() => {
  return products.value.filter(p => Boolean(p.specs?.hide_pricing)).length
})

const autoPricingCount = computed(() => {
  return products.value.filter(p => !p.specs?.hide_pricing && hasPricingSpecs(p)).length
})

const unconfiguredCount = computed(() => {
  return products.value.filter(p => !p.specs?.hide_pricing && !hasPricingSpecs(p)).length
})

function hasPricingSpecs(prod) {
  return Boolean(prod?.specs && Array.isArray(prod.specs.quantities) && prod.specs.quantities.length > 0)
}

function selectProduct(prod) {
  selectedProduct.value = prod
  alertMessage.value = ''

  let specsObj = prod.specs
  if (typeof specsObj === 'string') {
    try { specsObj = JSON.parse(specsObj) } catch (e) { specsObj = {} }
  }

  if (specsObj && typeof specsObj === 'object') {
    pricingSpecs.hide_pricing = Boolean(specsObj.hide_pricing)
    pricingSpecs.quantities = Array.isArray(specsObj.quantities) ? JSON.parse(JSON.stringify(specsObj.quantities)) : []
    pricingSpecs.sizes = Array.isArray(specsObj.sizes) ? JSON.parse(JSON.stringify(specsObj.sizes)) : []
    pricingSpecs.materials = Array.isArray(specsObj.materials) ? JSON.parse(JSON.stringify(specsObj.materials)) : []
    pricingSpecs.finishings = Array.isArray(specsObj.finishings) ? JSON.parse(JSON.stringify(specsObj.finishings)) : []
    pricingSpecs.custom_specs = Array.isArray(specsObj.custom_specs) ? JSON.parse(JSON.stringify(specsObj.custom_specs)) : []
  } else {
    // If not configured, auto guess preset or leave empty
    pricingSpecs.hide_pricing = false
    pricingSpecs.quantities = [
      { label: `500 ${prod.unit || 'cái'}`, count: 500, pricePerUnit: prod.base_price || 1000 },
      { label: `1.000 ${prod.unit || 'cái'}`, count: 1000, pricePerUnit: Math.round((prod.base_price || 1000) * 0.75) }
    ]
    pricingSpecs.sizes = []
    pricingSpecs.materials = []
    pricingSpecs.finishings = []
    pricingSpecs.custom_specs = []
  }

  simQuantityIndex.value = 0
  simSizeIndex.value = 0
  simMaterialIndex.value = 0
  simFinishingIndex.value = 0
}

function addCustomSpecRow(name = '', value = '') {
  if (!pricingSpecs.custom_specs) pricingSpecs.custom_specs = []
  pricingSpecs.custom_specs.push({ name, value })
}

function removeCustomSpecRow(idx) {
  if (pricingSpecs.custom_specs) {
    pricingSpecs.custom_specs.splice(idx, 1)
  }
}

function quickAddCustomSpecRow(name, defaultValue) {
  if (!pricingSpecs.custom_specs) pricingSpecs.custom_specs = []
  const existing = pricingSpecs.custom_specs.find(s => s.name.toLowerCase() === name.toLowerCase())
  if (existing) {
    existing.value = defaultValue
  } else {
    pricingSpecs.custom_specs.push({ name, value: defaultValue })
  }
}

function toggleHidePricing() {
  pricingSpecs.hide_pricing = !pricingSpecs.hide_pricing
  if (pricingSpecs.hide_pricing) {
    showAlert('Đã chuyển sang chế độ "Liên hệ báo giá". Vui lòng bấm "Lưu Bảng Giá" để cập nhật ra website!', 'success')
  } else {
    showAlert('Đã bật lại chế độ "Tính giá tự động". Vui lòng bấm "Lưu Bảng Giá" để cập nhật ra website!', 'success')
  }
}

async function applyFormPreset(tmpl) {
  const confirmed = await swalConfirm({
    title: 'Áp dụng bảng giá mẫu?',
    text: `Bạn có chắc muốn áp dụng "${tmpl.name}" cho sản phẩm này? Các mốc hiện tại sẽ được thay thế bằng mẫu chuẩn.`,
    icon: 'question',
    confirmText: 'Áp dụng ngay',
    cancelText: 'Hủy'
  })
  if (!confirmed) return

  pricingSpecs.quantities = JSON.parse(JSON.stringify(tmpl.specs.quantities))
  pricingSpecs.sizes = JSON.parse(JSON.stringify(tmpl.specs.sizes))
  pricingSpecs.materials = JSON.parse(JSON.stringify(tmpl.specs.materials))
  pricingSpecs.finishings = JSON.parse(JSON.stringify(tmpl.specs.finishings))
  if (Array.isArray(tmpl.specs.custom_specs)) {
    pricingSpecs.custom_specs = JSON.parse(JSON.stringify(tmpl.specs.custom_specs))
  }

  simQuantityIndex.value = 0
  simSizeIndex.value = 0
  simMaterialIndex.value = 0
  simFinishingIndex.value = 0

  showAlert(`Đã nạp ${tmpl.name}! Hãy kiểm tra lại đơn giá và bấm "Lưu Bảng Giá".`, 'success')
}

// Table row modifiers
function addQuantityRow() {
  const last = pricingSpecs.quantities[pricingSpecs.quantities.length - 1]
  const count = last ? last.count * 2 : 500
  const price = last ? Math.round(last.pricePerUnit * 0.8) : (selectedProduct.value?.base_price || 1000)
  pricingSpecs.quantities.push({
    label: `${count} ${selectedProduct.value?.unit || 'cái'}`,
    count,
    pricePerUnit: price
  })
}

function removeQuantityRow(idx) {
  pricingSpecs.quantities.splice(idx, 1)
}

function addSizeRow() {
  pricingSpecs.sizes.push({
    label: 'Kích thước mới',
    value: `size_${pricingSpecs.sizes.length + 1}`,
    extra: 0
  })
}

function removeSizeRow(idx) {
  pricingSpecs.sizes.splice(idx, 1)
}

function addMaterialRow() {
  pricingSpecs.materials.push({
    label: 'Chất liệu giấy mới',
    value: `mat_${pricingSpecs.materials.length + 1}`,
    extra: 0
  })
}

function removeMaterialRow(idx) {
  pricingSpecs.materials.splice(idx, 1)
}

function addFinishingRow() {
  pricingSpecs.finishings.push({
    label: 'Quy cách gia công mới',
    value: `fin_${pricingSpecs.finishings.length + 1}`,
    extra: 0
  })
}

function removeFinishingRow(idx) {
  pricingSpecs.finishings.splice(idx, 1)
}

// Live Simulated Calculation
const simulatedCalculation = computed(() => {
  const q = pricingSpecs.quantities[simQuantityIndex.value] || { count: 1, pricePerUnit: selectedProduct.value?.base_price || 0 }
  const sz = pricingSpecs.sizes[simSizeIndex.value] || { extra: 0 }
  const mat = pricingSpecs.materials[simMaterialIndex.value] || { extra: 0 }
  const fin = pricingSpecs.finishings[simFinishingIndex.value] || { extra: 0 }

  const count = Number(q.count) || 1
  const pricePerUnit = Number(q.pricePerUnit) || 0
  const extra = (Number(sz.extra) || 0) + (Number(mat.extra) || 0) + (Number(fin.extra) || 0)

  // Phụ phí cộng thêm trên mỗi đơn vị sản phẩm (cái/hộp/tờ) rồi nhân với số lượng
  const unitPrice = Math.max(0, pricePerUnit + extra)
  const total = Math.max(0, unitPrice * count)

  return { total, unitPrice }
})

// Save pricing config to database
async function savePricingConfig() {
  if (!selectedProduct.value) return

  isSaving.value = true
  alertMessage.value = ''

  try {
    const payload = {
      specs: {
        hide_pricing: Boolean(pricingSpecs.hide_pricing),
        quantities: pricingSpecs.quantities,
        sizes: pricingSpecs.sizes,
        materials: pricingSpecs.materials,
        finishings: pricingSpecs.finishings,
        custom_specs: pricingSpecs.custom_specs || []
      }
    }

    const res = await api.updateAdminProduct(selectedProduct.value.id, payload)
    if (res?.data) {
      selectedProduct.value.specs = res.data.specs
      // Update in products array
      const found = products.value.find(p => p.id === selectedProduct.value.id)
      if (found) {
        found.specs = res.data.specs
      }
    }

    showAlert(`✅ Đã lưu cấu hình bảng giá thành công cho sản phẩm "${selectedProduct.value.name}"! Khách ngoài website sẽ thấy bảng tính mới ngay lập tức.`, 'success')
  } catch (err) {
    showAlert('Lỗi khi lưu bảng giá: ' + (err.response?.data?.message || err.message), 'error')
  } finally {
    isSaving.value = false
  }
}

function showAlert(msg, type = 'success') {
  alertMessage.value = msg
  alertType.value = type
  swalToast(msg, type === 'error' ? 'error' : 'success')
}

function formatCurrency(num) {
  return new Intl.NumberFormat('vi-VN').format(Math.round(num || 0))
}

// Initial Data Load
async function loadData() {
  isLoading.value = true
  try {
    const [pRes, cRes] = await Promise.all([
      api.getProducts(),
      api.getCategories()
    ])

    products.value = pRes.data || []
    categories.value = cRes.data || []

    // If query product_id is passed, auto select it
    if (route.query.product_id) {
      const target = products.value.find(p => p.id === Number(route.query.product_id))
      if (target) {
        selectProduct(target)
      }
    } else if (products.value.length > 0) {
      selectProduct(products.value[0])
    }
  } catch (err) {
    console.error('Failed to load products for pricing config', err)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  loadData()
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

