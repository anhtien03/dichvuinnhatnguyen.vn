<template>
  <div
    class="rounded-2xl border p-5 sm:p-6 space-y-5 transition-all shadow-sm"
    :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
  >
    <!-- Header with Rank Math Badge & Score -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b gap-3" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
      <div class="flex items-center gap-3">
        <!-- Rank Math Branded Icon -->
        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-600 via-pink-600 to-indigo-600 text-white flex items-center justify-center font-black text-base shadow-md">
          RM
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h3 class="text-sm sm:text-base font-extrabold flex items-center gap-1.5" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              Rank Math SEO Analyzer
            </h3>
            <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-indigo-500/15 text-indigo-500 border border-indigo-500/20 uppercase tracking-wider">
              PRO
            </span>
          </div>
          <p class="text-[11px] text-slate-400 mt-0.5">
            Bộ đo lường & kiểm toán chuẩn SEO Google thời gian thực
          </p>
        </div>
      </div>

      <!-- Live SEO Score Circular Gauge -->
      <div class="flex items-center gap-3 self-end sm:self-auto">
        <div class="text-right">
          <div class="text-xs font-bold" :class="scoreStatus.colorClass">
            {{ scoreStatus.label }}
          </div>
          <div class="text-[10px] text-slate-400">
            {{ totalPassCount }} / {{ totalTestsCount }} tiêu chí đạt
          </div>
        </div>

        <div
          class="w-14 h-14 rounded-2xl flex flex-col items-center justify-center font-black text-lg border-2 shadow-sm transition-all"
          :class="[scoreStatus.badgeClass, isDarkMode ? 'bg-[#1A222C]' : 'bg-white']"
        >
          <span class="leading-none">{{ finalScore }}</span>
          <span class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter">/ 100</span>
        </div>
      </div>
    </div>

    <!-- 1. Focus Keyword Input -->
    <div class="space-y-2">
      <div class="flex items-center justify-between">
        <label class="text-xs font-bold flex items-center gap-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
          <Target class="w-3.5 h-3.5 text-indigo-500" />
          <span>Từ Khóa Mục Tiêu (Focus Keyword):</span>
        </label>
        <span class="text-[11px] text-slate-400">Từ khóa chính bạn muốn lên TOP Google</span>
      </div>

      <div class="relative">
        <input
          v-model="focusKeyword"
          type="text"
          placeholder="Ví dụ: in bao thư, in túi giấy giá rẻ, in namecard..."
          class="w-full px-3.5 py-2.5 rounded-xl border text-xs sm:text-sm font-semibold focus:outline-none transition-all pl-9"
          :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white focus:border-indigo-500' : 'bg-slate-50 border-slate-200 text-slate-900 focus:border-indigo-600 focus:bg-white'"
          @input="emitChanges"
        />
        <Search class="w-4 h-4 text-slate-400 absolute left-3 top-3" />
      </div>

      <!-- Quick Suggested Keywords based on Title -->
      <div v-if="suggestedKeywords.length > 0" class="flex items-center gap-1.5 flex-wrap pt-1">
        <span class="text-[10px] text-slate-400 font-semibold">Gợi ý từ khóa:</span>
        <button
          v-for="kw in suggestedKeywords"
          :key="kw"
          type="button"
          @click="setFocusKeyword(kw)"
          class="px-2 py-0.5 rounded-md text-[10px] font-bold transition-all cursor-pointer"
          :class="focusKeyword.toLowerCase() === kw.toLowerCase() ? 'bg-indigo-600 text-white' : 'bg-slate-200/70 hover:bg-slate-300 text-slate-700 dark:bg-slate-800 dark:text-slate-300'"
        >
          {{ kw }}
        </button>
      </div>
    </div>

    <!-- 2. One-Click AI / Instant Optimizer -->
    <div class="p-3.5 rounded-xl bg-gradient-to-r from-indigo-500/10 via-purple-500/10 to-pink-500/10 border border-indigo-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div class="space-y-0.5">
        <div class="text-xs font-bold text-indigo-400 flex items-center gap-1.5">
          <Zap class="w-3.5 h-3.5 text-amber-400" />
          <span>Tối Ưu Nhanh Chuẩn Rank Math 85+ Điểm</span>
        </div>
        <p class="text-[11px] text-slate-400">
          Tự động sinh Tiêu đề & Thẻ mô tả tối ưu từ khóa, độ dài và các từ kích thích click.
        </p>
      </div>

      <button
        type="button"
        @click="autoOptimizeSEO"
        class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-1.5 cursor-pointer shrink-0"
      >
        <Sparkles class="w-3.5 h-3.5" />
        <span>Tối Ưu 1 Chạm</span>
      </button>
    </div>

    <!-- 3. Google SERP Snippet Preview -->
    <div class="space-y-2 pt-2 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
          <Eye class="w-3.5 h-3.5" /> Xem trước kết quả tìm kiếm Google (SERP Preview):
        </span>
        <div class="flex items-center gap-1 bg-slate-200/60 dark:bg-slate-800 p-0.5 rounded-lg text-[10px] font-bold">
          <button
            type="button"
            @click="previewDevice = 'desktop'"
            :class="previewDevice === 'desktop' ? 'bg-white dark:bg-slate-700 text-indigo-600 shadow-xs' : 'text-slate-400'"
            class="px-2 py-1 rounded-md transition-all cursor-pointer"
          >
            Desktop
          </button>
          <button
            type="button"
            @click="previewDevice = 'mobile'"
            :class="previewDevice === 'mobile' ? 'bg-white dark:bg-slate-700 text-indigo-600 shadow-xs' : 'text-slate-400'"
            class="px-2 py-1 rounded-md transition-all cursor-pointer"
          >
            Mobile
          </button>
        </div>
      </div>

      <!-- Preview Box -->
      <div
        class="rounded-xl border p-4 bg-white text-slate-900 shadow-xs space-y-1.5 transition-all text-left"
        :class="previewDevice === 'mobile' ? 'max-w-sm mx-auto' : 'w-full'"
      >
        <div class="flex items-center gap-2 text-[12px] text-slate-600">
          <img src="/logo-icon.png" alt="Logo" class="w-4 h-4 rounded-full object-contain shrink-0" />
          <div class="truncate font-sans text-xs">
            <span class="font-bold text-slate-800">dichvuinnhatnguyen.vn</span>
            <span class="text-slate-400"> › {{ urlPrefix }} › </span>
            <span class="text-slate-600 font-mono">{{ slug || 'duong-dan-seo' }}</span>
          </div>
        </div>

        <div class="text-base sm:text-lg font-medium text-[#1a0dab] hover:underline cursor-pointer leading-snug line-clamp-2">
          {{ metaTitle || title || 'Tiêu Đề Ấn Phẩm In - Xưởng In Nhật Nguyên' }}
        </div>

        <p class="text-xs text-[#4d5156] leading-relaxed line-clamp-2">
          {{ metaDescription || defaultExcerpt || 'Xưởng in Offset & Kỹ thuật số Nhật Nguyên — Chuyên sản xuất ấn phẩm in ấn chất lượng cao, giá gốc trực tiếp không qua trung gian.' }}
        </p>
      </div>
    </div>

    <!-- 4. Rank Math SEO Checklist Tabs -->
    <div class="space-y-3 pt-2 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
      <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-bold border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
        <button
          type="button"
          @click="activeTab = 'basic'"
          :class="activeTab === 'basic' ? 'border-indigo-500 text-indigo-500' : 'border-transparent text-slate-400 hover:text-slate-300'"
          class="pb-2 border-b-2 transition-all flex items-center gap-1.5 cursor-pointer whitespace-nowrap"
        >
          <span>SEO Cơ Bản (Basic)</span>
          <span class="text-[10px] px-1.5 py-0.2 rounded-full" :class="basicPassRate >= 4 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400'">
            {{ basicPassCount }}/5
          </span>
        </button>

        <button
          type="button"
          @click="activeTab = 'additional'"
          :class="activeTab === 'additional' ? 'border-indigo-500 text-indigo-500' : 'border-transparent text-slate-400 hover:text-slate-300'"
          class="pb-2 border-b-2 transition-all flex items-center gap-1.5 cursor-pointer whitespace-nowrap"
        >
          <span>Bổ Sung (Additional)</span>
          <span class="text-[10px] px-1.5 py-0.2 rounded-full" :class="additionalPassRate >= 3 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400'">
            {{ additionalPassCount }}/4
          </span>
        </button>

        <button
          type="button"
          @click="activeTab = 'title'"
          :class="activeTab === 'title' ? 'border-indigo-500 text-indigo-500' : 'border-transparent text-slate-400 hover:text-slate-300'"
          class="pb-2 border-b-2 transition-all flex items-center gap-1.5 cursor-pointer whitespace-nowrap"
        >
          <span>Tiêu Đề (Title & CTR)</span>
          <span class="text-[10px] px-1.5 py-0.2 rounded-full" :class="titlePassRate >= 3 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400'">
            {{ titlePassCount }}/4
          </span>
        </button>
      </div>

      <!-- Tab 1: Basic SEO -->
      <div v-show="activeTab === 'basic'" class="space-y-2 text-xs">
        <!-- 1. Keyword in Title -->
        <div class="flex items-start gap-2.5 p-2 rounded-lg" :class="testKeywordInTitle ? 'bg-emerald-500/10' : 'bg-red-500/10'">
          <CheckCircle2 v-if="testKeywordInTitle" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
          <XCircle v-else class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" />
          <div class="min-w-0">
            <span class="font-bold" :class="testKeywordInTitle ? 'text-emerald-400' : 'text-rose-400'">
              Từ khóa chính có trong Tiêu Đề SEO (Meta Title)
            </span>
            <p class="text-[11px] text-slate-400 mt-0.5">
              {{ testKeywordInTitle ? 'Xuất sắc! Google sẽ dễ dàng xếp hạng nội dung cho từ khóa này.' : 'Chưa có từ khóa chính trong Tiêu Đề SEO.' }}
            </p>
          </div>
        </div>

        <!-- 2. Keyword in Meta Description -->
        <div class="flex items-start gap-2.5 p-2 rounded-lg" :class="testKeywordInDesc ? 'bg-emerald-500/10' : 'bg-red-500/10'">
          <CheckCircle2 v-if="testKeywordInDesc" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
          <XCircle v-else class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" />
          <div class="min-w-0">
            <span class="font-bold" :class="testKeywordInDesc ? 'text-emerald-400' : 'text-rose-400'">
              Từ khóa chính có trong Thẻ Mô Tả (Meta Description)
            </span>
            <p class="text-[11px] text-slate-400 mt-0.5">
              {{ testKeywordInDesc ? 'Đạt chuẩn! Giúp gia tăng tỷ lệ click (CTR) trên kết quả tìm kiếm.' : 'Thêm từ khóa mục tiêu vào thẻ mô tả để đạt điểm tối đa.' }}
            </p>
          </div>
        </div>

        <!-- 3. Keyword in URL Slug -->
        <div class="flex items-start gap-2.5 p-2 rounded-lg" :class="testKeywordInSlug ? 'bg-emerald-500/10' : 'bg-red-500/10'">
          <CheckCircle2 v-if="testKeywordInSlug" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
          <XCircle v-else class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" />
          <div class="min-w-0">
            <span class="font-bold" :class="testKeywordInSlug ? 'text-emerald-400' : 'text-rose-400'">
              Từ khóa chính có trong Đường Dẫn URL (Slug)
            </span>
            <p class="text-[11px] text-slate-400 mt-0.5">
              {{ testKeywordInSlug ? 'Tuyệt vời! URL thân thiện và chứa từ khóa không dấu.' : 'Đường dẫn chưa chứa từ khóa mục tiêu.' }}
            </p>
          </div>
        </div>

        <!-- 4. Keyword in 10% Intro Content -->
        <div class="flex items-start gap-2.5 p-2 rounded-lg" :class="testKeywordInIntro ? 'bg-emerald-500/10' : 'bg-amber-500/10'">
          <CheckCircle2 v-if="testKeywordInIntro" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
          <AlertCircle v-else class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
          <div class="min-w-0">
            <span class="font-bold" :class="testKeywordInIntro ? 'text-emerald-400' : 'text-amber-400'">
              Từ khóa chính xuất hiện ở đoạn đầu nội dung (10% đầu)
            </span>
            <p class="text-[11px] text-slate-400 mt-0.5">
              {{ testKeywordInIntro ? 'Tốt! Thuật toán Google nhận diện chủ đề bài viết ngay từ đoạn đầu.' : 'Nên nhắc lại từ khóa ở 1-2 câu đầu tiên của mô tả / nội dung.' }}
            </p>
          </div>
        </div>

        <!-- 5. Content Length -->
        <div class="flex items-start gap-2.5 p-2 rounded-lg" :class="testContentLength ? 'bg-emerald-500/10' : 'bg-amber-500/10'">
          <CheckCircle2 v-if="testContentLength" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
          <AlertCircle v-else class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
          <div class="min-w-0">
            <span class="font-bold" :class="testContentLength ? 'text-emerald-400' : 'text-amber-400'">
              Độ dài nội dung: {{ wordCount }} từ (Tối thiểu {{ minWordsTarget }} từ)
            </span>
            <p class="text-[11px] text-slate-400 mt-0.5">
              {{ testContentLength ? 'Nội dung có độ sâu và chi tiết cao, rất tốt cho SEO.' : `Bài viết/mô tả hiện có ${wordCount} từ. Hãy viết thêm để vượt mốc ${minWordsTarget} từ.` }}
            </p>
          </div>
        </div>
      </div>

      <!-- Tab 2: Additional SEO -->
      <div v-show="activeTab === 'additional'" class="space-y-2 text-xs">
        <!-- 1. Keyword in Subheadings H2/H3 or Bullet items -->
        <div class="flex items-start gap-2.5 p-2 rounded-lg" :class="testKeywordInSubheadings ? 'bg-emerald-500/10' : 'bg-amber-500/10'">
          <CheckCircle2 v-if="testKeywordInSubheadings" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
          <AlertCircle v-else class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
          <div class="min-w-0">
            <span class="font-bold" :class="testKeywordInSubheadings ? 'text-emerald-400' : 'text-amber-400'">
              Từ khóa xuất hiện trong các tiêu đề phụ (H2, H3, Quy cách)
            </span>
            <p class="text-[11px] text-slate-400 mt-0.5">
              {{ testKeywordInSubheadings ? 'Đạt chuẩn phân bổ từ khóa theo cấu trúc thẻ đề mục.' : 'Nên đặt từ khóa vào ít nhất một tiêu đề phụ H2 hoặc H3.' }}
            </p>
          </div>
        </div>

        <!-- 2. Image and Media -->
        <div class="flex items-start gap-2.5 p-2 rounded-lg" :class="testHasImage ? 'bg-emerald-500/10' : 'bg-red-500/10'">
          <CheckCircle2 v-if="testHasImage" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
          <XCircle v-else class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" />
          <div class="min-w-0">
            <span class="font-bold" :class="testHasImage ? 'text-emerald-400' : 'text-rose-400'">
              Đã có hình ảnh đại diện / ảnh ấn phẩm
            </span>
            <p class="text-[11px] text-slate-400 mt-0.5">
              {{ testHasImage ? 'Hình ảnh sắc nét giúp tăng chuyển đổi và tối ưu Google Hình Ảnh.' : 'Chưa có hình ảnh. Cần tải ảnh lên để Google lập chỉ mục.' }}
            </p>
          </div>
        </div>

        <!-- 3. Keyword Density -->
        <div class="flex items-start gap-2.5 p-2 rounded-lg" :class="testKeywordDensity ? 'bg-emerald-500/10' : 'bg-amber-500/10'">
          <CheckCircle2 v-if="testKeywordDensity" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
          <AlertCircle v-else class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
          <div class="min-w-0">
            <span class="font-bold" :class="testKeywordDensity ? 'text-emerald-400' : 'text-amber-400'">
              Mật độ từ khóa (Keyword Density): {{ keywordDensity }}% (Xuất hiện {{ keywordCount }} lần)
            </span>
            <p class="text-[11px] text-slate-400 mt-0.5">
              {{ testKeywordDensity ? 'Mật độ từ khóa hoàn hảo trong ngưỡng vàng (1.0% - 2.5%), tự nhiên và không spam.' : 'Mật độ từ khóa nên nằm trong khoảng 1.0% - 2.5%.' }}
            </p>
          </div>
        </div>

        <!-- 4. URL Length -->
        <div class="flex items-start gap-2.5 p-2 rounded-lg" :class="testUrlLength ? 'bg-emerald-500/10' : 'bg-amber-500/10'">
          <CheckCircle2 v-if="testUrlLength" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
          <AlertCircle v-else class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
          <div class="min-w-0">
            <span class="font-bold" :class="testUrlLength ? 'text-emerald-400' : 'text-amber-400'">
              Độ dài đường dẫn URL: {{ slug?.length || 0 }} ký tự (Ngắn gọn < 75 ký tự)
            </span>
            <p class="text-[11px] text-slate-400 mt-0.5">
              {{ testUrlLength ? 'Đường dẫn ngắn gọn, dễ nhớ, chuẩn khuyến nghị Googlebot.' : 'URL quá dài có thể bị cắt bớt trên điện thoại.' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Tab 3: Title & Readability -->
      <div v-show="activeTab === 'title'" class="space-y-2 text-xs">
        <!-- 1. Keyword at beginning of Title -->
        <div class="flex items-start gap-2.5 p-2 rounded-lg" :class="testKeywordAtTitleStart ? 'bg-emerald-500/10' : 'bg-amber-500/10'">
          <CheckCircle2 v-if="testKeywordAtTitleStart" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
          <AlertCircle v-else class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
          <div class="min-w-0">
            <span class="font-bold" :class="testKeywordAtTitleStart ? 'text-emerald-400' : 'text-amber-400'">
              Từ khóa chính nằm ở vị trí đầu của Tiêu Đề
            </span>
            <p class="text-[11px] text-slate-400 mt-0.5">
              {{ testKeywordAtTitleStart ? 'Rất tốt! Đặt từ khóa ở đầu giúp tăng trọng số SEO cao nhất.' : 'Nên chuyển từ khóa chính ra phía đầu tiêu đề.' }}
            </p>
          </div>
        </div>

        <!-- 2. Numbers / Year in Title -->
        <div class="flex items-start gap-2.5 p-2 rounded-lg" :class="testTitleHasNumber ? 'bg-emerald-500/10' : 'bg-amber-500/10'">
          <CheckCircle2 v-if="testTitleHasNumber" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
          <AlertCircle v-else class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
          <div class="min-w-0">
            <span class="font-bold" :class="testTitleHasNumber ? 'text-emerald-400' : 'text-amber-400'">
              Tiêu đề có chứa con số hoặc năm (ví dụ: 2026, 4h, 24h, 500 cái...)
            </span>
            <p class="text-[11px] text-slate-400 mt-0.5">
              {{ testTitleHasNumber ? 'Con số kích thích thị giác, giúp tăng 28% tỷ lệ nhấp chuột vào web.' : 'Nên thêm con số (ví dụ: 2026, 4h, 100%...) để thu hút người dùng.' }}
            </p>
          </div>
        </div>

        <!-- 3. Power Words in Title -->
        <div class="flex items-start gap-2.5 p-2 rounded-lg" :class="testTitleHasPowerWord ? 'bg-emerald-500/10' : 'bg-amber-500/10'">
          <CheckCircle2 v-if="testTitleHasPowerWord" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
          <AlertCircle v-else class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
          <div class="min-w-0">
            <span class="font-bold" :class="testTitleHasPowerWord ? 'text-emerald-400' : 'text-amber-400'">
              Tiêu đề chứa từ ngữ kêu gọi hành động (Power Words)
            </span>
            <p class="text-[11px] text-slate-400 mt-0.5">
              {{ testTitleHasPowerWord ? 'Tiêu đề có sức hút cao (chứa từ: Giá Rẻ, Tận Xưởng, Uy Tín, Lấy Ngay...).' : 'Nên thêm các từ: Giá Rẻ, Trực Tiếp, Tận Xưởng, Lấy Ngay, Đẹp, Chuẩn...' }}
            </p>
          </div>
        </div>

        <!-- 4. Title Length -->
        <div class="flex items-start gap-2.5 p-2 rounded-lg" :class="testTitleLength ? 'bg-emerald-500/10' : 'bg-amber-500/10'">
          <CheckCircle2 v-if="testTitleLength" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
          <AlertCircle v-else class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
          <div class="min-w-0">
            <span class="font-bold" :class="testTitleLength ? 'text-emerald-400' : 'text-amber-400'">
              Độ dài Tiêu Đề chuẩn: {{ metaTitle?.length || 0 }} / 65 ký tự
            </span>
            <p class="text-[11px] text-slate-400 mt-0.5">
              {{ testTitleLength ? 'Độ dài tối ưu (45 - 65 ký tự), không bị Google cắt bớt bằng dấu ba chấm (...).' : 'Tiêu đề quá ngắn (<40) hoặc quá dài (>65) ký tự.' }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  Target, Search, Zap, Sparkles, Eye, CheckCircle2,
  XCircle, AlertCircle
} from 'lucide-vue-next'
import { useAdminTheme } from '../../composables/useAdminTheme'
import { slugify } from '../../composables/useSEO'

const { isDarkMode } = useAdminTheme()

const props = defineProps({
  title: { type: String, default: '' },
  metaTitle: { type: String, default: '' },
  metaDescription: { type: String, default: '' },
  slug: { type: String, default: '' },
  content: { type: String, default: '' },
  image: { type: String, default: '' },
  initialKeyword: { type: String, default: '' },
  urlPrefix: { type: String, default: 'san-pham' },
  type: { type: String, default: 'product' }, // 'product' or 'post'
  defaultExcerpt: { type: String, default: '' }
})

const emit = defineEmits([
  'update:focusKeyword',
  'update:metaTitle',
  'update:metaDescription',
  'update:slug',
  'optimized'
])

const focusKeyword = ref(props.initialKeyword || '')
const previewDevice = ref('desktop')
const activeTab = ref('basic')

const minWordsTarget = computed(() => props.type === 'post' ? 500 : 200)

// Normalization function for Vietnamese accents
function removeAccents(str) {
  if (!str) return ''
  return str
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/đ/g, 'd')
    .replace(/Đ/g, 'd')
    .trim()
}

// Generate suggested keywords from Title
const suggestedKeywords = computed(() => {
  const base = props.title || props.metaTitle || ''
  if (!base) return []
  const clean = base.replace(/[-|–,.:]/g, ' ').trim()
  const words = clean.split(/\s+/).filter(w => w.length > 2)
  const suggestions = []

  // 2-word phrase
  if (words.length >= 2) {
    suggestions.push(words.slice(0, 2).join(' ').toLowerCase())
  }
  // 3-word phrase
  if (words.length >= 3) {
    suggestions.push(words.slice(0, 3).join(' ').toLowerCase())
  }
  // If product name
  if (words.length > 0) {
    suggestions.push(words.slice(0, 4).join(' ').toLowerCase())
  }
  return [...new Set(suggestions)].slice(0, 3)
})

function setFocusKeyword(kw) {
  focusKeyword.value = kw
  emitChanges()
}

function emitChanges() {
  emit('update:focusKeyword', focusKeyword.value)
}

// Combined content text
const fullContentText = computed(() => {
  return `${props.content || ''} ${props.defaultExcerpt || ''}`.replace(/<[^>]+>/g, ' ').trim()
})

const wordCount = computed(() => {
  if (!fullContentText.value) return 0
  return fullContentText.value.split(/\s+/).filter(Boolean).length
})

const keywordCount = computed(() => {
  if (!focusKeyword.value.trim() || !fullContentText.value) return 0
  const cleanContent = removeAccents(fullContentText.value)
  const cleanKw = removeAccents(focusKeyword.value)
  if (!cleanKw) return 0
  const matches = cleanContent.match(new RegExp('\\b' + cleanKw + '\\b', 'gi'))
  return matches ? matches.length : 0
})

const keywordDensity = computed(() => {
  if (wordCount.value === 0 || keywordCount.value === 0) return 0
  const kwWords = focusKeyword.value.trim().split(/\s+/).length
  const pct = ((keywordCount.value * kwWords) / wordCount.value) * 100
  return Number(pct.toFixed(1))
})

// ==========================================
// RANK MATH CHECKLIST TESTS
// ==========================================

// 1. Keyword in Meta Title
const testKeywordInTitle = computed(() => {
  if (!focusKeyword.value.trim()) return false
  const target = removeAccents(props.metaTitle || props.title || '')
  return target.includes(removeAccents(focusKeyword.value))
})

// 2. Keyword in Meta Description
const testKeywordInDesc = computed(() => {
  if (!focusKeyword.value.trim()) return false
  const target = removeAccents(props.metaDescription || props.defaultExcerpt || '')
  return target.includes(removeAccents(focusKeyword.value))
})

// 3. Keyword in Slug
const testKeywordInSlug = computed(() => {
  if (!focusKeyword.value.trim()) return false
  const cleanSlug = (props.slug || '').replace(/-/g, ' ')
  const cleanKw = removeAccents(focusKeyword.value)
  const kwParts = cleanKw.split(/\s+/)
  return kwParts.every(part => cleanSlug.includes(part))
})

// 4. Keyword in 10% Intro
const testKeywordInIntro = computed(() => {
  if (!focusKeyword.value.trim() || !fullContentText.value) return false
  const introLength = Math.max(150, Math.floor(fullContentText.value.length * 0.15))
  const introText = removeAccents(fullContentText.value.substring(0, introLength))
  return introText.includes(removeAccents(focusKeyword.value))
})

// 5. Content Length
const testContentLength = computed(() => {
  return wordCount.value >= minWordsTarget.value
})

// 6. Keyword in Subheadings
const testKeywordInSubheadings = computed(() => {
  if (!focusKeyword.value.trim() || !props.content) return false
  const hMatches = props.content.match(/<h[234][^>]*>(.*?)<\/h[234]>/gi) || []
  const cleanKw = removeAccents(focusKeyword.value)
  return hMatches.some(h => removeAccents(h).includes(cleanKw))
})

// 7. Has Image
const testHasImage = computed(() => {
  return !!props.image && props.image.length > 5
})

// 8. Keyword Density in safe range 0.8% - 2.8%
const testKeywordDensity = computed(() => {
  return keywordDensity.value >= 0.8 && keywordDensity.value <= 2.8
})

// 9. URL Length < 75 chars
const testUrlLength = computed(() => {
  return (props.slug || '').length > 3 && (props.slug || '').length <= 75
})

// 10. Keyword at start of Title
const testKeywordAtTitleStart = computed(() => {
  if (!focusKeyword.value.trim()) return false
  const title = removeAccents(props.metaTitle || props.title || '')
  const kw = removeAccents(focusKeyword.value)
  const idx = title.indexOf(kw)
  return idx >= 0 && idx <= 15
})

// 11. Title has Number or Year (2026, 4h, 24h, 100%, 500...)
const testTitleHasNumber = computed(() => {
  const target = props.metaTitle || props.title || ''
  return /\d+/.test(target)
})

// 12. Title has Power Words
const POWER_WORDS = [
  'gia re', 'tan xuong', 'lay ngay', 'lay lien', 'chat luong', 'chuyen nghiep',
  'uy tin', 'dep', 'chuan mau', 'mien phi', 'tphcm', '24h', 'nhanh', 'bao gia'
]
const testTitleHasPowerWord = computed(() => {
  const title = removeAccents(props.metaTitle || props.title || '')
  return POWER_WORDS.some(pw => title.includes(pw))
})

// 13. Title length 40 - 65 chars
const testTitleLength = computed(() => {
  const len = (props.metaTitle || props.title || '').length
  return len >= 40 && len <= 65
})

// Compute counts
const basicPassCount = computed(() => {
  return [testKeywordInTitle.value, testKeywordInDesc.value, testKeywordInSlug.value, testKeywordInIntro.value, testContentLength.value].filter(Boolean).length
})
const basicPassRate = computed(() => basicPassCount.value)

const additionalPassCount = computed(() => {
  return [testKeywordInSubheadings.value, testHasImage.value, testKeywordDensity.value, testUrlLength.value].filter(Boolean).length
})
const additionalPassRate = computed(() => additionalPassCount.value)

const titlePassCount = computed(() => {
  return [testKeywordAtTitleStart.value, testTitleHasNumber.value, testTitleHasPowerWord.value, testTitleLength.value].filter(Boolean).length
})
const titlePassRate = computed(() => titlePassCount.value)

const totalPassCount = computed(() => basicPassCount.value + additionalPassCount.value + titlePassCount.value)
const totalTestsCount = 13

// Final Score (0 - 100)
const finalScore = computed(() => {
  if (!focusKeyword.value.trim()) {
    // If no focus keyword, max score is 30
    return Math.min(30, (props.metaTitle ? 15 : 0) + (props.metaDescription ? 15 : 0))
  }
  let score = 0
  // Basic (40 pts)
  if (testKeywordInTitle.value) score += 10
  if (testKeywordInDesc.value) score += 10
  if (testKeywordInSlug.value) score += 10
  if (testKeywordInIntro.value) score += 5
  if (testContentLength.value) score += 5

  // Additional (30 pts)
  if (testKeywordInSubheadings.value) score += 8
  if (testHasImage.value) score += 8
  if (testKeywordDensity.value) score += 8
  if (testUrlLength.value) score += 6

  // Title & CTR (30 pts)
  if (testKeywordAtTitleStart.value) score += 8
  if (testTitleHasNumber.value) score += 7
  if (testTitleHasPowerWord.value) score += 8
  if (testTitleLength.value) score += 7

  return Math.min(100, Math.max(10, score))
})

const scoreStatus = computed(() => {
  const s = finalScore.value
  if (s >= 80) {
    return {
      label: 'Chuẩn SEO Rank Math',
      colorClass: 'text-emerald-500',
      badgeClass: 'border-emerald-500 text-emerald-500'
    }
  }
  if (s >= 50) {
    return {
      label: 'Khá, Cần Cải Thiện',
      colorClass: 'text-amber-500',
      badgeClass: 'border-amber-500 text-amber-500'
    }
  }
  return {
    label: 'Chưa Đạt Chuẩn SEO',
    colorClass: 'text-rose-500',
    badgeClass: 'border-rose-500 text-rose-500'
  }
})

// Auto Optimize 1-Click
function autoOptimizeSEO() {
  const baseName = props.title || 'Ấn Phẩm'
  const kw = focusKeyword.value.trim() || baseName.toLowerCase()
  if (!focusKeyword.value.trim()) {
    focusKeyword.value = kw
  }

  // Capitalize keyword for Title
  const titleKw = kw.charAt(0).toUpperCase() + kw.slice(1)

  // Meta Title (target 50 - 62 chars)
  const newTitle = `${titleKw} Giá Rẻ Tận Xưởng 2026 | In Nhật Nguyên`

  // Meta Description (target 135 - 155 chars)
  const newDesc = `Xưởng In Nhật Nguyên chuyên ${kw} chất lượng cao, chuẩn màu Offset 98%, lấy nhanh 4h - 24h, giá gốc trực tiếp tại xưởng không qua trung gian.`

  // Slug
  const newSlug = slugify(kw)

  emit('update:metaTitle', newTitle)
  emit('update:metaDescription', newDesc)
  emit('update:slug', newSlug)
  emit('update:focusKeyword', kw)
  emit('optimized', { metaTitle: newTitle, metaDescription: newDesc, slug: newSlug, focusKeyword: kw })
}

onMounted(() => {
  if (!focusKeyword.value && props.title) {
    const s = suggestedKeywords.value[0]
    if (s) focusKeyword.value = s
  }
})
</script>

