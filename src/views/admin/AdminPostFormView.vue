<template>
  <div class="space-y-6">
    <!-- Top Bar: Back & Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div class="flex items-center gap-3">
        <router-link
          to="/he-thong-nn/posts"
          class="p-2.5 rounded-xl border text-slate-400 hover:text-white transition-colors cursor-pointer"
          :class="isDarkMode ? 'border-[#2E3A47] hover:bg-[#2E3A47]' : 'border-slate-200 hover:bg-slate-100 hover:text-slate-900'"
          title="Quay lại danh sách bài viết"
        >
          <ArrowLeft class="w-4 h-4" />
        </router-link>
        <div>
          <div class="flex items-center gap-2">
            <h1 class="text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              {{ isEditMode ? 'Chỉnh Sửa Bài Viết' : 'Đăng Bài Viết Mới' }}
            </h1>
            <span
              class="px-2.5 py-0.5 rounded-full text-[11px] font-bold"
              :class="form.is_published ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20'"
            >
              {{ form.is_published ? 'Xuất Bản' : 'Bản Nháp' }}
            </span>
          </div>
          <p class="text-xs text-slate-400 mt-0.5">
            Soạn thảo cẩm nang in ấn, hướng dẫn kỹ thuật xuất file và tối ưu thứ hạng tìm kiếm Google.
          </p>
        </div>
      </div>

      <!-- Header Action Buttons -->
      <div class="flex items-center gap-3">
        <router-link
          to="/he-thong-nn/posts"
          class="px-4 py-2.5 rounded-xl border text-xs font-bold transition-colors cursor-pointer"
          :class="isDarkMode ? 'border-[#2E3A47] text-slate-300 hover:bg-[#2E3A47]' : 'border-slate-200 text-slate-700 hover:bg-slate-100'"
        >
          Hủy Bỏ
        </router-link>

        <button
          type="button"
          @click="savePost"
          :disabled="isSaving"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-gold to-amber-500 hover:from-amber-400 hover:to-brand-gold text-slate-950 font-black text-xs shadow-md transition-all transform hover:-translate-y-0.5 active:translate-y-0 cursor-pointer disabled:opacity-60"
        >
          <Loader2 v-if="isSaving" class="w-4 h-4 animate-spin" />
          <Save v-else class="w-4 h-4" />
          <span>{{ isSaving ? 'Đang lưu...' : (isEditMode ? 'Cập Nhật Bài Viết' : 'Đăng Bài Viết') }}</span>
        </button>
      </div>
    </div>

    <!-- Notification Alert -->
    <div
      v-if="toastMessage"
      class="p-4 rounded-xl text-xs font-semibold flex items-center justify-between border shadow-sm transition-all"
      :class="toastType === 'success' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-red-500/10 border-red-500/30 text-red-400'"
    >
      <div class="flex items-center gap-2">
        <CheckCircle2 v-if="toastType === 'success'" class="w-4 h-4 shrink-0" />
        <AlertCircle v-else class="w-4 h-4 shrink-0" />
        <span>{{ toastMessage }}</span>
      </div>
      <button @click="toastMessage = ''" class="text-slate-400 hover:text-white">
        <X class="w-4 h-4" />
      </button>
    </div>

    <!-- Main 2-Column Vertical Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
      <!-- CỘT TRÁI: FORM NỘI DUNG CHÍNH & SEO (7 Cột) -->
      <div class="lg:col-span-7 space-y-6">
        <!-- 1. Tiêu Đề & Slug -->
        <div
          class="p-6 rounded-2xl border space-y-4 shadow-sm"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <div>
            <label class="block text-xs font-bold mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
              Tiêu Đề Bài Viết <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.title"
              type="text"
              required
              placeholder="Ví dụ: Phân biệt giấy Ford, Couche, Ivory, Bristol khi đặt in"
              class="w-full rounded-xl px-4 py-3 border text-sm font-bold transition-colors focus:outline-none focus:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-white border-slate-200 text-slate-900 placeholder-slate-400'"
              @input="onTitleInput"
            />
          </div>

          <!-- Slug đường dẫn -->
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="text-xs font-bold" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                Đường Dẫn URL (Slug Thân Thiện SEO)
              </label>
              <button
                type="button"
                @click="generateSlugFromTitle"
                class="text-[11px] text-brand-gold hover:underline font-bold flex items-center gap-1 cursor-pointer"
              >
                <Sparkles class="w-3 h-3" />
                <span>Tạo tự động từ tiêu đề</span>
              </button>
            </div>
            <div class="relative">
              <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-mono pointer-events-none">
                /tin-tuc/
              </span>
              <input
                v-model="form.slug"
                type="text"
                required
                placeholder="phan-biet-giay-ford-couche-ivory"
                class="w-full rounded-xl pl-20 pr-4 py-2.5 border text-xs font-mono transition-colors focus:outline-none focus:border-brand-gold"
                :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-white border-slate-200 text-slate-900 placeholder-slate-400'"
              />
            </div>
          </div>

          <!-- Tóm tắt ngắn -->
          <div>
            <label class="block text-xs font-bold mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
              Tóm Tắt Ngắn (Mô tả mở đầu bài viết)
            </label>
            <textarea
              v-model="form.excerpt"
              rows="3"
              placeholder="Tóm tắt nội dung chính trong 2-3 câu giúp người đọc và công cụ tìm kiếm nắm bắt nhanh..."
              class="w-full rounded-xl px-4 py-2.5 border text-xs leading-relaxed transition-colors focus:outline-none focus:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-white border-slate-200 text-slate-900 placeholder-slate-400'"
            ></textarea>
          </div>
        </div>

        <!-- 2. Khung Soạn Thảo Nội Dung Bài Viết -->
        <div
          class="p-6 rounded-2xl border space-y-4 shadow-sm"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b pb-3" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <div>
              <label class="text-xs font-bold block" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                Nội Dung Chi Tiết Bài Viết (Hỗ trợ HTML / Định dạng)
              </label>
              <span class="text-[11px] text-slate-400">
                Sử dụng các nút bên dưới để chèn nhanh tiêu đề đoạn, danh sách hoặc hình ảnh minh họa.
              </span>
            </div>

            <button
              type="button"
              @click="openMediaForContent"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-gold/15 hover:bg-brand-gold/25 text-brand-gold font-bold text-xs transition-colors cursor-pointer self-start sm:self-auto"
            >
              <FolderOpen class="w-3.5 h-3.5" />
              <span>Chèn Ảnh từ Media</span>
            </button>
          </div>

          <!-- Quick Formatting Toolbar -->
          <div class="flex flex-wrap items-center gap-1.5 p-2 rounded-xl bg-slate-500/10 border" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
            <button
              type="button"
              @click="insertFormat('h2')"
              class="px-2.5 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer hover:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
              title="Tiêu đề đoạn H2"
            >
              H2
            </button>
            <button
              type="button"
              @click="insertFormat('h3')"
              class="px-2.5 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer hover:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
              title="Tiêu đề đoạn H3"
            >
              H3
            </button>
            <button
              type="button"
              @click="insertFormat('bold')"
              class="px-2.5 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer hover:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
              title="In đậm"
            >
              <strong>B</strong>
            </button>
            <button
              type="button"
              @click="insertFormat('italic')"
              class="px-2.5 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer hover:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
              title="In nghiêng"
            >
              <em>I</em>
            </button>
            <button
              type="button"
              @click="insertFormat('ul')"
              class="px-2.5 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer hover:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
              title="Danh sách gạch đầu dòng"
            >
              • Danh sách
            </button>
            <button
              type="button"
              @click="insertFormat('quote')"
              class="px-2.5 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer hover:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
              title="Đoạn trích dẫn"
            >
              “ Trích dẫn ”
            </button>
            <button
              type="button"
              @click="insertFormat('tip')"
              class="px-2.5 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer text-amber-500 hover:border-amber-500"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47]' : 'bg-white border-slate-200'"
              title="Hộp lưu ý xưởng in"
            >
              💡 Lưu ý xưởng
            </button>
          </div>

          <!-- Textarea Content -->
          <textarea
            ref="contentEditorRef"
            v-model="form.content"
            rows="16"
            placeholder="Nhập nội dung bài viết ở đây... Bạn có thể gõ nội dung hoặc dán mã HTML/văn bản."
            class="w-full rounded-xl px-4 py-3 border text-xs font-mono leading-relaxed transition-colors focus:outline-none focus:border-brand-gold"
            :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-white border-slate-200 text-slate-900 placeholder-slate-400'"
          ></textarea>
        </div>

        <!-- 3. Cấu hình SEO & Google SERP Snippet Preview -->
        <div
          class="p-6 rounded-2xl border space-y-5 shadow-sm"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <div class="flex items-center gap-2 border-b pb-3" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <Globe class="w-4 h-4 text-brand-gold" />
            <h3 class="text-sm font-bold" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              Cấu Hình SEO Google
            </h3>
          </div>

          <!-- Meta Title -->
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="text-xs font-bold" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                Tiêu Đề SEO (Meta Title)
              </label>
              <span class="text-[10px] font-mono" :class="form.meta_title?.length > 60 ? 'text-amber-400 font-bold' : 'text-slate-400'">
                {{ form.meta_title?.length || 0 }} / 60 ký tự
              </span>
            </div>
            <input
              v-model="form.meta_title"
              type="text"
              :placeholder="form.title || 'Tiêu đề hiển thị trên kết quả tìm kiếm Google'"
              class="w-full rounded-xl px-4 py-2.5 border text-xs transition-colors focus:outline-none focus:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-white border-slate-200 text-slate-900 placeholder-slate-400'"
            />
          </div>

          <!-- Meta Description -->
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="text-xs font-bold" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                Mô Tả SEO (Meta Description)
              </label>
              <span class="text-[10px] font-mono" :class="form.meta_description?.length > 160 ? 'text-amber-400 font-bold' : 'text-slate-400'">
                {{ form.meta_description?.length || 0 }} / 160 ký tự
              </span>
            </div>
            <textarea
              v-model="form.meta_description"
              rows="3"
              :placeholder="form.excerpt || 'Đoạn trích mô tả tóm tắt hiển thị bên dưới tiêu đề trên Google (khoảng 120-160 ký tự)...'"
              class="w-full rounded-xl px-4 py-2.5 border text-xs leading-relaxed transition-colors focus:outline-none focus:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-white border-slate-200 text-slate-900 placeholder-slate-400'"
            ></textarea>
          </div>

          <!-- Google SERP Snippet Preview -->
          <div class="pt-2">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">
              Xem trước kết quả tìm kiếm Google:
            </span>
            <div class="p-4 rounded-xl border bg-white text-slate-900 shadow-xs space-y-1">
              <div class="flex items-center gap-2 text-xs text-slate-600">
                <span class="w-4 h-4 rounded-full bg-brand-gold/30 text-[9px] font-bold flex items-center justify-center text-slate-900">N</span>
                <span class="font-medium text-slate-700">innhatnguyen.vn</span>
                <span class="text-slate-400">&rsaquo; tin-tuc &rsaquo; {{ form.slug || 'bai-viet' }}</span>
              </div>
              <div class="text-[#1a0dab] font-semibold text-base hover:underline cursor-pointer leading-snug line-clamp-1">
                {{ form.meta_title || form.title || 'Tiêu đề bài viết - In Ấn Nhật Nguyên' }}
              </div>
              <p class="text-xs text-[#4d5156] line-clamp-2 leading-relaxed">
                {{ form.meta_description || form.excerpt || 'Nội dung tóm tắt hiển thị trong kết quả tìm kiếm Google nhằm thúc đẩy người dùng click vào xem bài viết...' }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- CỘT PHẢI: XUẤT BẢN, CHUYÊN MỤC, ẢNH ĐẠI DIỆN & PREVIEW (5 Cột Sticky) -->
      <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-24">
        <!-- 1. Hộp Xuất Bản (Publish Box) -->
        <div
          class="p-6 rounded-2xl border space-y-4 shadow-sm"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <div class="flex items-center justify-between border-b pb-3" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <span class="text-xs font-bold" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
              Trạng Thái Bài Viết
            </span>
            <span
              class="px-2.5 py-0.5 rounded-full text-[10px] font-bold"
              :class="form.is_published ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-500/10 text-slate-400'"
            >
              {{ form.is_published ? 'Đang kích hoạt' : 'Bản lưu nháp' }}
            </span>
          </div>

          <!-- Switch Toggle -->
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold" :class="isDarkMode ? 'text-slate-300' : 'text-slate-600'">
              Xuất bản công khai trên web
            </span>
            <button
              type="button"
              @click="form.is_published = !form.is_published"
              class="w-11 h-6 rounded-full transition-colors relative flex items-center p-0.5 cursor-pointer"
              :class="form.is_published ? 'bg-emerald-500' : 'bg-slate-600'"
            >
              <div
                class="w-5 h-5 rounded-full bg-white shadow-md transform transition-transform"
                :class="form.is_published ? 'translate-x-5' : 'translate-x-0'"
              ></div>
            </button>
          </div>

          <!-- Main Publish Button -->
          <button
            type="button"
            @click="savePost"
            :disabled="isSaving"
            class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-brand-gold to-amber-500 hover:from-amber-400 hover:to-brand-gold text-slate-950 font-black text-xs shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-60"
          >
            <Loader2 v-if="isSaving" class="w-4 h-4 animate-spin" />
            <Save v-else class="w-4 h-4" />
            <span>{{ isSaving ? 'Đang lưu bài viết...' : (isEditMode ? 'Cập Nhật Bài Viết' : 'Xuất Bản Bài Viết Ngay') }}</span>
          </button>
        </div>

        <!-- 2. Chuyên Mục Bài Viết (Category) -->
        <div
          class="p-6 rounded-2xl border space-y-3 shadow-sm"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <label class="block text-xs font-bold" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
            Chuyên Mục Bài Viết
          </label>
          <input
            v-model="form.category"
            type="text"
            placeholder="Nhập chuyên mục hoặc chọn gợi ý bên dưới"
            class="w-full rounded-xl px-3.5 py-2.5 border text-xs transition-colors focus:outline-none focus:border-brand-gold"
            :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-900'"
          />

          <!-- Quick Category Suggestions -->
          <div class="flex flex-wrap items-center gap-1.5 pt-1">
            <button
              v-for="cat in categorySuggestions"
              :key="cat"
              type="button"
              @click="form.category = cat"
              class="px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-colors cursor-pointer"
              :class="form.category === cat
                ? 'bg-brand-gold text-slate-950 border-brand-gold'
                : (isDarkMode ? 'border-[#2E3A47] text-slate-300 hover:border-brand-gold' : 'border-slate-200 text-slate-600 hover:border-brand-blue')"
            >
              {{ cat }}
            </button>
          </div>
        </div>

        <!-- 3. Ảnh Đại Diện (Featured Thumbnail) -->
        <div
          class="p-6 rounded-2xl border space-y-4 shadow-sm"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <div class="flex items-center justify-between">
            <div>
              <label class="text-xs font-bold block" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                Ảnh Đại Diện Bài Viết
              </label>
              <span class="text-[10px] text-slate-400">
                Hiển thị trên danh sách bài viết & khi chia sẻ mạng xã hội.
              </span>
            </div>
            <button
              type="button"
              @click="isMediaModalOpen = true"
              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-brand-gold/15 hover:bg-brand-gold/25 text-brand-gold font-bold text-xs transition-colors cursor-pointer"
            >
              <FolderOpen class="w-3.5 h-3.5" />
              <span>Thư Viện</span>
            </button>
          </div>

          <!-- Thumbnail Input & Clear -->
          <div class="flex items-center gap-2">
            <input
              v-model="form.thumbnail"
              type="text"
              placeholder="https://... hoặc /anh.jpg hoặc chọn từ thư viện"
              class="flex-1 rounded-xl px-3.5 py-2.5 border text-xs font-mono transition-colors focus:outline-none focus:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-white border-slate-200 text-slate-900 placeholder-slate-400'"
            />
            <button
              v-if="form.thumbnail"
              type="button"
              @click="form.thumbnail = ''"
              class="p-2.5 rounded-xl border text-slate-400 hover:text-red-400 transition-colors cursor-pointer"
              :class="isDarkMode ? 'border-[#2E3A47] hover:bg-red-500/10' : 'border-slate-200 hover:bg-red-50'"
              title="Xóa ảnh"
            >
              <Trash2 class="w-4 h-4" />
            </button>
          </div>

          <!-- Direct Upload -->
          <div class="flex items-center gap-3">
            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-dashed text-xs font-semibold text-slate-400 hover:text-brand-gold hover:border-brand-gold transition-colors cursor-pointer">
              <Upload class="w-3.5 h-3.5" />
              <span>{{ isUploading ? 'Đang tải lên...' : 'Tải file từ máy tính' }}</span>
              <input
                type="file"
                accept="image/*"
                class="hidden"
                :disabled="isUploading"
                @change="handleDirectUpload"
              />
            </label>
            <span class="text-[10px] text-slate-400">
              Khuyên dùng: 1200 × 630 px (16:9)
            </span>
          </div>

          <!-- Thumbnail Live Preview -->
          <div v-if="form.thumbnail" class="rounded-xl overflow-hidden border aspect-[16/10] bg-slate-950 relative group" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
            <img
              :src="form.thumbnail"
              alt="Ảnh đại diện bài viết"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <span class="absolute bottom-2 right-2 px-2 py-0.5 rounded bg-black/60 text-white text-[10px] font-mono backdrop-blur-sm">
              Ảnh đại diện bài viết
            </span>
          </div>
        </div>

        <!-- 4. Thẻ Xem Trước Bài Viết (Card Preview) -->
        <div
          class="p-6 rounded-2xl border space-y-3 shadow-sm"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <div class="flex items-center justify-between border-b pb-2.5" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <span class="text-xs font-bold" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
              Xem Trước Thẻ Bài Viết Trên Web
            </span>
            <Eye class="w-3.5 h-3.5 text-brand-gold" />
          </div>

          <!-- Card Mockup -->
          <div class="bg-white rounded-2xl overflow-hidden border border-slate-200/90 shadow-sm text-slate-900">
            <div class="aspect-[16/10] bg-slate-100 overflow-hidden relative">
              <img
                :src="form.thumbnail || '/logo.png'"
                alt="Thumbnail"
                class="w-full h-full object-cover"
              />
              <span class="absolute top-2.5 left-2.5 px-2.5 py-0.5 rounded-md bg-brand-blue text-white text-[10px] font-bold">
                {{ form.category || 'Cẩm nang in ấn' }}
              </span>
            </div>
            <div class="p-4 space-y-2">
              <span class="text-[10px] text-slate-400 block">Hôm nay • 5 phút đọc</span>
              <h4 class="font-bold text-sm leading-snug line-clamp-2">
                {{ form.title || 'Tiêu đề bài viết hiển thị ở đây' }}
              </h4>
              <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                {{ form.excerpt || 'Tóm tắt bài viết sẽ hiển thị ở đây...' }}
              </p>
              <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-brand-blue font-bold">
                <span>Đọc bài viết &rarr;</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Integrated Media Library Modal -->
    <MediaLibraryModal
      v-if="isMediaModalOpen"
      :current-url="mediaTarget === 'thumbnail' ? form.thumbnail : ''"
      @select="onMediaSelected"
      @close="isMediaModalOpen = false"
    />
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
  ArrowLeft,
  Save,
  Loader2,
  FolderOpen,
  Trash2,
  Upload,
  Globe,
  Sparkles,
  Eye,
  CheckCircle2,
  AlertCircle,
  X
} from 'lucide-vue-next'
import { api } from '../../services/api'
import { useAdminTheme } from '../../composables/useAdminTheme'
import MediaLibraryModal from '../../components/admin/MediaLibraryModal.vue'

