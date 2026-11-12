<template>
  <div class="py-6 lg:py-10 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      
      <!-- 1. Breadcrumb & Header Section -->
      <div class="space-y-4">
        <!-- Breadcrumb -->
        <nav class="flex items-center text-xs text-slate-500 space-x-2">
          <router-link to="/" class="hover:text-brand-blue flex items-center gap-1 transition-colors">
            <Home class="w-3.5 h-3.5" />
            <span>Trang chủ</span>
          </router-link>
          <span>/</span>
          <router-link
            to="/san-pham"
            :class="isRootCatalogView && !searchKeyword ? 'text-slate-900 font-bold' : 'hover:text-brand-blue transition-colors'"
            @click="resetToCatalogRoot"
          >
            Danh mục sản phẩm
          </router-link>
          <template v-if="!isRootCatalogView && currentCategory">
            <span>/</span>
            <span class="text-brand-blue font-bold">{{ currentCategory.name }}</span>
          </template>
          <template v-else-if="searchKeyword">
            <span>/</span>
            <span class="text-brand-blue font-bold">Tìm kiếm: "{{ searchKeyword }}"</span>
          </template>
        </nav>

        <!-- Main Title & Stats -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pt-1">
          <div>
            <span class="text-xs font-bold text-brand-gold uppercase tracking-wider block mb-1">
              {{ isRootCatalogView && !searchKeyword ? 'TỔNG HỢP DANH MỤC ẤN PHẨM TRỰC TIẾP TẠI XƯỞNG' : 'DANH MỤC ẤN PHẨM CHI TIẾT' }}
            </span>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight flex items-center flex-wrap gap-3">
              <span>{{ headerTitle }}</span>
              <span
                v-if="!isRootCatalogView || searchKeyword"
                class="text-xs font-bold px-3 py-1 rounded-full bg-brand-blue/10 text-brand-blue border border-brand-blue/20"
              >
                {{ filteredAndSortedProducts.length }} ấn phẩm
              </span>
              <span
                v-else
                class="text-xs font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200"
              >
                {{ categoriesWithMeta.length }} ngành hàng in ấn
              </span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-2 max-w-2xl leading-relaxed">
              {{ headerDescription }}
            </p>
          </div>

          <!-- Quick Trust Badges -->
          <div class="flex items-center gap-2 flex-wrap">
            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-700 bg-white px-3 py-1.5 rounded-lg border border-slate-200/90 shadow-2xs">
              <ShieldCheck class="w-3.5 h-3.5 text-emerald-600" />
              Giá gốc tận xưởng
            </span>
            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-700 bg-white px-3 py-1.5 rounded-lg border border-slate-200/90 shadow-2xs">
              <Zap class="w-3.5 h-3.5 text-amber-500" />
              In gấp lấy liền
            </span>
            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-700 bg-white px-3 py-1.5 rounded-lg border border-slate-200/90 shadow-2xs">
              <Sparkles class="w-3.5 h-3.5 text-brand-blue" />
              Free check file in
            </span>
          </div>
        </div>
      </div>

      <!-- 2. Filters & Search Toolbar -->
      <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-xs space-y-3">
        
        <!-- Category Tab Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
          <button
            type="button"
            @click="selectCategory('all')"
            :class="[
              'px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all duration-200 flex items-center gap-1.5 shrink-0 cursor-pointer',
              activeCategory === 'all' && !searchKeyword
                ? 'bg-brand-blue text-white shadow-md'
                : 'bg-slate-100/90 text-slate-700 hover:bg-slate-200/80 hover:text-slate-900'
            ]"
          >
            <LayoutGrid class="w-3.5 h-3.5" />
            <span>Tất cả danh mục</span>
            <span
              :class="[
                'text-[10px] px-1.5 py-0.5 rounded-full font-bold',
                activeCategory === 'all' && !searchKeyword ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600'
              ]"
            >
              {{ categoriesWithMeta.length }}
            </span>
          </button>

          <button
            v-for="cat in visibleFilterCategories"
            :key="cat.id"
            type="button"
            @click="selectCategory(cat.slug)"
            :class="[
              'px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all duration-200 flex items-center gap-1.5 shrink-0 cursor-pointer',
              activeCategory === cat.slug
                ? 'bg-brand-blue text-white shadow-md'
                : 'bg-slate-100/90 text-slate-700 hover:bg-slate-200/80 hover:text-slate-900'
            ]"
          >
            <span>{{ cat.name }}</span>
            <span
              :class="[
                'text-[10px] px-1.5 py-0.5 rounded-full font-bold',
                activeCategory === cat.slug ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600'
              ]"
            >
              {{ getCategoryProductCount(cat.slug) }}
            </span>
          </button>
        </div>

        <!-- Sub Toolbar: Quick tags + Search input + Sort Dropdown -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pt-3 border-t border-slate-100 text-xs">
          
          <!-- Back button or Quick Filter Tags -->
          <div class="flex items-center gap-2 overflow-x-auto pb-1 lg:pb-0">
            <!-- Back to All Categories button when viewing a specific category -->
            <button
              v-if="!isRootCatalogView || searchKeyword"
              @click="resetToCatalogRoot"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs cursor-pointer transition-colors border border-slate-200 shrink-0"
            >
              <ArrowLeft class="w-3.5 h-3.5" />
              <span>← Xem tất cả danh mục</span>
            </button>

            <span class="text-slate-400 text-[11px] font-semibold whitespace-nowrap hidden sm:inline">Lọc nhanh:</span>
            <button
              v-for="tag in quickTags"
              :key="tag.value"
              type="button"
              @click="activeQuickTag = activeQuickTag === tag.value ? '' : tag.value"
              :class="[
                'px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all cursor-pointer border',
                activeQuickTag === tag.value
                  ? 'bg-brand-gold/15 text-slate-950 border-brand-gold font-bold shadow-2xs'
                  : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100'
              ]"
            >
              {{ tag.label }}
            </button>

            <button
              v-if="activeQuickTag || searchKeyword || activeCategory !== 'all'"
              @click="clearFilters"
              class="text-xs text-rose-600 hover:text-rose-700 font-semibold px-2 py-1 underline cursor-pointer whitespace-nowrap"
            >
              Đặt lại
            </button>
          </div>

          <!-- Search & Sort Controls -->
          <div class="flex items-center gap-2.5 w-full lg:w-auto">
            <!-- Search Input -->
            <div class="relative flex-1 lg:w-64">
              <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
              <input
                v-model="searchKeyword"
                type="text"
                placeholder="Tìm tên sản phẩm (namecard, túi...)"
                class="w-full pl-9 pr-8 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue bg-slate-50/50 focus:bg-white transition-colors"
                @focus="searchKeyword = ''"
              />
              <button
                v-if="searchKeyword"
                @click="searchKeyword = ''"
                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs font-bold p-1 cursor-pointer"
                title="Xóa tìm kiếm"
              >
                ✕
              </button>
            </div>

            <!-- Sort Select (Only active in product list view) -->
            <div v-if="!isRootCatalogView || searchKeyword" class="relative shrink-0">
              <select
                v-model="sortBy"
                class="appearance-none bg-slate-50/50 hover:bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 pr-7 text-xs font-semibold text-slate-700 cursor-pointer focus:outline-none focus:border-brand-blue transition-colors"
              >
                <option value="default">Sắp xếp: Mặc định</option>
                <option value="price-asc">Giá: Thấp đến Cao</option>
                <option value="price-desc">Giá: Cao đến Thấp</option>
                <option value="name-asc">Tên: A &rarr; Z</option>
              </select>
              <ArrowUpDown class="w-3.5 h-3.5 text-slate-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" />
            </div>
          </div>
        </div>
      </div>

      <!-- 3. PHƯƠNG ÁN 1: MẶC ĐỊNH HIỂN THỊ LƯỚI DANH MỤC LỚN (CATEGORY CARDS GRID) -->
      <template v-if="isRootCatalogView && !searchKeyword && !activeQuickTag">
        <!-- Skeleton loading danh mục khi đang tải -->
        <div v-if="isLoading" class="space-y-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 animate-pulse">
            <div v-for="n in 6" :key="n" class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden space-y-4 p-4 shadow-2xs">
              <div class="aspect-[16/9] w-full bg-slate-200/80 rounded-xl"></div>
              <div class="h-4 w-1/2 bg-slate-200 rounded"></div>
              <div class="h-3 w-4/5 bg-slate-200/70 rounded"></div>
              <div class="h-9 w-full bg-slate-100 rounded-xl"></div>
            </div>
          </div>
        </div>

        <div v-else class="space-y-4">
          <div class="flex items-center justify-between">
            <h2 class="text-sm sm:text-base font-bold text-slate-800 flex items-center gap-2">
              <FolderKanban class="w-4 h-4 text-brand-blue" />
              <span>Chọn danh mục ngành hàng bạn cần in ấn:</span>
            </h2>
            <span class="text-xs text-slate-500">
              Nhấp vào danh mục để xem các quy cách và bảng giá chi tiết
            </span>
          </div>

          <!-- Lưới các Card Danh Mục Ngành Hàng Lớn có Hình Ảnh Mockup Thực Tế từ Quản Lý Danh Mục -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="cat in categoriesWithMeta"
              :key="cat.id"
              class="group bg-white rounded-2xl border border-slate-200/90 hover:border-brand-blue/60 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden"
            >
              <div>
                <!-- 1. Hình ảnh Mockup đại diện Danh mục (Lấy trực tiếp từ trường Image của Admin) -->
                <router-link
                  :to="'/danh-muc/' + cat.slug"
                  class="block aspect-[16/9] w-full overflow-hidden bg-slate-100 relative group cursor-pointer"
                >
                  <img
                    v-if="cat.image"
                    :src="cat.image"
                    :alt="cat.name"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    loading="lazy"
                  />
                  <!-- Fallback nếu danh mục chưa upload ảnh -->
                  <div
                    v-else
                    class="w-full h-full bg-gradient-to-br from-slate-100 via-slate-50 to-slate-200 flex flex-col items-center justify-center text-slate-400 group-hover:scale-105 transition-transform duration-500"
                  >
                    <component :is="getCategoryIcon(cat.icon)" class="w-12 h-12 stroke-[1.4] text-slate-400" />
                    <span class="text-[11px] font-semibold text-slate-500 mt-2">Xưởng In Nhật Nguyên</span>
                  </div>

                  <!-- Overlay gradient nhẹ phía dưới ảnh -->
                  <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>

                  <!-- Badge nổi bật đè góc trên ảnh -->
                  <div class="absolute top-3 left-3 flex items-center gap-1.5 flex-wrap">
                    <span
                      v-if="cat.badge"
                      class="text-[10px] font-bold px-2.5 py-1 rounded-lg bg-brand-gold text-slate-950 shadow-md uppercase tracking-wider"
                    >
                      {{ cat.badge }}
                    </span>
                  </div>

                  <!-- Số lượng ấn phẩm góc dưới ảnh -->
                  <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-white text-xs">
                    <span class="font-bold px-2 py-0.5 rounded-md bg-black/60 backdrop-blur-xs text-[11px]">
                      {{ cat.productCount > 0 ? `${cat.productCount} ấn phẩm sẵn có` : 'Nhận in theo yêu cầu' }}
                    </span>
                    <span class="text-[11px] font-semibold text-amber-300 flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                      <span>Xem bảng giá</span>
                      <ArrowRight class="w-3 h-3" />
                    </span>
                  </div>
                </router-link>

                <!-- 2. Nội dung text bên dưới ảnh -->
                <div class="p-5 space-y-3">
                  <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-brand-blue/10 text-brand-blue flex items-center justify-center shrink-0">
                      <component :is="getCategoryIcon(cat.icon)" class="w-4 h-4 stroke-[1.8]" />
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900 group-hover:text-brand-blue transition-colors line-clamp-1">
                      <router-link :to="'/danh-muc/' + cat.slug" class="focus:outline-none">
                        {{ cat.name }}
                      </router-link>
                    </h3>
                  </div>

                  <p class="text-xs text-slate-600 leading-relaxed line-clamp-2">
                    {{ stripHtml(cat.meta_description || cat.description) }}
                  </p>

                  <!-- Liệt kê các ấn phẩm tiêu biểu trong danh mục -->
                  <div class="pt-3 border-t border-slate-100">
                    <span class="text-[11px] font-semibold text-slate-400 block mb-2">Ấn phẩm tiêu biểu:</span>
                    <div class="flex flex-wrap gap-1.5">
                      <template v-if="cat.sampleProducts && cat.sampleProducts.length > 0">
                        <router-link
                          v-for="p in cat.sampleProducts"
                          :key="p.id"
                          :to="'/san-pham/' + p.slug"
                          class="text-[11px] font-medium px-2 py-1 rounded-md bg-slate-50 hover:bg-brand-blue hover:text-white text-slate-700 border border-slate-200/80 transition-colors"
                        >
                          {{ p.name }}
                        </router-link>
                      </template>
                      <template v-else>
                        <span class="text-[11px] text-slate-400 italic">
                          Xưởng nhận gia công quy cách riêng theo file thiết kế
                        </span>
                      </template>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 3. Category Action Footer Button -->
              <div class="p-5 pt-0">
                <router-link
                  v-if="cat.productCount > 0"
                  :to="'/danh-muc/' + cat.slug"
                  class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-100 group-hover:bg-brand-blue group-hover:text-white text-slate-800 font-bold text-xs transition-all duration-200"
                >
                  <span>Xem {{ cat.productCount }} ấn phẩm chi tiết</span>
                  <ArrowRight class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                </router-link>
                <router-link
                  v-else
                  :to="'/yeu-cau-bao-gia?product=' + encodeURIComponent(cat.name)"
                  class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 font-bold text-xs transition-all duration-200"
                >
                  <span>Báo giá riêng 15 phút</span>
                  <ArrowRight class="w-3.5 h-3.5" />
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- Section: Tất cả ấn phẩm nổi bật bên dưới danh mục - Thiết kế Minimalist gọn đẹp theo ảnh mẫu -->
        <div class="mt-12 pt-8 border-t border-slate-200/80 space-y-5">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
              <h2 class="text-base sm:text-lg font-black text-slate-900 flex items-center gap-2">
                <Sparkles class="w-4 h-4 text-brand-gold" />
                <span>Ấn Phẩm Được Đặt Hàng Nhiều Nhất</span>
              </h2>
              <p class="text-xs text-slate-500 mt-0.5">Nhấp vào ấn phẩm để xem thông số và bảng giá tự động</p>
            </div>
            <span class="text-xs font-bold text-slate-500">
              {{ products.length }} ấn phẩm sẵn có
            </span>
          </div>

          <!-- Grid card nhỏ gọn (5 cột trên màn lớn, 4 cột màn vừa, 2 cột mobile) theo phong cách ảnh 2 -->
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-5">
            <router-link
              v-for="p in products"
              :key="p.id"
              :to="'/san-pham/' + p.slug"
              class="group flex flex-col cursor-pointer"
            >
              <!-- Khung ảnh bo tròn mềm mại chuẩn ảnh mẫu -->
              <div class="aspect-square w-full rounded-2xl overflow-hidden bg-slate-100 border border-slate-200/80 shadow-2xs group-hover:shadow-md group-hover:border-brand-blue/50 transition-all duration-300 relative">
                <img
                  :src="p.image"
                  :alt="p.name"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                  loading="lazy"
                />
                <!-- Huy hiệu tag nhỏ gọn nếu có -->
                <span
                  v-if="p.tag"
                  class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-black/60 backdrop-blur-xs text-[10px] font-bold text-white shadow-2xs"
                >
                  {{ p.tag }}
                </span>
              </div>

              <!-- Nội dung chữ tối giản, sạch sẽ bên dưới -->
              <div class="mt-2.5 space-y-1">
                <!-- Tên danh mục (màu xám nhạt) -->
                <span class="text-[11px] sm:text-xs text-slate-400 font-medium block truncate">
                  {{ p.categoryName || 'Ấn phẩm in ấn' }}
                </span>

                <!-- Tên ấn phẩm (in hoa / đậm chuẩn như ảnh 2: DANH THIẾP KỸ THUẬT SỐ) -->
                <h4 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-brand-blue transition-colors line-clamp-1 leading-snug uppercase">
                  {{ p.name }}
                </h4>

                <!-- Giá tiền gọn gàng màu xanh ngọc / emerald chuẩn như ảnh 2 (Chỉ từ ...đ/hộp) -->
                <div class="text-xs sm:text-sm font-bold text-emerald-700 pt-0.5">
                  <template v-if="p.specs?.hide_pricing">
                    <span>Liên hệ báo giá</span>
                  </template>
                  <template v-else>
                    <span>Chỉ từ {{ formatPrice(p.basePrice || p.base_price) }}đ</span>
                    <span v-if="p.unit" class="text-slate-400 font-normal text-[11px]">/{{ p.unit }}</span>
                  </template>
                </div>
              </div>
            </router-link>
          </div>
        </div>
      </template>

      <!-- 4. KHI KHÁCH ĐÃ CHỌN 1 DANH MỤC CỤ THỂ HOẶC TÌM KIẾM: HIỂN THỊ LƯỚI ẤN PHẨM CỦA DANH MỤC ĐÓ -->
      <template v-else>
        <!-- Category header banner when browsing a category -->
        <div
          v-if="currentCategory && !searchKeyword"
          class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs flex flex-col sm:flex-row sm:items-start justify-between gap-4"
        >
          <div class="flex items-start gap-4 flex-1 min-w-0">
            <div class="w-12 h-12 rounded-xl bg-brand-blue/10 text-brand-blue flex items-center justify-center shrink-0">
              <component :is="getCategoryIcon(currentCategory.icon)" class="w-6 h-6 stroke-[1.8]" />
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <h1 class="text-lg font-black text-slate-900">{{ currentCategory.name }}</h1>
                <span
                  v-if="currentCategory.badge"
                  class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200"
                >
                  {{ currentCategory.badge }}
                </span>
              </div>

              <!-- Category Description with Rich HTML Support -->
              <div v-if="currentCategory.description" class="mt-2">
                <div
                  class="category-description-html text-xs text-slate-600 leading-relaxed max-w-3xl overflow-hidden transition-all duration-300 relative"
                  :class="{ 'max-h-20': !isCategoryDescExpanded && isLongCategoryDesc }"
                >
                  <div v-html="currentCategory.description"></div>
                  <div
                    v-if="!isCategoryDescExpanded && isLongCategoryDesc"
                    class="absolute inset-x-0 bottom-0 h-8 bg-gradient-to-t from-white via-white/80 to-transparent pointer-events-none"
                  ></div>
                </div>

                <button
                  v-if="isLongCategoryDesc"
                  type="button"
                  @click="isCategoryDescExpanded = !isCategoryDescExpanded"
                  class="mt-1.5 text-[11px] font-bold text-brand-blue hover:text-brand-blue-dark inline-flex items-center gap-1 cursor-pointer transition-colors"
                >
                  <span>{{ isCategoryDescExpanded ? 'Thu gọn giới thiệu' : 'Xem thêm giới thiệu chi tiết' }}</span>
                  <ChevronDown class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': isCategoryDescExpanded }" />
                </button>
              </div>
            </div>
          </div>

          <div class="shrink-0 flex items-center gap-2 self-start sm:self-center">
            <router-link
              :to="'/yeu-cau-bao-gia?product=' + encodeURIComponent(currentCategory.name)"
              class="px-4 py-2 rounded-xl bg-brand-gold hover:bg-brand-gold-hover text-slate-950 font-bold text-xs shadow-2xs transition-all flex items-center gap-1.5"
            >
              <span>Yêu cầu quy cách riêng</span>
              <ArrowRight class="w-3.5 h-3.5" />
            </router-link>
          </div>
        </div>

        <!-- SKELETON LOADING STATE KHI TÌM KIẾM / CHUYỂN DANH MỤC -->
        <div v-if="isLoading" class="space-y-4">
          <!-- Status Pill thông báo đang truy vấn hệ thống -->
          <div class="flex items-center gap-3 p-4 rounded-2xl bg-white border border-brand-blue/20 shadow-xs">
            <div class="w-8 h-8 rounded-xl bg-brand-blue/10 flex items-center justify-center shrink-0">
              <Loader2 class="w-4 h-4 text-brand-blue animate-spin" />
            </div>
            <div class="text-xs">
              <span class="font-bold text-slate-800">
                {{ searchKeyword ? `Đang tìm kiếm ấn phẩm "${searchKeyword}"...` : 'Đang tải dữ liệu ấn phẩm từ hệ thống...' }}
              </span>
              <span class="text-slate-400 block mt-0.5">
                Đang kết nối hệ thống dữ liệu để cập nhật thông số và bảng giá xưởng mới nhất
              </span>
            </div>
          </div>

          <!-- Lưới Skeleton Cards chuyển động shimmer mượt mà (8 cards) -->
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            <div
              v-for="n in 8"
              :key="n"
              class="bg-white rounded-2xl border border-slate-200/80 p-4 space-y-3 animate-pulse shadow-2xs"
            >
              <div class="aspect-square w-full rounded-xl bg-slate-200/80"></div>
              <div class="h-3 w-1/3 bg-slate-200/70 rounded-md"></div>
              <div class="h-4 w-4/5 bg-slate-200/80 rounded-md"></div>
              <div class="h-3 w-3/5 bg-slate-200/60 rounded-md"></div>
              <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                <div class="h-4 w-2/5 bg-emerald-100 rounded-md"></div>
                <div class="h-7 w-20 bg-slate-200/70 rounded-lg"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Products Grid (4 cột gọn đẹp khi đã tải xong) -->
        <div v-else-if="filteredAndSortedProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
          <ProductCard
            v-for="p in filteredAndSortedProducts"
            :key="p.id"
            :product="p"
          />
        </div>

        <!-- Empty State khi không tìm thấy kết quả -->
        <div v-else class="text-center py-14 sm:py-16 bg-white rounded-3xl border border-slate-200/90 p-8 shadow-xs">
          <PackageSearch class="w-16 h-16 text-slate-300 mx-auto mb-4" />
          <h3 class="text-lg font-bold text-slate-800">
            {{ currentCategory ? `Chưa có mẫu sẵn trên web cho "${currentCategory.name}"` : 'Không tìm thấy ấn phẩm nào phù hợp' }}
          </h3>
          <p class="text-xs sm:text-sm text-slate-500 mt-2 max-w-md mx-auto leading-relaxed">
            Xưởng in Nhật Nguyên nhận gia công trực tiếp mọi quy cách đặc thù theo yêu cầu thiết kế của bạn. Báo giá nhanh trong 15 phút!
          </p>
          <div class="mt-6 flex flex-wrap justify-center gap-3">
            <button
              @click="resetToCatalogRoot"
              class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs cursor-pointer transition-colors"
            >
              ← Quay lại tất cả danh mục
            </button>
            <router-link
              :to="'/yeu-cau-bao-gia?product=' + encodeURIComponent(currentCategory?.name || 'In theo yêu cầu')"
              class="px-5 py-2.5 rounded-xl bg-brand-gold hover:bg-brand-gold-hover text-slate-950 font-bold text-xs shadow-md transition-all flex items-center gap-1.5"
            >
              <span>Gửi yêu cầu báo giá ngay</span>
              <ArrowRight class="w-3.5 h-3.5" />
            </router-link>
          </div>
        </div>
      </template>

      <!-- Full SEO Category Article at Bottom (Chuẩn SEO E-Commerce) -->
      <div
        v-if="currentCategory && currentCategory.description && !searchKeyword && isLongCategoryDesc"
        class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-4"
      >
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
          <div class="w-10 h-10 rounded-xl bg-brand-blue/10 text-brand-blue flex items-center justify-center shrink-0">
            <component :is="getCategoryIcon(currentCategory.icon)" class="w-5 h-5 stroke-[1.8]" />
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-black text-slate-900">
              Chi Tiết Dịch Vụ & Bảng Giá {{ currentCategory.name }} Tại Xưởng In Nhật Nguyên
            </h2>
            <p class="text-xs text-slate-400">
              Thông tin quy cách, công nghệ in ấn và chính sách bảo hành trực tiếp từ xưởng
            </p>
          </div>
        </div>

        <div
          class="category-description-html text-xs sm:text-sm text-slate-700 leading-relaxed"
          v-html="currentCategory.description"
        ></div>
      </div>

      <!-- 5. Bottom Custom Spec & Consultation Banner (Khối Báo Giá Quy Cách Riêng) -->
      <div class="bg-gradient-to-br from-brand-blue-dark via-brand-blue to-brand-blue-dark rounded-3xl p-8 sm:p-10 text-white shadow-xl relative overflow-hidden">
        <!-- Background decorative graphic shapes -->
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-64 h-64 rounded-full bg-brand-gold/10 blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
          <div class="space-y-3 max-w-2xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-brand-gold text-xs font-bold tracking-wider uppercase border border-white/15">
              <Sparkles class="w-3.5 h-3.5" />
              Dịch Vụ Gia Công Theo Yêu Cầu Riêng
            </span>
            <h2 class="text-xl sm:text-2xl lg:text-3xl font-black tracking-tight leading-tight">
              Bạn Cần In Ấn Quy Cách Đặc Biệt Hoặc Đơn Hàng Số Lượng Lớn?
            </h2>
            <p class="text-xs sm:text-sm text-slate-200 leading-relaxed">
              Nhật Nguyên hỗ trợ gia công mọi kỹ thuật cao cấp: ép kim ánh kim, dập nổi định hình, phủ UV cục bộ, bế khuôn theo thiết kế, cán màng nhung, in giấy mỹ thuật nhập khẩu. Đội ngũ kỹ thuật viên xưởng in luôn sẵn sàng tư vấn trực tiếp 24/7.
            </p>
          </div>

          <div class="flex flex-col gap-3 shrink-0 w-full lg:w-auto lg:min-w-[330px]">
            <router-link
              to="/yeu-cau-bao-gia"
              class="w-full inline-flex items-center justify-center px-6 py-3.5 rounded-xl bg-brand-gold hover:bg-brand-gold-hover text-slate-950 font-black text-xs sm:text-sm shadow-lg transition-all duration-200 gap-2 group text-center cursor-pointer whitespace-nowrap"
            >
              <span>Gửi Yêu Cầu Báo Giá Nhanh</span>
              <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
            </router-link>

            <div class="grid grid-cols-2 gap-2.5 w-full">
              <a
                href="https://zalo.me/0359005469"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center px-3 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition-all gap-1.5 whitespace-nowrap"
              >
                <MessageCircle class="w-4 h-4 text-emerald-400 shrink-0" />
                <span>Chat Zalo</span>
              </a>

              <a
                href="tel:0359005469"
                class="inline-flex items-center justify-center px-3 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition-all gap-1.5 whitespace-nowrap"
              >
                <PhoneCall class="w-4 h-4 text-amber-400 shrink-0" />
                <span>0359 005 469</span>
              </a>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
  Search,
  PackageSearch,
  Home,
  ShieldCheck,
  Zap,
  Sparkles,
  ArrowRight,
  ArrowLeft,
  ArrowUpDown,
  MessageCircle,
  PhoneCall,
  LayoutGrid,
  FolderKanban,
  Briefcase,
  Megaphone,
  Tag,
  Package,
  Flag,
  Calendar,
  Layers,
  Loader2,
  ChevronDown
} from 'lucide-vue-next'
import { products as fallbackProducts } from '../data/products'
import { categories as fallbackCategories } from '../data/categories'
import ProductCard from '../components/products/ProductCard.vue'
import { api } from '../services/api'
import { setMetaTags } from '../composables/useSEO'

