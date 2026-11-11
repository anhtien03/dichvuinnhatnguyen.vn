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

                <!-- Formatting & Image Insertion Toolbar -->
                <div class="flex flex-wrap items-center justify-between gap-2 p-2 rounded-xl border" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'">
                  <div class="flex flex-wrap items-center gap-1">
                    <button
                      type="button"
                      @click="applyDescFormat('h2')"
                      class="px-2.5 py-1 rounded-lg text-xs font-extrabold border transition-colors cursor-pointer hover:border-brand-gold"
                      :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                      title="Tiêu đề lớn H2"
                    >
                      H2
                    </button>
                    <button
                      type="button"
                      @click="applyDescFormat('h3')"
                      class="px-2.5 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer hover:border-brand-gold"
                      :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                      title="Tiêu đề phụ H3"
                    >
                      H3
                    </button>
                    <button
                      type="button"
                      @click="applyDescFormat('bold')"
                      class="px-2.5 py-1 rounded-lg text-xs font-black border transition-colors cursor-pointer hover:border-brand-gold"
                      :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                      title="In đậm"
                    >
                      B
                    </button>
                    <button
                      type="button"
                      @click="applyDescFormat('italic')"
                      class="px-2.5 py-1 rounded-lg text-xs italic font-serif font-bold border transition-colors cursor-pointer hover:border-brand-gold"
                      :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                      title="In nghiêng"
                    >
                      I
                    </button>
                    <button
                      type="button"
                      @click="applyDescFormat('underline')"
                      class="px-2.5 py-1 rounded-lg text-xs underline font-bold border transition-colors cursor-pointer hover:border-brand-gold"
                      :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                      title="Gạch chân"
                    >
                      U
                    </button>
                    <button
                      type="button"
                      @click="applyDescFormat('ul')"
                      class="px-2.5 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer hover:border-brand-gold"
                      :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
                      title="Danh sách chấm đầu dòng"
                    >
                      • Danh sách
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

                <!-- VISUAL WYSIWYG EDITOR -->
                <div
                  v-show="descEditorMode === 'visual'"
                  ref="visualEditorRef"
                  contenteditable="true"
                  @input="onVisualEditorInput"
                  @blur="onVisualEditorInput"
                  class="w-full min-h-[260px] max-h-[560px] overflow-y-auto rounded-xl p-4 text-sm border focus:outline-none focus:ring-2 focus:ring-brand-gold transition-all leading-relaxed desc-visual-editor"
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

            <!-- PHẦN 2: CẤU HÌNH RANK MATH SEO PRO -->
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
              <!-- Image Banner -->
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
  UploadCloud
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

// Rich Description Editor State
const descEditorMode = ref('visual') // 'visual' | 'html'
const visualEditorRef = ref(null)
const descTextareaRef = ref(null)
const descFileInputRef = ref(null)
const isUploadingDescImage = ref(false)

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
    visualEditorRef.value.focus()
    const sel = window.getSelection()
    if (sel && sel.rangeCount > 0 && visualEditorRef.value.contains(sel.anchorNode)) {
      document.execCommand('insertHTML', false, htmlSnippet)
    } else {
      visualEditorRef.value.innerHTML += htmlSnippet
    }
    onVisualEditorInput()
  } else {
    form.value.description = (form.value.description || '') + '\n' + htmlSnippet
    syncVisualFromModel()
  }
}

function applyDescFormat(type) {
  if (descEditorMode.value === 'visual' && visualEditorRef.value) {
    visualEditorRef.value.focus()
    const sel = window.getSelection()
    const hasSelection = sel && sel.toString().trim().length > 0 && visualEditorRef.value.contains(sel.anchorNode)

    if (type === 'bold' && hasSelection) {
      document.execCommand('bold', false, null)
      onVisualEditorInput()
      return
    }
    if (type === 'italic' && hasSelection) {
      document.execCommand('italic', false, null)
      onVisualEditorInput()
      return
    }
    if (type === 'underline' && hasSelection) {
      document.execCommand('underline', false, null)
      onVisualEditorInput()
      return
    }
    if (type === 'ul' && hasSelection) {
      document.execCommand('insertUnorderedList', false, null)
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
    case 'bold':
      snippet = '<strong>chữ in đậm</strong> '
      break
    case 'italic':
      snippet = '<em>chữ in nghiêng</em> '
      break
    case 'underline':
      snippet = '<u>chữ gạch chân</u> '
      break
    case 'ul':
      snippet = '<ul><li>Ưu điểm / quy cách thứ nhất</li><li>Ưu điểm / quy cách thứ hai</li></ul>'
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
  canonical_url: ''
})

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
        canonical_url: p.canonical_url || ''
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
      canonical_url: form.value.canonical_url || null
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

.desc-visual-editor :deep(h2) {
  font-size: 1.15rem;
  font-weight: 800;
  margin-top: 1rem;
  margin-bottom: 0.5rem;
}

.desc-visual-editor :deep(h3) {
  font-size: 1rem;
  font-weight: 700;
  margin-top: 0.75rem;
  margin-bottom: 0.4rem;
}

.desc-visual-editor :deep(p) {
  margin-bottom: 0.65rem;
}

.desc-visual-editor :deep(ul) {
  list-style-type: disc;
  padding-left: 1.4rem;
  margin-bottom: 0.75rem;
}

.desc-visual-editor :deep(li) {
  margin-bottom: 0.25rem;
}

.desc-visual-editor :deep(img) {
  max-width: 100%;
  height: auto;
  border-radius: 0.85rem;
  margin: 0.75rem auto;
  display: block;
}
</style>


