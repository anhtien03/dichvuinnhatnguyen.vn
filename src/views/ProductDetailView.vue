<template>
  <!-- 1. Branded Skeleton Loading State -->
  <div v-if="isLoading" class="py-10 lg:py-16 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Breadcrumb Skeleton -->
      <div class="flex items-center gap-2 mb-6">
        <div class="h-3 w-16 bg-slate-200/80 rounded-md animate-pulse"></div>
        <span class="text-slate-300">/</span>
        <div class="h-3 w-20 bg-slate-200/80 rounded-md animate-pulse"></div>
        <span class="text-slate-300">/</span>
        <div class="h-3 w-28 bg-slate-200/80 rounded-md animate-pulse"></div>
      </div>

      <!-- Main Layout Skeleton -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
        <div class="lg:col-span-5 space-y-6">
          <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm space-y-4">
            <div class="relative aspect-[4/3] rounded-2xl bg-gradient-to-br from-slate-100 via-slate-50 to-slate-100 border border-slate-100 flex flex-col items-center justify-center p-6 text-center">
              <div class="relative flex items-center justify-center mb-3">
                <span class="absolute inline-flex h-20 w-20 rounded-full bg-brand-blue/15 animate-ping"></span>
                <div class="relative w-16 h-16 rounded-2xl bg-white shadow-lg border border-slate-100 flex items-center justify-center p-2.5">
                  <img src="/logo-icon.png" alt="In Nhật Nguyên" class="w-full h-full object-contain animate-pulse" />
                </div>
              </div>
              <div class="flex items-center justify-center gap-1.5 mb-2">
                <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-bounce" style="animation-delay: -0.3s;"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-pink-500 animate-bounce" style="animation-delay: -0.15s;"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-bounce" style="animation-delay: 0s;"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-slate-900 animate-bounce" style="animation-delay: 0.15s;"></span>
              </div>
              <span class="text-xs font-black text-slate-800 tracking-tight block">Xưởng In Nhật Nguyên</span>
              <span class="text-[11px] text-slate-400 block mt-0.5">Đang tải thông số kỹ thuật ấn phẩm...</span>
            </div>
          </div>
        </div>

        <div class="lg:col-span-7 space-y-6">
          <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm space-y-3">
            <div class="h-3 w-24 bg-blue-100 rounded-md animate-pulse"></div>
            <div class="h-7 w-3/4 bg-slate-200/80 rounded-xl animate-pulse"></div>
            <div class="h-4 w-full bg-slate-100 rounded-md animate-pulse"></div>
          </div>
          <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm space-y-4">
            <div class="h-12 w-full bg-slate-50 rounded-xl animate-pulse border border-slate-100"></div>
            <div class="h-12 w-full bg-slate-50 rounded-xl animate-pulse border border-slate-100"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 2. Main Product Content -->
  <div v-else-if="product" class="py-8 lg:py-12 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Breadcrumb -->
      <nav class="flex items-center text-xs text-slate-500 mb-6 space-x-2 overflow-x-auto pb-1" aria-label="Breadcrumb">
        <router-link to="/" class="hover:text-brand-blue shrink-0">Trang chủ</router-link>
        <span class="text-slate-300">/</span>
        <router-link to="/san-pham" class="hover:text-brand-blue shrink-0">Sản phẩm</router-link>
        <template v-if="product.category?.parent">
          <span class="text-slate-300">/</span>
          <router-link :to="'/danh-muc/' + product.category.parent.slug" class="hover:text-brand-blue shrink-0">
            {{ product.category.parent.name }}
          </router-link>
        </template>
        <span class="text-slate-300">/</span>
        <router-link :to="'/danh-muc/' + product.categoryId" class="hover:text-brand-blue shrink-0 font-semibold">
          {{ product.categoryName }}
        </router-link>
        <span class="text-slate-300">/</span>
        <span class="text-slate-800 font-bold truncate">{{ product.name }}</span>
      </nav>

      <!-- ================================================================= -->
      <!-- ABOVE THE FOLD / HERO AREA                                        -->
      <!-- ================================================================= -->
      <div id="calculator-section" class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start mb-12">
        <!-- Left Column: Visuals, Badges & Quick Highlights -->
        <div class="lg:col-span-5 space-y-5">
          <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/90 shadow-sm space-y-4">
            <!-- Main Hero Image (Chuẩn tỉ lệ 800x600 px -> 4:3) -->
            <div class="relative aspect-[4/3] rounded-2xl overflow-hidden bg-slate-100 border border-slate-100 group">
              <img
                :src="activeImage || product.image || '/logo.png'"
                :alt="product.name"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
              />
              <span v-if="product.tag" class="absolute top-3 right-3 px-3 py-1 rounded-lg bg-brand-gold text-slate-950 font-black text-xs shadow-md">
                {{ product.tag }}
              </span>
              <div class="absolute bottom-3 left-3 px-2.5 py-1 rounded-lg bg-slate-900/70 backdrop-blur-xs text-white text-[11px] font-semibold flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span>SKU: NN-{{ String(product.id || '001').padStart(4, '0') }}</span>
              </div>
            </div>

            <!-- Thumbnail Gallery (nếu có nhiều ảnh) -->
            <div v-if="galleryImages.length > 1" class="flex items-center gap-2 overflow-x-auto pb-1">
              <button
                v-for="(img, idx) in galleryImages"
                :key="idx"
                type="button"
                @click="activeImage = img"
                :class="[
                  'w-16 h-16 rounded-xl overflow-hidden border-2 shrink-0 transition-all cursor-pointer bg-slate-100',
                  activeImage === img ? 'border-brand-blue ring-2 ring-brand-blue/30 scale-105' : 'border-slate-200 hover:border-slate-300 opacity-70 hover:opacity-100'
                ]"
              >
                <img :src="img" :alt="product.name" class="w-full h-full object-cover" />
              </button>
            </div>

            <!-- 4 Trust Badges Xưởng In -->
            <div class="grid grid-cols-2 gap-2 pt-1">
              <div class="p-2.5 rounded-xl bg-emerald-50/80 border border-emerald-200/80 flex items-center gap-2">
                <ShieldCheck class="w-4 h-4 text-emerald-600 shrink-0" />
                <div class="min-w-0">
                  <span class="text-[11px] font-bold text-emerald-950 block truncate">Xưởng in trực tiếp</span>
                  <span class="text-[10px] text-emerald-700 block truncate">Giá gốc không qua môi giới</span>
                </div>
              </div>
              <div class="p-2.5 rounded-xl bg-amber-50/80 border border-amber-200/80 flex items-center gap-2">
                <Sparkles class="w-4 h-4 text-amber-600 shrink-0" />
                <div class="min-w-0">
                  <span class="text-[11px] font-bold text-amber-950 block truncate">Chuẩn màu Offset</span>
                  <span class="text-[10px] text-amber-700 block truncate">Chuẩn sắc nét CMYK 98%</span>
                </div>
              </div>
              <div class="p-2.5 rounded-xl bg-blue-50/80 border border-blue-200/80 flex items-center gap-2">
                <Truck class="w-4 h-4 text-blue-600 shrink-0" />
                <div class="min-w-0">
                  <span class="text-[11px] font-bold text-blue-950 block truncate">Giao hàng toàn quốc</span>
                  <span class="text-[10px] text-blue-700 block truncate">Hỗ trợ gửi chành xe các tỉnh</span>
                </div>
              </div>
              <div class="p-2.5 rounded-xl bg-purple-50/80 border border-purple-200/80 flex items-center gap-2">
                <Clock class="w-4 h-4 text-purple-600 shrink-0" />
                <div class="min-w-0">
                  <span class="text-[11px] font-bold text-purple-950 block truncate">Tiến độ nhanh 4h - 24h</span>
                  <span class="text-[10px] text-purple-700 block truncate">Có in gấp lấy trong ngày</span>
                </div>
              </div>
            </div>

            <!-- Quick Specs Summary (Trích xuất nhanh điểm chính) -->
            <div class="pt-3 border-t border-slate-100 space-y-2">
              <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                <FileText class="w-3.5 h-3.5 text-brand-blue" />
                <span>Quy Cách & Tiêu Chuẩn Nhanh</span>
              </h4>
              <ul class="space-y-1.5 text-xs text-slate-600">
                <li v-if="product.turnaround" class="flex items-center justify-between py-1 border-b border-slate-100/60">
                  <span class="text-slate-400">Thời gian trả hàng:</span>
                  <span class="font-bold text-slate-800">{{ product.turnaround }}</span>
                </li>
                <li v-if="product.min_quantity" class="flex items-center justify-between py-1 border-b border-slate-100/60">
                  <span class="text-slate-400">Số lượng tối thiểu:</span>
                  <span class="font-bold text-slate-800">{{ product.min_quantity }} {{ product.unit || 'cái' }}</span>
                </li>
                <li class="flex items-center justify-between py-1 border-b border-slate-100/60">
                  <span class="text-slate-400">Duyệt mẫu trước khi in:</span>
                  <span class="font-bold text-emerald-600">Miễn phí 100%</span>
                </li>
                <li class="flex items-center justify-between py-1">
                  <span class="text-slate-400">Chính sách đổi trả:</span>
                  <span class="font-bold text-emerald-600">1 đổi 1 nếu lỗi xưởng in</span>
                </li>
              </ul>
            </div>

            <!-- CTA Báo giá riêng / Tư vấn Zalo nhanh -->
            <div class="pt-2">
              <router-link
                :to="'/yeu-cau-bao-gia?product=' + product.slug"
                class="w-full py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center gap-2 transition-colors"
              >
                <HelpCircle class="w-4 h-4 text-slate-500" />
                <span>Chưa rõ quy cách? Bấm để nhận báo giá riêng &rarr;</span>
              </router-link>
            </div>
          </div>
        </div>

        <!-- Right Column: Interactive Price Calculator Component -->
        <div class="lg:col-span-7">
          <PriceCalculator :product="product" />
        </div>
      </div>

      <!-- ================================================================= -->
      <!-- STICKY ANCHOR SUB-NAV BAR                                         -->
      <!-- ================================================================= -->
      <div class="sticky top-16 z-30 mb-8 bg-white/95 backdrop-blur-md rounded-2xl border border-slate-200/90 shadow-sm p-1.5 flex items-center gap-1.5 overflow-x-auto no-scrollbar">
        <a
          v-for="nav in subNavItems"
          :key="nav.id"
          :href="'#' + nav.id"
          class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-colors hover:bg-slate-100 text-slate-700 hover:text-brand-blue flex items-center gap-1.5"
        >
          <component :is="nav.icon" class="w-3.5 h-3.5 text-slate-400" />
          <span>{{ nav.label }}</span>
        </a>
      </div>

      <!-- ================================================================= -->
      <!-- SECTION 1: BẢNG THÔNG SỐ KỸ THUẬT TIÊU CHUẨN                      -->
      <!-- ================================================================= -->
      <section id="thong-so-ky-thuat" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm mb-8 scroll-mt-28">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
          <div>
            <span class="text-[11px] font-black uppercase tracking-wider text-brand-blue block">Quy Chuẩn Sản Xuất</span>
            <h2 class="text-lg sm:text-xl font-black text-slate-900 mt-0.5 flex items-center gap-2">
              <Sliders class="w-5 h-5 text-brand-blue" />
              <span>Thông Số Kỹ Thuật In {{ product.name }}</span>
            </h2>
          </div>
          <span class="hidden sm:inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">
            <CheckCircle2 class="w-3.5 h-3.5 text-emerald-600" /> Tiêu chuẩn xưởng
          </span>
        </div>

        <!-- Bảng thông số phẳng 2 cột -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3 text-xs sm:text-sm">
          <div class="flex items-center justify-between py-2.5 border-b border-slate-100">
            <span class="text-slate-500 font-medium">Tên ấn phẩm:</span>
            <span class="font-bold text-slate-900 text-right">{{ product.name }}</span>
          </div>
          <div class="flex items-center justify-between py-2.5 border-b border-slate-100">
            <span class="text-slate-500 font-medium">Mã quản lý (SKU):</span>
            <span class="font-bold text-slate-900 text-right">NN-{{ String(product.id || '001').padStart(4, '0') }}</span>
          </div>
          <div class="flex items-center justify-between py-2.5 border-b border-slate-100">
            <span class="text-slate-500 font-medium">Danh mục sản phẩm:</span>
            <span class="font-bold text-brand-blue text-right">{{ product.categoryName }}</span>
          </div>

          <!-- Khi sản phẩm có cấu hình quy cách riêng (custom_specs) -->
          <template v-if="customSpecsList.length > 0">
            <div
              v-for="(spec, sIdx) in customSpecsList"
              :key="sIdx"
              class="flex items-center justify-between py-2.5 border-b border-slate-100"
            >
              <span class="text-slate-500 font-medium">{{ spec.name }}:</span>
              <span class="font-bold text-slate-900 text-right">{{ spec.value }}</span>
            </div>
          </template>

          <!-- Khi chưa cấu hình quy cách riêng -->
          <template v-else>
            <div class="flex items-center justify-between py-2.5 border-b border-slate-100">
              <span class="text-slate-500 font-medium">Kích thước chuẩn:</span>
              <span class="font-bold text-slate-900 text-right">
                {{ availableSizesSummary || 'Theo kích thước chuẩn hoặc tùy chỉnh' }}
              </span>
            </div>
            <div class="flex items-center justify-between py-2.5 border-b border-slate-100">
              <span class="text-slate-500 font-medium">Chất liệu giấy / vật liệu:</span>
              <span class="font-bold text-slate-900 text-right">
                {{ availableMaterialsSummary || defaultMaterialSummary }}
              </span>
            </div>
            <div class="flex items-center justify-between py-2.5 border-b border-slate-100">
              <span class="text-slate-500 font-medium">Công nghệ in áp dụng:</span>
              <span class="font-bold text-slate-900 text-right">
                {{ defaultTechSummary }}
              </span>
            </div>
            <div class="flex items-center justify-between py-2.5 border-b border-slate-100">
              <span class="text-slate-500 font-medium">Gia công thành phẩm:</span>
              <span class="font-bold text-slate-900 text-right">
                {{ availableFinishingsSummary || 'Cắt bế, cấn gấp, hoàn thiện theo file' }}
              </span>
            </div>
          </template>

          <div class="flex items-center justify-between py-2.5 border-b border-slate-100">
            <span class="text-slate-500 font-medium">Thời gian hoàn thành:</span>
            <span class="font-bold text-emerald-700 text-right">{{ product.turnaround || '4h - 24h làm việc' }}</span>
          </div>
          <div class="flex items-center justify-between py-2.5 border-b border-slate-100 md:border-b-0">
            <span class="text-slate-500 font-medium">Số lượng tối thiểu:</span>
            <span class="font-bold text-slate-900 text-right">Từ {{ product.min_quantity || 1 }} {{ product.unit || 'cái' }}</span>
          </div>
          <div class="flex items-center justify-between py-2.5">
            <span class="text-slate-500 font-medium">Đóng gói & Vận chuyển:</span>
            <span class="font-bold text-slate-900 text-right">Thùng carton màng co, giao TPHCM & Chành xe toàn quốc</span>
          </div>
        </div>
      </section>

      <!-- ================================================================= -->
      <!-- SECTION 2: BẢNG BÁO GIÁ THAM KHẢO CHI TIẾT                        -->
      <!-- ================================================================= -->
      <section v-if="product.specs?.quantities?.length" id="bang-bao-gia" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm mb-8 scroll-mt-28">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 mb-6 border-b border-slate-100 gap-2">
          <div>
            <span class="text-[11px] font-black uppercase tracking-wider text-brand-blue block">Bảng Giá Minh Bạch</span>
            <h2 class="text-lg sm:text-xl font-black text-slate-900 mt-0.5 flex items-center gap-2">
              <FileCheck class="w-5 h-5 text-brand-gold" />
              <span>Bảng Báo Giá Tham Khảo In {{ product.name }}</span>
            </h2>
          </div>
          <span class="text-xs text-slate-500">
            * Đơn giá chưa bao gồm VAT và có thể thay đổi tùy chất liệu chọn thêm
          </span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs sm:text-sm">
            <thead>
              <tr class="bg-slate-100/80 text-slate-700 font-extrabold border-b border-slate-200">
                <th class="py-3 px-4 rounded-l-xl">Mốc Số Lượng</th>
                <th class="py-3 px-4">Đơn Giá / {{ product.unit || 'cái' }}</th>
                <th class="py-3 px-4">Tổng Tạm Tính</th>
                <th class="py-3 px-4">Thời Gian Dự Kiến</th>
                <th class="py-3 px-4 text-center rounded-r-xl">Ưu Đãi & Đặt Hàng</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="(q, idx) in product.specs.quantities"
                :key="idx"
                class="hover:bg-slate-50/80 transition-colors"
              >
                <td class="py-3.5 px-4 font-bold text-slate-900">
                  <div class="flex items-center gap-2">
                    <span>{{ q.label || (q.count + ' ' + (product.unit || 'cái')) }}</span>
                    <span
                      v-if="q.count >= 1000"
                      class="text-[10px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 font-bold shrink-0"
                    >
                      Tiết kiệm nhất
                    </span>
                    <span
                      v-else-if="q.count >= 500"
                      class="text-[10px] px-1.5 py-0.5 rounded bg-blue-100 text-brand-blue font-bold shrink-0"
                    >
                      Bán chạy
                    </span>
                  </div>
                </td>
                <td class="py-3.5 px-4 font-black text-brand-blue">
                  {{ formatCurrency(q.pricePerUnit) }}
                </td>
                <td class="py-3.5 px-4 font-extrabold text-slate-800">
                  {{ formatCurrency(q.pricePerUnit * q.count) }}
                </td>
                <td class="py-3.5 px-4 text-slate-600">
                  {{ product.turnaround || '4h - 24h' }}
                </td>
                <td class="py-3.5 px-4 text-center">
                  <button
                    type="button"
                    @click="scrollToCalculator"
                    class="px-3 py-1.5 rounded-lg bg-brand-gold hover:bg-brand-gold-hover text-slate-950 font-bold text-xs transition-colors cursor-pointer"
                  >
                    Chọn mốc này
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="mt-4 p-3.5 rounded-2xl bg-blue-50/70 border border-blue-100 flex items-center justify-between text-xs text-blue-900">
          <span>Cần in số lượng lớn hơn hoặc quy cách riêng biệt (kích thước khác, giấy mỹ thuật cao cấp)?</span>
          <router-link :to="'/yeu-cau-bao-gia?product=' + product.slug" class="font-bold underline hover:text-blue-700 ml-2 shrink-0">
            Gửi yêu cầu báo giá xưởng &rarr;
          </router-link>
        </div>
      </section>

      <!-- ================================================================= -->
      <!-- SECTION 3: MÔ TẢ CHI TIẾT & HƯỚNG DẪN FILE IN                     -->
      <!-- ================================================================= -->
      <section id="mo-ta-chi-tiet" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm mb-8 scroll-mt-28">
        <div class="flex items-center gap-2 pb-4 mb-6 border-b border-slate-100">
          <FileText class="w-5 h-5 text-brand-blue" />
          <h2 class="text-lg sm:text-xl font-black text-slate-900">
            Chi Tiết & Quy Cách Sản Phẩm {{ product.name }}
          </h2>
        </div>

        <!-- Nội dung mô tả thực tế từ xưởng (Hỗ trợ Rich HTML & Hình ảnh minh họa) -->
        <div class="prose max-w-none text-slate-700 text-sm leading-relaxed mb-8">
          <div
            class="product-rich-description bg-slate-50/70 p-5 sm:p-6 rounded-2xl border border-slate-100"
            v-html="formattedDescription"
          ></div>
        </div>

        <!-- Hướng dẫn chuẩn bị file in chuẩn xưởng -->
        <div class="border-t border-slate-100 pt-6">
          <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 mb-4 flex items-center gap-2">
            <CheckCircle2 class="w-4 h-4 text-emerald-600" />
            <span>Tiêu Chuẩn Chuẩn Bị File In Tại Nhật Nguyên</span>
          </h3>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
              <span class="text-xs font-bold text-slate-900 block">1. Định dạng file in</span>
              <p class="text-[11px] text-slate-600">Khuyên dùng PDF, AI, Corel (CDR), Photoshop (PSD), TIFF hoặc file ảnh chất lượng cao.</p>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
              <span class="text-xs font-bold text-slate-900 block">2. Hệ màu chuẩn CMYK</span>
              <p class="text-[11px] text-slate-600">File thiết kế bắt buộc để hệ màu CMYK để bảo đảm độ đồng đều và không bị lệch màu khi in.</p>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
              <span class="text-xs font-bold text-slate-900 block">3. Độ phân giải ảnh</span>
              <p class="text-[11px] text-slate-600">Độ phân giải tối thiểu 300 DPI theo đúng kích thước thật 100% để bản in không vỡ hạt.</p>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
              <span class="text-xs font-bold text-slate-900 block">4. Chừa lề xén an toàn</span>
              <p class="text-[11px] text-slate-600">Chừa biên mép xén (bleed) tối thiểu 2mm mỗi cạnh và convert toàn bộ font chữ sang vector (outlines).</p>
            </div>
          </div>
        </div>
      </section>

      <!-- ================================================================= -->
      <!-- SECTION 4: QUY TRÌNH 4 BƯỚC KHÉP KÍN TẠI XƯỞNG IN NHẬT NGUYÊN       -->
      <!-- ================================================================= -->
      <section id="quy-trinh" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm mb-8 scroll-mt-28">
        <div class="pb-4 mb-6 border-b border-slate-100">
          <span class="text-[11px] font-black uppercase tracking-wider text-brand-blue block">Khép Kín & Chuyên Nghiệp</span>
          <h2 class="text-lg sm:text-xl font-black text-slate-900 mt-0.5 flex items-center gap-2">
            <Zap class="w-5 h-5 text-brand-gold" />
            <span>Quy Trình 4 Bước Đặt In Tại Xưởng In Nhật Nguyên</span>
          </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Step 1 -->
          <div class="relative p-5 rounded-2xl bg-slate-50 border border-slate-100 hover:border-brand-blue/40 transition-colors space-y-2.5">
            <span class="text-2xl font-black text-brand-blue/30 block">01</span>
            <h4 class="text-sm font-bold text-slate-900">Tiếp nhận & Báo giá</h4>
            <p class="text-xs text-slate-600 leading-relaxed">
              Tư vấn chất liệu, số lượng, kích thước tối ưu chi phí và báo giá xưởng trong 5 - 15 phút.
            </p>
          </div>

          <!-- Step 2 -->
          <div class="relative p-5 rounded-2xl bg-slate-50 border border-slate-100 hover:border-brand-blue/40 transition-colors space-y-2.5">
            <span class="text-2xl font-black text-brand-blue/30 block">02</span>
            <h4 class="text-sm font-bold text-slate-900">Kiểm tra & Duyệt mẫu</h4>
            <p class="text-xs text-slate-600 leading-relaxed">
              Kỹ thuật viên kiểm tra chuẩn màu CMYK, căn chỉnh bù xén và gửi maquette duyệt trước khi xuất kẽm in.
            </p>
          </div>

          <!-- Step 3 -->
          <div class="relative p-5 rounded-2xl bg-slate-50 border border-slate-100 hover:border-brand-blue/40 transition-colors space-y-2.5">
            <span class="text-2xl font-black text-brand-blue/30 block">03</span>
            <h4 class="text-sm font-bold text-slate-900">In & Gia công xưởng</h4>
            <p class="text-xs text-slate-600 leading-relaxed">
              In Offset/Kỹ thuật số công nghiệp, bế khuôn dán thành phẩm, cán màng và kiểm tra KCS từng lô hàng.
            </p>
          </div>

          <!-- Step 4 -->
          <div class="relative p-5 rounded-2xl bg-slate-50 border border-slate-100 hover:border-brand-blue/40 transition-colors space-y-2.5">
            <span class="text-2xl font-black text-brand-blue/30 block">04</span>
            <h4 class="text-sm font-bold text-slate-900">Đóng gói & Giao hàng</h4>
            <p class="text-xs text-slate-600 leading-relaxed">
              Đóng kiện bọc màng co cẩn thận, giao hỏa tốc TPHCM và hỗ trợ gửi chành xe đi khắp cả nước.
            </p>
          </div>
        </div>
      </section>

      <!-- ================================================================= -->
      <!-- SECTION 5: CAM KẾT CHẤT LƯỢNG & CHÍNH SÁCH BẢO HÀNH               -->
      <!-- ================================================================= -->
      <section id="cam-ket" class="bg-gradient-to-br from-slate-900 to-brand-blue-dark text-white rounded-3xl p-6 sm:p-8 shadow-xl mb-8 scroll-mt-28">
        <div class="max-w-3xl mb-6">
          <span class="text-xs font-bold uppercase tracking-wider text-brand-gold block">Uy Tín Khẳng Định Thương Hiệu</span>
          <h2 class="text-xl sm:text-2xl font-black mt-1">4 Cam Kết Vàng Từ Xưởng In Trực Tiếp Nhật Nguyên</h2>
          <p class="text-xs text-slate-300 mt-2">
            Chúng tôi cam kết mang đến những ấn phẩm chuẩn mực nhất với giá thành trực tiếp tại xưởng không qua trung gian.
          </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-xs border border-white/10 space-y-2">
            <div class="w-8 h-8 rounded-xl bg-brand-gold text-slate-950 flex items-center justify-center font-black text-sm">
              1
            </div>
            <h4 class="text-sm font-bold">Giá Gốc Tại Xưởng</h4>
            <p class="text-xs text-slate-300">Không qua trung gian thương mại, tối ưu ngân sách 15% - 30% cho khách hàng.</p>
          </div>

          <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-xs border border-white/10 space-y-2">
            <div class="w-8 h-8 rounded-xl bg-brand-gold text-slate-950 flex items-center justify-center font-black text-sm">
              2
            </div>
            <h4 class="text-sm font-bold">Chuẩn Màu 98%</h4>
            <p class="text-xs text-slate-300">Hệ thống máy in hiện đại, cân chỉnh mực chuẩn CMYK sắc nét đến từng chi tiết nhỏ.</p>
          </div>

          <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-xs border border-white/10 space-y-2">
            <div class="w-8 h-8 rounded-xl bg-brand-gold text-slate-950 flex items-center justify-center font-black text-sm">
              3
            </div>
            <h4 class="text-sm font-bold">Đổi Trả 100% Miễn Phí</h4>
            <p class="text-xs text-slate-300">In lại hoặc hoàn tiền 100% nếu sản phẩm xảy ra lỗi kỹ thuật hoặc sai khác so với mẫu duyệt.</p>
          </div>

          <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-xs border border-white/10 space-y-2">
            <div class="w-8 h-8 rounded-xl bg-brand-gold text-slate-950 flex items-center justify-center font-black text-sm">
              4
            </div>
            <h4 class="text-sm font-bold">Đúng Tiến Độ Hẹn</h4>
            <p class="text-xs text-slate-300">Hoàn thành đúng hẹn cam kết, hỗ trợ in gấp trong ngày phục vụ sự kiện, khai trương.</p>
          </div>
        </div>
      </section>

      <!-- ================================================================= -->
      <!-- SECTION 6: CÂU HỎI THƯỜNG GẶP (FAQ ACCORDION)                     -->
      <!-- ================================================================= -->
      <section id="faq" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm mb-8 scroll-mt-28">
        <div class="pb-4 mb-6 border-b border-slate-100">
          <span class="text-[11px] font-black uppercase tracking-wider text-brand-blue block">Giải Đáp Nhanh</span>
          <h2 class="text-lg sm:text-xl font-black text-slate-900 mt-0.5 flex items-center gap-2">
            <HelpCircle class="w-5 h-5 text-brand-blue" />
            <span>Câu Hỏi Thường Gặp Về In {{ product.name }}</span>
          </h2>
        </div>

        <div class="divide-y divide-slate-100">
          <div
            v-for="(faq, fIdx) in faqs"
            :key="fIdx"
            class="py-4"
          >
            <button
              type="button"
              @click="toggleFaq(fIdx)"
              class="w-full flex items-center justify-between text-left gap-4 font-bold text-sm text-slate-800 hover:text-brand-blue transition-colors cursor-pointer"
            >
              <span>{{ faq.question }}</span>
              <ChevronDown
                :class="[
                  'w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0',
                  activeFaqIndex === fIdx ? 'rotate-180 text-brand-blue' : ''
                ]"
              />
            </button>
            <div
              v-show="activeFaqIndex === fIdx"
              class="mt-2.5 text-xs text-slate-600 leading-relaxed pr-6"
            >
              {{ faq.answer }}
            </div>
          </div>
        </div>
      </section>

      <!-- ================================================================= -->
      <!-- SECTION 7: KHỐI LIÊN HỆ CHUYÊN VIÊN TƯ VẤN NHANH                  -->
      <!-- ================================================================= -->
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-blue-200 bg-blue-50/20 shadow-sm mb-12 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-1.5 text-center md:text-left">
          <span class="text-xs font-bold text-brand-blue uppercase tracking-wider">Hỗ Trợ Kỹ Thuật Trực Tiếp 24/7</span>
          <h3 class="text-lg sm:text-xl font-black text-slate-900">
            Bạn cần tư vấn chất liệu, mẫu mã riêng hoặc in số lượng lớn?
          </h3>
          <p class="text-xs text-slate-600 max-w-xl">
            Đội ngũ kỹ thuật viên và nhân viên kinh doanh của Xưởng In Nhật Nguyên luôn sẵn sàng hỗ trợ kiểm tra file in và tư vấn quy cách tiết kiệm nhất.
          </p>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-3 shrink-0">
          <a
            :href="'tel:' + siteSettings.hotlines[0].number.replace(/\s+/g, '')"
            class="px-5 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center gap-2 transition-colors shadow-md"
          >
            <PhoneCall class="w-4 h-4 text-brand-gold" />
            <span>{{ siteSettings.hotlines[0].number }} ({{ siteSettings.hotlines[0].nvkd }})</span>
          </a>
          <a
            :href="siteSettings.zalo"
            target="_blank"
            rel="noopener"
            class="px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center gap-2 transition-colors shadow-md"
          >
            <MessageCircle class="w-4 h-4" />
            <span>Chat Zalo Ngay</span>
          </a>
        </div>
      </div>

      <!-- ================================================================= -->
      <!-- SECTION 8: ẤN PHẨM LIÊN QUAN                                      -->
      <!-- ================================================================= -->
      <div v-if="relatedProducts.length > 0" class="pt-8 border-t border-slate-200/80">
        <h3 class="text-xl font-black text-slate-900 mb-6">
          Ấn phẩm liên quan cùng danh mục
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
          <ProductCard
            v-for="p in relatedProducts"
            :key="p.id"
            :product="p"
          />
        </div>
      </div>
    </div>
  </div>

  <!-- 3. Not Found Empty State -->
  <div v-else class="py-20 bg-slate-50 min-h-screen flex items-center justify-center">
    <div class="text-center bg-white rounded-3xl p-8 border border-slate-200/90 max-w-md mx-auto shadow-xs">
      <h2 class="text-lg font-bold text-slate-800 mb-2">Ấn phẩm đang được cập nhật</h2>
      <p class="text-xs text-slate-500 mb-6">Sản phẩm này chưa được đưa lên hệ thống hoặc đang trong quá trình cập nhật bảng giá.</p>
      <div class="flex justify-center gap-3">
        <router-link to="/san-pham" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
          Xem các ấn phẩm khác
        </router-link>
        <router-link to="/yeu-cau-bao-gia" class="px-5 py-2.5 rounded-xl bg-brand-gold text-slate-950 font-bold text-xs">
          Báo giá riêng
        </router-link>
      </div>
    </div>
  </div>

  <!-- ================================================================= -->
  <!-- 4. FLOATING STICKY ACTION BAR ON MOBILE / TABLET                  -->
  <!-- ================================================================= -->
  <transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="transform translate-y-full opacity-0"
    enter-to-class="transform translate-y-0 opacity-100"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="transform translate-y-0 opacity-100"
    leave-to-class="transform translate-y-full opacity-0"
  >
    <div
      v-if="showStickyBar && product"
      class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/90 px-4 py-2.5 shadow-2xl flex items-center justify-between gap-3 lg:hidden"
    >
      <div class="flex items-center gap-2.5 min-w-0">
        <img
          :src="product.image || '/logo-icon.png'"
          :alt="product.name"
          class="w-10 h-10 object-cover rounded-lg border border-slate-200 shrink-0"
        />
        <div class="min-w-0">
          <span class="text-xs font-bold text-slate-900 block truncate">{{ product.name }}</span>
          <span class="text-[11px] font-black text-brand-blue block">
            Từ {{ formatCurrency(product.basePrice || product.base_price || 0) }} / {{ product.unit || 'cái' }}
          </span>
        </div>
      </div>

      <div class="flex items-center gap-2 shrink-0">
        <a
          :href="siteSettings.zalo"
          target="_blank"
          rel="noopener"
          class="p-2.5 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 hover:bg-blue-100 transition-colors"
          title="Chat Zalo"
        >
          <MessageCircle class="w-4 h-4" />
        </a>
        <button
          type="button"
          @click="scrollToCalculator"
          class="px-4 py-2.5 rounded-xl bg-brand-gold hover:bg-brand-gold-hover text-slate-950 font-black text-xs shadow-md transition-all cursor-pointer"
        >
          Tính giá & Đặt in
        </button>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import {
  CheckCircle2, PhoneCall, FileCheck, FileText, Sliders,
  ShieldCheck, Sparkles, Truck, Clock, HelpCircle, ChevronDown,
  MessageCircle, Zap
} from 'lucide-vue-next'
import { siteSettings } from '../data/siteSettings'
import PriceCalculator from '../components/products/PriceCalculator.vue'
import ProductCard from '../components/products/ProductCard.vue'
import { api } from '../services/api'
import { setMetaTags } from '../composables/useSEO'