const route = useRoute()
const router = useRouter()
const { isDarkMode } = useAdminTheme()

const isEditMode = computed(() => Boolean(route.params.id))
const isSaving = ref(false)
const isUploading = ref(false)
const isMediaModalOpen = ref(false)
const mediaTarget = ref('thumbnail') // 'thumbnail' or 'content'
const contentEditorRef = ref(null)

const toastMessage = ref('')
const toastType = ref('success')

const categorySuggestions = [
  'Cẩm nang in ấn',
  'Kỹ thuật xuất file',
  'Xu hướng thiết kế',
  'Bao bì & Nhãn dán',
  'Tin tức xưởng in'
]

const form = reactive({
  title: '',
  slug: '',
  excerpt: '',
  content: '',
  thumbnail: '',
  category: 'Cẩm nang in ấn',
  is_published: true,
  published_at: null,
  meta_title: '',
  meta_description: ''
})

function showToast(msg, type = 'success') {
  toastMessage.value = msg
  toastType.value = type
  setTimeout(() => {
    if (toastMessage.value === msg) {
      toastMessage.value = ''
    }
  }, 4000)
}

function convertToSlug(text) {
  if (!text) return ''
  return text
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[đĐ]/g, 'd')
    .replace(/[^a-z0-9\s-]/g, '')
    .trim()
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
}

