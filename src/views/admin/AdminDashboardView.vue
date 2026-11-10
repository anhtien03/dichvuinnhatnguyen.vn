<template>
  <div class="space-y-6">
    <!-- Header Greeting & Quick Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
          Bảng Điều Khiển Xưởng In
        </h1>
        <p class="text-xs mt-1" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
          {{ can('quotes.view') || can('orders.view') 
            ? 'Tổng hợp yêu cầu báo giá thực tế, tiến độ sản xuất và hiệu suất ấn phẩm In Ấn Nhật Nguyên.' 
            : 'Tổng quan bài viết, cẩm nang in ấn, banner quảng cáo và cấu trúc sản phẩm website.' }}
        </p>
      </div>

      <div class="flex items-center gap-2.5">
        <!-- Action for Quotes / Sales -->
        <router-link
          v-if="can('quotes.view')"
          to="/admin/quotes"
          class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-gold to-amber-500 hover:from-amber-400 hover:to-brand-gold text-slate-950 font-black text-xs shadow-md shadow-brand-gold/20 flex items-center gap-2 transition-all cursor-pointer"
        >
          <ClipboardList class="w-4 h-4" />
          <span>Xử Lý Đơn Báo Giá</span>
        </router-link>

        <!-- Action for Content Marketing -->
        <router-link
          v-else-if="can('posts.manage')"
          to="/admin/posts/create"
          class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-black text-xs shadow-md shadow-purple-600/20 flex items-center gap-2 transition-all cursor-pointer"
        >
          <Plus class="w-4 h-4" />
          <span>Đăng Bài Viết Mới</span>
        </router-link>

        <button
          @click="loadData"
          class="p-2.5 rounded-xl border transition-colors cursor-pointer"
          :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-300 hover:text-white' : 'bg-white border-slate-200 text-slate-600 hover:text-slate-900 shadow-sm'"
          title="Làm mới dữ liệu"
        >
          <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': isLoading }" />
        </button>
      </div>
    </div>

    <!-- =================================================================== -->
    <!-- CASE A: KPI METRICS CHO KINH DOANH & BAN QUẢN TRỊ (QUOTES / ORDERS) -->
    <!-- =================================================================== -->
    <div v-if="can('quotes.view') || can('orders.view')" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6">
      <!-- Card 1: Chờ Báo Giá -->
      <div
        class="rounded-2xl border p-5 sm:p-6 transition-all shadow-sm"
        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <div class="flex items-center justify-between">
          <div class="w-12 h-12 rounded-full flex items-center justify-center bg-amber-500/10 text-amber-500">
            <Clock class="w-6 h-6" />
          </div>
          <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-500 bg-amber-500/10 px-2 py-0.5 rounded-full">
            <AlertCircle class="w-3 h-3" />
            Cần xử lý
          </span>
        </div>

        <div class="mt-4 flex items-end justify-between">
          <div>
            <h4 class="text-2xl sm:text-3xl font-black" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              {{ stats?.quotes?.pending ?? 0 }}
            </h4>
            <span class="text-xs font-medium" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
              Đơn Chờ Báo Giá
            </span>
          </div>
          <span class="flex items-center gap-1 text-xs font-bold text-emerald-500">
            <ArrowUp class="w-3.5 h-3.5" />
            <span>+14.2%</span>
          </span>
        </div>
      </div>

      <!-- Card 2: Tổng Đơn Yêu Cầu -->
      <div
        class="rounded-2xl border p-5 sm:p-6 transition-all shadow-sm"
        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <div class="flex items-center justify-between">
          <div class="w-12 h-12 rounded-full flex items-center justify-center bg-blue-500/10 text-blue-500">
            <ClipboardCheck class="w-6 h-6" />
          </div>
          <span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-500 bg-blue-500/10 px-2 py-0.5 rounded-full">
            Toàn thời gian
          </span>
        </div>

        <div class="mt-4 flex items-end justify-between">
          <div>
            <h4 class="text-2xl sm:text-3xl font-black" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              {{ stats?.quotes?.total ?? 0 }}
            </h4>
            <span class="text-xs font-medium" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
              Tổng Lượt Gửi Yêu Cầu
            </span>
          </div>
          <span class="flex items-center gap-1 text-xs font-bold text-emerald-500">
            <ArrowUp class="w-3.5 h-3.5" />
            <span>+8.4%</span>
          </span>
        </div>
      </div>

      <!-- Card 3: Đơn Đã Chốt & Đang In -->
      <div
        class="rounded-2xl border p-5 sm:p-6 transition-all shadow-sm"
        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <div class="flex items-center justify-between">
          <div class="w-12 h-12 rounded-full flex items-center justify-center bg-emerald-500/10 text-emerald-500">
            <Printer class="w-6 h-6" />
          </div>
          <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded-full">
            Sản xuất
          </span>
        </div>

        <div class="mt-4 flex items-end justify-between">
          <div>
            <h4 class="text-2xl sm:text-3xl font-black" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              {{ stats?.quotes?.approved ?? 0 }}
            </h4>
            <span class="text-xs font-medium" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
              Đã Chốt Sản Xuất
            </span>
          </div>
          <span class="flex items-center gap-1 text-xs font-bold text-emerald-500">
            <ArrowUp class="w-3.5 h-3.5" />
            <span>+19.8%</span>
          </span>
        </div>
      </div>

      <!-- Card 4: Tổng Ấn Phẩm Đang Kinh Doanh -->
      <div
        class="rounded-2xl border p-5 sm:p-6 transition-all shadow-sm"
        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <div class="flex items-center justify-between">
          <div class="w-12 h-12 rounded-full flex items-center justify-center bg-purple-500/10 text-purple-500">
            <PackageCheck class="w-6 h-6" />
          </div>
          <span class="inline-flex items-center gap-1 text-[11px] font-bold text-purple-500 bg-purple-500/10 px-2 py-0.5 rounded-full">
            Catalog
          </span>
        </div>

        <div class="mt-4 flex items-end justify-between">
          <div>
            <h4 class="text-2xl sm:text-3xl font-black" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              {{ stats?.products?.active ?? 0 }}
              <span class="text-xs font-normal text-slate-400">/ {{ stats?.products?.total ?? 0 }}</span>
            </h4>
            <span class="text-xs font-medium" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
              Ấn Phẩm Hoạt Động
            </span>
          </div>
          <span class="text-xs font-bold text-brand-gold">
            {{ stats?.categories?.total ?? 6 }} nhóm
          </span>
        </div>
      </div>
    </div>

    <!-- =================================================================== -->
    <!-- CASE B: KPI METRICS CHO BIÊN TẬP (CONTENT TRƯỞNG PHÒNG)             -->
    <!-- =================================================================== -->
    <div v-else-if="can('products.manage')" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6">
      <!-- Card 1: Bài Viết & Cẩm Nang -->
      <div
        class="rounded-2xl border p-5 sm:p-6 transition-all shadow-sm"
        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <div class="flex items-center justify-between">
          <div class="w-12 h-12 rounded-full flex items-center justify-center bg-purple-500/10 text-purple-500">
            <Newspaper class="w-6 h-6" />
          </div>
          <span class="inline-flex items-center gap-1 text-[11px] font-bold text-purple-500 bg-purple-500/10 px-2 py-0.5 rounded-full">
            Tin Tức & Cẩm Nang
          </span>
        </div>

        <div class="mt-4 flex items-end justify-between">
          <div>
            <h4 class="text-2xl sm:text-3xl font-black" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              {{ stats?.posts?.total ?? 0 }}
            </h4>
            <span class="text-xs font-medium" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
              Bài Viết & Cẩm Nang
            </span>
          </div>
          <span class="text-xs font-bold text-emerald-500">
            {{ stats?.posts?.published ?? 0 }} đã xuất bản
          </span>
        </div>
      </div>

      <!-- Card 2: Banner & Slider -->
      <div
        class="rounded-2xl border p-5 sm:p-6 transition-all shadow-sm"
        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <div class="flex items-center justify-between">
          <div class="w-12 h-12 rounded-full flex items-center justify-center bg-blue-500/10 text-blue-500">
            <ImageIcon class="w-6 h-6" />
          </div>
          <span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-500 bg-blue-500/10 px-2 py-0.5 rounded-full">
            Quảng Cáo
          </span>
        </div>

        <div class="mt-4 flex items-end justify-between">
          <div>
            <h4 class="text-2xl sm:text-3xl font-black" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              {{ stats?.banners?.active ?? 0 }}
              <span class="text-xs font-normal text-slate-400">/ {{ stats?.banners?.total ?? 0 }}</span>
            </h4>
            <span class="text-xs font-medium" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
              Banner & Slider Hoạt Động
            </span>
          </div>
          <span class="text-xs font-bold text-blue-500">
            Trang chủ
          </span>
        </div>
      </div>

      <!-- Card 3: Danh Mục Nhóm In -->
      <div
        class="rounded-2xl border p-5 sm:p-6 transition-all shadow-sm"
        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <div class="flex items-center justify-between">
          <div class="w-12 h-12 rounded-full flex items-center justify-center bg-amber-500/10 text-amber-500">
            <Tags class="w-6 h-6" />
          </div>
          <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-500 bg-amber-500/10 px-2 py-0.5 rounded-full">
            Cấu Trúc Web
          </span>
        </div>

        <div class="mt-4 flex items-end justify-between">
          <div>
            <h4 class="text-2xl sm:text-3xl font-black" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              {{ stats?.categories?.total ?? 0 }}
            </h4>
            <span class="text-xs font-medium" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
              Danh Mục Nhóm In
            </span>
          </div>
          <span class="text-xs font-bold text-amber-500">
            Chuẩn SEO
          </span>
        </div>
      </div>

      <!-- Card 4: Tổng Ấn Phẩm -->
      <div
        class="rounded-2xl border p-5 sm:p-6 transition-all shadow-sm"
        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <div class="flex items-center justify-between">
          <div class="w-12 h-12 rounded-full flex items-center justify-center bg-emerald-500/10 text-emerald-500">
            <PackageCheck class="w-6 h-6" />
          </div>
          <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded-full">
            Catalog
          </span>
        </div>

        <div class="mt-4 flex items-end justify-between">
          <div>
            <h4 class="text-2xl sm:text-3xl font-black" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              {{ stats?.products?.active ?? 0 }}
              <span class="text-xs font-normal text-slate-400">/ {{ stats?.products?.total ?? 0 }}</span>
            </h4>
            <span class="text-xs font-medium" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
              Ấn Phẩm Hoạt Động
            </span>
          </div>
          <span class="text-xs font-bold text-brand-gold">
            Đang hiển thị
          </span>
        </div>
      </div>
    </div>

    <!-- =================================================================== -->
    <!-- CASE C: KPI METRICS CHO CỘNG TÁC VIÊN VIẾT BÀI (WRITER ROLE)        -->
    <!-- =================================================================== -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6">
      <!-- Card 1: Tổng Số Bài Viết -->
      <div
        class="rounded-2xl border p-5 sm:p-6 transition-all shadow-sm"
        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <div class="flex items-center justify-between">
          <div class="w-12 h-12 rounded-full flex items-center justify-center bg-purple-500/10 text-purple-500">
            <Newspaper class="w-6 h-6" />
          </div>
          <span class="inline-flex items-center gap-1 text-[11px] font-bold text-purple-500 bg-purple-500/10 px-2 py-0.5 rounded-full">
            Biên Tập
          </span>
        </div>

        <div class="mt-4 flex items-end justify-between">
          <div>
            <h4 class="text-2xl sm:text-3xl font-black" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              {{ stats?.posts?.total ?? 0 }}
            </h4>
            <span class="text-xs font-medium" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
              Tổng Bài Viết Hệ Thống
            </span>
          </div>
          <span class="text-xs font-bold text-purple-500">
            Cẩm nang in
          </span>
        </div>
      </div>

      <!-- Card 2: Đã Xuất Bản -->
      <div
        class="rounded-2xl border p-5 sm:p-6 transition-all shadow-sm"
        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <div class="flex items-center justify-between">
          <div class="w-12 h-12 rounded-full flex items-center justify-center bg-emerald-500/10 text-emerald-500">
            <CheckCircle class="w-6 h-6" />
          </div>
          <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded-full">
            Đang Hoạt Động
          </span>
        </div>

        <div class="mt-4 flex items-end justify-between">
          <div>
            <h4 class="text-2xl sm:text-3xl font-black" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              {{ stats?.posts?.published ?? 0 }}
            </h4>
            <span class="text-xs font-medium" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
              Bài Đã Xuất Bản Live
            </span>
          </div>
          <span class="text-xs font-bold text-emerald-500">
            Top Google SEO
          </span>
        </div>
      </div>

      <!-- Card 3: Bản Nháp -->
      <div
        class="rounded-2xl border p-5 sm:p-6 transition-all shadow-sm"
        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <div class="flex items-center justify-between">
          <div class="w-12 h-12 rounded-full flex items-center justify-center bg-amber-500/10 text-amber-500">
            <Clock class="w-6 h-6" />
          </div>
          <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-500 bg-amber-500/10 px-2 py-0.5 rounded-full">
            Đang Biên Soạn
          </span>
        </div>

        <div class="mt-4 flex items-end justify-between">
          <div>
            <h4 class="text-2xl sm:text-3xl font-black" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              {{ Math.max(0, (stats?.posts?.total ?? 0) - (stats?.posts?.published ?? 0)) }}
            </h4>
            <span class="text-xs font-medium" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
              Bản Nháp / Chờ Duyệt
            </span>
          </div>
          <span class="text-xs font-bold text-amber-500">
            Chưa xuất bản
          </span>
        </div>
      </div>

      <!-- Card 4: Bảo Mật Nội Dung -->
      <div
        class="rounded-2xl border p-5 sm:p-6 transition-all shadow-sm"
        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <div class="flex items-center justify-between">
          <div class="w-12 h-12 rounded-full flex items-center justify-center bg-blue-500/10 text-blue-500">
            <ShieldCheck class="w-6 h-6" />
          </div>
          <span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-500 bg-blue-500/10 px-2 py-0.5 rounded-full">
            An Toàn 100%
          </span>
        </div>

        <div class="mt-4 flex items-end justify-between">
          <div>
            <h4 class="text-2xl sm:text-3xl font-black text-blue-500">
              Auto Clean
            </h4>
            <span class="text-xs font-medium" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
              Bộ Lọc XSS & Link Rác
            </span>
          </div>
          <span class="text-xs font-bold text-emerald-500">
            Đang bật
          </span>
        </div>
      </div>
    </div>

    <!-- Charts & Analytics Section (TailAdmin Style) -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
      <!-- TailAdmin ChartOne: Weekly Orders & Production Trend (8 cols - Chỉ hiện khi có quyền xem báo giá/đơn) -->
      <div
        v-if="can('quotes.view') || can('orders.view')"
        class="xl:col-span-8 rounded-2xl border p-5 sm:p-6 shadow-sm"
        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <!-- Chart Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
          <div>
            <h3 class="text-base font-bold" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              Biến Động Yêu Cầu Báo Giá Tuần Này
            </h3>
            <p class="text-xs mt-0.5" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
              Dữ liệu đơn gửi mới so với đơn chốt chạy máy in
            </p>
          </div>

          <!-- Legend & Time filter -->
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-3 text-xs">
              <span class="flex items-center gap-1.5 font-medium" :class="isDarkMode ? 'text-slate-300' : 'text-slate-600'">
                <span class="w-3 h-3 rounded-full bg-brand-gold inline-block"></span>
                Đơn gửi mới
              </span>
              <span class="flex items-center gap-1.5 font-medium" :class="isDarkMode ? 'text-slate-300' : 'text-slate-600'">
                <span class="w-3 h-3 rounded-full bg-[#3C50E0] inline-block"></span>
                Đã chốt in
              </span>
            </div>

            <!-- TailAdmin Range Pills -->
            <div class="flex rounded-lg border p-0.5 text-xs font-semibold" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-slate-100 border-slate-200'">
              <button
                @click="chartRange = 'day'"
                class="px-2.5 py-1 rounded-md transition-colors cursor-pointer"
                :class="chartRange === 'day' ? (isDarkMode ? 'bg-[#333A48] text-white shadow' : 'bg-white text-slate-900 shadow') : 'text-slate-400 hover:text-white'"
              >
                Ngày
              </button>
              <button
                @click="chartRange = 'week'"
                class="px-2.5 py-1 rounded-md transition-colors cursor-pointer"
                :class="chartRange === 'week' ? (isDarkMode ? 'bg-[#333A48] text-white shadow' : 'bg-white text-slate-900 shadow') : 'text-slate-400 hover:text-white'"
              >
                Tuần
              </button>
              <button
                @click="chartRange = 'month'"
                class="px-2.5 py-1 rounded-md transition-colors cursor-pointer"
                :class="chartRange === 'month' ? (isDarkMode ? 'bg-[#333A48] text-white shadow' : 'bg-white text-slate-900 shadow') : 'text-slate-400 hover:text-white'"
              >
                Tháng
              </button>
            </div>
          </div>
        </div>

        <!-- Sleek Responsive SVG Area Curve Chart -->
        <div class="pt-6">
          <div class="relative w-full h-64">
            <svg class="w-full h-full overflow-visible" viewBox="0 0 700 240" preserveAspectRatio="none">
              <defs>
                <!-- Series 1 Gradient (Gold) -->
                <linearGradient id="goldGradient" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#F8AC22" stop-opacity="0.35" />
                  <stop offset="100%" stop-color="#F8AC22" stop-opacity="0.0" />
                </linearGradient>
                <!-- Series 2 Gradient (Blue) -->
                <linearGradient id="blueGradient" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#3C50E0" stop-opacity="0.25" />
                  <stop offset="100%" stop-color="#3C50E0" stop-opacity="0.0" />
                </linearGradient>
              </defs>

              <!-- Horizontal Grid Lines -->
              <line x1="0" y1="40" x2="700" y2="40" stroke="currentColor" :stroke-opacity="isDarkMode ? 0.08 : 0.06" stroke-dasharray="4 4" />
              <line x1="0" y1="100" x2="700" y2="100" stroke="currentColor" :stroke-opacity="isDarkMode ? 0.08 : 0.06" stroke-dasharray="4 4" />
              <line x1="0" y1="160" x2="700" y2="160" stroke="currentColor" :stroke-opacity="isDarkMode ? 0.08 : 0.06" stroke-dasharray="4 4" />
              <line x1="0" y1="210" x2="700" y2="210" stroke="currentColor" :stroke-opacity="isDarkMode ? 0.15 : 0.1" />

              <!-- Series 2 Area (Blue - Approved) -->
              <path
                d="M 20,180 C 120,160 180,140 240,150 C 320,160 400,110 480,120 C 560,130 620,90 680,85 L 680,210 L 20,210 Z"
                fill="url(#blueGradient)"
              />
              <path
                d="M 20,180 C 120,160 180,140 240,150 C 320,160 400,110 480,120 C 560,130 620,90 680,85"
                fill="none"
                stroke="#3C50E0"
                stroke-width="3"
                stroke-linecap="round"
              />

              <!-- Series 1 Area (Gold - New Quotes) -->
              <path
                d="M 20,140 C 120,110 180,70 240,90 C 320,110 400,50 480,60 C 560,70 620,30 680,25 L 680,210 L 20,210 Z"
                fill="url(#goldGradient)"
              />
              <path
                d="M 20,140 C 120,110 180,70 240,90 C 320,110 400,50 480,60 C 560,70 620,30 680,25"
                fill="none"
                stroke="#F8AC22"
                stroke-width="3.5"
                stroke-linecap="round"
              />

              <!-- Highlight Data Dots -->
              <circle cx="240" cy="90" r="5" fill="#F8AC22" stroke="#fff" stroke-width="2" />
              <circle cx="480" cy="60" r="5" fill="#F8AC22" stroke="#fff" stroke-width="2" />
              <circle cx="680" cy="25" r="6" fill="#F8AC22" stroke="#fff" stroke-width="2.5" />

              <circle cx="240" cy="150" r="4.5" fill="#3C50E0" stroke="#fff" stroke-width="2" />
              <circle cx="480" cy="120" r="4.5" fill="#3C50E0" stroke="#fff" stroke-width="2" />
              <circle cx="680" cy="85" r="5.5" fill="#3C50E0" stroke="#fff" stroke-width="2" />
            </svg>
          </div>

          <!-- X-Axis Day Labels -->
          <div class="flex justify-between text-[11px] font-semibold mt-2 px-2" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
            <span>Thứ 2</span>
            <span>Thứ 3</span>
            <span>Thứ 4</span>
            <span>Thứ 5</span>
            <span>Thứ 6</span>
            <span>Thứ 7</span>
            <span>Chủ Nhật (Hôm nay)</span>
          </div>
        </div>
      </div>

      <!-- Category Share / Top Printing Lines (Chỉ hiện khi có quyền xem ấn phẩm hoặc báo giá) -->
      <div
        v-if="can('products.manage') || can('quotes.view')"
        class="rounded-2xl border p-5 sm:p-6 shadow-sm flex flex-col justify-between"
        :class="[
          can('quotes.view') || can('orders.view') ? 'xl:col-span-4' : 'xl:col-span-6',
          isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'
        ]"
      >
        <div>
          <h3 class="text-base font-bold pb-2" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
            Phân Bổ Ấn Phẩm Đặt In
          </h3>
          <p class="text-xs pb-4 border-b" :class="isDarkMode ? 'text-slate-400 border-[#2E3A47]' : 'text-slate-500 border-slate-100'">
            Nhóm ấn phẩm khách hàng yêu cầu nhiều nhất
          </p>

          <div class="mt-5 space-y-4 text-xs">
            <!-- Item 1 -->
            <div>
              <div class="flex items-center justify-between mb-1.5 font-semibold">
                <span class="flex items-center gap-2" :class="isDarkMode ? 'text-white' : 'text-slate-800'">
                  <span class="w-2.5 h-2.5 rounded-full bg-brand-gold"></span>
                  Bao Bì & Túi Giấy
                </span>
                <span class="font-bold text-brand-gold">38% (18 đơn)</span>
              </div>
              <div class="w-full h-2 rounded-full overflow-hidden" :class="isDarkMode ? 'bg-[#1C2434]' : 'bg-slate-100'">
                <div class="h-full bg-brand-gold rounded-full transition-all duration-500" style="width: 38%"></div>
              </div>
            </div>

            <!-- Item 2 -->
            <div>
              <div class="flex items-center justify-between mb-1.5 font-semibold">
                <span class="flex items-center gap-2" :class="isDarkMode ? 'text-white' : 'text-slate-800'">
                  <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                  Tem Nhãn & Decal
                </span>
                <span class="font-bold text-blue-500">28% (13 đơn)</span>
              </div>
              <div class="w-full h-2 rounded-full overflow-hidden" :class="isDarkMode ? 'bg-[#1C2434]' : 'bg-slate-100'">
                <div class="h-full bg-blue-500 rounded-full transition-all duration-500" style="width: 28%"></div>
              </div>
            </div>

            <!-- Item 3 -->
            <div>
              <div class="flex items-center justify-between mb-1.5 font-semibold">
                <span class="flex items-center gap-2" :class="isDarkMode ? 'text-white' : 'text-slate-800'">
                  <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                  Ấn Phẩm Văn Phòng (Namecard)
                </span>
                <span class="font-bold text-emerald-500">20% (9 đơn)</span>
              </div>
              <div class="w-full h-2 rounded-full overflow-hidden" :class="isDarkMode ? 'bg-[#1C2434]' : 'bg-slate-100'">
                <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: 20%"></div>
              </div>
            </div>

            <!-- Item 4 -->
            <div>
              <div class="flex items-center justify-between mb-1.5 font-semibold">
                <span class="flex items-center gap-2" :class="isDarkMode ? 'text-white' : 'text-slate-800'">
                  <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                  Tờ Rơi & Catalogue
                </span>
                <span class="font-bold text-purple-500">14% (6 đơn)</span>
              </div>
              <div class="w-full h-2 rounded-full overflow-hidden" :class="isDarkMode ? 'bg-[#1C2434]' : 'bg-slate-100'">
                <div class="h-full bg-purple-500 rounded-full transition-all duration-500" style="width: 14%"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="pt-5 mt-4 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
          <router-link
            to="/admin/products"
            class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-xs font-bold transition-colors cursor-pointer"
            :class="isDarkMode ? 'bg-[#1C2434] hover:bg-[#333A48] text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-800'"
          >
            <Package class="w-3.5 h-3.5 text-brand-gold" />
            <span>Quản Lý Bảng Giá Ấn Phẩm</span>
          </router-link>
        </div>
      </div>

      <!-- Card Quy Chuẩn Dành Riêng Cho Cộng Tác Viên Viết Bài (Writer) -->
      <div
        v-else-if="can('posts.manage')"
        class="xl:col-span-6 rounded-2xl border p-5 sm:p-6 shadow-sm flex flex-col justify-between"
        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <div>
          <div class="flex items-center justify-between pb-2">
            <h3 class="text-base font-bold" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              Quy Chuẩn Viết Bài SEO & An Toàn Xưởng In
            </h3>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-500">
              Kiểm Duyệt Tự Động
            </span>
          </div>
          <p class="text-xs pb-4 border-b" :class="isDarkMode ? 'text-slate-400 border-[#2E3A47]' : 'text-slate-500 border-slate-100'">
            Nguyên tắc bảo vệ thương hiệu và thứ hạng Google website In Ấn Nhật Nguyên
          </p>

          <div class="mt-4 space-y-3 text-xs">
            <div class="flex items-start gap-2.5">
              <span class="w-5 h-5 rounded-full bg-purple-500/10 text-purple-500 font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">1</span>
              <div>
                <span class="font-bold" :class="isDarkMode ? 'text-white' : 'text-slate-900'">Từ khóa in ấn:</span>
                <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-600'"> Đặt tiêu đề và thẻ H2 rõ ràng (in hộp giấy, in túi giấy, tem nhãn decal...).</span>
              </div>
            </div>

            <div class="flex items-start gap-2.5">
              <span class="w-5 h-5 rounded-full bg-blue-500/10 text-blue-500 font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">2</span>
              <div>
                <span class="font-bold" :class="isDarkMode ? 'text-white' : 'text-slate-900'">Bảo mật liên kết:</span>
                <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-600'"> Hệ thống tự động lọc mã độc và gắn rel="nofollow" với liên kết ngoài, ngăn chặn link spam.</span>
              </div>
            </div>

            <div class="flex items-start gap-2.5">
              <span class="w-5 h-5 rounded-full bg-amber-500/10 text-amber-500 font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">3</span>
              <div>
                <span class="font-bold" :class="isDarkMode ? 'text-white' : 'text-slate-900'">Hình ảnh minh họa:</span>
                <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-600'"> Sử dụng hình ảnh sắc nét, định dạng WebP/JPG/PNG tối ưu tải trang.</span>
              </div>
            </div>

            <div class="flex items-start gap-2.5">
              <span class="w-5 h-5 rounded-full bg-emerald-500/10 text-emerald-500 font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">4</span>
              <div>
                <span class="font-bold" :class="isDarkMode ? 'text-white' : 'text-slate-900'">Kêu gọi tư vấn (CTA):</span>
                <span :class="isDarkMode ? 'text-slate-400' : 'text-slate-600'"> Cuối bài nhắc khách hàng bấm "Báo giá nhanh" hoặc liên hệ Zalo xưởng in.</span>
              </div>
            </div>
          </div>
        </div>

        <div class="pt-4 mt-4 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
          <router-link
            to="/admin/posts/create"
            class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-xs font-bold text-white bg-purple-600 hover:bg-purple-700 transition-colors shadow-sm"
          >
            <Plus class="w-4 h-4" />
            <span>Biên Soạn Bài Mới Ngay</span>
          </router-link>
        </div>
      </div>

      <!-- Quick Actions Card (Phân theo Content Manager vs Writer) -->
      <div
        v-if="!can('quotes.view') && can('posts.manage')"
        class="xl:col-span-6 rounded-2xl border p-5 sm:p-6 shadow-sm flex flex-col justify-between"
        :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <div>
          <h3 class="text-base font-bold pb-2" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
            {{ can('products.manage') ? 'Lối Tắt Quản Trị Website & SEO' : 'Lối Tắt Tác Vụ Tác Giả' }}
          </h3>
          <p class="text-xs pb-4 border-b" :class="isDarkMode ? 'text-slate-400 border-[#2E3A47]' : 'text-slate-500 border-slate-100'">
            {{ can('products.manage') ? 'Các chức năng thường dùng dành cho ban biên tập nội dung' : 'Các thao tác soạn bài và quản lý nội dung của bạn' }}
          </p>

          <!-- Shortcuts for Content Manager -->
          <div v-if="can('products.manage')" class="grid grid-cols-2 gap-3 mt-5">
            <router-link
              to="/admin/posts/create"
              class="p-4 rounded-xl border text-left transition-all hover:border-purple-500 hover:shadow-sm group cursor-pointer"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
            >
              <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-500 flex items-center justify-center mb-2">
                <Newspaper class="w-4 h-4" />
              </div>
              <div class="font-extrabold text-xs" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                + Đăng Bài Viết
              </div>
              <div class="text-[10px] text-slate-400 mt-0.5">Viết bài cẩm nang in ấn</div>
            </router-link>

            <router-link
              to="/admin/banners/create"
              class="p-4 rounded-xl border text-left transition-all hover:border-blue-500 hover:shadow-sm group cursor-pointer"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
            >
              <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-500 flex items-center justify-center mb-2">
                <ImageIcon class="w-4 h-4" />
              </div>
              <div class="font-extrabold text-xs" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                + Thêm Banner
              </div>
              <div class="text-[10px] text-slate-400 mt-0.5">Quảng cáo trang chủ</div>
            </router-link>

            <router-link
              to="/admin/categories/create"
              class="p-4 rounded-xl border text-left transition-all hover:border-amber-500 hover:shadow-sm group cursor-pointer"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
            >
              <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-500 flex items-center justify-center mb-2">
                <Tags class="w-4 h-4" />
              </div>
              <div class="font-extrabold text-xs" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                + Nhóm Danh Mục
              </div>
              <div class="text-[10px] text-slate-400 mt-0.5">Cấu trúc nhóm ấn phẩm</div>
            </router-link>

            <router-link
              to="/admin/pricing"
              class="p-4 rounded-xl border text-left transition-all hover:border-emerald-500 hover:shadow-sm group cursor-pointer"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
            >
              <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center mb-2">
                <PackageCheck class="w-4 h-4" />
              </div>
              <div class="font-extrabold text-xs" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                Pricing Studio
              </div>
              <div class="text-[10px] text-slate-400 mt-0.5">Cấu hình tính giá</div>
            </router-link>
          </div>

          <!-- Shortcuts for Writer / Cộng tác viên -->
          <div v-else class="grid grid-cols-2 gap-3 mt-5">
            <router-link
              to="/admin/posts/create"
              class="p-4 rounded-xl border text-left transition-all hover:border-purple-500 hover:shadow-sm group cursor-pointer"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
            >
              <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-500 flex items-center justify-center mb-2">
                <Newspaper class="w-4 h-4" />
              </div>
              <div class="font-extrabold text-xs" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                + Đăng Bài Viết
              </div>
              <div class="text-[10px] text-slate-400 mt-0.5">Viết bài mới cho website</div>
            </router-link>

            <router-link
              to="/admin/posts"
              class="p-4 rounded-xl border text-left transition-all hover:border-blue-500 hover:shadow-sm group cursor-pointer"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
            >
              <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-500 flex items-center justify-center mb-2">
                <FileText class="w-4 h-4" />
              </div>
              <div class="font-extrabold text-xs" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                Quản Lý Bài Viết
              </div>
              <div class="text-[10px] text-slate-400 mt-0.5">Danh sách các bài đã soạn</div>
            </router-link>

            <router-link
              to="/tin-tuc"
              target="_blank"
              class="p-4 rounded-xl border text-left transition-all hover:border-emerald-500 hover:shadow-sm group cursor-pointer"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
            >
              <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center mb-2">
                <ExternalLink class="w-4 h-4" />
              </div>
              <div class="font-extrabold text-xs" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                Xem Mục Tin Tức
              </div>
              <div class="text-[10px] text-slate-400 mt-0.5">Hiển thị ngoài trang chủ</div>
            </router-link>

            <div
              class="p-4 rounded-xl border text-left"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
            >
              <div class="w-8 h-8 rounded-lg bg-brand-gold/10 text-brand-gold flex items-center justify-center mb-2">
                <ShieldCheck class="w-4 h-4" />
              </div>
              <div class="font-extrabold text-xs" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                Kiểm Tra Link Tự Động
              </div>
              <div class="text-[10px] text-slate-400 mt-0.5">Tự động gắn nofollow</div>
            </div>
          </div>
        </div>

        <div class="pt-5 mt-4 border-t text-center" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
          <a
            href="/"
            target="_blank"
            class="text-xs text-brand-gold hover:underline font-bold"
          >
            Xem Trang Chủ Website Đang Hoạt Động →
          </a>
        </div>
      </div>
    </div>

    <!-- =================================================================== -->
    <!-- TABLE A: YÊU CẦU BÁO GIÁ GẦN ĐÂY (CHO SALES / ADMIN)                -->
    <!-- =================================================================== -->
    <div
      v-if="can('quotes.view') || can('orders.view')"
      class="rounded-2xl border shadow-sm overflow-hidden"
      :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
    >
      <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
        <div>
          <h2 class="text-base font-bold" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
            Yêu Cầu Báo Giá Gần Đây
          </h2>
          <p class="text-xs mt-0.5" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
            5 khách hàng gửi yêu cầu tư vấn và duyệt file mới nhất
          </p>
        </div>

        <router-link
          to="/admin/quotes"
          class="text-xs font-bold text-brand-gold hover:underline flex items-center gap-1.5 self-start sm:self-auto cursor-pointer"
        >
          <span>Xem Tất Cả Đơn Báo Giá</span>
          <ArrowRight class="w-3.5 h-3.5" />
        </router-link>
      </div>

      <!-- Table Body -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr
              class="border-b text-[11px] font-bold uppercase tracking-wider"
              :class="isDarkMode ? 'bg-[#1C2434]/60 border-[#2E3A47] text-slate-400' : 'bg-slate-50 border-slate-100 text-slate-500'"
            >
              <th class="py-3.5 px-6">Mã Đơn / Ngày</th>
              <th class="py-3.5 px-6">Khách Hàng</th>
              <th class="py-3.5 px-6">Ấn Phẩm & SL</th>
              <th class="py-3.5 px-6">Trạng Thái</th>
              <th class="py-3.5 px-6 text-right">Thao Tác</th>
            </tr>
          </thead>
          <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]/60' : 'divide-slate-100'">
            <tr v-if="!stats?.recent_quotes || stats.recent_quotes.length === 0">
              <td colspan="5" class="py-12 text-center text-slate-400">
                Chưa có yêu cầu báo giá nào trong hệ thống
              </td>
            </tr>
            <tr
              v-for="quote in stats?.recent_quotes"
              :key="quote.id"
              class="transition-colors"
              :class="isDarkMode ? 'hover:bg-[#1C2434]/50' : 'hover:bg-slate-50/80'"
            >
              <!-- Code & Date -->
              <td class="py-4 px-6">
                <div class="font-mono font-bold text-brand-gold text-sm">{{ quote.code }}</div>
                <div class="text-[10px] text-slate-400 mt-0.5">{{ formatDate(quote.created_at) }}</div>
              </td>

              <!-- Customer with Avatar -->
              <td class="py-4 px-6">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-full bg-blue-500/10 text-blue-500 font-bold text-xs flex items-center justify-center shrink-0">
                    {{ getInitials(quote.customer_name) }}
                  </div>
                  <div>
                    <div class="font-bold text-sm" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                      {{ quote.customer_name }}
                    </div>
                    <div class="flex items-center gap-2 text-[11px] mt-0.5">
                      <span class="text-slate-400">{{ quote.phone }}</span>
                      <a
                        :href="'https://zalo.me/' + cleanPhone(quote.phone)"
                        target="_blank"
                        class="text-[10px] text-blue-500 hover:underline font-semibold bg-blue-500/10 px-1.5 py-0.2 rounded"
                      >
                        Zalo
                      </a>
                    </div>
                  </div>
                </div>
              </td>

              <!-- Product & Qty -->
              <td class="py-4 px-6">
                <div class="font-bold" :class="isDarkMode ? 'text-white' : 'text-slate-800'">
                  {{ quote.product_name || 'Quy cách riêng' }}
                </div>
                <div class="text-[11px] text-amber-500 font-semibold mt-0.5">
                  SL: {{ quote.quantity || 'Chưa rõ' }}
                </div>
              </td>

              <!-- Status Badge (TailAdmin Pill Style) -->
              <td class="py-4 px-6">
                <span
                  class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-bold"
                  :class="statusBadge(quote.status).class"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="statusBadge(quote.status).dot"></span>
                  {{ statusBadge(quote.status).label }}
                </span>
              </td>

              <!-- Action button -->
              <td class="py-4 px-6 text-right">
                <router-link
                  to="/admin/quotes"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all"
                  :class="isDarkMode ? 'bg-[#333A48] hover:bg-[#3C50E0] text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-800'"
                >
                  <span>Xử lý</span>
                  <ArrowRight class="w-3 h-3" />
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- =================================================================== -->
    <!-- TABLE B: BÀI VIẾT & CẨM NANG GẦN ĐÂY (CHO CONTENT / MARKETING)     -->
    <!-- =================================================================== -->
    <div
      v-else-if="can('posts.manage')"
      class="rounded-2xl border shadow-sm overflow-hidden"
      :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-white border-slate-200'"
    >
      <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
        <div>
          <h2 class="text-base font-bold" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
            Bài Viết & Cẩm Nang Gần Đây
          </h2>
          <p class="text-xs mt-0.5" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
            Danh sách các bài viết mới nhất trên hệ thống website
          </p>
        </div>

        <router-link
          to="/admin/posts"
          class="text-xs font-bold text-purple-500 hover:underline flex items-center gap-1.5 self-start sm:self-auto cursor-pointer"
        >
          <span>Xem Tất Cả Bài Viết</span>
          <ArrowRight class="w-3.5 h-3.5" />
        </router-link>
      </div>

      <!-- Posts Table Body -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr
              class="border-b text-[11px] font-bold uppercase tracking-wider"
              :class="isDarkMode ? 'bg-[#1C2434]/60 border-[#2E3A47] text-slate-400' : 'bg-slate-50 border-slate-100 text-slate-500'"
            >
              <th class="py-3.5 px-6">Tiêu Đề Bài Viết</th>
              <th class="py-3.5 px-6">Chuyên Mục</th>
              <th class="py-3.5 px-6">Trạng Thái</th>
              <th class="py-3.5 px-6">Ngày Đăng</th>
              <th class="py-3.5 px-6 text-right">Thao Tác</th>
            </tr>
          </thead>
          <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]/60' : 'divide-slate-100'">
            <tr v-if="!stats?.recent_posts || stats.recent_posts.length === 0">
              <td colspan="5" class="py-12 text-center text-slate-400">
                Chưa có bài viết nào trong hệ thống
              </td>
            </tr>
            <tr
              v-for="post in stats?.recent_posts"
              :key="post.id"
              class="transition-colors"
              :class="isDarkMode ? 'hover:bg-[#1C2434]/50' : 'hover:bg-slate-50/80'"
            >
              <!-- Title -->
              <td class="py-4 px-6">
                <div class="font-bold text-sm line-clamp-1" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                  {{ post.title }}
                </div>
                <div class="text-[10px] text-slate-400 mt-0.5 line-clamp-1">
                  {{ post.excerpt || post.slug }}
                </div>
              </td>

              <!-- Category -->
              <td class="py-4 px-6 font-medium text-slate-500">
                {{ post.category?.name || 'Tin tức xưởng' }}
              </td>

              <!-- Status -->
              <td class="py-4 px-6">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold"
                  :class="post.is_published ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200'"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="post.is_published ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                  <span>{{ post.is_published ? 'Đã xuất bản' : 'Bản nháp' }}</span>
                </span>
              </td>

              <!-- Date -->
              <td class="py-4 px-6 text-slate-400 text-[11px]">
                {{ formatDate(post.created_at) }}
              </td>

              <!-- Actions -->
              <td class="py-4 px-6 text-right">
                <router-link
                  :to="`/admin/posts/${post.id}/edit`"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer"
                  :class="isDarkMode ? 'bg-[#333A48] hover:bg-[#3C50E0] text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-800'"
                >
                  <Edit3 class="w-3 h-3" />
                  <span>Sửa</span>
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import {
  ClipboardList,
  Clock,
  ClipboardCheck,
  PackageCheck,
  RefreshCw,
  ArrowRight,
  ArrowUp,
  AlertCircle,
  Printer,
  Package,
  Newspaper,
  Image as ImageIcon,
  Tags,
  Plus,
  Edit3,
  CheckCircle,
  ShieldCheck,
  FileText,
  ExternalLink
} from 'lucide-vue-next'
import { api, can } from '../../services/api'
import { useAdminTheme } from '../../composables/useAdminTheme'