const route = useRoute()
const isLoading = ref(true)
const product = ref(null)
const allProducts = ref([])
const activeImage = ref('')
const activeFaqIndex = ref(0)
const showStickyBar = ref(false)

const subNavItems = [
  { id: 'thong-so-ky-thuat', label: 'Thông số kỹ thuật', icon: Sliders },
  { id: 'bang-bao-gia', label: 'Bảng báo giá tham khảo', icon: FileCheck },
  { id: 'mo-ta-chi-tiet', label: 'Chi tiết & Mô tả', icon: FileText },
  { id: 'quy-trinh', label: 'Quy trình 4 bước', icon: Zap },
  { id: 'cam-ket', label: 'Cam kết chất lượng', icon: ShieldCheck },
  { id: 'faq', label: 'Câu hỏi thường gặp', icon: HelpCircle }
]

const faqs = [
  {
    question: '1. Tôi chưa có file thiết kế xưởng có hỗ trợ không?',
    answer: 'Có. Đội ngũ thiết kế của Xưởng In Nhật Nguyên hỗ trợ kiểm tra file miễn phí hoặc thiết kế mới theo ý tưởng, bộ nhận diện thương hiệu của quý khách với chi phí hợp lý nhất.'
  },
  {
    question: '2. Xưởng có nhận in số lượng ít không?',
    answer: 'Có. Nhật Nguyên trang bị cả hệ thống máy in Kỹ thuật số công nghiệp (nhận in từ số lượng ít, lấy nhanh) và dàn máy in Offset công nghiệp (chuyên cho số lượng lớn, giá cực rẻ).'
  },
  {
    question: '3. Xưởng có in test mẫu để duyệt trước khi sản xuất hàng loạt không?',
    answer: 'Có. Với các đơn hàng in ấn theo yêu cầu hoặc số lượng lớn, xưởng đều gửi maquette điện tử duyệt màu kỹ thuật hoặc in test mẫu thực tế trước khi bấm máy sản xuất hàng loạt.'
  },
  {
    question: '4. Thời gian sản xuất và giao hàng mất bao lâu?',
    answer: 'Tùy loại ấn phẩm và số lượng, thông thường từ 4h - 24h làm việc. Với các đơn hàng sự kiện cần gấp, xưởng có dịch vụ in nhanh lấy trong ngày.'
  },
  {
    question: '5. Khách hàng ở các tỉnh thành xưởng hỗ trợ giao hàng thế nào?',
    answer: 'Xưởng đóng thùng carton bọc màng co cẩn thận và hỗ trợ gửi hàng ra các chành xe quen của khách, các bến xe lớn (Miền Đông, Miền Tây, An Sương) hoặc chuyển phát nhanh Viettel Post, GHTK giao tận nơi.'
  }
]