function onTitleInput() {
  if (!isEditMode.value && !form.slug) {
    form.slug = convertToSlug(form.title)
  }
}

function generateSlugFromTitle() {
  if (form.title.trim()) {
    form.slug = convertToSlug(form.title)
    showToast('Đã tạo slug thân thiện SEO!')
  }
}

// Formatting shortcuts in content editor
function insertFormat(type) {
  let snippet = ''
  switch (type) {
    case 'h2':
      snippet = '\n\n<h2>Tiêu đề đoạn chính</h2>\n<p>Nội dung chi tiết ở đây...</p>\n'
      break
    case 'h3':
      snippet = '\n\n<h3>Tiêu đề phụ</h3>\n<p>Nội dung chi tiết...</p>\n'
      break
    case 'bold':
      snippet = '<strong>đoạn chữ in đậm</strong>'
      break
    case 'italic':
      snippet = '<em>đoạn chữ in nghiêng</em>'
      break
    case 'ul':
      snippet = '\n<ul>\n  <li>Ý thứ nhất: mô tả chi tiết</li>\n  <li>Ý thứ hai: mô tả chi tiết</li>\n</ul>\n'
      break
    case 'quote':
      snippet = '\n<blockquote class="p-4 border-l-4 border-brand-gold bg-slate-100 my-4 italic">"Trích dẫn hoặc lời khuyên chuyên gia ở đây..."</blockquote>\n'
      break
    case 'tip':
      snippet = '\n<div class="p-4 rounded-xl border border-amber-300 bg-amber-50 text-slate-800 my-4 text-xs font-medium">💡 <strong>Lưu ý từ Xưởng In Nhật Nguyên:</strong> Hãy luôn kiểm tra file CMYK trước khi in hàng loạt.</div>\n'
      break
  }

  form.content = (form.content || '') + snippet
}