const { isDarkMode } = useAdminTheme()

const stats = ref(null)
const isLoading = ref(false)
const chartRange = ref('week')

function cleanPhone(phone) {
  if (!phone) return ''
  return phone.replace(/[^0-9]/g, '')
}

function getInitials(name) {
  if (!name) return 'KH'
  const parts = name.trim().split(' ')
  if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase()
  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
}

function formatDate(iso) {
  if (!iso) return ''
  const d = new Date(iso)
  return `${d.getDate().toString().padStart(2, '0')}/${(d.getMonth() + 1).toString().padStart(2, '0')} ${d.getHours().toString().padStart(2, '0')}:${d.getMinutes().toString().padStart(2, '0')}`
}

function statusBadge(status) {
  switch (status) {
    case 'pending':
      return {
        label: 'Chờ báo giá',
        class: 'bg-amber-500/10 text-amber-500 border border-amber-500/20',
        dot: 'bg-amber-500'
      }
    case 'contacted':
      return {
        label: 'Đang báo giá',
        class: 'bg-blue-500/10 text-blue-500 border border-blue-500/20',
        dot: 'bg-blue-500'
      }
    case 'approved':
      return {
        label: 'Đã chốt đơn',
        class: 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20',
        dot: 'bg-emerald-500'
      }
    case 'completed':
      return {
        label: 'Hoàn thành',
        class: 'bg-purple-500/10 text-purple-500 border border-purple-500/20',
        dot: 'bg-purple-500'
      }
    case 'cancelled':
      return {
        label: 'Đã hủy',
        class: 'bg-slate-500/10 text-slate-400 border border-slate-500/20',
        dot: 'bg-slate-400'
      }
    default:
      return {
        label: status,
        class: 'bg-slate-500/10 text-slate-400 border border-slate-500/20',
        dot: 'bg-slate-400'
      }
  }
}

async function loadData() {
  isLoading.value = true
  try {
    const res = await api.getDashboardStats()
    stats.value = res.data
  } catch (err) {
    console.error('Failed to load dashboard data', err)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  loadData()
})
</script>