function toggleFaq(index) {
  activeFaqIndex.value = activeFaqIndex.value === index ? -1 : index
}

function handleScroll() {
  showStickyBar.value = window.scrollY > 450
}

function scrollToCalculator() {
  const el = document.getElementById('calculator-section')
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
  }
}

const galleryImages = computed(() => {
  if (!product.value) return []
  const list = []
  if (product.value.image) list.push(product.value.image)
  if (Array.isArray(product.value.gallery)) {
    product.value.gallery.forEach(img => {
      if (img && !list.includes(img)) list.push(img)
    })
  } else if (typeof product.value.gallery === 'string') {
    try {
      const parsed = JSON.parse(product.value.gallery)
      if (Array.isArray(parsed)) {
        parsed.forEach(img => {
          if (img && !list.includes(img)) list.push(img)
        })
      }
    } catch (e) {}
  }
  return list
})

const availableSizesSummary = computed(() => {
  const sizes = product.value?.specs?.sizes
  if (Array.isArray(sizes) && sizes.length > 0) {
    return sizes.map(s => s.label).join(', ')
  }
  return ''
})

const availableMaterialsSummary = computed(() => {
  const mats = product.value?.specs?.materials
  if (Array.isArray(mats) && mats.length > 0) {
    return mats.map(m => m.label).join(', ')
  }
  return ''
})