const route = useRoute()
const router = useRouter()

const products = ref([...fallbackProducts])
const categories = ref([...fallbackCategories])
const isLoading = ref(true)

// Read both params and query for seamless compatibility
function getInitialCategory() {
  return route.params.categorySlug || route.query.cat || 'all'
}

const activeCategory = ref(getInitialCategory())
const searchKeyword = ref(route.query.q || '')
const activeQuickTag = ref('')
const sortBy = ref('default')

// Map icon string to Lucide component
function getCategoryIcon(iconName) {
  const map = {
    Briefcase,
    Megaphone,
    Tag,
    Package,
    Flag,
    Calendar,
    Layers
  }
  return map[iconName] || Package
}

// Check if currently viewing the Root Catalog page (/san-pham without sub-category filter)
const isRootCatalogView = computed(() => {
  return activeCategory.value === 'all'
})

// Quick filter tags
const quickTags = [
  { label: '⚡ In lấy liền / 24h', value: 'fast' },
  { label: '⭐ Bán chạy nhất', value: 'popular' },
  { label: '🏷️ Giá xưởng', value: 'bulk' }
]

function applyCategorySEO() {
  if (activeCategory.value === 'all') {
    setMetaTags({
      title: 'Danh Mục Sản Phẩm & Dịch Vụ In Ấn Trực Tiếp Tại Xưởng | Nhật Nguyên',
      description: 'Bảng tổng hợp các ngành danh mục in Offset và In Kỹ Thuật Số trực tiếp tại xưởng in Nhật Nguyên: Hộp giấy, Túi giấy, Decal, Namecard, Catalogue, Tờ rơi giá gốc.',
      keywords: 'danh muc in an, in hop giay, in tui giay, in decal, in catalogue, in to roi, in namecard, xuong in tphcm',
      url: 'https://dichvuinnhatnguyen.vn/san-pham'
    })
    return
  }

  const cat = currentCategory.value
  if (cat) {
    setMetaTags({
      title: cat.meta_title || `${cat.name} - Giá Gốc Trực Tiếp Tại Xưởng TPHCM | Nhật Nguyên`,
      description: cat.meta_description || cat.description || `Xưởng in Nhật Nguyên chuyên sản xuất ${cat.name} chất lượng cao, công nghệ in hiện đại, giá xưởng trực tiếp không qua trung gian.`,
      keywords: cat.meta_keywords || `${cat.name}, xuong in nhat nguyen, in gia goc, bao gia ${cat.name}`,
      url: cat.canonical_url || `https://dichvuinnhatnguyen.vn/danh-muc/${cat.slug}`,
      schema: {
        '@context': 'https://schema.org',
        '@type': 'CollectionPage',
        name: cat.meta_title || cat.name,
        description: cat.meta_description || cat.description,
        url: `https://dichvuinnhatnguyen.vn/danh-muc/${cat.slug}`
      }
    })
  }
}

