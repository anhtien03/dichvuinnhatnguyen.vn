<template>
  <div class="max-w-7xl mx-auto py-2 sm:py-4 space-y-6">
    <!-- Top Bar Navigation -->
    <div class="flex items-center justify-between gap-4">
      <router-link
        to="/he-thong-admin/products"
        class="inline-flex items-center gap-2 text-xs font-bold transition-colors cursor-pointer"
        :class="isDarkMode ? 'text-slate-400 hover:text-white' : 'text-slate-600 hover:text-slate-900'"
      >
        <ArrowLeft class="w-4 h-4" />
        <span>← Quay lại danh sách ấn phẩm</span>
      </router-link>

      <div class="flex items-center gap-3">
        <span
          class="text-[11px] font-bold px-3 py-1 rounded-full border"
          :class="isEditMode
            ? 'bg-amber-500/10 text-amber-500 border-amber-500/20'
            : 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20'"
        >
          {{ isEditMode ? 'Chế độ chỉnh sửa' : 'Thêm ấn phẩm mới' }}
        </span>
      </div>
    </div>

    <!-- 2 CỘT: CỘT TRÁI ĐIỀN THÔNG TIN - CỘT PHẢI LIVE PREVIEW THEO THỜI GIAN THỰC -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      
      <!-- ===================================================================== -->
      <!-- CỘT TRÁI (7 COLS): FORM NHẬP THÔNG TIN DỌC TRUYỀN THỐNG -->
      <!-- ===================================================================== -->
      <div class="lg:col-span-7 space-y-6">
        <div
          class="rounded-2xl border shadow-sm overflow-hidden"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <!-- Card Header -->
          <div
            class="p-5 sm:p-6 border-b flex items-center justify-between"
            :class="isDarkMode ? 'border-[#2E3A47] bg-[#1C2434]/40' : 'border-slate-100 bg-slate-50/50'"
          >
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-brand-gold/15 text-brand-gold flex items-center justify-center font-bold shrink-0">
                <Edit3 v-if="isEditMode" class="w-5 h-5" />
                <PackagePlus v-else class="w-5 h-5" />
              </div>
              <div>
                <h1 class="text-lg font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                  {{ isEditMode ? 'Chỉnh Sửa Ấn Phẩm In' : 'Thêm Mới Ấn Phẩm In' }}
                </h1>
                <p class="text-xs mt-0.5" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
                  Điền thông tin bên dưới, khung bên phải sẽ tự động hiển thị bản xem trước theo thời gian thực.
                </p>
              </div>
            </div>
          </div>

          <!-- Error Alert Banner -->
          <div v-if="errorMessage" class="m-5 sm:m-6 mb-0 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs flex items-center gap-2">
            <AlertCircle class="w-4 h-4 shrink-0" />
            <span>{{ errorMessage }}</span>
          </div>

          <!-- Success Alert Banner -->
          <div v-if="successMessage" class="m-5 sm:m-6 mb-0 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs flex items-center gap-2 font-bold">
            <CheckCircle2 class="w-4 h-4 shrink-0" />
            <span>{{ successMessage }}</span>
          </div>

          <!-- FORM DỌC -->
          <form @submit.prevent="handleSubmit" class="p-5 sm:p-6 space-y-6 text-xs">
            
            <!-- PHẦN 1: THÔNG TIN CƠ BẢN -->
            <div class="space-y-4">
              <h2 class="text-xs font-black uppercase tracking-wider text-brand-gold border-b pb-2 flex items-center gap-2" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
                <Package class="w-4 h-4" />
                <span>1. Thông Tin Chi Tiết Ấn Phẩm</span>
              </h2>

              <!-- Tên Ấn Phẩm -->
              <div>
                <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                  Tên Ấn Phẩm In <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.name"
                  @input="handleNameChange"
                  type="text"
                  required
                  placeholder="Ví dụ: In Hộp Trà Đông Trùng Hạ Thảo Cao Cấp"
                  class="w-full rounded-xl px-4 py-2.5 text-sm border focus:outline-none focus:ring-2 focus:ring-brand-gold transition-all"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-slate-50 border-slate-200 text-slate-800 placeholder-slate-400'"
                />
              </div>

              <!-- Đường Dẫn SEO (Slug) -->
              <div>
                <div class="flex items-center justify-between mb-1.5">
                  <label class="font-bold" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                    Đường Dẫn SEO (URL Slug) <span class="text-red-500">*</span>
                  </label>
                  <button
                    type="button"
                    @click="autoGenerateSlug"
                    class="text-[11px] text-brand-gold hover:underline font-semibold cursor-pointer"
                  >
                    Tạo tự động từ tên
                  </button>
                </div>
                <div class="relative flex items-center">
                  <span
                    class="px-3 py-2.5 text-xs font-mono rounded-l-xl border border-r-0 select-none hidden sm:inline-block"
                    :class="isDarkMode ? 'bg-[#1C2434]/80 border-[#2E3A47] text-slate-400' : 'bg-slate-100 border-slate-200 text-slate-500'"
                  >
                    /san-pham/
                  </span>
                  <input
                    v-model="form.slug"
                    type="text"
                    required
                    placeholder="in-hop-tra-dong-trung-ha-thao"
                    class="w-full rounded-xl sm:rounded-l-none px-4 py-2.5 text-xs font-mono border focus:outline-none focus:ring-2 focus:ring-brand-gold transition-all"
                    :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                  />
                </div>
              </div>

              <!-- Danh Mục -->
              <div>
                <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                  Thuộc Danh Mục Cha <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.category_id"
                  required
                  class="w-full rounded-xl px-4 py-2.5 text-sm border focus:outline-none focus:ring-2 focus:ring-brand-gold cursor-pointer transition-all"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                >
                  <option value="">-- Chọn danh mục in ấn (VD: In thẻ & danh thiếp) --</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                    {{ cat.parent ? `📂 [${cat.parent.name}] › ${cat.name}` : `⭐ ${cat.name} (Nhóm ngành lớn)` }}
                  </option>
                </select>
              </div>

              <!-- Giá Cơ Sở & Đơn Vị Tính -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                    Giá Gốc Chỉ Từ (VNĐ) <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <input
                      v-model.number="form.base_price"
                      type="number"
                      min="0"
                      step="any"
                      required
                      placeholder="200"
                      class="w-full rounded-xl px-4 py-2.5 text-sm font-mono border focus:outline-none focus:ring-2 focus:ring-brand-gold transition-all"
                      :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                    />
                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 pointer-events-none">VNĐ</span>
                  </div>
                </div>

                <div>
                  <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                    Đơn Vị Tính <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="form.unit"
                    type="text"
                    required
                    placeholder="cái, hộp, tờ, cuốn, bộ..."
                    class="w-full rounded-xl px-4 py-2.5 text-sm border focus:outline-none focus:ring-2 focus:ring-brand-gold transition-all"
                    :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                  />
                </div>
              </div>

              <!-- Thời Gian In & Số Lượng Tối Thiểu -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                    Thời Gian Hoàn Thành (In Ấn)
                  </label>
                  <input
                    v-model="form.turnaround"
                    type="text"
                    placeholder="24h - 48h, Lấy ngay trong ngày..."
                    class="w-full rounded-xl px-4 py-2.5 text-sm border focus:outline-none focus:ring-2 focus:ring-brand-gold transition-all"
                    :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                  />
                </div>

                <div>
                  <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                    Số Lượng Tối Thiểu Nhận In
                  </label>
                  <input
                    v-model.number="form.min_quantity"
                    type="number"
                    min="1"
                    placeholder="10"
                    class="w-full rounded-xl px-4 py-2.5 text-sm border focus:outline-none focus:ring-2 focus:ring-brand-gold transition-all"
                    :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                  />
                </div>
              </div>

              <!-- Huy Hiệu Tag -->
              <div>
                <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                  Huy Hiệu Tag (Hiển thị góc ảnh)
                </label>
                <input
                  v-model="form.tag"
                  type="text"
                  placeholder="Ví dụ: Giá xưởng, Số lượng ít, Bán chạy, In nhanh lấy ngay..."
                  class="w-full rounded-xl px-4 py-2.5 text-sm border focus:outline-none focus:ring-2 focus:ring-brand-gold transition-all"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                />
              </div>

              <!-- Ảnh Đại Diện Mockup (Tích hợp Thư Viện Media WordPress / Shopify) -->
              <div>
                <div class="flex items-center justify-between mb-1.5">
                  <label class="font-bold" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                    Ảnh Đại Diện Mockup Ấn Phẩm <span class="text-red-500">*</span>
                  </label>
                  <button
                    type="button"
                    @click="openMediaForThumbnail"
                    class="text-xs font-bold text-amber-500 hover:text-amber-400 flex items-center gap-1 cursor-pointer"
                  >
                    <Images class="w-3.5 h-3.5" />
                    <span>Mở Thư Viện Media Xưởng</span>
                  </button>
                </div>

                <div class="flex flex-col sm:flex-row items-start gap-3">
                  <!-- Thumbnail preview box -->
                  <div
                    @click="openMediaForThumbnail"
                    class="w-24 h-24 rounded-2xl border-2 border-dashed flex flex-col items-center justify-center shrink-0 overflow-hidden cursor-pointer group transition-all"
                    :class="[
                      form.image
                        ? 'border-brand-gold bg-black/10'
                        : (isDarkMode ? 'border-[#2E3A47] hover:border-brand-gold bg-[#1C2434]' : 'border-slate-300 hover:border-brand-gold bg-slate-50')
                    ]"
                    title="Bấm để mở thư viện ảnh hoặc tải ảnh mới"
                  >
                    <img
                      v-if="form.image"
                      :src="form.image"
                      alt="Thumbnail"
                      class="w-full h-full object-cover transition-transform group-hover:scale-105"
                      @error="handleImageError"
                    />
                    <div v-else class="text-center p-2 text-slate-400 group-hover:text-brand-gold">
                      <UploadCloud class="w-6 h-6 mx-auto mb-1" />
                      <span class="text-[10px] font-bold block">Chọn ảnh</span>
                    </div>
                  </div>

                  <!-- Quick Action Buttons & Input URL -->
                  <div class="flex-1 w-full space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                      <button
                        type="button"
                        @click="openMediaForThumbnail"
                        class="px-3 py-1.5 rounded-xl bg-brand-gold text-slate-950 font-bold text-xs flex items-center gap-1.5 shadow-sm hover:bg-amber-400 cursor-pointer"
                      >
                        <Images class="w-3.5 h-3.5" />
                        <span>Chọn Từ Thư Viện</span>
                      </button>

                      <button
                        type="button"
                        @click="triggerDirectUpload"
                        class="px-3 py-1.5 rounded-xl border font-bold text-xs flex items-center gap-1.5 cursor-pointer transition-colors"
                        :class="isDarkMode ? 'border-[#2E3A47] hover:bg-[#1C2434] text-slate-300' : 'border-slate-300 hover:bg-slate-100 text-slate-700'"
                      >
                        <UploadCloud class="w-3.5 h-3.5" />
                        <span>Tải Từ Máy Tính</span>
                      </button>

                      <input
                        ref="quickFileInputRef"
                        type="file"
                        accept="image/*"
                        class="hidden"
                        @change="handleQuickUpload"
                      />

                      <button
                        v-if="form.image"
                        type="button"
                        @click="form.image = ''"
                        class="px-2.5 py-1.5 rounded-xl text-red-400 hover:bg-red-500/10 font-bold text-xs cursor-pointer"
                      >
                        Xóa ảnh
                      </button>
                    </div>

                    <!-- Direct URL input -->
                    <input
                      v-model="form.image"
                      type="text"
                      placeholder="https://... hoặc /storage/products/..."
                      class="w-full rounded-xl px-3 py-2 text-xs font-mono border focus:outline-none focus:ring-1 focus:ring-brand-gold"
                      :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                    />
                    <p class="text-[11px] text-slate-400">
                      Bấm "Chọn Từ Thư Viện" để duyệt kho ảnh mẫu xưởng hoặc tải ảnh mới từ máy tính.
                    </p>
                  </div>
                </div>
              </div>

              <!-- Mô Tả Chi Tiết Ấn Phẩm (Rich Text & Image Editor) -->
              <div class="rounded-2xl border p-4 space-y-3" :class="isDarkMode ? 'bg-[#1C2434]/80 border-[#2E3A47]' : 'bg-slate-50/70 border-slate-200'">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-2.5 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
                  <div>
                    <label class="block font-bold text-xs sm:text-sm" :class="isDarkMode ? 'text-slate-200' : 'text-slate-800'">
                      Mô Tả Chi Tiết Ấn Phẩm & Hình Ảnh Minh Họa
                    </label>
                    <span class="text-[11px] text-slate-400">
                      Soạn thảo trực quan, định dạng tiêu đề, in đậm, danh sách và chèn nhiều ảnh thực tế vào bài mô tả sản phẩm.
                    </span>
                  </div>

                  <!-- Mode Toggle: Trực quan vs Mã HTML -->
                  <div class="flex items-center gap-1 bg-slate-500/10 p-1 rounded-xl self-start sm:self-auto">
                    <button
                      type="button"
                      @click="switchEditorMode('visual')"
                      class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all cursor-pointer"
                      :class="descEditorMode === 'visual' ? 'bg-brand-gold text-slate-950 shadow-xs' : 'text-slate-400 hover:text-white'"
                    >
                      👁 Soạn Trực Quan
                    </button>
                    <button
                      type="button"
                      @click="switchEditorMode('html')"
                      class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all cursor-pointer"
                      :class="descEditorMode === 'html' ? 'bg-brand-gold text-slate-950 shadow-xs' : 'text-slate-400 hover:text-white'"
                    >
                      &lt;/&gt; Mã HTML
                    </button>
                  </div>
                </div>

                <!-- WORD-STYLE RIBBON TOOLBAR -->
                <div class="rounded-xl border divide-y" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] divide-[#2E3A47]' : 'bg-white border-slate-200 divide-slate-100'">
                  <!-- ROW 1: Undo/Redo, Kiểu đoạn, Phông chữ, Cỡ chữ (A+/A-), B/I/U/S, Màu chữ, Tô sáng nền, Xóa định dạng -->
                  <div class="flex flex-wrap items-center gap-1.5 p-2">
                    <!-- Undo / Redo -->
                    <div class="flex items-center gap-0.5 pr-1.5 border-r" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
                      <button
                        type="button"
                        @mousedown.prevent="execWordCmd('undo')"
                        class="p-1.5 rounded-lg border transition-colors cursor-pointer hover:border-brand-gold"
                        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
                        title="Hoàn tác (Ctrl+Z)"
                      >
                        <Undo2 class="w-3.5 h-3.5" />
                      </button>
                      <button
                        type="button"
                        @mousedown.prevent="execWordCmd('redo')"
                        class="p-1.5 rounded-lg border transition-colors cursor-pointer hover:border-brand-gold"
                        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
                        title="Làm lại (Ctrl+Y)"
                      >
                        <Redo2 class="w-3.5 h-3.5" />
                      </button>
                    </div>

                    <!-- Kiểu đoạn văn (Heading / Paragraph) -->
                    <select
                      v-model="selectedHeading"
                      @change="applyHeadingBlock"
                      class="rounded-lg px-2 py-1 text-xs font-bold border focus:outline-none focus:border-brand-gold cursor-pointer"
                      :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                      title="Kiểu đoạn văn / Tiêu đề"
                    >
                      <option value="p">Đoạn văn thường</option>
                      <option value="h1">Tiêu đề lớn (H1)</option>
                      <option value="h2">Tiêu đề mục (H2)</option>
                      <option value="h3">Tiêu đề phụ (H3)</option>
                      <option value="h4">Tiêu đề nhỏ (H4)</option>
                      <option value="blockquote">Trích dẫn (“ ”)</option>
                    </select>

                    <!-- Phông chữ (Font Family) -->
                    <select
                      v-model="selectedFontFamily"
                      @change="applyFontFamily"
                      class="rounded-lg px-2 py-1 text-xs font-semibold border focus:outline-none focus:border-brand-gold cursor-pointer max-w-[130px]"
                      :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                      title="Chọn phông chữ (Font)"
                    >
                      <option value="">Phông mặc định</option>
                      <option value="Arial, sans-serif">Arial</option>
                      <option value="'Times New Roman', serif">Times New Roman</option>
                      <option value="Tahoma, sans-serif">Tahoma</option>
                      <option value="Verdana, sans-serif">Verdana</option>
                      <option value="Georgia, serif">Georgia</option>
                      <option value="'Courier New', monospace">Courier New</option>
                    </select>

                    <!-- Cỡ chữ (Font Size px + A+ / A-) -->
                    <div class="flex items-center gap-1 pr-1.5 border-r" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
                      <select
                        v-model="selectedFontSize"
                        @change="applyFontSizePx(selectedFontSize)"
                        class="rounded-lg px-2 py-1 text-xs font-bold border focus:outline-none focus:border-brand-gold cursor-pointer"
                        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-brand-gold' : 'bg-slate-50 border-slate-200 text-slate-900'"
                        title="Cỡ chữ (px)"
                      >
                        <option value="12px">12px</option>
                        <option value="13px">13px</option>
                        <option value="14px">14px (Chuẩn)</option>
                        <option value="15px">15px</option>
                        <option value="16px">16px</option>
                        <option value="18px">18px (Lớn)</option>
                        <option value="20px">20px</option>
                        <option value="24px">24px (Tiêu đề)</option>
                        <option value="28px">28px</option>
                        <option value="32px">32px</option>
                        <option value="36px">36px</option>
                      </select>

                      <button
                        type="button"
                        @mousedown.prevent="stepFontSize(1)"
                        class="px-2 py-1 rounded-lg text-xs font-black border transition-colors cursor-pointer hover:border-brand-gold"
                        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                        title="Tăng cỡ chữ (A+)"
                      >
                        A<sup class="text-[9px] text-brand-gold">+</sup>
                      </button>
                      <button
                        type="button"
                        @mousedown.prevent="stepFontSize(-1)"
                        class="px-2 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer hover:border-brand-gold"
                        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-700'"
                        title="Giảm cỡ chữ (A-)"
                      >
                        A<sup class="text-[9px]">-</sup>
                      </button>
                    </div>

                    <!-- B / I / U / S -->
                    <div class="flex items-center gap-1 pr-1.5 border-r" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
                      <button
                        type="button"
                        @mousedown.prevent="applyDescFormat('bold')"
                        class="w-7 h-7 rounded-lg text-xs font-black border transition-colors cursor-pointer hover:border-brand-gold flex items-center justify-center"
                        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                        title="In đậm (Ctrl+B)"
                      >
                        B
                      </button>
                      <button
                        type="button"
                        @mousedown.prevent="applyDescFormat('italic')"
                        class="w-7 h-7 rounded-lg text-xs italic font-serif font-bold border transition-colors cursor-pointer hover:border-brand-gold flex items-center justify-center"
                        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                        title="In nghiêng (Ctrl+I)"
                      >
                        I
                      </button>
                      <button
                        type="button"
                        @mousedown.prevent="applyDescFormat('underline')"
                        class="w-7 h-7 rounded-lg text-xs underline font-bold border transition-colors cursor-pointer hover:border-brand-gold flex items-center justify-center"
                        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                        title="Gạch chân (Ctrl+U)"
                      >
                        U
                      </button>
                      <button
                        type="button"
                        @mousedown.prevent="execWordCmd('strikeThrough')"
                        class="w-7 h-7 rounded-lg text-xs line-through font-bold border transition-colors cursor-pointer hover:border-brand-gold flex items-center justify-center"
                        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-700'"
                        title="Gạch ngang chữ"
                      >
                        S
                      </button>
                    </div>

                    <!-- Màu chữ (Text Color) & Tô nền (Highlight) -->
                    <div class="flex items-center gap-1.5 pr-1.5 border-r" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
                      <!-- Quick Text Colors -->
                      <div class="flex items-center gap-1 px-1.5 py-0.5 rounded-lg border" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'">
                        <span class="text-[10px] font-black pr-0.5" :style="{ color: currentTextColor }">A</span>
                        <button
                          v-for="c in QUICK_TEXT_COLORS"
                          :key="c.hex"
                          type="button"
                          @mousedown.prevent="applyTextColor(c.hex)"
                          class="w-3.5 h-3.5 rounded-full border border-white/20 cursor-pointer hover:scale-125 transition-transform"
                          :style="{ backgroundColor: c.hex }"
                          :title="'Màu chữ: ' + c.label"
                        ></button>
                        <input
                          type="color"
                          v-model="currentTextColor"
                          @input="applyTextColor(currentTextColor)"
                          class="w-4 h-4 p-0 border-0 bg-transparent cursor-pointer"
                          title="Chọn màu chữ tùy ý"
                        />
                      </div>

                      <!-- Quick Highlight Colors -->
                      <div class="flex items-center gap-1 px-1.5 py-0.5 rounded-lg border" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'">
                        <span class="text-[10px] font-bold px-1 rounded bg-yellow-300 text-slate-900" title="Tô nền chữ (Highlight)">ab</span>
                        <button
                          v-for="hc in QUICK_HIGHLIGHT_COLORS"
                          :key="hc.hex"
                          type="button"
                          @mousedown.prevent="applyHighlightColor(hc.hex)"
                          class="w-3.5 h-3.5 rounded border border-slate-300 cursor-pointer hover:scale-125 transition-transform"
                          :style="{ backgroundColor: hc.hex }"
                          :title="'Tô nền: ' + hc.label"
                        ></button>
                        <button
                          type="button"
                          @mousedown.prevent="applyHighlightColor('transparent')"
                          class="text-[10px] text-slate-400 hover:text-red-400 px-0.5 cursor-pointer"
                          title="Xóa màu nền tô sáng"
                        >
                          ✕
                        </button>
                      </div>
                    </div>

                    <!-- Xóa định dạng (Clear Formatting) -->
                    <button
                      type="button"
                      @mousedown.prevent="clearSelectedFormatting"
                      class="p-1.5 rounded-lg border transition-colors cursor-pointer hover:border-red-400 text-slate-400 hover:text-red-400"
                      :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
                      title="Xóa mọi định dạng vùng đang chọn (Đưa về chữ chuẩn)"
                    >
                      <Eraser class="w-3.5 h-3.5" />
                    </button>
                  </div>

                  <!-- ROW 2: Căn lề (Trái/Giữa/Phải/Đều), Danh sách (Chấm/Số), Thụt lề, Bảng, Link, Lưu ý & Chèn Ảnh -->
                  <div class="flex flex-wrap items-center justify-between gap-2 p-2">
                    <div class="flex flex-wrap items-center gap-1">
                      <!-- Alignment: Left, Center, Right, Justify -->
                      <div class="flex items-center gap-0.5 pr-1.5 border-r" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
                        <button
                          type="button"
                          @mousedown.prevent="execWordCmd('justifyLeft')"
                          class="p-1.5 rounded-lg border transition-colors cursor-pointer hover:border-brand-gold"
                          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
                          title="Căn lề trái"
                        >
                          <AlignLeft class="w-3.5 h-3.5" />
                        </button>
                        <button
                          type="button"
                          @mousedown.prevent="execWordCmd('justifyCenter')"
                          class="p-1.5 rounded-lg border transition-colors cursor-pointer hover:border-brand-gold"
                          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
                          title="Căn giữa"
                        >
                          <AlignCenter class="w-3.5 h-3.5" />
                        </button>
                        <button
                          type="button"
                          @mousedown.prevent="execWordCmd('justifyRight')"
                          class="p-1.5 rounded-lg border transition-colors cursor-pointer hover:border-brand-gold"
                          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
                          title="Căn lề phải"
                        >
                          <AlignRight class="w-3.5 h-3.5" />
                        </button>
                        <button
                          type="button"
                          @mousedown.prevent="execWordCmd('justifyFull')"
                          class="p-1.5 rounded-lg border transition-colors cursor-pointer hover:border-brand-gold"
                          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
                          title="Căn đều 2 bên (Justify)"
                        >
                          <AlignJustify class="w-3.5 h-3.5" />
                        </button>
                      </div>

                      <!-- Lists & Indent -->
                      <div class="flex items-center gap-0.5 pr-1.5 border-r" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
                        <button
                          type="button"
                          @mousedown.prevent="applyDescFormat('ul')"
                          class="p-1.5 rounded-lg border transition-colors cursor-pointer hover:border-brand-gold"
                          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
                          title="Danh sách dấu chấm tròn (Bullets)"
                        >
                          <List class="w-3.5 h-3.5" />
                        </button>
                        <button
                          type="button"
                          @mousedown.prevent="applyDescFormat('ol')"
                          class="p-1.5 rounded-lg border transition-colors cursor-pointer hover:border-brand-gold"
                          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
                          title="Danh sách đánh số (1, 2, 3...)"
                        >
                          <ListOrdered class="w-3.5 h-3.5" />
                        </button>
                        <button
                          type="button"
                          @mousedown.prevent="execWordCmd('outdent')"
                          class="p-1.5 rounded-lg border transition-colors cursor-pointer hover:border-brand-gold"
                          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
                          title="Giảm thụt lề"
                        >
                          <Outdent class="w-3.5 h-3.5" />
                        </button>
                        <button
                          type="button"
                          @mousedown.prevent="execWordCmd('indent')"
                          class="p-1.5 rounded-lg border transition-colors cursor-pointer hover:border-brand-gold"
                          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
                          title="Tăng thụt lề đầu dòng"
                        >
                          <Indent class="w-3.5 h-3.5" />
                        </button>
                      </div>

                      <!-- Bảng biểu, Liên kết, Đường kẻ ngang, Lưu ý -->
                      <div class="flex flex-wrap items-center gap-1">
                        <button
                          type="button"
                          @click="insertSampleTable"
                          class="px-2 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer hover:border-brand-gold flex items-center gap-1"
                          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
                          title="Chèn bảng thông số kỹ thuật / bảng giá"
                        >
                          <Table class="w-3.5 h-3.5 text-brand-gold" />
                          <span>Bảng</span>
                        </button>
                        <button
                          type="button"
                          @click="insertHyperlink"
                          class="px-2 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer hover:border-brand-gold flex items-center gap-1"
                          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
                          title="Chèn liên kết (Link)"
                        >
                          <Link2 class="w-3.5 h-3.5 text-blue-400" />
                          <span>Link</span>
                        </button>
                        <button
                          type="button"
                          @mousedown.prevent="execWordCmd('insertHorizontalRule')"
                          class="px-2 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer hover:border-brand-gold flex items-center gap-1"
                          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
                          title="Chèn đường kẻ ngang phân cách"
                        >
                          <Minus class="w-3.5 h-3.5" />
                        </button>
                        <button
                          type="button"
                          @click="applyDescFormat('tip')"
                          class="px-2.5 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer text-amber-500 hover:border-amber-500"
                          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
                          title="Chèn hộp lưu ý kỹ thuật"
                        >
                          💡 Lưu ý
                        </button>
                      </div>
                    </div>

                    <!-- Image Insert Buttons -->
                    <div class="flex flex-wrap items-center gap-1.5">
                      <button
                        type="button"
                        @click="openMediaForDescription"
                        class="px-2.5 py-1.5 rounded-lg bg-brand-gold/15 hover:bg-brand-gold/25 text-amber-500 border border-brand-gold/30 font-bold text-xs flex items-center gap-1.5 cursor-pointer transition-colors"
                      >
                        <Images class="w-3.5 h-3.5" />
                        <span>Chèn Ảnh Thư Viện</span>
                      </button>

                      <button
                        type="button"
                        @click="triggerDescImageUpload"
                        :disabled="isUploadingDescImage"
                        class="px-2.5 py-1.5 rounded-lg bg-brand-gold hover:bg-amber-400 text-slate-950 font-bold text-xs flex items-center gap-1.5 cursor-pointer transition-colors disabled:opacity-50"
                      >
                        <Loader2 v-if="isUploadingDescImage" class="w-3.5 h-3.5 animate-spin" />
                        <UploadCloud v-else class="w-3.5 h-3.5" />
                        <span>{{ isUploadingDescImage ? 'Đang tải...' : 'Thêm Ảnh Từ Máy' }}</span>
                      </button>
                      <input
                        ref="descFileInputRef"
                        type="file"
                        accept="image/*"
                        multiple
                        class="hidden"
                        @change="handleDescImageUpload"
                      />
                    </div>
                  </div>

                  <!-- CONTEXTUAL IMAGE TOOLBAR (Hiện ra khi click vào 1 ảnh trong bài mô tả) -->
                  <div
                    v-if="selectedImageEl"
                    class="flex flex-wrap items-center justify-between gap-2 px-3 py-2 bg-amber-500/10 text-xs"
                  >
                    <div class="flex flex-wrap items-center gap-1.5">
                      <span class="font-bold text-amber-400">🖼 Tùy chỉnh ảnh đang chọn:</span>
                      <button
                        v-for="w in ['25%', '50%', '75%', '100%']"
                        :key="w"
                        type="button"
                        @click="resizeSelectedImage(w)"
                        class="px-2 py-0.5 rounded border font-bold cursor-pointer hover:border-brand-gold"
                        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                      >
                        Rộng {{ w }}
                      </button>
                      <span class="text-slate-500">|</span>
                      <button
                        type="button"
                        @click="alignSelectedImage('left')"
                        class="px-2 py-0.5 rounded border font-semibold cursor-pointer hover:border-brand-gold"
                        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-white border-slate-200 text-slate-700'"
                      >
                        Căn trái
                      </button>
                      <button
                        type="button"
                        @click="alignSelectedImage('center')"
                        class="px-2 py-0.5 rounded border font-semibold cursor-pointer hover:border-brand-gold"
                        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-white border-slate-200 text-slate-700'"
                      >
                        Căn giữa
                      </button>
                      <button
                        type="button"
                        @click="alignSelectedImage('right')"
                        class="px-2 py-0.5 rounded border font-semibold cursor-pointer hover:border-brand-gold"
                        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-white border-slate-200 text-slate-700'"
                      >
                        Căn phải
                      </button>
                    </div>
                    <button
                      type="button"
                      @click="removeSelectedImage"
                      class="px-2.5 py-0.5 rounded bg-red-500/20 text-red-400 hover:bg-red-500/30 font-bold cursor-pointer"
                    >
                      Xóa ảnh này
                    </button>
                  </div>
                </div>

                <!-- VISUAL WYSIWYG EDITOR -->
                <div
                  v-show="descEditorMode === 'visual'"
                  ref="visualEditorRef"
                  contenteditable="true"
                  @input="onVisualEditorInput"
                  @blur="onVisualEditorInput"
                  @mouseup="onEditorSelectionChange"
                  @keyup="onEditorSelectionChange"
                  @click="onEditorClick"
                  class="w-full min-h-[300px] max-h-[620px] overflow-y-auto rounded-xl p-4 sm:p-5 text-sm border focus:outline-none focus:ring-2 focus:ring-brand-gold transition-all leading-relaxed desc-visual-editor"
                  :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-slate-100' : 'bg-white border-slate-200 text-slate-800'"
                  data-placeholder="Nhập nội dung mô tả chi tiết ấn phẩm, ưu điểm chất liệu, quy cách gia công và bấm 'Thêm Ảnh Từ Máy' hoặc 'Chèn Ảnh Thư Viện' để minh họa..."
                ></div>

                <!-- RAW HTML CODE EDITOR -->
                <textarea
                  v-show="descEditorMode === 'html'"
                  ref="descTextareaRef"
                  v-model="form.description"
                  @input="syncVisualFromModel"
                  rows="12"
                  placeholder="Mô tả công nghệ in (Offset / Kỹ thuật số), các loại chất liệu giấy, quy cách gia công cán màng, ép kim..."
                  class="w-full rounded-xl px-4 py-3 text-xs font-mono border focus:outline-none focus:ring-2 focus:ring-brand-gold transition-all leading-relaxed"
                  :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                ></textarea>
              </div>

              <!-- Trạng Thái Bật / Tắt -->
              <div class="p-4 rounded-xl border flex flex-wrap items-center gap-6" :class="isDarkMode ? 'bg-[#1C2434]/60 border-[#2E3A47]' : 'bg-slate-50 border-slate-200'">
                <label class="flex items-center gap-2.5 cursor-pointer font-bold select-none" :class="isDarkMode ? 'text-slate-200' : 'text-slate-800'">
                  <input
                    v-model="form.is_active"
                    type="checkbox"
                    class="w-4 h-4 rounded text-brand-gold focus:ring-brand-gold cursor-pointer"
                  />
                  <span>Hiển thị trên website ngay</span>
                </label>

                <label class="flex items-center gap-2.5 cursor-pointer font-bold select-none" :class="isDarkMode ? 'text-slate-200' : 'text-slate-800'">
                  <input
                    v-model="form.is_popular"
                    type="checkbox"
                    class="w-4 h-4 rounded text-brand-gold focus:ring-brand-gold cursor-pointer"
                  />
                  <span>★ Đánh dấu Bán Chạy / Nổi Bật</span>
                </label>
              </div>
            </div>

            <!-- PHẦN 2: THÔNG SỐ QUY CÁCH IN ẤN & KỸ THUẬT (SPECIFICATIONS) -->
            <div class="space-y-4 pt-4 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b pb-3" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
                <div>
                  <h2 class="text-xs font-black uppercase tracking-wider text-brand-gold flex items-center gap-2">
                    <Sliders class="w-4 h-4" />
                    <span>2. Thông Số Quy Cách In Ấn (Specifications)</span>
                  </h2>
                  <p class="text-[11px] text-slate-400 mt-0.5">
                    Tùy chỉnh thông số hiển thị ngoài website cho ấn phẩm này (Màu sắc, Loại giấy/chất liệu, Kỹ thuật in, Cán màng, Kích thước...)
                  </p>
                </div>

                <div class="flex items-center gap-2">
                  <button
                    type="button"
                    @click="addCustomSpec('', '')"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-brand-gold text-slate-950 hover:bg-amber-400 transition-all cursor-pointer shadow-xs"
                  >
                    <Plus class="w-3.5 h-3.5" />
                    <span>Thêm Dòng Thông Số</span>
                  </button>
                </div>
              </div>

              <!-- Gợi ý nạp nhanh các thông số thông dụng -->
              <div class="flex flex-wrap items-center gap-1.5 text-xs">
                <span class="text-[11px] text-slate-400 font-semibold mr-1">Thêm nhanh:</span>
                <button
                  type="button"
                  @click="quickAddCustomSpec('Màu sắc', 'In 4 màu CMYK chuẩn nét')"
                  class="px-2.5 py-1 rounded-lg border text-[11px] font-semibold transition-all hover:border-brand-gold cursor-pointer"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-700'"
                >
                  + Màu sắc
                </button>
                <button
                  type="button"
                  @click="quickAddCustomSpec('Loại giấy / Chất liệu', 'Decal lưới dán kính chống nắng (nhìn 1 chiều)')"
                  class="px-2.5 py-1 rounded-lg border text-[11px] font-semibold transition-all hover:border-brand-gold cursor-pointer"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-700'"
                >
                  + Loại giấy / Decal
                </button>
                <button
                  type="button"
                  @click="quickAddCustomSpec('Kỹ thuật in', 'In kỹ thuật số UV / Mực dầu cao cấp ngoài trời')"
                  class="px-2.5 py-1 rounded-lg border text-[11px] font-semibold transition-all hover:border-brand-gold cursor-pointer"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-700'"
                >
                  + Kỹ thuật in
                </button>
                <button
                  type="button"
                  @click="quickAddCustomSpec('Cán màng / Gia công', 'Màng mờ / Màng bóng bảo vệ chống nước')"
                  class="px-2.5 py-1 rounded-lg border text-[11px] font-semibold transition-all hover:border-brand-gold cursor-pointer"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-700'"
                >
                  + Cán màng
                </button>
                <button
                  type="button"
                  @click="quickAddCustomSpec('Kích thước chuẩn', 'Theo kích thước thực tế hoặc theo file đặt in')"
                  class="px-2.5 py-1 rounded-lg border text-[11px] font-semibold transition-all hover:border-brand-gold cursor-pointer"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-700'"
                >
                  + Kích thước
                </button>
                <button
                  type="button"
                  @click="quickAddCustomSpec('Độ bền ngoài trời', 'Kháng nước, chống nắng 1 - 3 năm không phai màu')"
                  class="px-2.5 py-1 rounded-lg border text-[11px] font-semibold transition-all hover:border-brand-gold cursor-pointer"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-700'"
                >
                  + Độ bền
                </button>
                <button
                  type="button"
                  @click="quickAddCustomSpec('Thành phẩm', 'Cắt xén thẳng / Bế demi theo viền')"
                  class="px-2.5 py-1 rounded-lg border text-[11px] font-semibold transition-all hover:border-brand-gold cursor-pointer"
                  :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-700'"
                >
                  + Thành phẩm
                </button>
              </div>

              <!-- Bảng danh sách thông số quy cách in -->
              <div class="rounded-xl border overflow-hidden" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'">
                <div v-if="!form.specs?.custom_specs || form.specs.custom_specs.length === 0" class="p-6 text-center text-slate-400 space-y-2">
                  <Sliders class="w-8 h-8 mx-auto opacity-30" />
                  <p class="text-xs">Chưa có thông số quy cách riêng cho sản phẩm này.</p>
                  <p class="text-[11px] text-slate-500">Bấm các nút thêm nhanh ở trên hoặc "Thêm Dòng Thông Số" để tùy chỉnh (Ví dụ: Màu sắc - 2 màu, Loại giấy - Decal lưới...).</p>
                </div>

                <div v-else class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
                  <div
                    v-for="(spec, idx) in form.specs.custom_specs"
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
                      @click="removeCustomSpec(idx)"
                      class="self-end sm:self-auto p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-colors cursor-pointer"
                      title="Xóa dòng này"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </div>

              <!-- Tùy chọn Ẩn / Hiện Báo Giá Riêng -->
              <div class="p-3.5 rounded-xl border flex items-center justify-between gap-4" :class="isDarkMode ? 'bg-[#1C2434]/60 border-[#2E3A47]' : 'bg-slate-50 border-slate-200'">
                <div>
                  <span class="block text-xs font-bold" :class="isDarkMode ? 'text-slate-200' : 'text-slate-800'">
                    Chế độ báo giá riêng cho ấn phẩm này:
                  </span>
                  <span class="text-[11px] text-slate-400">
                    Bật tùy chọn này nếu sản phẩm cần kỹ thuật viên báo giá qua Zalo / Hotline thay vì tính giá tự động.
                  </span>
                </div>

                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                  <input
                    type="checkbox"
                    v-model="form.specs.hide_pricing"
                    class="sr-only peer"
                  />
                  <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                </label>
              </div>
            </div>

            <!-- PHẦN 3: CẤU HÌNH RANK MATH SEO PRO -->
            <div class="space-y-4 pt-4 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
              <RankMathSeoBox
                :title="form.name"
                :meta-title="form.meta_title"
                :meta-description="form.meta_description"
                :slug="form.slug"
                :content="form.description"
                :image="previewImage"
                :initial-keyword="form.meta_keywords"
                url-prefix="san-pham"
                type="product"
                :default-excerpt="form.meta_description || form.description"
                @update:focusKeyword="form.meta_keywords = $event"
                @update:metaTitle="form.meta_title = $event"
                @update:metaDescription="form.meta_description = $event"
                @update:slug="form.slug = $event"
                @update:content="onSeoContentUpdate"
              />

              <!-- Tùy chỉnh thẻ Meta Tags chi tiết -->
              <div class="p-4 rounded-2xl border space-y-3" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'">
                <div class="flex items-center justify-between">
                  <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    Tùy chỉnh thẻ Meta Tags thủ công:
                  </h4>
                  <button
                    type="button"
                    @click="autoFillSEO"
                    class="text-[11px] font-bold text-amber-500 hover:text-amber-400 cursor-pointer"
                  >
                    ⚡ Điền mẫu chuẩn xưởng
                  </button>
                </div>

                <!-- Meta Title -->
                <div>
                  <div class="flex items-center justify-between mb-1.5">
                    <label class="font-bold text-xs" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                      Tiêu Đề SEO (Meta Title)
                    </label>
                    <span class="text-[11px] font-mono font-semibold" :class="metaTitleLengthClass">
                      {{ form.meta_title ? form.meta_title.length : 0 }} / 65 ký tự
                    </span>
                  </div>
                  <input
                    v-model="form.meta_title"
                    type="text"
                    placeholder="VD: In Bao Thư Nhỏ Giá Rẻ Tận Xưởng 2026 | In Nhật Nguyên"
                    class="w-full rounded-xl px-4 py-2 text-xs border focus:outline-none focus:ring-1 focus:ring-brand-gold transition-all"
                    :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                  />
                </div>

                <!-- Meta Description -->
                <div>
                  <div class="flex items-center justify-between mb-1.5">
                    <label class="font-bold text-xs" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                      Thẻ Mô Tả (Meta Description)
                    </label>
                    <span class="text-[11px] font-mono font-semibold" :class="metaDescLengthClass">
                      {{ form.meta_description ? form.meta_description.length : 0 }} / 160 ký tự
                    </span>
                  </div>
                  <textarea
                    v-model="form.meta_description"
                    rows="3"
                    placeholder="VD: Xưởng In Nhật Nguyên chuyên in bao thư nhỏ chất lượng cao, chuẩn màu Offset 98%, lấy nhanh 4h - 24h, giá gốc tận xưởng không qua trung gian."
                    class="w-full rounded-xl px-4 py-2 text-xs border focus:outline-none focus:ring-1 focus:ring-brand-gold transition-all leading-relaxed"
                    :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                  ></textarea>
                </div>

                <!-- Canonical URL -->
                <div>
                  <label class="block font-bold text-xs mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                    Đường Dẫn Chuẩn (Canonical URL)
                  </label>
                  <input
                    v-model="form.canonical_url"
                    type="text"
                    :placeholder="previewFullUrl"
                    class="w-full rounded-xl px-4 py-2 text-xs font-mono border focus:outline-none focus:ring-1 focus:ring-brand-gold transition-all"
                    :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                  />
                </div>
              </div>
            </div>

            <!-- PHẦN 3: BUTTON ACTIONS (DƯỚI CÙNG LÀ 2 NÚT [HỦY] VÀ [LƯU ẤN PHẨM]) -->
            <div
              class="pt-6 border-t flex flex-col-reverse sm:flex-row items-center justify-end gap-3"
              :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'"
            >
              <!-- Nút 1: [Hủy] -->
              <button
                type="button"
                @click="handleCancel"
                class="w-full sm:w-auto px-6 py-2.5 rounded-xl border font-bold text-xs transition-colors cursor-pointer text-center"
                :class="isDarkMode
                  ? 'border-[#2E3A47] text-slate-300 hover:bg-[#1C2434] hover:text-white'
                  : 'border-slate-300 text-slate-700 hover:bg-slate-100 hover:text-slate-900'"
              >
                Hủy
              </button>

              <!-- Nút 2: [Lưu Ấn Phẩm] -->
              <button
                type="submit"
                :disabled="isSaving"
                class="w-full sm:w-auto px-8 py-2.5 rounded-xl bg-gradient-to-r from-brand-gold to-amber-500 hover:from-amber-400 hover:to-brand-gold text-slate-950 font-black text-xs shadow-md shadow-brand-gold/25 flex items-center justify-center gap-2 transition-all cursor-pointer disabled:opacity-50"
              >
                <Loader2 v-if="isSaving" class="w-4 h-4 animate-spin" />
                <Save v-else class="w-4 h-4" />
                <span>{{ isSaving ? 'Đang Lưu...' : 'Lưu Ấn Phẩm' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- ===================================================================== -->
      <!-- CỘT PHẢI (5 COLS): LIVE REALTIME PREVIEW (XEM TRƯỚC THEO THỜI GIAN THỰC) -->
      <!-- ===================================================================== -->
      <div class="lg:col-span-5 sticky top-24 space-y-4">
        <div
          class="rounded-2xl border shadow-sm p-5 space-y-4"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <!-- Preview Header & Tab Switcher -->
          <div class="flex items-center justify-between pb-3 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
              <span class="text-xs font-bold uppercase tracking-wider" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                Bản Xem Trước Thực Tế
              </span>
            </div>

            <!-- Tab Switcher (Website Card vs Google SERP) -->
            <div class="flex items-center bg-slate-100 dark:bg-[#1C2434] p-1 rounded-xl text-[11px] font-bold">
              <button
                type="button"
                @click="previewTab = 'card'"
                class="px-3 py-1 rounded-lg transition-all cursor-pointer"
                :class="previewTab === 'card'
                  ? 'bg-brand-gold text-slate-950 shadow-xs font-black'
                  : 'text-slate-400 hover:text-slate-200'"
              >
                Thẻ Web
              </button>
              <button
                type="button"
                @click="previewTab = 'serp'"
                class="px-3 py-1 rounded-lg transition-all cursor-pointer"
                :class="previewTab === 'serp'
                  ? 'bg-brand-gold text-slate-950 shadow-xs font-black'
                  : 'text-slate-400 hover:text-slate-200'"
              >
                Google
              </button>
            </div>
          </div>

          <!-- TAB 1: WEBSITE PRODUCT CARD LIVE PREVIEW -->
          <div v-if="previewTab === 'card'" class="space-y-3">
            <div class="text-[11px] text-slate-400 flex items-center justify-between">
              <span>Khách hàng sẽ nhìn thấy thẻ này trên trang chủ & catalog:</span>
              <span class="text-brand-gold font-bold">dichvuinnhatnguyen.vn</span>
            </div>

            <!-- Real Mockup Card Component -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 text-slate-900">
              <!-- Image Banner (Chuẩn 800x600 px -> 4:3) -->
              <div class="relative aspect-[4/3] bg-slate-100 overflow-hidden">
                <img
                  :src="previewImage"
                  :alt="form.name || 'Ảnh ấn phẩm'"
                  class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                  @error="onPreviewImageError"
                />
                
                <!-- Tag Badge -->
                <span
                  v-if="form.tag"
                  class="absolute top-2.5 right-2.5 px-2.5 py-1 rounded-lg bg-brand-gold text-slate-950 font-black text-[10px] shadow-md uppercase tracking-wider"
                >
                  {{ form.tag }}
                </span>

                <!-- Popular Ribbon -->
                <span
                  v-if="form.is_popular"
                  class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded-md bg-amber-500 text-slate-950 font-bold text-[10px] shadow-sm flex items-center gap-1"
                >
                  ★ Bán Chạy
                </span>
              </div>

              <!-- Card Body -->
              <div class="p-4 space-y-3">
                <!-- Category Name -->
                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-gold block">
                  {{ selectedCategoryName }}
                </span>

                <!-- Product Name -->
                <h3 class="font-bold text-sm text-slate-900 leading-snug line-clamp-2 min-h-[40px]">
                  {{ form.name || 'Tên Ấn Phẩm In Sẽ Xuất Hiện Ở Đây' }}
                </h3>

                <!-- Price & Unit -->
                <div class="pt-2 border-t border-slate-100 flex items-baseline justify-between">
                  <div>
                    <span class="text-[10px] text-slate-400 block font-medium">Giá gốc chỉ từ:</span>
                    <span class="text-base font-black text-brand-gold font-mono">
                      {{ formatCurrency(form.base_price) }}đ
                    </span>
                    <span class="text-xs text-slate-500"> / {{ form.unit || 'cái' }}</span>
                  </div>

                  <div class="text-right">
                    <span class="text-[10px] text-slate-400 block">Thời gian in:</span>
                    <span class="text-xs font-semibold text-slate-700">
                      ⏱ {{ form.turnaround || '24h - 48h' }}
                    </span>
                  </div>
                </div>

                <!-- Fake CTA Button -->
                <div class="pt-1">
                  <div class="w-full py-2 rounded-xl bg-slate-900 text-white font-bold text-xs text-center flex items-center justify-center gap-1.5 shadow-sm">
                    <Eye class="w-3.5 h-3.5 text-brand-gold" />
                    <span>Xem Chi Tiết & Báo Giá</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- TAB 2: GOOGLE SERP PREVIEW (LIVE SEARCH RESULT) -->
          <div v-else class="space-y-3">
            <div class="text-[11px] text-slate-400 flex items-center justify-between">
              <span>Mô phỏng hiển thị trên Google Tìm Kiếm:</span>
              <span class="text-emerald-500 font-bold font-mono">Google Search</span>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-1.5 text-left">
              <!-- Google Favicon + URL Breadcrumb -->
              <div class="flex items-center gap-2">
                <img src="/logo-icon.png" alt="Favicon" class="w-4 h-4 rounded-full object-contain shrink-0" />
                <div class="text-[12px] text-slate-700 truncate font-sans">
                  https://dichvuinnhatnguyen.vn <span class="text-slate-400">› san-pham ›</span> <span class="font-medium text-slate-900">{{ form.slug || 'slug-san-pham' }}</span>
                </div>
              </div>

              <!-- Google Blue Clickable Title -->
              <div class="text-base sm:text-lg font-medium text-[#1a0dab] hover:underline cursor-pointer leading-snug line-clamp-2">
                {{ form.meta_title || (form.name ? `${form.name} | Xưởng In Nhật Nguyên` : 'Tiêu Đề Ấn Phẩm In - Xưởng In Nhật Nguyên') }}
              </div>

              <!-- Google Snippet Description -->
              <p class="text-xs text-[#4d5156] leading-relaxed line-clamp-3">
                {{ form.meta_description || stripHtml(form.description) || 'Xưởng in Offset & Kỹ thuật số Nhật Nguyên — Chuyên sản xuất ấn phẩm bao bì, hộp giấy, túi giấy chất lượng cao, giá gốc trực tiếp.' }}
              </p>
            </div>
          </div>

          <!-- Helpful Advice -->
          <div class="p-3.5 rounded-xl border text-[11px] leading-relaxed flex items-start gap-2.5" :class="isDarkMode ? 'bg-[#1C2434]/70 border-[#2E3A47] text-slate-400' : 'bg-slate-50 border-slate-200 text-slate-600'">
            <Sparkles class="w-4 h-4 text-brand-gold shrink-0 mt-0.5" />
            <div>
              <strong>Gợi ý:</strong> Kiểm tra hình ảnh rõ nét và giá cơ sở chính xác để khách hàng dễ dàng gửi yêu cầu báo giá.
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Media Library Modal (Chuẩn WordPress / Shopify) -->
    <MediaLibraryModal
      v-if="isMediaModalOpen"
      :current-url="mediaTarget === 'image' ? form.image : ''"
      @select="onMediaSelected"
      @close="isMediaModalOpen = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
  ArrowLeft,
  PackagePlus,
  Edit3,
  Save,
  Loader2,
  AlertCircle,
  CheckCircle2,
  Globe,
  Package,
  Eye,
  Sparkles,
  Images,
  UploadCloud,
  Undo2,
  Redo2,
  AlignLeft,
  AlignCenter,
  AlignRight,
  AlignJustify,
  List,
  ListOrdered,
  Outdent,
  Indent,
  Table,
  Link2,
  Minus,
  Eraser,
  Sliders,
  Plus,
  Trash2
} from 'lucide-vue-next'
import { api } from '../../services/api'
import { useAdminTheme } from '../../composables/useAdminTheme'
import { slugify } from '../../composables/useSEO'
import MediaLibraryModal from '../../components/admin/MediaLibraryModal.vue'
import RankMathSeoBox from '../../components/admin/RankMathSeoBox.vue'

const route = useRoute()
const router = useRouter()
const { isDarkMode } = useAdminTheme()

const isEditMode = computed(() => Boolean(route.params.id))
const categories = ref([])
const isSaving = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const previewTab = ref('card') // 'card' | 'serp'

const isMediaModalOpen = ref(false)
const mediaTarget = ref('image') // 'image' | 'description'
const quickFileInputRef = ref(null)

// Rich Description Editor State (Word-style)
const descEditorMode = ref('visual') // 'visual' | 'html'
const visualEditorRef = ref(null)
const descTextareaRef = ref(null)
const descFileInputRef = ref(null)
const isUploadingDescImage = ref(false)

const selectedHeading = ref('p')
const selectedFontFamily = ref('')
const selectedFontSize = ref('14px')
const currentTextColor = ref('#F59E0B')
const selectedImageEl = ref(null)
let savedSelectionRange = null

const FONT_SIZE_STEPS = ['12px', '13px', '14px', '15px', '16px', '18px', '20px', '24px', '28px', '32px', '36px']

const QUICK_TEXT_COLORS = [
  { label: 'Mặc định / Đen', hex: '#0F172A' },
  { label: 'Trắng sáng', hex: '#F8FAFC' },
  { label: 'Xanh Thương Hiệu', hex: '#1E3A8A' },
  { label: 'Đỏ Nhấn Mạnh', hex: '#DC2626' },
  { label: 'Vàng Cam Xưởng', hex: '#D97706' },
  { label: 'Xanh Lá', hex: '#059669' }
]

const QUICK_HIGHLIGHT_COLORS = [
  { label: 'Vàng Dạ Quang', hex: '#FEF08A' },
  { label: 'Xanh Nhạt', hex: '#DBEAFE' },
  { label: 'Xanh Lá Nhạt', hex: '#D1FAE5' },
  { label: 'Hồng Nhạt', hex: '#FCE7F3' }
]

function onEditorSelectionChange() {
  if (!visualEditorRef.value) return
  const sel = window.getSelection()
  if (sel && sel.rangeCount > 0) {
    const range = sel.getRangeAt(0)
    if (visualEditorRef.value.contains(range.commonAncestorContainer)) {
      savedSelectionRange = range.cloneRange()
    }
  }
}

function restoreEditorSelection() {
  if (!visualEditorRef.value) return
  visualEditorRef.value.focus()
  if (savedSelectionRange) {
    const sel = window.getSelection()
    if (sel) {
      sel.removeAllRanges()
      sel.addRange(savedSelectionRange)
    }
  }
}

function onEditorClick(e) {
  onEditorSelectionChange()
  if (e.target && e.target.tagName === 'IMG') {
    selectedImageEl.value = e.target
  } else {
    selectedImageEl.value = null
  }
}

function resizeSelectedImage(widthPercent) {
  if (!selectedImageEl.value) return
  selectedImageEl.value.style.width = widthPercent
  selectedImageEl.value.style.maxWidth = '100%'
  selectedImageEl.value.style.height = 'auto'
  onVisualEditorInput()
}

function alignSelectedImage(align) {
  if (!selectedImageEl.value) return
  const figure = selectedImageEl.value.closest('figure')
  if (figure) {
    figure.style.textAlign = align
  }
  if (align === 'left') {
    selectedImageEl.value.style.marginLeft = '0'
    selectedImageEl.value.style.marginRight = 'auto'
  } else if (align === 'right') {
    selectedImageEl.value.style.marginLeft = 'auto'
    selectedImageEl.value.style.marginRight = '0'
  } else {
    selectedImageEl.value.style.marginLeft = 'auto'
    selectedImageEl.value.style.marginRight = 'auto'
  }
  onVisualEditorInput()
}

function removeSelectedImage() {
  if (!selectedImageEl.value) return
  const figure = selectedImageEl.value.closest('figure')
  if (figure && visualEditorRef.value?.contains(figure)) {
    figure.remove()
  } else {
    selectedImageEl.value.remove()
  }
  selectedImageEl.value = null
  onVisualEditorInput()
}

function execWordCmd(cmd, val = null) {
  if (descEditorMode.value !== 'visual') {
    switchEditorMode('visual')
  }
  restoreEditorSelection()
  document.execCommand(cmd, false, val)
  onEditorSelectionChange()
  onVisualEditorInput()
}

function applyHeadingBlock() {
  const tag = selectedHeading.value || 'p'
  if (descEditorMode.value !== 'visual') {
    switchEditorMode('visual')
  }
  restoreEditorSelection()
  document.execCommand('formatBlock', false, `<${tag}>`)
  onEditorSelectionChange()
  onVisualEditorInput()
}

function applyFontFamily() {
  const font = selectedFontFamily.value
  if (descEditorMode.value !== 'visual') {
    switchEditorMode('visual')
  }
  restoreEditorSelection()
  if (font) {
    document.execCommand('fontName', false, font)
  } else {
    document.execCommand('removeFormat', false, null)
  }
  onEditorSelectionChange()
  onVisualEditorInput()
}

function applyFontSizePx(sizePx) {
  if (!sizePx) return
  selectedFontSize.value = sizePx
  if (descEditorMode.value !== 'visual') {
    switchEditorMode('visual')
  }
  restoreEditorSelection()
  const sel = window.getSelection()
  const hasSelection = sel && sel.toString().length > 0 && visualEditorRef.value?.contains(sel.anchorNode)

  if (hasSelection) {
    document.execCommand('fontSize', false, '7')
    const fonts = visualEditorRef.value.querySelectorAll('font[size="7"]')
    fonts.forEach(fontEl => {
      const span = document.createElement('span')
      span.style.fontSize = sizePx
      span.innerHTML = fontEl.innerHTML
      fontEl.parentNode.replaceChild(span, fontEl)
    })
  } else {
    document.execCommand('insertHTML', false, `<span style="font-size: ${sizePx};">Chữ cỡ ${sizePx}</span> `)
  }
  onEditorSelectionChange()
  onVisualEditorInput()
}

function stepFontSize(direction) {
  const idx = FONT_SIZE_STEPS.indexOf(selectedFontSize.value)
  const currentIdx = idx >= 0 ? idx : 2
  const nextIdx = Math.min(FONT_SIZE_STEPS.length - 1, Math.max(0, currentIdx + direction))
  applyFontSizePx(FONT_SIZE_STEPS[nextIdx])
}

function applyTextColor(hexColor) {
  currentTextColor.value = hexColor
  execWordCmd('foreColor', hexColor)
}

function applyHighlightColor(hexColor) {
  if (descEditorMode.value !== 'visual') {
    switchEditorMode('visual')
  }
  restoreEditorSelection()
  if (!document.execCommand('hiliteColor', false, hexColor)) {
    document.execCommand('backColor', false, hexColor)
  }
  onEditorSelectionChange()
  onVisualEditorInput()
}

function clearSelectedFormatting() {
  if (descEditorMode.value !== 'visual') {
    switchEditorMode('visual')
  }
  restoreEditorSelection()
  document.execCommand('removeFormat', false, null)
  document.execCommand('formatBlock', false, '<p>')
  onEditorSelectionChange()
  onVisualEditorInput()
}

function insertSampleTable() {
  const tableHtml = `
    <table style="width: 100%; border-collapse: collapse; margin: 14px 0; font-size: 13px;">
      <thead>
        <tr style="background-color: #f1f5f9; color: #0f172a; font-weight: 700;">
          <th style="border: 1px solid #cbd5e1; padding: 8px 12px; text-align: left;">Hạng mục / Quy cách</th>
          <th style="border: 1px solid #cbd5e1; padding: 8px 12px; text-align: left;">Thông số tiêu chuẩn</th>
          <th style="border: 1px solid #cbd5e1; padding: 8px 12px; text-align: left;">Ghi chú</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="border: 1px solid #cbd5e1; padding: 8px 12px;">Chất liệu giấy</td>
          <td style="border: 1px solid #cbd5e1; padding: 8px 12px;">Couche / Decal / Fort cao cấp</td>
          <td style="border: 1px solid #cbd5e1; padding: 8px 12px;">Tùy chọn định lượng</td>
        </tr>
        <tr>
          <td style="border: 1px solid #cbd5e1; padding: 8px 12px;">Công nghệ in</td>
          <td style="border: 1px solid #cbd5e1; padding: 8px 12px;">In Offset / Kỹ thuật số 4 màu CMYK</td>
          <td style="border: 1px solid #cbd5e1; padding: 8px 12px;">Sắc nét 98%</td>
        </tr>
      </tbody>
    </table><p></p>`
  insertHtmlIntoDescription(tableHtml)
}

function insertHyperlink() {
  restoreEditorSelection()
  const url = prompt('Nhập đường dẫn liên kết (URL):', 'https://dichvuinnhatnguyen.vn')
  if (!url) return
  const sel = window.getSelection()
  if (sel && sel.toString().trim().length > 0) {
    document.execCommand('createLink', false, url)
    onVisualEditorInput()
  } else {
    insertHtmlIntoDescription(`<a href="${url}" target="_blank" style="color: #2563eb; text-decoration: underline; font-weight: 600;">${url}</a> `)
  }
}

function stripHtml(html) {
  if (!html) return ''
  return String(html).replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim()
}

function normalizeDescriptionHtml(raw) {
  if (!raw) return ''
  const str = String(raw).trim()
  if (/<[a-z][\s\S]*>/i.test(str)) {
    return str
  }
  return str
    .split(/\n{2,}/)
    .map(p => `<p>${p.replace(/\n/g, '<br>')}</p>`)
    .join('')
}

function syncVisualFromModel() {
  if (visualEditorRef.value) {
    visualEditorRef.value.innerHTML = normalizeDescriptionHtml(form.value.description)
  }
}

function onVisualEditorInput() {
  if (visualEditorRef.value) {
    form.value.description = visualEditorRef.value.innerHTML
  }
}

function switchEditorMode(mode) {
  if (mode === 'html' && descEditorMode.value === 'visual') {
    onVisualEditorInput()
  } else if (mode === 'visual' && descEditorMode.value === 'html') {
    syncVisualFromModel()
  }
  descEditorMode.value = mode
}

function insertHtmlIntoDescription(htmlSnippet) {
  if (descEditorMode.value === 'visual' && visualEditorRef.value) {
    restoreEditorSelection()
    const sel = window.getSelection()
    if (sel && sel.rangeCount > 0 && visualEditorRef.value.contains(sel.anchorNode)) {
      document.execCommand('insertHTML', false, htmlSnippet)
    } else {
      visualEditorRef.value.innerHTML += htmlSnippet
    }
    onEditorSelectionChange()
    onVisualEditorInput()
  } else {
    form.value.description = (form.value.description || '') + '\n' + htmlSnippet
    syncVisualFromModel()
  }
}

function applyDescFormat(type) {
  if (descEditorMode.value === 'visual' && visualEditorRef.value) {
    restoreEditorSelection()
    const sel = window.getSelection()
    const hasSelection = sel && sel.toString().trim().length > 0 && visualEditorRef.value.contains(sel.anchorNode)

    if (type === 'bold') {
      document.execCommand('bold', false, null)
      onEditorSelectionChange()
      onVisualEditorInput()
      return
    }
    if (type === 'italic') {
      document.execCommand('italic', false, null)
      onEditorSelectionChange()
      onVisualEditorInput()
      return
    }
    if (type === 'underline') {
      document.execCommand('underline', false, null)
      onEditorSelectionChange()
      onVisualEditorInput()
      return
    }
    if (type === 'ul') {
      document.execCommand('insertUnorderedList', false, null)
      onEditorSelectionChange()
      onVisualEditorInput()
      return
    }
    if (type === 'ol') {
      document.execCommand('insertOrderedList', false, null)
      onEditorSelectionChange()
      onVisualEditorInput()
      return
    }
    if ((type === 'h2' || type === 'h3') && hasSelection) {
      document.execCommand('formatBlock', false, `<${type}>`)
      onEditorSelectionChange()
      onVisualEditorInput()
      return
    }
  }

  let snippet = ''
  switch (type) {
    case 'h2':
      snippet = '<h2>Tiêu đề mục chính</h2><p>Nội dung chi tiết...</p>'
      break
    case 'h3':
      snippet = '<h3>Tiêu đề mục phụ</h3><p>Nội dung chi tiết...</p>'
      break
    case 'ul':
      snippet = '<ul><li>Ưu điểm / quy cách thứ nhất</li><li>Ưu điểm / quy cách thứ hai</li></ul>'
      break
    case 'ol':
      snippet = '<ol><li>Bước 1 / Quy cách 1</li><li>Bước 2 / Quy cách 2</li></ol>'
      break
    case 'tip':
      snippet = '<div class="p-4 rounded-xl border border-amber-300 bg-amber-50 text-slate-800 my-4 text-xs font-medium">💡 <strong>Lưu ý từ Xưởng In Nhật Nguyên:</strong> Hỗ trợ thiết kế & kiểm tra file chuẩn màu CMYK miễn phí trước khi in.</div><p></p>'
      break
  }

  if (snippet) {
    insertHtmlIntoDescription(snippet)
  }
}

function openMediaForThumbnail() {
  mediaTarget.value = 'image'
  isMediaModalOpen.value = true
}

function openMediaForDescription() {
  mediaTarget.value = 'description'
  isMediaModalOpen.value = true
}

function triggerDescImageUpload() {
  descFileInputRef.value?.click()
}

async function handleDescImageUpload(e) {
  const files = Array.from(e.target.files || [])
  if (!files.length) return

  isUploadingDescImage.value = true
  try {
    for (const file of files) {
      const formData = new FormData()
      formData.append('file', file)
      formData.append('folder', 'products')
      const res = await api.uploadAdminMedia(formData)
      if (res.data?.url) {
        const imgHtml = `<figure class="my-5 text-center"><img src="${res.data.url}" alt="${form.value.name || 'Hình ảnh thực tế ấn phẩm'}" class="rounded-2xl max-w-full h-auto mx-auto shadow-md" /><figcaption class="text-xs text-slate-500 mt-2 italic">${form.value.name || 'Mẫu in thực tế tại Xưởng In Nhật Nguyên'}</figcaption></figure><p></p>`
        insertHtmlIntoDescription(imgHtml)
      }
    }
  } catch (err) {
    alert('Lỗi tải ảnh mô tả: ' + (err.data?.message || err.message))
  } finally {
    isUploadingDescImage.value = false
    if (descFileInputRef.value) descFileInputRef.value.value = ''
  }
}

const form = ref({
  name: '',
  slug: '',
  category_id: '',
  base_price: 5000,
  unit: 'cái',
  turnaround: '24h - 48h',
  min_quantity: 10,
  tag: '',
  image: '',
  description: '',
  is_active: true,
  is_popular: false,
  meta_title: '',
  meta_description: '',
  meta_keywords: '',
  canonical_url: '',
  specs: {
    hide_pricing: false,
    custom_specs: [],
    sizes: [],
    materials: [],
    finishings: [],
    quantities: []
  }
})

function addCustomSpec(name = '', value = '') {
  if (!form.value.specs) {
    form.value.specs = { hide_pricing: false, custom_specs: [] }
  }
  if (!Array.isArray(form.value.specs.custom_specs)) {
    form.value.specs.custom_specs = []
  }
  form.value.specs.custom_specs.push({ name, value })
}

function removeCustomSpec(idx) {
  if (form.value.specs?.custom_specs) {
    form.value.specs.custom_specs.splice(idx, 1)
  }
}

function quickAddCustomSpec(name, defaultValue) {
  if (!form.value.specs) {
    form.value.specs = { hide_pricing: false, custom_specs: [] }
  }
  if (!Array.isArray(form.value.specs.custom_specs)) {
    form.value.specs.custom_specs = []
  }
  const existing = form.value.specs.custom_specs.find(s => s.name.toLowerCase() === name.toLowerCase())
  if (existing) {
    existing.value = defaultValue
  } else {
    form.value.specs.custom_specs.push({ name, value: defaultValue })
  }
}

async function onSeoContentUpdate(newHtml) {
  form.value.description = newHtml || ''
  await nextTick()
  syncVisualFromModel()
}

const defaultPlaceholderImage = '/logo.png'

const previewImage = computed(() => {
  return form.value.image ? form.value.image : defaultPlaceholderImage
})

function onPreviewImageError(e) {
  e.target.src = defaultPlaceholderImage
}

const selectedCategoryName = computed(() => {
  const cat = categories.value.find(c => c.id === form.value.category_id)
  return cat ? cat.name : 'Danh Mục Ấn Phẩm'
})

function formatCurrency(val) {
  return new Intl.NumberFormat('vi-VN').format(val || 0)
}

const previewFullUrl = computed(() => {
  return `https://dichvuinnhatnguyen.vn/san-pham/${form.value.slug || 'slug-san-pham'}`
})

const metaTitleLengthClass = computed(() => {
  const len = form.value.meta_title ? form.value.meta_title.length : 0
  if (len === 0) return 'text-slate-400'
  if (len >= 45 && len <= 65) return 'text-emerald-500 font-bold'
  if (len > 65) return 'text-amber-500 font-bold'
  return 'text-blue-500'
})

const metaDescLengthClass = computed(() => {
  const len = form.value.meta_description ? form.value.meta_description.length : 0
  if (len === 0) return 'text-slate-400'
  if (len >= 120 && len <= 160) return 'text-emerald-500 font-bold'
  if (len > 160) return 'text-amber-500 font-bold'
  return 'text-blue-500'
})

function handleNameChange() {
  if (!isEditMode.value && !form.value.slug) {
    form.value.slug = slugify(form.value.name)
  }
}

function autoGenerateSlug() {
  form.value.slug = slugify(form.value.name)
}

function onMediaSelected(mediaItem) {
  const url = typeof mediaItem === 'string' ? mediaItem : (mediaItem?.url || '')
  if (!url) return

  if (mediaTarget.value === 'description') {
    const imgHtml = `<figure class="my-5 text-center"><img src="${url}" alt="${form.value.name || 'Hình ảnh thực tế ấn phẩm'}" class="rounded-2xl max-w-full h-auto mx-auto shadow-md" /><figcaption class="text-xs text-slate-500 mt-2 italic">${form.value.name || 'Mẫu in thực tế tại Xưởng In Nhật Nguyên'}</figcaption></figure><p></p>`
    insertHtmlIntoDescription(imgHtml)
  } else {
    form.value.image = url
  }
  isMediaModalOpen.value = false
}

function triggerDirectUpload() {
  quickFileInputRef.value?.click()
}

async function handleQuickUpload(e) {
  const file = e.target.files?.[0]
  if (!file) return
  try {
    const formData = new FormData()
    formData.append('file', file)
    formData.append('folder', 'products')
    const res = await api.uploadAdminMedia(formData)
    if (res.data?.url) {
      form.value.image = res.data.url
    }
  } catch (err) {
    alert('Lỗi tải ảnh lên: ' + (err.data?.message || err.message))
  } finally {
    if (quickFileInputRef.value) quickFileInputRef.value.value = ''
  }
}

function autoFillSEO() {
  if (!form.value.name) return
  form.value.meta_title = `${form.value.name} - Giá Gốc Trực Tiếp Tại Xưởng | Nhật Nguyên`
  form.value.meta_description = `Xưởng in Nhật Nguyên chuyên ${form.value.name.toLowerCase()} chất lượng cao, công nghệ in hiện đại, lấy nhanh trong ${form.value.turnaround || '24h-48h'}, giá gốc tận xưởng không qua trung gian.`
  form.value.canonical_url = `https://dichvuinnhatnguyen.vn/san-pham/${form.value.slug || slugify(form.value.name)}`
}

async function loadCategories() {
  try {
    const res = await api.getCategories()
    categories.value = res.data || []
    if (!form.value.category_id && categories.value.length > 0 && !isEditMode.value) {
      form.value.category_id = categories.value[0].id
    }
  } catch (err) {
    console.error('Lỗi tải danh mục', err)
  }
}

async function loadProductDetail(id) {
  try {
    const res = await api.getProductBySlug(id)
    if (res.data) {
      const p = res.data
      let pSpecs = p.specs
      if (typeof pSpecs === 'string') {
        try { pSpecs = JSON.parse(pSpecs) } catch (e) { pSpecs = {} }
      }
      pSpecs = (pSpecs && typeof pSpecs === 'object') ? pSpecs : {}

      form.value = {
        name: p.name || '',
        slug: p.slug || '',
        category_id: p.category_id || '',
        base_price: p.base_price || 0,
        unit: p.unit || 'cái',
        turnaround: p.turnaround || '24h - 48h',
        min_quantity: p.min_quantity || 1,
        tag: p.tag || '',
        image: p.image || '',
        description: p.description || '',
        is_active: Boolean(p.is_active),
        is_popular: Boolean(p.is_popular),
        meta_title: p.meta_title || '',
        meta_description: p.meta_description || '',
        meta_keywords: p.meta_keywords || '',
        canonical_url: p.canonical_url || '',
        specs: {
          hide_pricing: Boolean(pSpecs.hide_pricing),
          custom_specs: Array.isArray(pSpecs.custom_specs) ? pSpecs.custom_specs : [],
          sizes: Array.isArray(pSpecs.sizes) ? pSpecs.sizes : [],
          materials: Array.isArray(pSpecs.materials) ? pSpecs.materials : [],
          finishings: Array.isArray(pSpecs.finishings) ? pSpecs.finishings : [],
          quantities: Array.isArray(pSpecs.quantities) ? pSpecs.quantities : []
        }
      }
      await nextTick()
      syncVisualFromModel()
    }
  } catch (err) {
    errorMessage.value = 'Không thể tải thông tin ấn phẩm cần sửa.'
  }
}

function handleCancel() {
  router.push('/he-thong-admin/products')
}

async function handleSubmit() {
  errorMessage.value = ''
  successMessage.value = ''
  if (descEditorMode.value === 'visual') {
    onVisualEditorInput()
  }
  isSaving.value = true

  try {
    const payload = {
      name: form.value.name,
      slug: form.value.slug || slugify(form.value.name),
      category_id: form.value.category_id,
      base_price: Number(form.value.base_price),
      unit: form.value.unit,
      turnaround: form.value.turnaround,
      min_quantity: Number(form.value.min_quantity),
      tag: form.value.tag || null,
      image: form.value.image || null,
      description: form.value.description || null,
      is_active: form.value.is_active,
      is_popular: form.value.is_popular,
      meta_title: form.value.meta_title || null,
      meta_description: form.value.meta_description || null,
      meta_keywords: form.value.meta_keywords || null,
      canonical_url: form.value.canonical_url || null,
      specs: form.value.specs || null
    }

    if (isEditMode.value) {
      await api.updateAdminProduct(route.params.id, payload)
      successMessage.value = 'Đã cập nhật ấn phẩm & cấu hình SEO thành công! Đang chuyển hướng...'
    } else {
      await api.createAdminProduct(payload)
      successMessage.value = 'Đã tạo ấn phẩm mới & tối ưu SEO thành công! Đang chuyển hướng...'
    }

    setTimeout(() => {
      router.push('/he-thong-admin/products')
    }, 600)
  } catch (err) {
    errorMessage.value = err.message || 'Có lỗi xảy ra khi lưu ấn phẩm. Vui lòng thử lại.'
  } finally {
    isSaving.value = false
  }
}

onMounted(async () => {
  await loadCategories()
  if (isEditMode.value) {
    await loadProductDetail(route.params.id)
  } else {
    syncVisualFromModel()
  }
})
</script>

<style scoped>
.desc-visual-editor:empty:before {
  content: attr(data-placeholder);
  color: #64748b;
  pointer-events: none;
}

.desc-visual-editor :deep(h1) {
  font-size: 1.4rem;
  font-weight: 900;
  margin-top: 1.1rem;
  margin-bottom: 0.6rem;
}

.desc-visual-editor :deep(h2) {
  font-size: 1.2rem;
  font-weight: 800;
  margin-top: 1rem;
  margin-bottom: 0.5rem;
}

.desc-visual-editor :deep(h3) {
  font-size: 1.05rem;
  font-weight: 700;
  margin-top: 0.75rem;
  margin-bottom: 0.4rem;
}

.desc-visual-editor :deep(h4) {
  font-size: 0.95rem;
  font-weight: 700;
  margin-top: 0.6rem;
  margin-bottom: 0.35rem;
}

.desc-visual-editor :deep(p) {
  margin-bottom: 0.65rem;
}

.desc-visual-editor :deep(ul) {
  list-style-type: disc;
  padding-left: 1.4rem;
  margin-bottom: 0.75rem;
}

.desc-visual-editor :deep(ol) {
  list-style-type: decimal;
  padding-left: 1.4rem;
  margin-bottom: 0.75rem;
}

.desc-visual-editor :deep(li) {
  margin-bottom: 0.25rem;
}

.desc-visual-editor :deep(blockquote) {
  border-left: 4px solid #f59e0b;
  padding: 0.65rem 1rem;
  margin: 0.85rem 0;
  font-style: italic;
  background: rgba(245, 158, 11, 0.08);
  border-radius: 0 0.75rem 0.75rem 0;
}

.desc-visual-editor :deep(table) {
  width: 100%;
  border-collapse: collapse;
  margin: 0.85rem 0;
}

.desc-visual-editor :deep(th),
.desc-visual-editor :deep(td) {
  border: 1px solid #475569;
  padding: 0.5rem 0.75rem;
}

.desc-visual-editor :deep(hr) {
  border: none;
  border-top: 1px solid #475569;
  margin: 1rem 0;
}

.desc-visual-editor :deep(img) {
  max-width: 100%;
  height: auto;
  border-radius: 0.85rem;
  margin: 0.75rem auto;
  display: block;
  cursor: pointer;
}
</style>