const availableFinishingsSummary = computed(() => {
  const fins = product.value?.specs?.finishings
  if (Array.isArray(fins) && fins.length > 0) {
    return fins.map(f => f.label).join(', ')
  }
  return ''
})

const customSpecsList = computed(() => {
  const cs = product.value?.specs?.custom_specs
  if (Array.isArray(cs)) {
    return cs.filter(s => s && (s.name || s.value))
  }
  return []
})

const defaultMaterialSummary = computed(() => {
  const nameLower = (product.value?.name || '').toLowerCase()
  if (nameLower.includes('decal') || nameLower.includes('tem') || nameLower.includes('sticker')) {
    return 'Decal chuyên dụng (Decal lưới dán kính, decal sữa, decal trong...)'
  }
  if (nameLower.includes('túi')) {
    return 'Giấy Kraft nâu, Couche, Ivory theo yêu cầu'
  }
  if (nameLower.includes('hộp')) {
    return 'Giấy Ivory, Duplex bồi carton sóng cao cấp'
  }
  return 'Giấy Fort, Couche, Ivory, Kraft theo yêu cầu'
})

const defaultTechSummary = computed(() => {
  const nameLower = (product.value?.name || '').toLowerCase()
  if (nameLower.includes('decal') || nameLower.includes('bạt') || nameLower.includes('standee')) {
    return 'In Kỹ thuật số UV / In phun khổ lớn chất lượng cao'
  }
  return 'In Offset công nghiệp 4 màu CMYK / In Kỹ thuật số'
})