// Keep in sync with route changes
watch(
  () => [route.params.categorySlug, route.query.cat, route.query.q],
  ([newParamsSlug, newQueryCat, newQueryQ]) => {
    activeCategory.value = newParamsSlug || newQueryCat || 'all'
    if (newQueryQ !== undefined) {
      searchKeyword.value = newQueryQ || ''
    }
    isCategoryDescExpanded.value = false
    applyCategorySEO()
  }
)

const currentCategory = computed(() => {
  return categories.value.find(c => c.slug === activeCategory.value)
})

const isCategoryDescExpanded = ref(false)

const isLongCategoryDesc = computed(() => {
  const desc = currentCategory.value?.description || ''
  const plain = stripHtml(desc)
  return plain.length > 200 || desc.includes('<h2') || desc.includes('<h3') || desc.includes('<ul') || desc.includes('<ol')
})

function stripHtml(html) {
  if (!html) return ''
  return html.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').replace(/&amp;/g, '&').replace(/\s+/g, ' ').trim()
}

// Danh sách các danh mục kèm thông tin số lượng sản phẩm và danh sách sản phẩm mẫu
// CHỈ hiển thị danh mục đang kích hoạt (is_active !== false) VÀ ĐÃ CÓ ít nhất 1 sản phẩm liên kết (> 0)
const categoriesWithMeta = computed(() => {
  return categories.value
    .filter(c => c.is_active !== false && getCategoryProductCount(c.slug) > 0)
    .map(c => {
      const prods = products.value.filter(p => isProductInCategoryOrChild(p, c.slug))
      return {
        ...c,
        productCount: prods.length,
        sampleProducts: prods.slice(0, 4)
      }
    })
})

