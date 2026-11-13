<template>
  <header class="sticky top-0 z-40 bg-white shadow-md transition-all">
    <!-- 1. Main Header Bar (Logo, Search, Direct Hotline) -->
    <div class="border-b border-slate-100 bg-white">
      <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-20 lg:h-28 gap-2 sm:gap-4 py-1.5 sm:py-2">
          <!-- Logo -->
          <router-link to="/" class="flex items-center shrink-0 group py-1">
            <img
              src="/logo.png"
              alt="Nhật Nguyên Design & Printing"
              class="h-11 sm:h-16 lg:h-[86px] w-auto max-w-[170px] sm:max-w-none object-contain transition-transform group-hover:scale-[1.02]"
            />
          </router-link>

          <!-- Search Input Box (Desktop: Nhập trực tiếp, Dropdown gợi ý ngay bên dưới, không Popup) -->
          <div class="hidden md:flex items-center flex-1 max-w-lg mx-6 relative" ref="searchContainerRef">
            <form
              @submit.prevent="executeSearch"
              class="w-full flex items-center bg-slate-50 hover:bg-white focus-within:bg-white border border-slate-200 focus-within:border-brand-blue rounded-xl transition-all shadow-inner overflow-hidden pr-1.5"
            >
              <div class="pl-3.5 pr-2 text-brand-blue flex items-center justify-center shrink-0">
                <Search class="w-4 h-4" />
              </div>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Tìm ấn phẩm (namecard, tờ rơi, decal, túi giấy...)"
                class="w-full py-2.5 text-xs text-slate-800 placeholder-slate-400 bg-transparent focus:outline-none"
                @focus="handleSearchFocus"
                @input="handleSearchInput"
              />
              <button
                v-if="searchQuery"
                type="button"
                @click="clearSearch"
                class="p-1 text-slate-400 hover:text-slate-600 rounded-full cursor-pointer mr-1"
                title="Xóa tìm kiếm"
              >
                <X class="w-3.5 h-3.5" />
              </button>
              <button
                type="submit"
                :disabled="isSearching"
                class="px-3 py-1.5 text-[11px] font-bold text-slate-700 hover:text-brand-blue bg-white hover:bg-slate-100 border border-slate-200 rounded-lg shadow-2xs transition-all shrink-0 cursor-pointer min-w-[70px] flex items-center justify-center gap-1.5"
              >
                <Loader2 v-if="isSearching" class="w-3.5 h-3.5 text-brand-blue animate-spin" />
                <span v-else>Tìm kiếm</span>
              </button>
            </form>

            <!-- DROPDOWN GỢI Ý KẾT QUẢ TỨC THÌ (XỔ NHẸ BÊN DƯỚI Ô INPUT) -->
            <transition
              enter-active-class="transition duration-150 ease-out"
              enter-from-class="opacity-0 translate-y-1"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition duration-100 ease-in"
              leave-from-class="opacity-100 translate-y-0"
              leave-to-class="opacity-0 translate-y-1"
            >
              <div
                v-if="isSearchDropdownOpen && searchQuery.trim().length > 0"
                class="absolute left-0 right-0 top-[calc(100%+6px)] bg-white rounded-2xl shadow-2xl border border-slate-200/90 z-50 overflow-hidden"
              >
                <!-- Có kết quả gợi ý -->
                <div v-if="suggestedProducts.length > 0" class="p-2 space-y-1 max-h-80 overflow-y-auto">
                  <div class="px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between">
                    <span>Ấn phẩm gợi ý ({{ suggestedProducts.length }})</span>
                    <span class="text-brand-blue font-semibold">Nhấn Enter để xem tất cả</span>
                  </div>
                  <router-link
                    v-for="item in suggestedProducts"
                    :key="item.id || item.slug"
                    :to="'/san-pham/' + item.slug"
                    class="flex items-center gap-3 p-2 rounded-xl hover:bg-brand-blue-light/50 transition-colors group cursor-pointer"
                    @click="isSearchDropdownOpen = false"
                  >
                    <img
                      :src="item.image || '/logo-icon.png'"
                      :alt="item.name"
                      class="w-10 h-10 object-cover rounded-lg border border-slate-200 shrink-0 bg-slate-100"
                    />
                    <div class="flex-1 min-w-0">
                      <div class="flex items-center gap-1.5">
                        <span class="font-bold text-xs text-slate-800 group-hover:text-brand-blue truncate">
                          {{ item.name }}
                        </span>
                        <span v-if="item.tag" class="text-[9px] px-1.5 py-0.2 rounded font-bold shrink-0 bg-amber-100 text-amber-800">
                          {{ item.tag }}
                        </span>
                      </div>
                      <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-2">
                        <span>{{ item.category?.name || item.categoryName || 'Ấn phẩm in' }}</span>
                        <span>•</span>
                        <span class="text-emerald-700 font-bold">
                          Từ {{ formatCurrency(item.base_price || item.basePrice || 0) }}đ
                        </span>
                      </div>
                    </div>
                    <ChevronRight class="w-3.5 h-3.5 text-slate-300 group-hover:text-brand-blue group-hover:translate-x-0.5 transition-all shrink-0" />
                  </router-link>
                </div>

                <!-- Không tìm thấy kết quả -->
                <div v-else class="p-5 text-center text-xs text-slate-500 space-y-2">
                  <p>Không tìm thấy ấn phẩm nào với từ khóa "<strong>{{ searchQuery }}</strong>"</p>
                  <router-link
                    :to="'/yeu-cau-bao-gia?product=' + encodeURIComponent(searchQuery)"
                    class="inline-block text-brand-blue font-bold hover:underline"
                    @click="isSearchDropdownOpen = false"
                  >
                    Gửi yêu cầu báo giá theo quy cách riêng &rarr;
                  </router-link>
                </div>

                <!-- Footer xem toàn bộ kết quả -->
                <div class="p-2.5 bg-slate-50 border-t border-slate-100 text-center">
                  <button
                    type="button"
                    @click="executeSearch"
                    class="text-xs font-bold text-brand-blue hover:text-brand-blue-dark inline-flex items-center gap-1 cursor-pointer"
                  >
                    <span>Xem tất cả kết quả cho "{{ searchQuery }}"</span>
                    <ArrowRight class="w-3.5 h-3.5" />
                  </button>
                </div>
              </div>
            </transition>
          </div>

          <!-- Right: Hotline Box & Mobile Controls -->
          <div class="flex items-center space-x-1.5 sm:space-x-3 shrink-0">
            <!-- Desktop Hotline Contact Highlight (Gọn gàng 2 số xưởng in) -->
            <div class="hidden lg:flex items-center gap-2.5 bg-slate-50 border border-slate-200/90 px-3.5 py-1.5 rounded-xl">
              <div class="w-8 h-8 rounded-lg bg-brand-blue/10 text-brand-blue flex items-center justify-center shrink-0">
                <PhoneCall class="w-4 h-4 text-brand-blue" />
              </div>
              <div class="text-left leading-tight">
                <span class="text-[10px] text-slate-400 font-semibold block uppercase tracking-wider">Hotline</span>
                <div class="flex items-center gap-1.5 text-xs font-black text-slate-800">
                  <a
                    :href="'tel:' + siteSettings.hotlines[0].number.replace(/\s+/g, '')"
                    class="hover:text-brand-blue transition-colors"
                    :title="siteSettings.hotlines[0].label"
                  >
                    {{ siteSettings.hotlines[0].number }}
                  </a>
                  <span class="text-slate-300 font-normal">•</span>
                  <a
                    :href="'tel:' + siteSettings.hotlines[1].number.replace(/\s+/g, '')"
                    class="hover:text-brand-blue transition-colors"
                    :title="siteSettings.hotlines[1].label"
                  >
                    {{ siteSettings.hotlines[1].number }}
                  </a>
                </div>
              </div>
            </div>

            <!-- Mobile Quick Call Button -->
            <a
              :href="'tel:' + siteSettings.hotlines[0].number.replace(/\s+/g, '')"
              class="p-2 sm:p-2.5 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-xl md:hidden transition-colors flex items-center justify-center"
              aria-label="Gọi hotline"
              title="Gọi hotline tư vấn nhanh"
            >
              <PhoneCall class="w-5 h-5" />
            </a>

            <!-- Mobile Search Icon -->
            <button
              type="button"
              @click="isSearchOpen = true"
              class="p-2 sm:p-2.5 text-slate-600 hover:text-brand-blue hover:bg-slate-100 rounded-xl md:hidden transition-colors"
              aria-label="Tìm kiếm"
            >
              <Search class="w-5 h-5" />
            </button>

            <!-- Mobile Hamburger Toggle -->
            <button
              type="button"
              @click="isMobileMenuOpen = true"
              class="p-2 sm:p-2.5 text-slate-700 hover:text-brand-blue hover:bg-slate-100 rounded-xl lg:hidden transition-colors"
              aria-label="Menu"
            >
              <Menu class="w-6 h-6" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- 2. Brand Blue Navigation Bar (FlatSome / In Nhật Nguyên Signature Style) -->
    <div
      class="hidden lg:block bg-brand-blue text-white shadow-inner relative"
      @mouseleave="handleMouseLeave"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-12">
          <!-- Main Menu Links: Chỉ giữ 1 mục Dropdown "Ấn Phẩm In Ấn" duy nhất -->
          <nav class="flex items-center space-x-1 sm:space-x-1.5 text-xs sm:text-sm font-bold">
            <router-link
              to="/"
              class="px-3.5 py-2 rounded-lg hover:bg-brand-blue-dark transition-colors"
              active-class="bg-brand-blue-dark text-brand-gold"
              @mouseenter="closeMegaMenu"
            >
              Trang chủ
            </router-link>

            <!-- 1 MỤC DROPDOWN DUY NHẤT: "ẤN PHẨM IN ẤN" -->
            <div
              class="relative"
              @mouseenter="openMegaMenu"
            >
              <router-link
                to="/san-pham"
                class="px-3.5 py-2 rounded-lg transition-all flex items-center gap-1.5 cursor-pointer"
                :class="isMegaOpen ? 'bg-brand-blue-dark text-brand-gold shadow-xs' : 'hover:bg-brand-blue-dark hover:text-brand-gold'"
              >
                <span>Ấn Phẩm In Ấn</span>
                <ChevronDown
                  class="w-4 h-4 transition-transform duration-200"
                  :class="isMegaOpen ? 'rotate-180 text-brand-gold' : 'text-brand-gold'"
                />
              </router-link>
            </div>

            <router-link
              to="/gioi-thieu"
              class="px-3.5 py-2 rounded-lg hover:bg-brand-blue-dark transition-colors"
              active-class="bg-brand-blue-dark text-brand-gold"
              @mouseenter="closeMegaMenu"
            >
              Giới thiệu
            </router-link>

            <router-link
              to="/tin-tuc"
              class="px-3.5 py-2 rounded-lg hover:bg-brand-blue-dark transition-colors"
              active-class="bg-brand-blue-dark text-brand-gold"
              @mouseenter="closeMegaMenu"
            >
              Cẩm nang in ấn
            </router-link>

            <router-link
              to="/lien-he"
              class="px-3.5 py-2 rounded-lg hover:bg-brand-blue-dark transition-colors"
              active-class="bg-brand-blue-dark text-brand-gold"
              @mouseenter="closeMegaMenu"
            >
              Liên hệ
            </router-link>
          </nav>

          <!-- Right Action: Request Quote Button (Brand Gold) -->
          <router-link
            to="/yeu-cau-bao-gia"
            class="inline-flex items-center justify-center px-4 py-1.5 text-xs font-extrabold text-slate-950 bg-brand-gold hover:bg-brand-gold-hover rounded-lg shadow-md transition-all transform hover:-translate-y-0.5 active:translate-y-0 gap-1.5 shrink-0"
            @mouseenter="closeMegaMenu"
          >
            <Calculator class="w-4 h-4" />
            <span>Yêu cầu báo giá nhanh</span>
          </router-link>
        </div>
      </div>

      <!-- THEGIOIINAN-STYLE MEGA DROPDOWN PANEL (1 DROP DUY NHẤT RÕ RÀNG THEO TỪNG NGÀNH) -->
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div
          v-if="isMegaOpen && currentCategoryData"
          class="absolute left-0 right-0 top-full pt-1 z-50 pointer-events-auto"
          @mouseenter="cancelClose"
          @mouseleave="handleMouseLeave"
        >
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white text-slate-900 rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden">
              
              <!-- THANH TAB PHÂN NGÀNH TRONG DROPDOWN (Màu sáng tinh tế, bỏ hoàn toàn nền đen) -->
              <div class="bg-slate-50 border-b border-slate-200/80 px-4 sm:px-6 py-2.5 flex items-center gap-1.5 overflow-x-auto scrollbar-none select-none">
                <button
                  v-for="cat in megaMenuData"
                  :key="cat.id"
                  type="button"
                  @mouseenter="selectedCategoryTabId = cat.id"
                  class="px-3 py-1.5 sm:px-3.5 sm:py-2 text-xs font-bold transition-all flex items-center gap-1.5 shrink-0 rounded-xl cursor-pointer"
                  :class="selectedCategoryTabId === cat.id
                    ? 'bg-brand-blue text-white shadow-sm font-black'
                    : 'bg-white hover:bg-slate-200/80 text-slate-700 border border-slate-200/70'"
                >
                  <span>{{ cat.title }}</span>
                  <span
                    v-if="cat.badge"
                    class="text-[9px] px-1.5 py-0.2 rounded-full font-black uppercase"
                    :class="selectedCategoryTabId === cat.id ? 'bg-brand-gold text-slate-950' : (cat.badgeColor || 'bg-amber-100 text-amber-800')"
                  >
                    {{ cat.badge }}
                  </span>
                </button>
              </div>

              <!-- NỘI DUNG CHI TIẾT NGÀNH: CÁC CỘT ẤN PHẨM RỘNG RÃI, THÔNG THOÁNG -->
              <div class="p-6 lg:p-8 bg-white">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                  <div
                    v-for="(col, colIdx) in currentCategoryData.columns"
                    :key="colIdx"
                    class="space-y-5"
                  >
                    <!-- Nhóm Con 1 (Cấp 2) -->
                    <div>
                      <h4 class="text-xs font-black text-emerald-700 uppercase tracking-tight pb-2 border-b border-slate-100 flex items-center justify-between">
                        <span>{{ col.heading }}</span>
                      </h4>

                      <ul class="mt-2.5 space-y-1.5 text-xs text-slate-700">
                        <li
                          v-for="(item, itemIdx) in col.items"
                          :key="itemIdx"
                        >
                          <a
                            href="javascript:void(0)"
                            @click="handleItemClick(item)"
                            class="group/item flex items-center justify-between py-1.5 px-2 rounded-lg hover:bg-slate-50 transition-all cursor-pointer"
                            :title="item.hasDetail ? 'Bấm để xem thông số & bảng giá' : 'Nhận in theo yêu cầu - Bấm để nhận báo giá nhanh trong 15p'"
                          >
                            <span class="flex items-center gap-2 text-slate-700 group-hover/item:text-brand-blue group-hover/item:font-bold group-hover/item:translate-x-1 transition-all">
                              <span
                                class="w-1.5 h-1.5 rounded-full transition-colors shrink-0"
                                :class="item.hasDetail ? 'bg-brand-blue' : 'bg-slate-300 group-hover/item:bg-brand-gold'"
                              ></span>
                              <span>{{ item.name }}</span>
                            </span>

                            <!-- Badges -->
                            <div v-if="item.badge" class="shrink-0 ml-2">
                              <span
                                class="text-[9px] px-1.5 py-0.5 rounded font-bold"
                                :class="{
                                  'bg-amber-100 text-amber-800': item.badge.includes('In nhanh') || item.badge.includes('Gấp') || item.badge.includes('2H'),
                                  'bg-red-100 text-red-700': item.badge === 'Hot',
                                  'bg-emerald-100 text-emerald-800': item.badge === 'Mới' || item.badge === 'Bán chạy',
                                  'bg-blue-100 text-brand-blue': item.badge === 'Giá xưởng' || item.badge === 'Giá gốc' || item.badge === 'Chuẩn màu',
                                  'bg-slate-100 text-slate-600': !['In nhanh', 'Hot', 'Mới', 'Bán chạy', 'Giá xưởng', 'Giá gốc'].includes(item.badge)
                                }"
                              >
                                {{ item.badge }}
                              </span>
                            </div>
                          </a>
                        </li>
                      </ul>
                    </div>

                    <!-- Nhóm Con Phụ (Subheading nếu có) -->
                    <div v-if="col.subheading" class="pt-2">
                      <h4 class="text-xs font-black text-emerald-700 uppercase tracking-tight pb-2 border-b border-slate-100">
                        {{ col.subheading }}
                      </h4>

                      <ul class="mt-2.5 space-y-1.5 text-xs text-slate-700">
                        <li
                          v-for="(subItem, subIdx) in col.subitems"
                          :key="subIdx"
                        >
                          <a
                            href="javascript:void(0)"
                            @click="handleItemClick(subItem)"
                            class="group/item flex items-center justify-between py-1.5 px-2 rounded-lg hover:bg-slate-50 transition-all cursor-pointer"
                          >
                            <span class="flex items-center gap-2 text-slate-700 group-hover/item:text-brand-blue group-hover/item:font-bold group-hover/item:translate-x-1 transition-all">
                              <span class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover/item:bg-brand-gold shrink-0"></span>
                              <span>{{ subItem.name }}</span>
                            </span>
                            <span v-if="subItem.badge" class="text-[9px] px-1.5 py-0.5 rounded font-bold bg-amber-100 text-amber-800 shrink-0 ml-2">
                              {{ subItem.badge }}
                            </span>
                          </a>
                        </li>
                      </ul>
                    </div>

                  </div>
                </div>
              </div>

              <!-- Bottom Trust & Contact Bar: Báo giá 15 phút & Cam kết xưởng -->
              <div class="px-6 py-2.5 bg-slate-100/90 border-t border-slate-200/80 flex items-center justify-between text-[11px] font-semibold text-slate-600">
                <span class="flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                  <span>Sản phẩm chưa có mẫu trên web? Xưởng nhận sản xuất theo mọi quy cách riêng</span>
                </span>

                <div class="flex items-center gap-4">
                  <a
                    :href="'tel:' + siteSettings.hotlines[0].number.replace(/\s+/g, '')"
                    class="hover:text-brand-blue transition-colors flex items-center gap-1"
                  >
                    <Phone class="w-3.5 h-3.5 text-brand-blue" />
                    <span>Hotline/Zalo: <strong>{{ siteSettings.hotlines[0].number }}</strong></span>
                  </a>
                  <span class="text-slate-300">•</span>
                  <router-link
                    to="/san-pham"
                    class="text-slate-600 hover:text-brand-blue font-bold"
                    @click="isMegaOpen = false"
                  >
                    Xem toàn bộ ấn phẩm
                  </router-link>
                  <span class="text-slate-300">•</span>
                  <router-link
                    to="/yeu-cau-bao-gia"
                    class="text-brand-blue hover:underline font-bold flex items-center gap-1"
                    @click="isMegaOpen = false"
                  >
                    <span>Mở Form Báo Giá Nhanh (15 phút)</span>
                    <ArrowRight class="w-3 h-3" />
                  </router-link>
                </div>
              </div>

            </div>
          </div>
        </div>
      </transition>
    </div>

    <!-- 3. Mobile Slide-Over Drawer Menu (Clean, Full Screen Overlay, Zero top-overlap) -->
    <div
      v-if="isMobileMenuOpen"
      class="fixed inset-0 z-50 overflow-hidden"
      role="dialog"
      aria-modal="true"
    >
      <!-- Backdrop -->
      <div
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
        @click="isMobileMenuOpen = false"
      ></div>

      <!-- Drawer Content -->
      <div class="fixed inset-y-0 right-0 max-w-xs w-full bg-white shadow-2xl flex flex-col z-10">
        <!-- Drawer Header -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-white text-slate-800">
          <router-link to="/" class="flex items-center" @click="isMobileMenuOpen = false">
            <img src="/logo.png" alt="Nhật Nguyên Logo" class="h-14 w-auto object-contain" />
          </router-link>
          <button
            type="button"
            @click="isMobileMenuOpen = false"
            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100"
            aria-label="Đóng menu"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Drawer Links List -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
          <div class="space-y-1">
            <router-link
              to="/"
              class="block px-3 py-2.5 rounded-xl text-sm font-bold text-slate-800 hover:bg-slate-50 hover:text-brand-blue"
              @click="isMobileMenuOpen = false"
            >
              Trang chủ
            </router-link>

            <router-link
              to="/san-pham"
              class="block px-3 py-2.5 rounded-xl text-sm font-bold text-slate-800 hover:bg-slate-50 hover:text-brand-blue"
              @click="isMobileMenuOpen = false"
            >
              Tất Cả Ấn Phẩm (Xem bảng giá)
            </router-link>

            <!-- Mega Categories Accordion for Mobile -->
            <div class="my-2 space-y-1 border-y border-slate-100 py-2">
              <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider px-3 block mb-1">
                MỤC LỤC ẤN PHẨM THEO NGÀNH
              </span>
              <div
                v-for="cat in megaMenuData"
                :key="cat.id"
                class="rounded-xl overflow-hidden"
              >
                <div
                  @click="toggleMobileCategory(cat.id)"
                  class="flex items-center justify-between px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 cursor-pointer"
                >
                  <span class="flex items-center gap-2">
                    <span>{{ cat.title }}</span>
                    <span
                      v-if="cat.badge"
                      class="text-[9px] px-1.5 py-0.2 rounded-full font-black"
                      :class="cat.badgeColor || 'bg-brand-gold text-slate-950'"
                    >
                      {{ cat.badge }}
                    </span>
                  </span>
                  <ChevronDown
                    class="w-4 h-4 text-slate-400 transition-transform duration-200"
                    :class="expandedMobileCategory === cat.id ? 'rotate-180 text-brand-gold' : ''"
                  />
                </div>

                <!-- Expanded sub items in mobile -->
                <div
                  v-if="expandedMobileCategory === cat.id"
                  class="bg-slate-50 px-3 py-2.5 space-y-3 text-xs border-l-2 border-brand-gold ml-3 my-1 rounded-r-xl"
                >
                  <div v-for="(col, cIdx) in cat.columns" :key="cIdx" class="space-y-1.5">
                    <span class="text-[11px] font-black text-brand-blue uppercase block">
                      {{ col.heading }}
                    </span>
                    <ul class="space-y-1 pl-1 text-slate-600">
                      <li v-for="(mItem, mIdx) in col.items" :key="mIdx">
                        <a
                          href="javascript:void(0)"
                          @click="handleMobileItemClick(mItem)"
                          class="flex items-center justify-between py-1 text-xs hover:text-brand-blue"
                        >
                          <span class="line-clamp-1">• {{ mItem.name }}</span>
                          <span
                            v-if="mItem.badge"
                            class="text-[8px] px-1 rounded bg-amber-100 text-amber-800 font-bold shrink-0 ml-1"
                          >
                            {{ mItem.badge }}
                          </span>
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <router-link
              to="/tin-tuc"
              class="block px-3 py-2.5 rounded-xl text-sm font-bold text-slate-800 hover:bg-slate-50 hover:text-brand-blue"
              @click="isMobileMenuOpen = false"
            >
              Cẩm nang in ấn
            </router-link>

            <router-link
              to="/gioi-thieu"
              class="block px-3 py-2.5 rounded-xl text-sm font-bold text-slate-800 hover:bg-slate-50 hover:text-brand-blue"
              @click="isMobileMenuOpen = false"
            >
              Giới thiệu
            </router-link>

            <router-link
              to="/lien-he"
              class="block px-3 py-2.5 rounded-xl text-sm font-bold text-slate-800 hover:bg-slate-50 hover:text-brand-blue"
              @click="isMobileMenuOpen = false"
            >
              Thông tin liên hệ
            </router-link>
          </div>
        </div>

        <!-- Drawer Footer CTA -->
        <div class="p-4 border-t border-slate-100 bg-slate-50 space-y-2.5">
          <router-link
            to="/yeu-cau-bao-gia"
            class="w-full py-3 px-4 rounded-xl bg-brand-gold hover:bg-brand-gold-hover text-slate-950 font-black text-xs text-center flex items-center justify-center gap-2 shadow-md"
            @click="isMobileMenuOpen = false"
          >
            <Calculator class="w-4 h-4" />
            <span>Yêu cầu báo giá nhanh</span>
          </router-link>

          <a
            :href="'tel:' + siteSettings.hotlines[0].number.replace(/\s+/g, '')"
            class="w-full py-2.5 px-4 rounded-xl bg-brand-blue hover:bg-brand-blue-dark text-white font-bold text-xs text-center flex items-center justify-center gap-2 shadow-sm"
          >
            <Phone class="w-4 h-4" />
            <span>Gọi ngay: {{ siteSettings.hotlines[0].number }}</span>
          </a>
        </div>
      </div>
    </div>

    <!-- Search Autocomplete Modal -->
    <SearchModal v-model="isSearchOpen" />
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import {
  Search, ChevronDown, ChevronRight, Menu, X, Phone, Calculator, PhoneCall,
  Sparkles, ArrowRight, Zap, CheckCircle2, Loader2
} from 'lucide-vue-next'
import { categories } from '../../data/categories'
import { siteSettings } from '../../data/siteSettings'
import { megaMenuData } from '../../data/megaMenuData'
import { api } from '../../services/api'
import { usePageLoader } from '../../composables/usePageLoader'
import SearchModal from '../common/SearchModal.vue'

