<template>
  <header class="sticky top-0 z-40 bg-white shadow-md transition-all">
    <!-- 1. Main Header Bar (Logo, Search, Direct Hotline) -->
    <div class="border-b border-slate-100 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-24 sm:h-28 gap-4 py-2">
          <!-- Logo -->
          <router-link to="/" class="flex items-center shrink-0 group py-1">
            <img
              src="/logo.png"
              alt="Nhật Nguyên Design & Printing"
              class="h-16 sm:h-20 lg:h-[86px] w-auto object-contain transition-transform group-hover:scale-[1.02]"
            />
          </router-link>

          <!-- Search Input Box (Desktop) -->
          <div class="hidden md:flex items-center flex-1 max-w-lg mx-6">
            <button
              type="button"
              @click="isSearchOpen = true"
              class="w-full flex items-center justify-between px-4 py-2.5 text-sm text-slate-400 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition-all shadow-inner group"
            >
              <span class="flex items-center gap-2.5 text-slate-500 group-hover:text-slate-800">
                <Search class="w-4 h-4 text-brand-blue shrink-0" />
                <span>Tìm ấn phẩm (namecard, tờ rơi, decal, túi giấy...)</span>
              </span>
              <kbd class="hidden sm:inline-block px-2 py-0.5 text-[11px] font-semibold text-slate-500 bg-white border border-slate-200 rounded-md shadow-sm">
                Tìm kiếm
              </kbd>
            </button>
          </div>

          <!-- Right: Hotline Box & Mobile Hamburger -->
          <div class="flex items-center space-x-2 sm:space-x-3">
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

            <!-- Mobile Search Icon -->
            <button
              type="button"
              @click="isSearchOpen = true"
              class="p-2.5 text-slate-600 hover:text-brand-blue hover:bg-slate-100 rounded-xl md:hidden"
              aria-label="Tìm kiếm"
            >
              <Search class="w-5 h-5" />
            </button>

            <!-- Mobile Hamburger Toggle -->
            <button
              type="button"
              @click="isMobileMenuOpen = true"
              class="p-2.5 text-slate-700 hover:text-brand-blue hover:bg-slate-100 rounded-xl lg:hidden"
              aria-label="Menu"
            >
              <Menu class="w-6 h-6" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- 2. Brand Blue Navigation Bar (FlatSome / In Nhật Nguyên Signature Style) -->
    <div class="hidden lg:block bg-brand-blue text-white shadow-inner">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-12">
          <!-- Main Menu Links -->
          <nav class="flex items-center space-x-1 text-xs sm:text-sm font-bold">
            <router-link
              to="/"
              class="px-3.5 py-2 rounded-lg hover:bg-brand-blue-dark transition-colors"
              active-class="bg-brand-blue-dark text-brand-gold"
            >
              Trang chủ
            </router-link>

            <!-- Category Mega Dropdown Trigger -->
            <div class="relative group">
              <router-link
                to="/san-pham"
                class="px-3.5 py-2 rounded-lg hover:bg-brand-blue-dark transition-colors flex items-center gap-1.5"
                active-class="bg-brand-blue-dark text-brand-gold"
              >
                <span>Ấn Phẩm</span>
                <ChevronDown class="w-4 h-4 text-brand-gold group-hover:rotate-180 transition-transform duration-200" />
              </router-link>

              <!-- Mega Dropdown Panel: Phong Cách "The Print Directory" (Mục Lục Ấn Phẩm Mở) -->
              <div class="absolute left-0 top-full pt-2 w-[960px] xl:w-[1020px] opacity-0 translate-y-2 pointer-events-none group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto transition-all duration-200 z-50">
                <div class="bg-white text-slate-900 rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden">
                  
                  <!-- Top Bar Trong Menu: Tiêu đề & Nút Xem tất cả -->
                  <div class="px-6 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                    <span class="text-xs font-black uppercase text-brand-blue tracking-wider block">
                      MỤC LỤC ẤN PHẨM SẢN XUẤT TẬN XƯỞNG
                    </span>

                    <router-link
                      to="/san-pham"
                      class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full border border-slate-200 hover:border-brand-blue text-xs font-bold text-slate-700 hover:text-brand-blue transition-colors bg-white hover:shadow-xs"
                    >
                      <span>Xem toàn bộ 20+ ấn phẩm</span>
                      <ArrowRight class="w-3.5 h-3.5" />
                    </router-link>
                  </div>

                  <!-- Nội Dung Chính: 4 Cột Thông Thoáng Không Đóng Hộp -->
                  <div class="p-6 grid grid-cols-4 gap-6 items-stretch">
                    
                    <!-- CỘT 1: ẤN PHẨM VĂN PHÒNG -->
                    <div class="space-y-3">
                      <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 text-brand-blue flex items-center justify-center shrink-0">
                          <Briefcase class="w-4 h-4" />
                        </div>
                        <div>
                          <h4 class="text-xs font-black text-slate-900 uppercase tracking-tight">VĂN PHÒNG</h4>
                          <span class="text-[10px] text-slate-400 font-medium block">Nhận diện doanh nghiệp</span>
                        </div>
                      </div>

                      <ul class="space-y-1.5 text-xs text-slate-700">
                        <li>
                          <router-link to="/danh-muc/in-the-danh-thiep" class="hover:text-brand-blue hover:translate-x-1 transition-all flex items-center gap-1.5 py-0.5 font-medium group/link">
                            <span class="w-1 h-1 rounded-full bg-slate-300 group-hover/link:bg-brand-blue transition-colors"></span>
                            <span>Danh thiếp - Namecard</span>
                          </router-link>
                        </li>
                        <li>
                          <router-link to="/san-pham?cat=an-pham-van-phong" class="hover:text-brand-blue hover:translate-x-1 transition-all flex items-center gap-1.5 py-0.5 font-medium group/link">
                            <span class="w-1 h-1 rounded-full bg-slate-300 group-hover/link:bg-brand-blue transition-colors"></span>
                            <span>Kẹp file hồ sơ Folder A4</span>
                          </router-link>
                        </li>
                        <li>
                          <router-link to="/san-pham/bao-thu-nho" class="hover:text-brand-blue hover:translate-x-1 transition-all flex items-center gap-1.5 py-0.5 font-medium group/link">
                            <span class="w-1 h-1 rounded-full bg-slate-300 group-hover/link:bg-brand-blue transition-colors"></span>
                            <span>Bao thư nhỏ & lớn A4</span>
                          </router-link>
                        </li>
                        <li>
                          <router-link to="/san-pham?cat=an-pham-van-phong" class="hover:text-brand-blue hover:translate-x-1 transition-all flex items-center gap-1.5 py-0.5 font-medium group/link">
                            <span class="w-1 h-1 rounded-full bg-slate-300 group-hover/link:bg-brand-blue transition-colors"></span>
                            <span>Giấy tiêu đề thư công ty</span>
                          </router-link>
                        </li>
                        <li>
                          <router-link to="/san-pham?cat=an-pham-van-phong" class="hover:text-brand-blue hover:translate-x-1 transition-all flex items-center gap-1.5 py-0.5 font-medium group/link">
                            <span class="w-1 h-1 rounded-full bg-slate-300 group-hover/link:bg-brand-blue transition-colors"></span>
                            <span>Sổ tay & Giấy ghi chú</span>
                          </router-link>
                        </li>
                      </ul>
                    </div>

                    <!-- CỘT 2: QUẢNG CÁO & TIẾP THỊ -->
                    <div class="space-y-3">
                      <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 text-brand-blue flex items-center justify-center shrink-0">
                          <Megaphone class="w-4 h-4" />
                        </div>
                        <div>
                          <h4 class="text-xs font-black text-slate-900 uppercase tracking-tight">QUẢNG CÁO</h4>
                          <span class="text-[10px] text-slate-400 font-medium block">Truyền thông & Sự kiện</span>
                        </div>
                      </div>

                      <ul class="space-y-1.5 text-xs text-slate-700">
                        <li>
                          <router-link to="/san-pham?cat=tiep-thi-quang-cao" class="hover:text-brand-blue hover:translate-x-1 transition-all flex items-center gap-1.5 py-0.5 font-medium group/link">
                            <span class="w-1 h-1 rounded-full bg-slate-300 group-hover/link:bg-brand-blue transition-colors"></span>
                            <span>Tờ rơi - Flyer - Brochure</span>
                          </router-link>
                        </li>
                        <li>
                          <router-link to="/san-pham?cat=tiep-thi-quang-cao" class="hover:text-brand-blue hover:translate-x-1 transition-all flex items-center gap-1.5 py-0.5 font-medium group/link">
                            <span class="w-1 h-1 rounded-full bg-slate-300 group-hover/link:bg-brand-blue transition-colors"></span>
                            <span>Catalogue & Profile công ty</span>
                          </router-link>
                        </li>
                        <li>
                          <router-link to="/san-pham?cat=su-kien-trien-lam" class="hover:text-brand-blue hover:translate-x-1 transition-all flex items-center gap-1.5 py-0.5 font-medium group/link">
                            <span class="w-1 h-1 rounded-full bg-slate-300 group-hover/link:bg-brand-blue transition-colors"></span>
                            <span>Standee chân cuốn & chữ X</span>
                          </router-link>
                        </li>
                        <li>
                          <router-link to="/san-pham?cat=su-kien-trien-lam" class="hover:text-brand-blue hover:translate-x-1 transition-all flex items-center gap-1.5 py-0.5 font-medium group/link">
                            <span class="w-1 h-1 rounded-full bg-slate-300 group-hover/link:bg-brand-blue transition-colors"></span>
                            <span>Poster, Backdrop hội nghị</span>
                          </router-link>
                        </li>
                        <li>
                          <router-link to="/san-pham?cat=tiep-thi-quang-cao" class="hover:text-brand-blue hover:translate-x-1 transition-all flex items-center gap-1.5 py-0.5 font-medium group/link">
                            <span class="w-1 h-1 rounded-full bg-slate-300 group-hover/link:bg-brand-blue transition-colors"></span>
                            <span>Voucher & Phiếu quà tặng</span>
                          </router-link>
                        </li>
                      </ul>
                    </div>

                    <!-- CỘT 3: BAO BÌ & TEM NHÃN -->
                    <div class="space-y-3">
                      <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 text-brand-blue flex items-center justify-center shrink-0">
                          <Package class="w-4 h-4" />
                        </div>
                        <div>
                          <h4 class="text-xs font-black text-slate-900 uppercase tracking-tight">BAO BÌ & TEM</h4>
                          <span class="text-[10px] text-slate-400 font-medium block">Đóng gói & Nhãn mác</span>
                        </div>
                      </div>

                      <ul class="space-y-1.5 text-xs text-slate-700">
                        <li>
                          <router-link to="/san-pham?cat=bao-bi-tui-giay" class="hover:text-brand-blue hover:translate-x-1 transition-all flex items-center gap-1.5 py-0.5 font-medium group/link">
                            <span class="w-1 h-1 rounded-full bg-slate-300 group-hover/link:bg-brand-blue transition-colors"></span>
                            <span>Túi giấy shop & quà tặng</span>
                          </router-link>
                        </li>
                        <li>
                          <router-link to="/san-pham?cat=bao-bi-tui-giay" class="hover:text-brand-blue hover:translate-x-1 transition-all flex items-center gap-1.5 py-0.5 font-medium group/link">
                            <span class="w-1 h-1 rounded-full bg-slate-300 group-hover/link:bg-brand-blue transition-colors"></span>
                            <span>Hộp giấy carton, hộp cứng</span>
                          </router-link>
                        </li>
                        <li>
                          <router-link to="/san-pham?cat=tem-nhan-decal" class="hover:text-brand-blue hover:translate-x-1 transition-all flex items-center gap-1.5 py-0.5 font-medium group/link">
                            <span class="w-1 h-1 rounded-full bg-slate-300 group-hover/link:bg-brand-blue transition-colors"></span>
                            <span>Tem nhãn decal bế demi</span>
                          </router-link>
                        </li>
                        <li>
                          <router-link to="/san-pham?cat=tem-nhan-decal" class="hover:text-brand-blue hover:translate-x-1 transition-all flex items-center gap-1.5 py-0.5 font-medium group/link">
                            <span class="w-1 h-1 rounded-full bg-slate-300 group-hover/link:bg-brand-blue transition-colors"></span>
                            <span>Tag treo - Nhãn mác quần áo</span>
                          </router-link>
                        </li>
                        <li>
                          <router-link to="/san-pham?cat=tem-nhan-decal" class="hover:text-brand-blue hover:translate-x-1 transition-all flex items-center gap-1.5 py-0.5 font-medium group/link">
                            <span class="w-1 h-1 rounded-full bg-slate-300 group-hover/link:bg-brand-blue transition-colors"></span>
                            <span>Tem bảo hành vỡ niêm phong</span>
                          </router-link>
                        </li>
                      </ul>
                    </div>

                    <!-- CỘT 4: GIẢI PHÁP ĐẶC BIỆT CỦA XƯỞNG NHẬT NGUYÊN (Khối nổi bật) -->
                    <div class="bg-gradient-to-b from-slate-50 to-blue-50/40 rounded-2xl p-4 border border-slate-200/90 flex flex-col justify-between">
                      <div class="space-y-3">
                        <div class="flex items-center gap-1.5 text-xs font-black text-brand-blue uppercase tracking-tight">
                          <Sparkles class="w-4 h-4 text-brand-gold shrink-0" />
                          <span>GIẢI PHÁP TẠI XƯỞNG</span>
                        </div>

                        <div class="space-y-2 text-xs text-slate-700">
                          <div class="flex items-start gap-2">
                            <Zap class="w-3.5 h-3.5 text-amber-500 shrink-0 mt-0.5" />
                            <span class="text-[11px] leading-tight"><strong>In hỏa tốc 2h - 24h:</strong> Ưu tiên đơn hàng gấp</span>
                          </div>
                          <div class="flex items-start gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0 mt-1.5"></span>
                            <span class="text-[11px] leading-tight"><strong>Gia công cao cấp:</strong> Ép kim, dập nổi, UV định hình</span>
                          </div>
                          <div class="flex items-start gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-blue shrink-0 mt-1.5"></span>
                            <span class="text-[11px] leading-tight"><strong>Free kiểm tra file:</strong> Chỉnh hệ màu CMYK chuẩn</span>
                          </div>
                        </div>
                      </div>

                      <div class="pt-3 border-t border-slate-200/80 mt-3">
                        <router-link
                          to="/yeu-cau-bao-gia"
                          class="w-full py-2 px-3 rounded-xl bg-brand-blue hover:bg-brand-blue-dark text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-xs transition-colors"
                        >
                          <span>Gửi file báo giá riêng</span>
                          <ArrowRight class="w-3.5 h-3.5" />
                        </router-link>
                      </div>
                    </div>

                  </div>

                  <!-- Bottom Trust Bar: Cam Kết Của Xưởng -->
                  <div class="px-6 py-2.5 bg-slate-100/70 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-slate-600">
                    <span class="flex items-center gap-1">
                      <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                      Xưởng in trực tiếp: Đông Hưng Thuận 40, Q.12, TP.HCM
                    </span>
                    <span>📞 Kỹ thuật viên Zalo 24/7: <strong>0359 005 469</strong></span>
                    <span class="text-brand-blue font-bold">✓ Giao hàng toàn quốc & Đổi trả nếu lỗi</span>
                  </div>

                </div>
              </div>
            </div>

            <router-link
              to="/gioi-thieu"
              class="px-3.5 py-2 rounded-lg hover:bg-brand-blue-dark transition-colors"
              active-class="bg-brand-blue-dark text-brand-gold"
            >
              Giới thiệu
            </router-link>

            <router-link
              to="/tin-tuc"
              class="px-3.5 py-2 rounded-lg hover:bg-brand-blue-dark transition-colors"
              active-class="bg-brand-blue-dark text-brand-gold"
            >
              Cẩm nang in ấn
            </router-link>

            <router-link
              to="/lien-he"
              class="px-3.5 py-2 rounded-lg hover:bg-brand-blue-dark transition-colors"
              active-class="bg-brand-blue-dark text-brand-gold"
            >
              Thông tin liên hệ
            </router-link>
          </nav>

          <!-- Right Action: Request Quote Button (Brand Gold) -->
          <router-link
            to="/yeu-cau-bao-gia"
            class="inline-flex items-center justify-center px-4 py-1.5 text-xs font-extrabold text-slate-950 bg-brand-gold hover:bg-brand-gold-hover rounded-lg shadow-md transition-all transform hover:-translate-y-0.5 active:translate-y-0 gap-1.5"
          >
            <Calculator class="w-4 h-4" />
            <span>Yêu cầu báo giá nhanh</span>
          </router-link>
        </div>
      </div>
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
              Ấn Phẩm In Ấn (Tất cả)
            </router-link>

            <!-- Sub categories list -->
            <div class="pl-4 space-y-1 border-l-2 border-brand-blue/30 my-2">
              <router-link
                v-for="cat in categories"
                :key="cat.id"
                :to="'/danh-muc/' + cat.slug"
                class="block px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-brand-blue"
                @click="isMobileMenuOpen = false"
              >
                • {{ cat.name }}
              </router-link>
            </div>

            <router-link
              to="/gioi-thieu"
              class="block px-3 py-2.5 rounded-xl text-sm font-bold text-slate-800 hover:bg-slate-50 hover:text-brand-blue"
              @click="isMobileMenuOpen = false"
            >
              Giới thiệu
            </router-link>

            <router-link
              to="/tin-tuc"
              class="block px-3 py-2.5 rounded-xl text-sm font-bold text-slate-800 hover:bg-slate-50 hover:text-brand-blue"
              @click="isMobileMenuOpen = false"
            >
              Cẩm nang in ấn
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
import { ref } from 'vue'
import {
  Search, ChevronDown, Menu, X, Phone, Calculator, PhoneCall,
  Briefcase, Megaphone, Tag, Package, Flag, Calendar, Sparkles, ArrowRight, Zap
} from 'lucide-vue-next'
import { categories } from '../../data/categories'
import { siteSettings } from '../../data/siteSettings'
import SearchModal from '../common/SearchModal.vue'

const isSearchOpen = ref(false)
const isMobileMenuOpen = ref(false)

function getCategoryIcon(name) {
  const map = {
    Briefcase,
    Megaphone,
    Tag,
    Package,
    Flag,
    Calendar
  }
  return map[name] || Package
}
</script>