// Danh mục hiển thị trên thanh tab lọc (cũng CHỈ hiện danh mục ĐÃ CÓ sản phẩm)
const visibleFilterCategories = computed(() => {
  return categories.value.filter(c => c.is_active !== false && getCategoryProductCount(c.slug) > 0)
})

const headerTitle = computed(() => {
  if (searchKeyword.value) return `Kết quả tìm kiếm cho "${searchKeyword.value}"`
  if (isRootCatalogView.value) return 'Tất Cả Danh Mục In Ấn'
  return currentCategory.value ? currentCategory.value.name : 'Danh Mục Sản Phẩm'
})

const headerDescription = computed(() => {
  if (searchKeyword.value) {
    return `Tìm thấy ${filteredAndSortedProducts.value.length} ấn phẩm phù hợp với từ khóa của bạn.`
  }
  if (isRootCatalogView.value) {
    return 'Lựa chọn ngành hàng ấn phẩm bạn đang quan tâm để xem các quy cách in ấn, mẫu mã thực tế và công cụ tính giá tự động chuẩn xác nhất.'
  }
  return currentCategory.value ? (currentCategory.value.meta_description || currentCategory.value.description) : 'Các ấn phẩm chất lượng cao sản xuất trực tiếp tại xưởng in Nhật Nguyên.'
})