function formatCurrency(val) {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val || 0)
}

function stripHtml(html) {
  if (!html) return ''
  return String(html).replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim()
}

const formattedDescription = computed(() => {
  const raw = product.value?.description
  if (!raw || !String(raw).trim()) {
    return '<p>Sản phẩm in ấn chất lượng cao trực tiếp tại Xưởng In Nhật Nguyên với trang thiết bị hiện đại, chuẩn màu và sắc nét.</p>'
  }
  const str = String(raw).trim()
  if (/<[a-z][\s\S]*>/i.test(str)) {
    return str
  }
  return str
    .split(/\n{2,}/)
    .map(p => `<p>${p.replace(/\n/g, '<br>')}</p>`)
    .join('')
})

function applyProductSEO(prod) {
  if (!prod) return
  const plainDesc = stripHtml(prod.description)
  setMetaTags({
    title: prod.meta_title || `${prod.name} - Giá Gốc Xưởng In Nhật Nguyên`,
    description: prod.meta_description || plainDesc || 'Xưởng in Offset & Kỹ thuật số Nhật Nguyên — Chuyên sản xuất ấn phẩm bao bì, hộp giấy, túi giấy chất lượng cao, giá gốc trực tiếp.',
    keywords: prod.meta_keywords || `${prod.name}, in bao bi, xuong in nhat nguyen, in gia goc`,
    image: prod.image,
    url: prod.canonical_url || window.location.href,
    type: 'product',
    schema: {
      '@context': 'https://schema.org',
      '@type': 'Product',
      name: prod.name,
      description: prod.meta_description || plainDesc || 'Sản phẩm in ấn chất lượng cao tại Xưởng In Nhật Nguyên',
      image: prod.image ? (prod.image.startsWith('http') ? prod.image : `https://dichvuinnhatnguyen.vn${prod.image}`) : 'https://dichvuinnhatnguyen.vn/logo.png',
      sku: `NN-${prod.id || prod.slug || 'IN-AN'}`,
      mpn: `NN-${prod.id || 'PRODUCT'}`,
      brand: {
        '@type': 'Brand',
        name: 'In Ấn Nhật Nguyên'
      },
      aggregateRating: {
        '@type': 'AggregateRating',
        ratingValue: '4.9',
        reviewCount: '156',
        bestRating: '5',
        worstRating: '1'
      },
      offers: {
        '@type': 'Offer',
        price: (prod.base_price && prod.base_price > 0) ? prod.base_price : 500,
        priceCurrency: 'VND',
        priceValidUntil: '2027-12-31',
        itemCondition: 'https://schema.org/NewCondition',
        availability: 'https://schema.org/InStock',
        url: prod.canonical_url || window.location.href,
        seller: {
          '@type': 'Organization',
          name: 'Xưởng In Ấn Nhật Nguyên'
        }
      }
    }
  })
}

