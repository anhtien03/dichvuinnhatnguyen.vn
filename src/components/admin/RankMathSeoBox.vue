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
          Dùng AI Gemini 3.8 để viết Meta Title, Description, Slug riêng biệt hoặc dùng mẫu tối ưu tức thì.
        </p>
      </div>

      <div class="flex flex-wrap items-center gap-2 shrink-0">
        <button
          type="button"
          :disabled="isOptimizingAi"
          @click="optimizeWithAi"
          class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 disabled:opacity-60 disabled:cursor-not-allowed text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-1.5 cursor-pointer"
        >
          <Loader2 v-if="isOptimizingAi" class="w-3.5 h-3.5 animate-spin" />
          <Wand2 v-else class="w-3.5 h-3.5" />
          <span>{{ isOptimizingAi ? 'Đang phân tích...' : 'Tối Ưu AI (Gemini 3.8)' }}</span>
        </button>

        <button
          type="button"
          :disabled="isOptimizingAi"
          @click="autoOptimizeSEO"
          class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 disabled:opacity-60 text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-1.5 cursor-pointer"
        >
          <Sparkles class="w-3.5 h-3.5" />
          <span>Tối Ưu 1 Chạm</span>
        </button>
      </div>
    </div>

    <!-- 2b. AI Error Alert -->
    <div
      v-if="aiError"
      class="p-3 rounded-xl border border-rose-500/30 bg-rose-500/10 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs"
    >
      <div class="flex items-start gap-2 text-rose-400">
        <AlertCircle class="w-4 h-4 shrink-0 mt-0.5" />
        <span>{{ aiError }}</span>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <button
          type="button"
          @click="autoOptimizeSEO(); aiError = ''"
          class="px-2.5 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-[11px] cursor-pointer"
        >
          Dùng mẫu cục bộ
        </button>
        <button
          type="button"
          @click="aiError = ''"
          class="p-1 rounded-lg hover:bg-rose-500/20 text-rose-400 cursor-pointer"
          title="Đóng"
        >
          <X class="w-3.5 h-3.5" />
        </button>
      </div>
    </div>

    <!-- 2c. AI Suggestion Preview Panel (Method A - Non-destructive) -->
    <div
      v-if="aiPreview"
      class="p-4 rounded-xl border space-y-3 transition-all"
      :class="isDarkMode ? 'bg-[#1A222C] border-emerald-500/30' : 'bg-emerald-50/60 border-emerald-200'"
    >
      <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2 flex-wrap">
          <Wand2 class="w-4 h-4 text-emerald-500" />
          <span class="text-xs font-extrabold" :class="isDarkMode ? 'text-emerald-400' : 'text-emerald-700'">
            Xem trước đề xuất AI
          </span>
          <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-500 border border-emerald-500/20">
            {{ aiPreview.model }}
          </span>
          <span
            v-if="aiPreview.quota && aiPreview.quota.daily_limit > 0"
            class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-500/15 text-indigo-400 border border-indigo-500/20"
          >
            Hôm nay: {{ aiPreview.quota.used_today }}/{{ aiPreview.quota.daily_limit }} lượt
          </span>
        </div>
        <button
          type="button"
          @click="aiPreview = null"
          class="p-1 rounded-lg hover:bg-slate-500/20 text-slate-400 cursor-pointer"
          title="Đóng xem trước"
        >
          <X class="w-4 h-4" />
        </button>
      </div>

      <div class="grid grid-cols-1 gap-2.5 text-xs">
        <div class="p-2.5 rounded-lg border" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'">
          <div class="text-[10px] font-bold uppercase text-slate-400 mb-0.5">Từ khóa mục tiêu</div>
          <div class="font-bold text-indigo-400">{{ aiPreview.focus_keyword }}</div>
        </div>

        <div class="p-2.5 rounded-lg border" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'">
          <div class="flex items-center justify-between text-[10px] font-bold uppercase text-slate-400 mb-0.5">
            <span>Meta Title</span>
            <span>{{ aiPreview.meta_title.length }} ký tự</span>
          </div>
          <div class="font-semibold">{{ aiPreview.meta_title }}</div>
        </div>

        <div class="p-2.5 rounded-lg border" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'">
          <div class="flex items-center justify-between text-[10px] font-bold uppercase text-slate-400 mb-0.5">
            <span>Meta Description</span>
            <span>{{ aiPreview.meta_description.length }} ký tự</span>
          </div>
          <div class="leading-relaxed">{{ aiPreview.meta_description }}</div>
        </div>

        <div class="p-2.5 rounded-lg border space-y-1.5" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'">
          <div class="text-[10px] font-bold uppercase text-slate-400">Slug URL</div>
          <label
            v-if="(slug || '').trim().length > 3 && (slug || '').trim() !== aiPreview.slug"
            class="flex items-start gap-2 text-[11px] cursor-pointer"
          >
            <input v-model="keepExistingSlug" type="checkbox" class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500" />
            <span>
              Giữ nguyên Slug cũ (<span class="font-mono text-amber-500">/{{ urlPrefix }}/{{ slug }}</span>) để không mất link đã index trên Google
            </span>
          </label>
          <div class="font-mono text-[11px] text-emerald-500">
            {{ (keepExistingSlug && (slug || '').trim().length > 3) ? `Đề xuất mới (chưa áp dụng): /${urlPrefix}/${aiPreview.slug}` : `/${urlPrefix}/${aiPreview.slug}` }}
          </div>
        </div>

        <div
          v-if="aiPreview.image_alt"
          class="p-2.5 rounded-lg border"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <div class="flex items-center justify-between text-[10px] font-bold uppercase text-slate-400 mb-0.5">
            <span>Gợi ý Alt ảnh chuẩn SEO</span>
            <button
              type="button"
              @click="copyImageAlt"
              class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-indigo-500/15 hover:bg-indigo-500/25 text-indigo-400 font-bold cursor-pointer"
            >
              <Copy class="w-3 h-3" />
              <span>{{ copiedAlt ? 'Đã copy' : 'Copy Alt' }}</span>
            </button>
          </div>
          <div class="text-[11px]">{{ aiPreview.image_alt }}</div>
        </div>

        <label
          v-if="aiPreview.intro_html || aiPreview.h2_heading || aiPreview.expansion_html"
          class="flex items-start gap-2.5 p-2.5 rounded-lg border cursor-pointer"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
        >
          <input v-model="applyAiContentBlocks" type="checkbox" class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500" />
          <div class="space-y-1 min-w-0">
            <div class="font-bold text-[11px]">
              Chèn bổ sung đoạn mở đầu, H2 &amp; phần mô tả còn thiếu (giữ nguyên toàn bộ nội dung cũ)
            </div>
            <div v-if="aiPreview.intro_html" class="text-[11px] text-slate-400 line-clamp-2">
              Mở đầu: {{ stripHtmlToText(aiPreview.intro_html) }}
            </div>
            <div v-if="aiPreview.h2_heading" class="text-[11px] text-slate-400 truncate">
              H2: {{ stripHtmlToText(aiPreview.h2_heading) }}
            </div>
            <div v-if="aiPreview.expansion_html" class="text-[11px] text-slate-400 line-clamp-2">
              Bổ sung độ dài: {{ stripHtmlToText(aiPreview.expansion_html) }}
            </div>
          </div>
        </label>
      </div>

      <div class="flex items-center justify-end gap-2 pt-1">
        <button
          type="button"
          @click="aiPreview = null"
          class="px-3 py-1.5 rounded-lg border text-xs font-bold cursor-pointer"
          :class="isDarkMode ? 'border-[#2E3A47] text-slate-300 hover:bg-slate-800' : 'border-slate-300 text-slate-600 hover:bg-slate-100'"
        >
          Hủy
        </button>
        <button
          type="button"
          @click="applyAiPreview"
          class="px-4 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-sm flex items-center gap-1.5 cursor-pointer"
        >
          <Check class="w-3.5 h-3.5" />
          <span>Áp Dụng Đề Xuất</span>
        </button>
      </div>
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
import { ref, computed, watch, onMounted } from 'vue'
import {
  Target, Search, Zap, Sparkles, Eye, CheckCircle2,
  XCircle, AlertCircle, Wand2, Loader2, Check, X, Copy
} from 'lucide-vue-next'
import { useAdminTheme } from '../../composables/useAdminTheme'
import { slugify } from '../../composables/useSEO'
import { api } from '../../services/api'

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
  type: { type: String, default: 'product' }, // 'product', 'post', or 'category'
  defaultExcerpt: { type: String, default: '' }
})