function isProductInCategoryOrChild(p, targetSlug) {
  if (targetSlug === 'all') return true
  const directSlug = p.categoryId || p.category?.slug
  if (directSlug === targetSlug) return true

  // Kiểm tra nếu targetSlug là Cấp 1, khớp luôn sản phẩm thuộc Cấp con
  const parentSlug = p.category?.parent?.slug
  if (parentSlug === targetSlug) return true

  const targetCat = categories.value.find(c => c.slug === targetSlug)
  const prodCat = categories.value.find(c => c.slug === directSlug || Number(c.id) === Number(p.category_id))
  if (targetCat && prodCat && Number(prodCat.parent_id) === Number(targetCat.id)) {
    return true
  }
  return false
}

function getCategoryProductCount(slug) {
  return products.value.filter(p => isProductInCategoryOrChild(p, slug)).length
}

const filteredAndSortedProducts = computed(() => {
  let list = products.value.filter(p => {
    // Nếu đang chọn 1 category cụ thể
    const matchCat = activeCategory.value === 'all' || isProductInCategoryOrChild(p, activeCategory.value)

    // Search keyword match (name or description)
    const q = searchKeyword.value.toLowerCase().trim()
    const matchKey = !q ||
      p.name.toLowerCase().includes(q) ||
      (p.description && p.description.toLowerCase().includes(q)) ||
      (p.tag && p.tag.toLowerCase().includes(q))

    // Quick tag match
    let matchTag = true
    const pPrice = p.base_price !== undefined ? p.base_price : p.basePrice
    const pTurnaround = p.turnaround || ''
    if (activeQuickTag.value === 'fast') {
      matchTag = pTurnaround.includes('24h') || pTurnaround.includes('2h') || pTurnaround.includes('30 phút') || (p.tag && p.tag.toLowerCase().includes('lấy'))
    } else if (activeQuickTag.value === 'popular') {
      matchTag = p.is_popular || (p.tag && (p.tag.toLowerCase().includes('bán chạy') || p.tag.toLowerCase().includes('nhất'))) || p.id <= 4
    } else if (activeQuickTag.value === 'bulk') {
      matchTag = pPrice <= 5000 || (p.tag && p.tag.toLowerCase().includes('xưởng'))
    }

    return matchCat && matchKey && matchTag
  })

  // Sort logic
  if (sortBy.value === 'price-asc') {
    list = [...list].sort((a, b) => (a.base_price || a.basePrice || 0) - (b.base_price || b.basePrice || 0))
  } else if (sortBy.value === 'price-desc') {
    list = [...list].sort((a, b) => (b.base_price || b.basePrice || 0) - (a.base_price || a.basePrice || 0))
  } else if (sortBy.value === 'name-asc') {
    list = [...list].sort((a, b) => a.name.localeCompare(b.name, 'vi'))
  }

  return list
})