async function loadProduct() {
  const slug = route.params.slug
  isLoading.value = true
  try {
    const res = await api.getProductBySlug(slug)
    if (res?.data) {
      const p = res.data
      let pSpecs = p.specs
      if (typeof pSpecs === 'string') {
        try { pSpecs = JSON.parse(pSpecs) } catch (e) { pSpecs = {} }
      }
      pSpecs = (pSpecs && typeof pSpecs === 'object') ? pSpecs : {}

      product.value = {
        ...p,
        basePrice: p.base_price,
        categoryId: p.category?.slug || p.category_id,
        categoryName: p.category?.name || 'Ấn phẩm in',
        minQuantity: p.min_quantity || 1,
        specs: pSpecs,
        features: p.features || [
          'In sắc nét trên máy Offset & Kỹ thuật số công nghiệp',
          'Miễn phí kiểm tra file in & chuẩn màu CMYK',
          'Giao hàng tận nơi toàn quốc, hỗ trợ gửi chành xe'
        ]
      }
      activeImage.value = p.image || ''
      applyProductSEO(product.value)
      return
    }
  } catch (err) {
    // API error
  } finally {
    isLoading.value = false
  }
  product.value = null
}

onMounted(() => {
  loadProduct()
  window.addEventListener('scroll', handleScroll, { passive: true })
  api.getProducts().then(res => {
    if (res?.data?.length) {
      allProducts.value = res.data
    }
  }).catch(() => {})
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})

