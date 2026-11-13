<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
          Cài Đặt Hệ Thống & Cấu Hình SEO
        </h1>
        <p class="text-xs mt-1" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
          Cập nhật số hotline xưởng, tài khoản Zalo nhận file in và thẻ meta phục vụ tối ưu hóa tìm kiếm Google.
        </p>
      </div>

      <button
        @click="handleSave"
        :disabled="isSaving"
        class="self-start sm:self-auto px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-gold to-amber-500 hover:from-amber-400 hover:to-brand-gold text-slate-950 font-black text-xs shadow-md shadow-brand-gold/20 flex items-center gap-2 transition-all cursor-pointer"
      >
        <Loader2 v-if="isSaving" class="w-4 h-4 animate-spin" />
        <Save v-else class="w-4 h-4" />
        <span>{{ isSaving ? 'Đang lưu...' : 'Lưu Tất Cả Cài Đặt' }}</span>
      </button>
    </div>

    <!-- Success banner -->
    <div v-if="saveSuccess" class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 text-xs flex items-center gap-2 font-semibold">
      <CheckCircle2 class="w-4 h-4 shrink-0" />
      <span>Đã lưu cấu hình thành công! Mọi thông tin xưởng in và SEO đã được cập nhật.</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 text-xs">
      <!-- Section 1: Contact & Hotline (TailAdmin Card Style) -->
      <div
        class="rounded-2xl border p-6 space-y-4 shadow-sm"
        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <h2 class="text-sm font-bold border-b pb-3 flex items-center gap-2" :class="isDarkMode ? 'text-white border-[#2E3A47]' : 'text-slate-900 border-slate-100'">
          <Phone class="w-4 h-4 text-brand-gold" />
          <span>Thông Tin Liên Hệ & Xưởng In</span>
        </h2>

        <div>
          <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">Tên Thương Hiệu</label>
          <input
            v-model="form.brand_name"
            type="text"
            class="w-full rounded-xl px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-brand-gold"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
          />
        </div>

        <div>
          <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">Slogan Xưởng In</label>
          <input
            v-model="form.slogan"
            type="text"
            class="w-full rounded-xl px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-brand-gold"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
          />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">Hotline 1 (Kinh Doanh)</label>
            <input
              v-model="form.hotline_1"
              type="text"
              class="w-full rounded-xl px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-brand-gold"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            />
          </div>

          <div>
            <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">Hotline 2 (Kỹ Thuật File)</label>
            <input
              v-model="form.hotline_2"
              type="text"
              class="w-full rounded-xl px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-brand-gold"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">Link Zalo Tiếp Nhận</label>
            <input
              v-model="form.zalo"
              type="text"
              placeholder="https://zalo.me/..."
              class="w-full rounded-xl px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-brand-gold"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            />
          </div>

          <div>
            <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">Email Xưởng In</label>
            <input
              v-model="form.email"
              type="email"
              class="w-full rounded-xl px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-brand-gold"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            />
          </div>
        </div>

        <div>
          <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">Địa Chỉ Xưởng Sản Xuất</label>
          <input
            v-model="form.address_factory"
            type="text"
            class="w-full rounded-xl px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-brand-gold"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
          />
        </div>

        <div>
          <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">Địa Chỉ Văn Phòng Giao Dịch</label>
          <input
            v-model="form.address_office"
            type="text"
            class="w-full rounded-xl px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-brand-gold"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
          />
        </div>

        <div>
          <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">Thời Gian Làm Việc</label>
          <input
            v-model="form.working_hours"
            type="text"
            class="w-full rounded-xl px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-brand-gold"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
          />
        </div>
      </div>

      <!-- Section 2: SEO & Meta Config (TailAdmin Card Style) -->
      <div
        class="rounded-2xl border p-6 space-y-4 shadow-sm"
        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <h2 class="text-sm font-bold border-b pb-3 flex items-center gap-2" :class="isDarkMode ? 'text-white border-[#2E3A47]' : 'text-slate-900 border-slate-100'">
          <Globe class="w-4 h-4 text-brand-gold" />
          <span>Cấu Hình Tối Ưu Hóa Tìm Kiếm (SEO)</span>
        </h2>

        <div>
          <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">
            Meta Title Mặc Định (Tiêu đề Google)
          </label>
          <input
            v-model="form.meta_title_default"
            type="text"
            class="w-full rounded-xl px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-brand-gold"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
          />
          <p class="text-[10px] text-slate-400 mt-1">Độ dài lý tưởng: 50 - 65 ký tự (Hiện tại: {{ form.meta_title_default?.length || 0 }} ký tự)</p>
        </div>

        <div>
          <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">
            Meta Description Mặc Định (Trích dẫn Google)
          </label>
          <textarea
            v-model="form.meta_description_default"
            rows="4"
            class="w-full rounded-xl px-3 py-2 border focus:outline-none focus:ring-1 focus:ring-brand-gold"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
          ></textarea>
          <p class="text-[10px] text-slate-400 mt-1">Độ dài lý tưởng: 120 - 160 ký tự (Hiện tại: {{ form.meta_description_default?.length || 0 }} ký tự)</p>
        </div>

        <!-- Google SERP Preview Card -->
        <div class="pt-4 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
          <span class="text-[11px] font-bold uppercase tracking-wider block mb-2" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
            Xem Trước Hiển Thị Google:
          </span>
          <div class="bg-white rounded-xl p-4 text-slate-800 space-y-1 border border-slate-200 shadow-sm">
            <div class="text-[11px] text-emerald-800 truncate">https://dichvuinnhatnguyen.vn</div>
            <div class="text-sm font-semibold text-blue-800 hover:underline cursor-pointer line-clamp-1">
              {{ form.meta_title_default || 'Xưởng In Ấn Nhật Nguyên' }}
            </div>
            <p class="text-xs text-slate-600 line-clamp-2">
              {{ form.meta_description_default || 'Xưởng in offset & kỹ thuật số giá gốc trực tiếp...' }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Section 3: Google Search Console & Webmaster Tools Integration -->
    <div
      class="rounded-2xl border p-6 space-y-5 shadow-sm text-xs"
      :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
    >
      <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b gap-3" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center font-black">
            <Search class="w-4 h-4" />
          </div>
          <div>
            <h2 class="text-sm font-bold" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              Tích Hợp Google Search Console & Google Analytics
            </h2>
            <p class="text-[11px] text-slate-400 mt-0.5">
              Xác minh quyền sở hữu website và quản lý chỉ mục tìm kiếm (Indexing) của Google
            </p>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <a
            href="https://search.google.com/search-console?resource_id=https://dichvuinnhatnguyen.vn/"
            target="_blank"
            class="px-3.5 py-1.5 rounded-xl font-bold bg-blue-600 hover:bg-blue-500 text-white flex items-center gap-1.5 transition-all shadow-sm"
          >
            <span>Mở Google Search Console</span>
            <ExternalLink class="w-3.5 h-3.5" />
          </a>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Verification Code Input -->
        <div class="space-y-4">
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="font-bold flex items-center gap-1.5" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">
                <ShieldCheck class="w-4 h-4 text-emerald-400" />
                <span>Mã Xác Minh Google Search Console (HTML Tag)</span>
              </label>
              <span
                v-if="form.google_site_verification"
                class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"
              >
                Đã Cấu Hình
              </span>
              <span
                v-else
                class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20"
              >
                Chưa Xác Minh
              </span>
            </div>

            <div class="flex items-center gap-2">
              <input
                v-model="form.google_site_verification"
                @blur="cleanVerificationCode"
                @keyup.enter="handleSave"
                type="text"
                placeholder="Dán mã content hoặc cả thẻ <meta name='google-site-verification' content='...' />"
                class="flex-1 rounded-xl px-3 py-2.5 border font-mono text-[11px] focus:outline-none focus:ring-1 focus:ring-brand-gold"
                :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
              />
              <button
                type="button"
                @click="handleSave"
                :disabled="isSaving"
                class="px-4 py-2.5 rounded-xl bg-brand-gold hover:bg-amber-400 text-slate-950 font-black text-xs shrink-0 flex items-center gap-1.5 shadow-sm transition-all cursor-pointer disabled:opacity-50"
              >
                <Loader2 v-if="isSaving" class="w-3.5 h-3.5 animate-spin" />
                <Save v-else class="w-3.5 h-3.5" />
                <span>{{ isSaving ? 'Đang lưu...' : 'Lưu Mã' }}</span>
              </button>
            </div>
            <p class="text-[10px] text-slate-400 mt-1">
              Bạn có thể dán trực tiếp chuỗi mã hoặc dán nguyên thẻ meta HTML từ Google, hệ thống sẽ tự động lọc chuẩn.
            </p>
            <div v-if="form.google_site_verification" class="mt-2 p-2 rounded-lg bg-black/20 border border-emerald-500/30 text-[11px] font-mono text-emerald-400 select-all flex items-center justify-between">
              <span>&lt;meta name="google-site-verification" content="{{ form.google_site_verification }}" /&gt;</span>
              <span class="text-[10px] text-emerald-500 font-bold uppercase ml-2 shrink-0">Sẵn sàng xác minh</span>
            </div>
          </div>

          <div>
            <label class="block font-bold mb-1.5 flex items-center gap-1.5" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">
              <BarChart3 class="w-4 h-4 text-brand-gold" />
              <span>Google Analytics 4 (GA4 Measurement ID)</span>
            </label>
            <div class="flex items-center gap-2">
              <input
                v-model="form.google_analytics_id"
                @keyup.enter="handleSave"
                type="text"
                placeholder="VD: G-XXXXXXXXXX"
                class="flex-1 rounded-xl px-3 py-2.5 border font-mono text-xs focus:outline-none focus:ring-1 focus:ring-brand-gold"
                :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
              />
              <button
                type="button"
                @click="handleSave"
                :disabled="isSaving"
                class="px-4 py-2.5 rounded-xl border font-bold text-xs shrink-0 flex items-center gap-1.5 transition-all cursor-pointer disabled:opacity-50"
                :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] hover:border-brand-gold text-slate-200' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-300'"
              >
                <span>Lưu GA4</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Quick SEO Verification & Sitemap Tools -->
        <div class="p-4 rounded-xl border space-y-3" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'">
          <div class="font-bold text-brand-gold uppercase tracking-wider text-[11px] flex items-center gap-1.5">
            <FileCode class="w-4 h-4" />
            <span>Tệp Kiểm Tra Chỉ Mục Tự Động (Sitemap & Robots)</span>
          </div>

          <div class="space-y-2 text-xs">
            <div class="flex items-center justify-between p-2.5 rounded-lg border bg-black/10" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
              <div>
                <div class="font-bold">Sitemap XML (Sơ đồ website):</div>
                <div class="text-[11px] text-slate-400 font-mono">https://dichvuinnhatnguyen.vn/sitemap.xml</div>
              </div>
              <a
                href="/sitemap.xml"
                target="_blank"
                class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-brand-gold text-slate-950 hover:bg-amber-400 transition-all flex items-center gap-1"
              >
                <span>Xem XML</span>
                <ExternalLink class="w-3 h-3" />
              </a>
            </div>

            <div class="flex items-center justify-between p-2.5 rounded-lg border bg-black/10" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
              <div>
                <div class="font-bold">Robots.txt (Quy tắc thu thập Googlebot):</div>
                <div class="text-[11px] text-slate-400 font-mono">https://dichvuinnhatnguyen.vn/robots.txt</div>
              </div>
              <a
                href="/robots.txt"
                target="_blank"
                class="px-2.5 py-1 rounded-md text-[11px] font-bold border transition-all flex items-center gap-1"
                :class="isDarkMode ? 'border-[#2E3A47] text-slate-200 hover:text-white' : 'border-slate-300 text-slate-700'"
              >
                <span>Xem Tệp</span>
                <ExternalLink class="w-3 h-3" />
              </a>
            </div>
          </div>

          <!-- Guide Steps -->
          <div class="p-3 rounded-lg bg-blue-500/10 border border-blue-500/20 text-[11px] space-y-1 text-slate-300">
            <div class="font-bold text-blue-400">3 Bước kết nối Google Search Console:</div>
            <div>1. Mở Search Console, chọn thêm tài sản bằng <strong>Tiền tố URL</strong> (<code class="text-brand-gold">https://dichvuinnhatnguyen.vn</code>).</div>
            <div>2. Chọn phương thức <strong>Thẻ HTML (HTML Tag)</strong>, dán mã vào ô bên trái rồi bấm <strong>Lưu Tất Cả Cài Đặt</strong>.</div>
            <div>3. Quay lại Search Console bấm <strong>XÁC MINH</strong>, sau đó vào mục <strong>Sơ đồ trang web (Sitemaps)</strong> gửi link <code class="text-emerald-400">sitemap.xml</code>.</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Section 4: Google Indexing API (Tự Động Bắn Index 100%) -->
    <div
      class="rounded-2xl border p-6 space-y-5 shadow-sm text-xs"
      :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
    >
      <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b gap-3" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center font-black">
            <Zap class="w-4 h-4" />
          </div>
          <div>
            <div class="flex items-center gap-2">
              <h2 class="text-sm font-bold" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                Google Indexing API (Tự Động Báo Index Tức Thì 100%)
              </h2>
              <span
                v-if="indexingConfig.is_configured"
                class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"
              >
                Đã Sẵn Sàng
              </span>
              <span
                v-else
                class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20"
              >
                Chưa Cấu Hình Service Account
              </span>
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5">
              Hệ thống sẽ âm thầm gọi máy chủ Google để ép index URL ngay khi anh Lưu sản phẩm hoặc Đăng bài viết
            </p>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <button
            type="button"
            @click="testIndexingConnection"
            :disabled="isTestingIndexing || !indexingJsonInput"
            class="px-3.5 py-1.5 rounded-xl font-bold border flex items-center gap-1.5 transition-all cursor-pointer disabled:opacity-40"
            :class="isDarkMode ? 'border-[#2E3A47] bg-[#1C2434] text-slate-200 hover:text-white' : 'border-slate-300 bg-slate-100 text-slate-700 hover:bg-slate-200'"
          >
            <Loader2 v-if="isTestingIndexing" class="w-3.5 h-3.5 animate-spin" />
            <Sparkles v-else class="w-3.5 h-3.5 text-brand-gold" />
            <span>Kiểm Tra Kết Nối Google</span>
          </button>
        </div>
      </div>

      <!-- Service Account Email Notice (If Configured) -->
      <div
        v-if="indexingConfig.client_email"
        class="p-4 rounded-xl border flex flex-col md:flex-row md:items-center justify-between gap-3 bg-emerald-500/5 border-emerald-500/20"
      >
        <div class="space-y-1">
          <div class="text-[11px] font-bold text-emerald-400 flex items-center gap-1.5">
            <CheckCircle2 class="w-4 h-4" />
            <span>Email Service Account Đã Nhận Diện:</span>
          </div>
          <div class="font-mono text-xs text-white font-bold select-all bg-black/20 px-2 py-1 rounded inline-block">
            {{ indexingConfig.client_email }}
          </div>
          <p class="text-[10px] text-slate-400">
            👉 <strong>Quan trọng:</strong> Hãy thêm email này làm <strong>Chủ sở hữu (Owner)</strong> trong mục <em>Cài đặt → Người dùng và quyền</em> của Google Search Console.
          </p>
        </div>

        <button
          type="button"
          @click="copyToClipboard(indexingConfig.client_email, 'Đã copy email Service Account!')"
          class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 transition-colors shrink-0"
        >
          Sao chép Email
        </button>
      </div>

      <!-- Main Config Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: JSON Key Input -->
        <div class="lg:col-span-7 space-y-4">
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="font-bold flex items-center gap-1.5" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">
                <KeyRound class="w-4 h-4 text-brand-gold" />
                <span>Nội Dung Khóa Service Account (JSON Key)</span>
              </label>

              <label class="text-[11px] text-brand-gold hover:underline cursor-pointer flex items-center gap-1 font-bold">
                <UploadCloud class="w-3.5 h-3.5" />
                <span>Chọn File .json từ máy tính</span>
                <input type="file" accept=".json,application/json" @change="handleFileUpload" class="hidden" />
              </label>
            </div>

            <textarea
              v-model="indexingJsonInput"
              rows="6"
              placeholder='Dán toàn bộ nội dung file Service Account JSON (bắt đầu bằng { "type": "service_account", ... })'
              class="w-full rounded-xl p-3 border font-mono text-[11px] leading-relaxed focus:outline-none focus:ring-1 focus:ring-brand-gold"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-800'"
            ></textarea>
          </div>

          <div class="flex items-center gap-2">
            <input
              id="autoPublishToggle"
              v-model="indexingConfig.auto_publish"
              type="checkbox"
              class="w-4 h-4 accent-amber-500 rounded cursor-pointer"
            />
            <label for="autoPublishToggle" class="cursor-pointer font-bold select-none" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
              Tự động gửi Index tới Google mỗi khi Lưu Sản Phẩm & Đăng Bài Viết
            </label>
          </div>

          <div class="flex items-center gap-3">
            <button
              type="button"
              @click="saveIndexingConfig"
              :disabled="isSavingIndexing"
              class="px-4 py-2 rounded-xl text-xs font-bold bg-brand-gold hover:bg-amber-400 text-slate-950 flex items-center gap-2 shadow-sm transition-all cursor-pointer disabled:opacity-40"
            >
              <Loader2 v-if="isSavingIndexing" class="w-3.5 h-3.5 animate-spin" />
              <Save v-else class="w-3.5 h-3.5" />
              <span>Lưu Cấu Hình Google Indexing API</span>
            </button>

            <span v-if="indexingSaveMsg" class="text-xs font-bold text-emerald-400 flex items-center gap-1">
              <CheckCircle2 class="w-3.5 h-3.5" />
              <span>{{ indexingSaveMsg }}</span>
            </span>
          </div>
        </div>

        <!-- Right: Manual Direct Ping & Guide -->
        <div class="lg:col-span-5 space-y-4">
          <!-- Manual URL Pinger -->
          <div class="p-4 rounded-xl border space-y-3" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'">
            <div class="font-bold text-brand-gold text-[11px] uppercase tracking-wider flex items-center gap-1.5">
              <Send class="w-4 h-4" />
              <span>Bắn Thử Index Ngay Cho 1 Đường Dẫn</span>
            </div>

            <div class="space-y-2">
              <input
                v-model="manualPingUrl"
                type="url"
                placeholder="https://dichvuinnhatnguyen.vn/san-pham/..."
                class="w-full rounded-xl px-3 py-2 border font-mono text-xs focus:outline-none focus:ring-1 focus:ring-brand-gold"
                :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
              />

              <div class="flex items-center gap-2">
                <select
                  v-model="manualPingType"
                  class="rounded-xl px-2.5 py-1.5 border text-xs font-bold focus:outline-none"
                  :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
                >
                  <option value="URL_UPDATED">Cập nhật / Thêm mới (URL_UPDATED)</option>
                  <option value="URL_DELETED">Xóa link khỏi Google (URL_DELETED)</option>
                </select>

                <button
                  type="button"
                  @click="sendManualPing"
                  :disabled="isPingingUrl || !manualPingUrl"
                  class="flex-1 py-1.5 px-3 rounded-xl font-bold bg-blue-600 hover:bg-blue-500 text-white flex items-center justify-center gap-1.5 transition-all cursor-pointer disabled:opacity-40"
                >
                  <Loader2 v-if="isPingingUrl" class="w-3.5 h-3.5 animate-spin" />
                  <Send v-else class="w-3.5 h-3.5" />
                  <span>Bắn Ngay</span>
                </button>
              </div>

              <!-- Response message -->
              <div v-if="manualPingResult" class="p-2.5 rounded-lg text-[11px] leading-relaxed border" :class="manualPingResult.success ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-300' : 'bg-red-500/10 border-red-500/20 text-red-300'">
                <div class="font-bold">{{ manualPingResult.message }}</div>
                <div v-if="manualPingResult.notifyTime" class="text-[10px] text-slate-400 mt-0.5">
                  Thời gian ghi nhận: {{ manualPingResult.notifyTime }}
                </div>
              </div>
            </div>
          </div>

          <!-- Guide -->
          <div class="p-3.5 rounded-xl border bg-black/15 text-[11px] space-y-1.5 text-slate-300" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
            <div class="font-bold text-amber-400 flex items-center gap-1">
              <Sparkles class="w-3.5 h-3.5" />
              <span>3 Bước Lấy Service Account JSON (Miễn phí 100%):</span>
            </div>
            <ol class="list-decimal list-inside space-y-1 text-slate-400">
              <li>Vào <a href="https://console.cloud.google.com/" target="_blank" class="text-blue-400 underline">Google Cloud Console</a>, tạo Dự án mới và bật API tên là <strong>"Web Search Indexing API"</strong>.</li>
              <li>Vào mục <strong>IAM & Admin → Service Accounts</strong>, tạo một Service Account và tạo 1 Khóa (Key dạng JSON) tải về máy.</li>
              <li>Dán nội dung JSON vào khung bên trái, rồi copy email cấp quyền Owner trong Google Search Console là xong!</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Sticky bottom Save bar -->
    <div class="sticky bottom-4 z-20 p-3.5 rounded-2xl border shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 backdrop-blur-md" :class="isDarkMode ? 'bg-[#1C2434]/95 border-[#2E3A47]' : 'bg-white/95 border-slate-200'">
      <div class="flex items-center gap-2 text-xs">
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
        <span :class="isDarkMode ? 'text-slate-300' : 'text-slate-600'">
          Bấm lưu để cập nhật thông tin hệ thống, database và mã xác minh vào tệp mã nguồn HTML.
        </span>
      </div>

      <button
        type="button"
        @click="handleSave"
        :disabled="isSaving"
        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-brand-gold to-amber-500 hover:from-amber-400 hover:to-brand-gold text-slate-950 font-black text-xs shadow-md shadow-brand-gold/20 flex items-center justify-center gap-2 transition-all cursor-pointer disabled:opacity-50"
      >
        <Loader2 v-if="isSaving" class="w-4 h-4 animate-spin" />
        <Save v-else class="w-4 h-4" />
        <span>{{ isSaving ? 'Đang lưu...' : 'Lưu Tất Cả Cài Đặt' }}</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import {
  Phone,
  Globe,
  Save,
  CheckCircle2,
  Loader2,
  Search,
  ExternalLink,
  ShieldCheck,
  BarChart3,
  FileCode,
  Zap,
  Sparkles,
  KeyRound,
  UploadCloud,
  Send
} from 'lucide-vue-next'
import { api } from '../../services/api'
import { useAdminTheme } from '../../composables/useAdminTheme'
import { swalToast, swalSuccess, swalError, swalWarning } from '../../utils/swal'

const { isDarkMode } = useAdminTheme()

const form = ref({
  brand_name: '',
  slogan: '',
  hotline_1: '',
  hotline_2: '',
  zalo: '',
  email: '',
  address_factory: '',
  address_office: '',
  working_hours: '',
  meta_title_default: '',
  meta_description_default: '',
  google_site_verification: '',
  google_analytics_id: ''
})

const isLoading = ref(false)
const isSaving = ref(false)
const saveSuccess = ref(false)

// Google Indexing API State
const indexingConfig = ref({
  is_configured: false,
  client_email: '',
  project_id: '',
  auto_publish: true
})
const indexingJsonInput = ref('')
const isSavingIndexing = ref(false)
const indexingSaveMsg = ref('')
const isTestingIndexing = ref(false)
const manualPingUrl = ref('https://dichvuinnhatnguyen.vn/')
const manualPingType = ref('URL_UPDATED')
const isPingingUrl = ref(false)
const manualPingResult = ref(null)

function copyToClipboard(text, alertMsg = 'Đã sao chép vào bộ nhớ tạm!') {
  if (!text) return
  navigator.clipboard.writeText(text).then(() => {
    swalToast(alertMsg)
  }).catch(() => {
    prompt('Hãy copy nội dung sau:', text)
  })
}

function handleFileUpload(event) {
  const file = event.target.files?.[0]
  if (!file) return
  const reader = new FileReader()
  reader.onload = (e) => {
    indexingJsonInput.value = e.target.result
    try {
      const parsed = JSON.parse(e.target.result)
      if (parsed.client_email) {
        indexingConfig.value.client_email = parsed.client_email
      }
    } catch (err) {}
  }
  reader.readAsText(file)
}

async function loadIndexingConfig() {
  try {
    const res = await api.getGoogleIndexingConfig()
    if (res?.data) {
      indexingConfig.value = res.data
    }
  } catch (err) {
    console.warn('Cannot load Google Indexing config', err)
  }
}

async function saveIndexingConfig() {
  isSavingIndexing.value = true
  indexingSaveMsg.value = ''
  try {
    const payload = {
      auto_publish: indexingConfig.value.auto_publish
    }
    if (indexingJsonInput.value.trim()) {
      payload.service_account_json = indexingJsonInput.value.trim()
    }
    const res = await api.saveGoogleIndexingConfig(payload)
    if (res?.data) {
      indexingConfig.value = res.data
      indexingSaveMsg.value = 'Đã lưu cấu hình API thành công!'
      setTimeout(() => {
        indexingSaveMsg.value = ''
      }, 3500)
    }
  } catch (err) {
    swalError('Lỗi lưu cấu hình', err.response?.data?.message || err.message)
  } finally {
    isSavingIndexing.value = false
  }
}

async function testIndexingConnection() {
  isTestingIndexing.value = true
  try {
    // If user edited JSON in textarea, save it first
    if (indexingJsonInput.value.trim()) {
      await api.saveGoogleIndexingConfig({
        service_account_json: indexingJsonInput.value.trim(),
        auto_publish: indexingConfig.value.auto_publish
      })
    }
    const res = await api.testGoogleIndexingConnection()
    swalSuccess('Kết nối thành công!', res.message || 'Kết nối Google Indexing API thành công!')
    await loadIndexingConfig()
  } catch (err) {
    swalError('Kiểm tra thất bại', err.response?.data?.message || err.message || 'Không thể kết nối Google API')
  } finally {
    isTestingIndexing.value = false
  }
}

async function sendManualPing() {
  if (!manualPingUrl.value.trim()) {
    swalWarning('Thiếu URL', 'Vui lòng nhập đường dẫn URL cần bắn Index!')
    return
  }
  isPingingUrl.value = true
  manualPingResult.value = null
  try {
    const res = await api.publishGoogleIndexingUrl(manualPingUrl.value.trim(), manualPingType.value)
    manualPingResult.value = {
      success: true,
      message: res.message || 'Đã gửi yêu cầu Index tới Google thành công!',
      notifyTime: res.notifyTime
    }
  } catch (err) {
    manualPingResult.value = {
      success: false,
      message: err.response?.data?.message || err.message || 'Lỗi khi gửi yêu cầu'
    }
  } finally {
    isPingingUrl.value = false
  }
}

function cleanVerificationCode() {
  let val = form.value.google_site_verification?.trim() || ''
  if (!val) return

  // If user pasted full meta tag: <meta name="google-site-verification" content="..." />
  const match = val.match(/content=["']([^"']+)["']/)
  if (match && match[1]) {
    form.value.google_site_verification = match[1]
  }
}

async function loadSettings() {
  isLoading.value = true
  try {
    const res = await api.getSettings()
    if (res.data) {
      Object.assign(form.value, res.data)
      if (!form.value.google_site_verification) {
        const existingMeta = document.querySelector('meta[name="google-site-verification"]')
        if (existingMeta && existingMeta.getAttribute('content')) {
          form.value.google_site_verification = existingMeta.getAttribute('content')
        }
      }
      applyGoogleTags(form.value)
    }
  } catch (err) {
    console.error('Failed to load settings', err)
  } finally {
    isLoading.value = false
  }
}

function applyGoogleTags(data) {
  if (data.google_site_verification) {
    let meta = document.querySelector('meta[name="google-site-verification"]')
    if (!meta) {
      meta = document.createElement('meta')
      meta.setAttribute('name', 'google-site-verification')
      document.head.appendChild(meta)
    }
    meta.setAttribute('content', data.google_site_verification)
  }
}

async function handleSave() {
  cleanVerificationCode()
  isSaving.value = true
  saveSuccess.value = false
  try {
    const res = await api.updateAdminSettings(form.value)
    if (res?.data) {
      Object.assign(form.value, res.data)
    }
    applyGoogleTags(form.value)
    saveSuccess.value = true
    swalSuccess('Đã lưu cài đặt thành công!', 'Mã xác minh Google Search Console và GA4 đã được lưu vĩnh viễn.')
    setTimeout(() => {
      saveSuccess.value = false
    }, 4000)
  } catch (err) {
    swalError('Lỗi lưu cài đặt', err.message || 'Không thể lưu')
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  loadSettings()
  loadIndexingConfig()
})
</script>