function openMediaForContent() {
  mediaTarget.value = 'content'
  isMediaModalOpen.value = true
}

function onMediaSelected(mediaItem) {
  const url = typeof mediaItem === 'string' ? mediaItem : (mediaItem?.url || '')
  if (!url) return

  if (mediaTarget.value === 'thumbnail') {
    form.thumbnail = url
    showToast('Đã chọn ảnh đại diện bài viết!')
  } else if (mediaTarget.value === 'content') {
    const imgHtml = `\n\n<figure class="my-6 text-center">\n  <img src="${url}" alt="${form.title || 'Hình ảnh in ấn'}" class="rounded-2xl max-w-full h-auto mx-auto shadow-md" />\n  <figcaption class="text-xs text-slate-400 mt-2 italic">Hình minh họa từ In Nhật Nguyên</figcaption>\n</figure>\n\n`
    form.content = (form.content || '') + imgHtml
    showToast('Đã chèn ảnh vào nội dung bài viết!')
  }

  isMediaModalOpen.value = false
}

async function handleDirectUpload(e) {
  const file = e.target.files?.[0]
  if (!file) return

  try {
    isUploading.value = true
    const formData = new FormData()
    formData.append('file', file)
    formData.append('folder', 'posts')

    const res = await api.uploadAdminMedia(formData)
    if (res.data?.url) {
      form.thumbnail = res.data.url
      showToast('Tải ảnh đại diện lên thành công!')
    }
  } catch (err) {
    console.error('Upload failed', err)
    showToast(err.data?.message || err.message || 'Tải ảnh thất bại', 'error')
  } finally {
    isUploading.value = false
    e.target.value = ''
  }
}