function selectCategory(slug) {
  activeCategory.value = slug
  searchKeyword.value = ''
  if (slug === 'all') {
    router.push('/san-pham')
  } else {
    router.push('/danh-muc/' + slug)
  }
}

function resetToCatalogRoot() {
  activeCategory.value = 'all'
  searchKeyword.value = ''
  activeQuickTag.value = ''
  sortBy.value = 'default'
  router.push('/san-pham')
}

function clearFilters() {
  resetToCatalogRoot()
}

function formatPrice(val) {
  if (!val && val !== 0) return '0'
  return new Intl.NumberFormat('vi-VN').format(val)
}

async function loadData() {
  if (products.value.length === 0) {
    isLoading.value = true
  }
  try {
    const [catRes, prodRes] = await Promise.all([
      api.getCategories().catch(() => null),
      api.getProducts().catch(() => null)
    ])
    if (catRes?.data?.length) {
      categories.value = catRes.data
    }
    if (prodRes?.data && Array.isArray(prodRes.data)) {
      products.value = prodRes.data
        .filter(p => p.is_active !== false)
        .map(p => ({
          ...p,
          basePrice: p.base_price,
          categoryId: p.category?.slug || p.category_id,
          categoryName: p.category?.name || 'Ấn phẩm in'
        }))
    }
  } catch (err) {
    console.error('Failed to load products from API', err)
  } finally {
    isLoading.value = false
  }
  applyCategorySEO()
}