const { startNavLoading, finishNavLoading } = usePageLoader()
const router = useRouter()
const isSearching = ref(false)
const isSearchOpen = ref(false)
const isMobileMenuOpen = ref(false)
const isMegaOpen = ref(false)
const selectedCategoryTabId = ref('van-phong')
const expandedMobileCategory = ref(null)
let leaveTimeout = null

// Desktop direct search & instant dropdown
const searchQuery = ref('')
const isSearchDropdownOpen = ref(false)
const searchContainerRef = ref(null)
const allProducts = ref([])

function formatCurrency(val) {
  if (!val && val !== 0) return '0'
  return new Intl.NumberFormat('vi-VN').format(val)
}

async function loadProductsForSearch() {
  try {
    const res = await api.getProducts().catch(() => null)
    if (res?.data && Array.isArray(res.data)) {
      allProducts.value = res.data.filter(p => p.is_active !== false)
    }
  } catch (e) {
    allProducts.value = []
  }
}

const suggestedProducts = computed(() => {
  const q = searchQuery.value.toLowerCase().trim()
  if (!q) return []
  return allProducts.value.filter(p => {
    const name = (p.name || '').toLowerCase()
    const desc = (p.description || '').toLowerCase()
    const tag = (p.tag || '').toLowerCase()
    const catName = (p.category?.name || '').toLowerCase()
    return name.includes(q) || desc.includes(q) || tag.includes(q) || catName.includes(q)
  }).slice(0, 6)
})