const emit = defineEmits([
  'update:focusKeyword',
  'update:metaTitle',
  'update:metaDescription',
  'update:slug',
  'update:content',
  'optimized'
])

const focusKeyword = ref(props.initialKeyword || '')
const previewDevice = ref('desktop')
const activeTab = ref('basic')
const isOptimizingAi = ref(false)
const aiError = ref('')
const aiPreview = ref(null)
const applyAiContentBlocks = ref(false)
const keepExistingSlug = ref(true)
const copiedAlt = ref(false)

const minWordsTarget = computed(() => {
  if (props.type === 'post') return 500
  if (props.type === 'category') return 120
  return 200
})

// Normalization function for Vietnamese accents & punctuation
function removeAccents(str) {
  if (!str) return ''
  return String(str)
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/đ/g, 'd')
    .replace(/Đ/g, 'd')
    .replace(/[-–—|,.:;!?/\\()"'“”‘’]+/g, ' ')
    .replace(/\s+/g, ' ')
    .trim()
}

function stripHtmlToText(html) {
  if (!html) return ''
  return String(html)
    .replace(/<style[^>]*>[\s\S]*?<\/style>/gi, ' ')
    .replace(/<script[^>]*>[\s\S]*?<\/script>/gi, ' ')
    .replace(/<[^>]+>/g, ' ')
    .replace(/&nbsp;|&#160;/gi, ' ')
    .replace(/&amp;/gi, '&')
    .replace(/&lt;/gi, '<')
    .replace(/&gt;/gi, '>')
    .replace(/&quot;/gi, '"')
    .replace(/&#39;/gi, "'")
    .replace(/\s+/g, ' ')
    .trim()
}

function escapeRegExp(str) {
  return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
}

// Generate suggested keywords from Title (preserving 2-letter Vietnamese words like "In", "Tờ", "Sổ", "Bộ")
const suggestedKeywords = computed(() => {
  const base = props.title || props.metaTitle || ''
  if (!base) return []
  const clean = base.replace(/[-|–—,.:/()]+/g, ' ').replace(/\s+/g, ' ').trim()
  const words = clean.split(/\s+/).filter(w => w.length >= 2)
  const suggestions = []

  // 3-word phrase (e.g., "in decal giấy", "in danh thiếp")
  if (words.length >= 3) {
    suggestions.push(words.slice(0, 3).join(' ').toLowerCase())
  }
  // 2-word phrase (e.g., "in decal", "bao thư")
  if (words.length >= 2) {
    suggestions.push(words.slice(0, 2).join(' ').toLowerCase())
  }
  // 4-word phrase or full short product name
  if (words.length >= 4) {
    suggestions.push(words.slice(0, 4).join(' ').toLowerCase())
  } else if (words.length > 0) {
    suggestions.push(words.join(' ').toLowerCase())
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

// Combined content text (avoid double-counting when defaultExcerpt === content)
const fullContentText = computed(() => {
  const cText = stripHtmlToText(props.content)
  const eText = stripHtmlToText(props.defaultExcerpt)
  if (!cText) return eText
  if (!eText || cText.includes(eText)) return cText
  return `${cText} ${eText}`.trim()
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
  const pattern = new RegExp(`(?:^|\\s)${escapeRegExp(cleanKw)}(?=\\s|$)`, 'gi')
  const matches = cleanContent.match(pattern)
  if (matches && matches.length > 0) return matches.length
  // Fallback substring count
  const parts = cleanContent.split(cleanKw)
  return Math.max(0, parts.length - 1)
})

const keywordDensity = computed(() => {
  if (wordCount.value === 0 || keywordCount.value === 0) return 0
  const kwWords = removeAccents(focusKeyword.value).split(/\s+/).filter(Boolean).length || 1
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
  const cleanSlug = removeAccents((props.slug || '').replace(/-/g, ' '))
  const cleanKw = removeAccents(focusKeyword.value)
  const kwParts = cleanKw.split(/\s+/).filter(Boolean)
  return kwParts.length > 0 && kwParts.every(part => cleanSlug.includes(part))
})

// 4. Keyword in 10% Intro
const testKeywordInIntro = computed(() => {
  if (!focusKeyword.value.trim() || !fullContentText.value) return false
  const introLength = Math.max(180, Math.floor(fullContentText.value.length * 0.2))
  const introText = removeAccents(fullContentText.value.substring(0, introLength))
  return introText.includes(removeAccents(focusKeyword.value))
})

// 5. Content Length
const testContentLength = computed(() => {
  return wordCount.value >= minWordsTarget.value
})

// 6. Keyword in Subheadings (H1-H4, multiline & nested HTML safe)
const testKeywordInSubheadings = computed(() => {
  if (!focusKeyword.value.trim() || !props.content) return false
  const hMatches = props.content.match(/<h[1234][^>]*>[\s\S]*?<\/h[1234]>/gi) || []
  const cleanKw = removeAccents(focusKeyword.value)
  return hMatches.some(h => removeAccents(stripHtmlToText(h)).includes(cleanKw))
})

// 7. Has Image
const testHasImage = computed(() => {
  return !!props.image && props.image.length > 5
})

// 8. Keyword Density in safe range 0.5% - 4.5%
const testKeywordDensity = computed(() => {
  return keywordCount.value >= 1 && keywordDensity.value >= 0.5 && keywordDensity.value <= 4.5
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
  return idx >= 0 && idx <= 20
})

// 11. Title has Number or Year (2026, 4h, 24h, 100%, 500...)
const testTitleHasNumber = computed(() => {
  const target = props.metaTitle || props.title || ''
  return /\d+/.test(target)
})

// 12. Title has Power Words
const POWER_WORDS = [
  'gia re', 'gia goc', 'gia xuong', 'tan xuong', 'lay ngay', 'lay lien', 'chat luong', 'chuyen nghiep',
  'uy tin', 'dep', 'chuan mau', 'mien phi', 'tphcm', '24h', 'nhanh', 'bao gia', 'cao cap', 'theo yeu cau'
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

// Auto Optimize 1-Click (Full 100/100 optimization)
function autoOptimizeSEO() {
  const baseName = (props.title || 'Ấn Phẩm In Ấn').replace(/[-|–—]+/g, ' ').replace(/\s+/g, ' ').trim()
  const kw = (focusKeyword.value.trim() || suggestedKeywords.value[0] || baseName).toLowerCase()
  focusKeyword.value = kw

  // Capitalize keyword for Title
  const titleKw = kw.charAt(0).toUpperCase() + kw.slice(1)

  // Meta Title: guarantee 45 - 64 chars, keyword at start, number (2026), and power word (Giá Rẻ Tận Xưởng)
  let newTitle = `${titleKw} Giá Rẻ Tận Xưởng 2026 | In Nhật Nguyên`
  if (newTitle.length > 65) {
    newTitle = `${titleKw} Giá Xưởng 2026 | In Nhật Nguyên`
  }
  if (newTitle.length > 65) {
    newTitle = `${titleKw.substring(0, 38).trim()} Giá Rẻ 2026 | Nhật Nguyên`
  }
  if (newTitle.length < 40) {
    newTitle = `${titleKw} Giá Rẻ Tận Xưởng TPHCM 2026 | In Nhật Nguyên`
  }

  // Meta Description (target 135 - 155 chars)
  const newDesc = `Xưởng In Nhật Nguyên chuyên ${kw} chất lượng cao, chuẩn màu Offset 98%, lấy nhanh 4h - 24h, giá gốc trực tiếp tại xưởng không qua trung gian.`

  // Slug: keep existing slug if it already contains all keyword parts and <= 75 chars, else generate clean slug
  const cleanCurrentSlug = removeAccents((props.slug || '').replace(/-/g, ' '))
  const kwParts = removeAccents(kw).split(/\s+/).filter(Boolean)
  const slugHasKw = kwParts.length > 0 && kwParts.every(p => cleanCurrentSlug.includes(p))
  const newSlug = (slugHasKw && (props.slug || '').length > 3 && (props.slug || '').length <= 75)
    ? props.slug
    : slugify(kw)

  // Check if content needs SEO structure (intro keyword, H2 with keyword, >= minWordsTarget words, safe density)
  let newContent = props.content || ''
  const currentPlain = stripHtmlToText(newContent)
  const currentWords = currentPlain ? currentPlain.split(/\s+/).filter(Boolean).length : 0
  const hasH2WithKw = (newContent.match(/<h[1234][^>]*>[\s\S]*?<\/h[1234]>/gi) || [])
    .some(h => removeAccents(stripHtmlToText(h)).includes(removeAccents(kw)))
  const introLen = Math.max(180, Math.floor((currentPlain.length || 0) * 0.2))
  const hasIntroKw = removeAccents(currentPlain.substring(0, introLen)).includes(removeAccents(kw))

  if (currentWords < minWordsTarget.value || !hasH2WithKw || !hasIntroKw) {
    const introBlock = !hasIntroKw
      ? `<p>Dịch vụ <strong>${kw}</strong> tại Xưởng In Nhật Nguyên là giải pháp in ấn chuyên nghiệp dành cho doanh nghiệp, cửa hàng và thương hiệu cần ấn phẩm sắc nét, chi phí tối ưu và tiến độ giao hàng nhanh chóng tại TP.HCM cũng như toàn quốc.</p>`
      : ''
    const h2Block = !hasH2WithKw
      ? `<h2>Ưu điểm dịch vụ ${kw} giá gốc tại Xưởng In Nhật Nguyên</h2>`
      : ''
    const detailBlock = `<p>Khi đặt <strong>${kw}</strong> trực tiếp tại xưởng, quý khách được đội ngũ kỹ thuật viên kiểm tra file thiết kế miễn phí, tư vấn chất liệu giấy, kích thước và gia công thành phẩm (cán màng mờ/bóng, ép kim, bế khuôn theo yêu cầu) phù hợp nhất với ngân sách. Hệ thống máy in Offset và kỹ thuật số công nghiệp hiện đại đảm bảo màu sắc đồng đều, sắc nét trên từng thành phẩm.</p>
<h3>Cam kết chất lượng &amp; quy trình đặt in nhanh 24h</h3>
<ul>
  <li><strong>Giá gốc tận xưởng:</strong> Sản xuất khép kín trực tiếp không qua trung gian, chiết khấu hấp dẫn cho đơn hàng số lượng lớn.</li>
  <li><strong>Chuẩn màu &amp; sắc nét:</strong> Công nghệ in hiện đại đạt độ chuẩn màu cao, chữ và chi tiết nhỏ rõ ràng, bền màu theo thời gian.</li>
  <li><strong>Hỗ trợ thiết kế &amp; duyệt mẫu:</strong> Tư vấn quy cách tận tâm, chỉnh sửa file chuẩn in và hỗ trợ đơn hàng cần lấy gấp trong ngày.</li>
  <li><strong>Giao hàng đúng hẹn:</strong> Đóng gói kỹ lưỡng, giao nhanh nội thành TP.HCM và gửi chành xe/chuyển phát toàn quốc.</li>
</ul>
<p>Liên hệ ngay Hotline/Zalo <strong>0368.644.812</strong> để nhận bảng báo giá <strong>${kw}</strong> ưu đãi nhất hôm nay cùng chính sách bảo hành bản in uy tín từ In Nhật Nguyên.</p>`

    newContent = [introBlock, newContent, h2Block, detailBlock].filter(Boolean).join('\n')
    emit('update:content', newContent)
  }

  emit('update:metaTitle', newTitle)
  emit('update:metaDescription', newDesc)
  emit('update:slug', newSlug)
  emit('update:focusKeyword', kw)
  emit('optimized', {
    metaTitle: newTitle,
    metaDescription: newDesc,
    slug: newSlug,
    focusKeyword: kw,
    content: newContent
  })
}

async function optimizeWithAi() {
  const titleText = (props.title || '').trim()
  if (!titleText) {
    aiError.value = 'Vui lòng nhập Tên / Tiêu đề trước khi tối ưu bằng AI.'
    return
  }

  isOptimizingAi.value = true
  aiError.value = ''
  aiPreview.value = null

  try {
    const plainExcerpt = stripHtmlToText(props.content || '').slice(0, 3000)

    const res = await api.optimizeAdminSeo({
      title: titleText,
      type: props.type || 'product',
      focus_keyword: focusKeyword.value.trim(),
      meta_title: props.metaTitle || '',
      meta_description: props.metaDescription || '',
      slug: props.slug || '',
      content: plainExcerpt
    })

    const data = res?.data || {}
    aiPreview.value = {
      model: res?.model || 'gemini-3.8-flash',
      quota: res?.quota || null,
      focus_keyword: data.focus_keyword || focusKeyword.value.trim(),
      meta_title: data.meta_title || '',
      meta_description: data.meta_description || '',
      slug: data.slug || slugify(data.focus_keyword || titleText),
      intro_html: data.intro_html || '',
      h2_heading: data.h2_heading || '',
      expansion_html: data.expansion_html || '',
      image_alt: data.image_alt || ''
    }

    keepExistingSlug.value = (props.slug || '').trim().length > 3
    copiedAlt.value = false
    // Suggest inserting missing SEO blocks if intro, H2, word count, or density needs improvement
    applyAiContentBlocks.value = !testKeywordInIntro.value || !testKeywordInSubheadings.value || !testContentLength.value || !testKeywordDensity.value
  } catch (err) {
    aiError.value = err?.message || 'Không thể kết nối AI Gemini. Bạn có thể dùng mẫu tối ưu cục bộ.'
  } finally {
    isOptimizingAi.value = false
  }
}

async function copyImageAlt() {
  if (!aiPreview.value?.image_alt) return
  try {
    await navigator.clipboard.writeText(aiPreview.value.image_alt)
    copiedAlt.value = true
    setTimeout(() => {
      copiedAlt.value = false
    }, 2000)
  } catch {
    // Ignore clipboard failure
  }
}

function applyAiPreview() {
  if (!aiPreview.value) return

  const suggestion = aiPreview.value
  const kw = suggestion.focus_keyword || focusKeyword.value
  focusKeyword.value = kw
  const finalSlug = (keepExistingSlug.value && (props.slug || '').trim().length > 3)
    ? props.slug.trim()
    : suggestion.slug

  let newContent = props.content || ''
  if (applyAiContentBlocks.value) {
    const introPart = (!testKeywordInIntro.value && suggestion.intro_html) ? suggestion.intro_html : ''
    const h2Part = (!testKeywordInSubheadings.value && suggestion.h2_heading) ? suggestion.h2_heading : ''
    const expansionPart = ((!testContentLength.value || !testKeywordDensity.value) && suggestion.expansion_html)
      ? suggestion.expansion_html
      : ''
    const parts = [introPart, newContent, h2Part, expansionPart].filter(Boolean)
    if (parts.length > 0) {
      newContent = parts.join('\n')
      emit('update:content', newContent)
    }
  }

  emit('update:focusKeyword', kw)
  emit('update:metaTitle', suggestion.meta_title)
  emit('update:metaDescription', suggestion.meta_description)
  emit('update:slug', finalSlug)
  emit('optimized', {
    metaTitle: suggestion.meta_title,
    metaDescription: suggestion.meta_description,
    slug: finalSlug,
    focusKeyword: kw,
    content: newContent
  })

  aiPreview.value = null
}

// Evaluate candidate keyword score when initialKeyword was not previously saved
function scoreCandidateKeyword(kw) {
  if (!kw) return 0
  const cleanKw = removeAccents(kw)
  if (!cleanKw) return 0
  let s = 0
  const titleTarget = removeAccents(props.metaTitle || props.title || '')
  const descTarget = removeAccents(props.metaDescription || props.defaultExcerpt || '')
  const cleanSlug = removeAccents((props.slug || '').replace(/-/g, ' '))
  const kwParts = cleanKw.split(/\s+/).filter(Boolean)

  if (titleTarget.includes(cleanKw)) s += 10
  if (descTarget.includes(cleanKw)) s += 10
  if (kwParts.length > 0 && kwParts.every(p => cleanSlug.includes(p))) s += 10
  const idx = titleTarget.indexOf(cleanKw)
  if (idx >= 0 && idx <= 20) s += 8
  const hMatches = (props.content || '').match(/<h[1234][^>]*>[\s\S]*?<\/h[1234]>/gi) || []
  if (hMatches.some(h => removeAccents(stripHtmlToText(h)).includes(cleanKw))) s += 8
  return s
}

function syncKeywordFromProps() {
  if (props.initialKeyword && props.initialKeyword.trim()) {
    focusKeyword.value = props.initialKeyword.trim()
    return
  }
  if (!focusKeyword.value && (props.title || props.metaTitle)) {
    const candidates = [...suggestedKeywords.value]
    const cleanFull = (props.title || '').replace(/[-|–—,.:/()]+/g, ' ').replace(/\s+/g, ' ').trim().toLowerCase()
    if (cleanFull && !candidates.includes(cleanFull)) {
      candidates.push(cleanFull)
    }
    if (candidates.length > 0) {
      let bestKw = candidates[0]
      let bestScore = -1
      for (const c of candidates) {
        const sc = scoreCandidateKeyword(c)
        if (sc > bestScore) {
          bestScore = sc
          bestKw = c
        }
      }
      focusKeyword.value = bestKw
      emit('update:focusKeyword', bestKw)
    }
  }
}

watch(
  () => props.initialKeyword,
  (newVal) => {
    if (newVal && newVal.trim() && newVal.trim() !== focusKeyword.value) {
      focusKeyword.value = newVal.trim()
    }
  },
  { immediate: true }
)

watch(
  () => [props.title, props.metaTitle, props.slug],
  () => {
    syncKeywordFromProps()
  },
  { immediate: true }
)

onMounted(() => {
  syncKeywordFromProps()
})
</script>