async function fetchPostDetail() {
  if (!isEditMode.value) return
  try {
    const res = await api.getAdminPost(route.params.id)
    if (res.data) {
      const p = res.data
      form.title = p.title || ''
      form.slug = p.slug || ''
      form.excerpt = p.excerpt || ''
      form.content = p.content || ''
      form.thumbnail = p.thumbnail || ''
      form.category = p.category || 'Cẩm nang in ấn'
      form.is_published = p.is_published ?? true
      form.published_at = p.published_at || null
      form.meta_title = p.meta_title || ''
      form.meta_description = p.meta_description || ''
    }
  } catch (err) {
    console.error('Failed to load post detail', err)
    showToast('Không thể tải thông tin bài viết', 'error')
  }
}

async function savePost() {
  if (!form.title.trim()) {
    showToast('Vui lòng nhập tiêu đề bài viết', 'error')
    return
  }

  try {
    isSaving.value = true
    const payload = {
      title: form.title.trim(),
      slug: form.slug?.trim() || convertToSlug(form.title),
      excerpt: form.excerpt?.trim() || null,
      content: form.content?.trim() || null,
      thumbnail: form.thumbnail?.trim() || null,
      category: form.category?.trim() || 'Cẩm nang in ấn',
      is_published: Boolean(form.is_published),
      published_at: form.published_at,
      meta_title: form.meta_title?.trim() || null,
      meta_description: form.meta_description?.trim() || null
    }

    if (isEditMode.value) {
      await api.updateAdminPost(route.params.id, payload)
      showToast('Cập nhật bài viết thành công!')
    } else {
      const res = await api.createAdminPost(payload)
      showToast('Đăng bài viết mới thành công!')
      router.push(`/he-thong-nn/posts/${res.data.id}/edit`)
    }
  } catch (err) {
    console.error('Failed to save post', err)
    showToast(err.message || 'Lỗi khi lưu bài viết', 'error')
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  if (isEditMode.value) {
    fetchPostDetail()
  }
})
</script>