onMounted(() => {
  loadData()
})
</script>

<style scoped>
.category-description-html :deep(p) {
  margin-bottom: 0.5rem;
}
.category-description-html :deep(p:last-child) {
  margin-bottom: 0;
}
.category-description-html :deep(strong),
.category-description-html :deep(b) {
  font-weight: 700;
  color: #0f172a;
}
.category-description-html :deep(h2) {
  font-size: 0.95rem;
  font-weight: 800;
  color: #0f172a;
  margin-top: 0.75rem;
  margin-bottom: 0.375rem;
}
.category-description-html :deep(h3) {
  font-size: 0.875rem;
  font-weight: 700;
  color: #1e293b;
  margin-top: 0.625rem;
  margin-bottom: 0.25rem;
}
.category-description-html :deep(ul) {
  list-style-type: disc;
  padding-left: 1.25rem;
  margin-top: 0.375rem;
  margin-bottom: 0.5rem;
}
.category-description-html :deep(ol) {
  list-style-type: decimal;
  padding-left: 1.25rem;
  margin-top: 0.375rem;
  margin-bottom: 0.5rem;
}
.category-description-html :deep(li) {
  margin-bottom: 0.25rem;
}
.category-description-html :deep(a) {
  color: #1d4ed8;
  text-decoration: underline;
}
.category-description-html :deep(img) {
  border-radius: 0.75rem;
  max-width: 100%;
  margin: 0.5rem 0;
}
</style>