watch(() => route.params.slug, () => {
  loadProduct()
  window.scrollTo({ top: 0, behavior: 'smooth' })
})

const relatedProducts = computed(() => {
  if (!product.value) return []
  return allProducts.value.filter(p => p.id !== product.value.id && p.slug !== product.value.slug).slice(0, 3)
})
</script>

<style scoped>
/* Ẩn thanh cuộn ngang nhưng vẫn cuộn được cho sub-nav */
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.product-rich-description :deep(h1) {
  font-size: 1.45rem;
  font-weight: 900;
  color: #0f172a;
  margin-top: 1.35rem;
  margin-bottom: 0.65rem;
}

.product-rich-description :deep(h2) {
  font-size: 1.25rem;
  font-weight: 800;
  color: #0f172a;
  margin-top: 1.25rem;
  margin-bottom: 0.6rem;
}

.product-rich-description :deep(h3) {
  font-size: 1.05rem;
  font-weight: 700;
  color: #1e293b;
  margin-top: 1rem;
  margin-bottom: 0.5rem;
}

.product-rich-description :deep(h4) {
  font-size: 0.95rem;
  font-weight: 700;
  color: #1e293b;
  margin-top: 0.85rem;
  margin-bottom: 0.4rem;
}

.product-rich-description :deep(p) {
  margin-bottom: 0.75rem;
  line-height: 1.75;
}

.product-rich-description :deep(ul) {
  list-style-type: disc;
  padding-left: 1.4rem;
  margin-bottom: 0.85rem;
}

.product-rich-description :deep(ol) {
  list-style-type: decimal;
  padding-left: 1.4rem;
  margin-bottom: 0.85rem;
}

.product-rich-description :deep(li) {
  margin-bottom: 0.35rem;
}

.product-rich-description :deep(blockquote) {
  border-left: 4px solid #f59e0b;
  padding: 0.75rem 1rem;
  margin: 1rem 0;
  font-style: italic;
  background: #fffbeb;
  border-radius: 0 0.75rem 0.75rem 0;
}

.product-rich-description :deep(table) {
  width: 100%;
  border-collapse: collapse;
  margin: 1rem 0;
}

.product-rich-description :deep(th),
.product-rich-description :deep(td) {
  border: 1px solid #cbd5e1;
  padding: 0.55rem 0.8rem;
}

.product-rich-description :deep(img) {
  max-width: 100%;
  height: auto;
  border-radius: 1rem;
  margin: 1rem auto;
  display: block;
}
</style>