function handleSearchFocus(e) {
  // Auto-clear on Focus: Tự động xóa sạch từ khóa cũ khi người dùng click vào ô tìm kiếm
  searchQuery.value = ''
  isSearchDropdownOpen.value = true
}

function handleSearchInput() {
  isSearchDropdownOpen.value = true
}

function clearSearch() {
  searchQuery.value = ''
  isSearchDropdownOpen.value = false
}

function executeSearch() {
  isSearchDropdownOpen.value = false
  const q = searchQuery.value.trim()
  isSearching.value = true
  startNavLoading()

  const target = q ? { path: '/san-pham', query: { q } } : '/san-pham'
  router.push(target).catch(() => {}).finally(() => {
    setTimeout(() => {
      isSearching.value = false
    }, 200)
  })
}

function handleClickOutside(e) {
  if (searchContainerRef.value && !searchContainerRef.value.contains(e.target)) {
    isSearchDropdownOpen.value = false
  }
}

onMounted(() => {
  loadProductsForSearch()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

const currentCategoryData = computed(() => {
  return megaMenuData.find(m => m.id === selectedCategoryTabId.value) || megaMenuData[1] || megaMenuData[0]
})

function openMegaMenu() {
  if (leaveTimeout) {
    clearTimeout(leaveTimeout)
    leaveTimeout = null
  }
  isMegaOpen.value = true
}

function cancelClose() {
  if (leaveTimeout) {
    clearTimeout(leaveTimeout)
    leaveTimeout = null
  }
}

function closeMegaMenu() {
  isMegaOpen.value = false
}

function handleMouseLeave() {
  leaveTimeout = setTimeout(() => {
    isMegaOpen.value = false
  }, 200)
}

function handleItemClick(item) {
  isMegaOpen.value = false
  if (item.hasDetail && item.slug) {
    router.push(`/san-pham/${item.slug}`)
  } else {
    router.push({
      path: '/yeu-cau-bao-gia',
      query: { product: item.name }
    })
  }
}

function handleFeaturedClick(featured) {
  isMegaOpen.value = false
  if (featured.hasDetail && featured.slug) {
    router.push(`/san-pham/${featured.slug}`)
  } else {
    router.push({
      path: '/yeu-cau-bao-gia',
      query: { product: featured.productName || featured.title }
    })
  }
}

function toggleMobileCategory(id) {
  expandedMobileCategory.value = expandedMobileCategory.value === id ? null : id
}

function handleMobileItemClick(item) {
  isMobileMenuOpen.value = false
  handleItemClick(item)
}
</script>
