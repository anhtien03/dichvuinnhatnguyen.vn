<template>
  <div class="py-10 lg:py-16 bg-slate-50 min-h-screen relative overflow-hidden">
    <!-- Ambient Background Lighting Effects -->
    <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[800px] h-[350px] bg-gradient-to-b from-brand-blue/10 via-brand-gold/5 to-transparent blur-3xl pointer-events-none -z-0"></div>
    <div class="absolute top-1/3 -left-40 w-96 h-96 bg-brand-blue/5 rounded-full blur-3xl pointer-events-none -z-0"></div>
    <div class="absolute top-2/3 -right-40 w-96 h-96 bg-brand-gold/5 rounded-full blur-3xl pointer-events-none -z-0"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16 relative z-10">
      
      <!-- Section 1: Hero Introduction with Fade-In Animation -->
      <div class="text-center max-w-3xl mx-auto space-y-4 animate-fade-in-up">
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/10 border border-brand-gold/30 text-xs font-bold text-amber-700 uppercase tracking-wider shadow-2xs">
          <Award class="w-4 h-4 text-brand-gold animate-bounce" />
          VỀ CHÚNG TÔI — IN ẤN NHẬT NGUYÊN
        </span>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight leading-tight">
          Giải Pháp In Ấn Trực Tiếp Chất Lượng Cao <br />
          <span class="bg-gradient-to-r from-brand-blue via-sky-600 to-brand-gold bg-clip-text text-transparent">
            Tiêu Chí: Nhanh – Đẹp – Giá Tốt
          </span>
        </h1>
        <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-normal">
          In Ấn Nhật Nguyên là đơn vị chuyên cung cấp dịch vụ in ấn chất lượng cao tại TP.HCM, với thế mạnh hơn 8 năm kinh nghiệm trong lĩnh vực in nhanh kỹ thuật số, in offset và in ấn quảng cáo. Chúng tôi sản xuất trực tiếp tại xưởng, không qua trung gian, mang lại giải pháp in ấn tối ưu chi phí và nâng cao hình ảnh thương hiệu của bạn.
        </p>
      </div>

      <!-- Section 2: Highlights / Stats Grid with Hover Shimmer & Lift -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 items-stretch">
        <div
          v-for="(st, idx) in siteSettings.stats"
          :key="idx"
          class="group h-full"
        >
          <div
            class="h-full bg-white p-6 sm:p-7 rounded-2xl border border-slate-200/80 shadow-sm text-center group-hover:shadow-xl group-hover:-translate-y-1.5 group-hover:border-brand-blue/40 transition-all duration-300 relative overflow-hidden transform-gpu cursor-default"
          >
            <!-- Top Border Accent on Hover -->
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-brand-blue to-brand-gold opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            
            <!-- Shimmer Sweep Effect -->
            <div class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 bg-gradient-to-r from-transparent via-white/40 to-transparent pointer-events-none"></div>

            <div class="text-2xl sm:text-3xl lg:text-4xl font-black text-brand-blue group-hover:scale-110 group-hover:text-brand-blue-dark transition-all duration-300">
              {{ st.value }}
            </div>
            <div class="text-xs sm:text-sm text-slate-600 font-semibold mt-1.5 group-hover:text-slate-900 transition-colors">
              {{ st.label }}
            </div>
          </div>
        </div>
      </div>

      <!-- Section 3: Why Choose Us (4 Pillar Advantages with Interactive Hover) -->
      <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200/90 shadow-sm space-y-10 hover:shadow-lg transition-shadow">
        <div class="text-center max-w-2xl mx-auto">
          <span class="text-xs font-bold text-brand-gold uppercase tracking-wider block mb-1">
            GIÁ TRỊ TẠO NÊN SỰ KHÁC BIỆT
          </span>
          <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
            Tại Sao Khách Hàng Tin Chọn In Ấn Nhật Nguyên?
          </h2>
          <p class="text-xs sm:text-sm text-slate-500 mt-2">
            Không chỉ là một xưởng in đơn thuần, Nhật Nguyên là đối tác đồng hành nâng tầm nhận diện thương hiệu cho bạn.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch">
          <!-- Pillar 1 -->
          <div class="group h-full">
            <div class="h-full p-6 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between space-y-3 group-hover:bg-white group-hover:border-brand-blue group-hover:shadow-xl group-hover:-translate-y-1.5 transition-all duration-300 transform-gpu cursor-default">
              <div>
                <div class="w-12 h-12 rounded-xl bg-brand-blue/10 text-brand-blue flex items-center justify-center font-bold mb-4 group-hover:scale-110 group-hover:bg-brand-blue group-hover:text-white transition-all duration-300 shadow-xs">
                  <Printer class="w-6 h-6 group-hover:rotate-6 transition-transform duration-300" />
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-2 group-hover:text-brand-blue transition-colors">Công Nghệ In Hiện Đại (KTS & Offset)</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                  Ứng dụng in kỹ thuật số (KTS) xử lý nhanh chóng đơn hàng lấy liền sau 30 phút. Dây chuyền in Offset hiện đại đảm bảo màu sắc chuẩn hệ CMYK mịn mượt, sắc nét và bền bỉ.
                </p>
              </div>
            </div>
          </div>

          <!-- Pillar 2 -->
          <div class="group h-full">
            <div class="h-full p-6 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between space-y-3 group-hover:bg-white group-hover:border-brand-gold group-hover:shadow-xl group-hover:-translate-y-1.5 transition-all duration-300 transform-gpu cursor-default">
              <div>
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-brand-gold flex items-center justify-center font-bold mb-4 group-hover:scale-110 group-hover:bg-brand-gold group-hover:text-slate-950 transition-all duration-300 shadow-xs">
                  <BadgePercent class="w-6 h-6 group-hover:-rotate-6 transition-transform duration-300" />
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-2 group-hover:text-amber-600 transition-colors">Giá Xưởng Cạnh Tranh — Không Qua Trung Gian</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                  Sản xuất trực tiếp tại xưởng giúp mức giá luôn ổn định và phản ánh đúng giá trị thực. Chính sách chiết khấu linh hoạt cho khách hàng in định kỳ hoặc đơn hàng số lượng lớn.
                </p>
              </div>
            </div>
          </div>

          <!-- Pillar 3 -->
          <div class="group h-full">
            <div class="h-full p-6 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between space-y-3 group-hover:bg-white group-hover:border-emerald-500 group-hover:shadow-xl group-hover:-translate-y-1.5 transition-all duration-300 transform-gpu cursor-default">
              <div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold mb-4 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 shadow-xs">
                  <Sparkles class="w-6 h-6 group-hover:rotate-12 transition-transform duration-300" />
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-2 group-hover:text-emerald-600 transition-colors">Hỗ Trợ Thiết Kế & Kiểm Tra File Free</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                  Đội ngũ kỹ thuật hỗ trợ kiểm tra độ phân giải, hiệu chỉnh hệ màu CMYK và căn chỉnh lề xén (bleed) hoàn toàn miễn phí, hạn chế tối đa nguy cơ sai hỏng file khi in ấn.
                </p>
              </div>
            </div>
          </div>

          <!-- Pillar 4 -->
          <div class="group h-full">
            <div class="h-full p-6 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between space-y-3 group-hover:bg-white group-hover:border-rose-500 group-hover:shadow-xl group-hover:-translate-y-1.5 transition-all duration-300 transform-gpu cursor-default">
              <div>
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold mb-4 group-hover:scale-110 group-hover:bg-rose-600 group-hover:text-white transition-all duration-300 shadow-xs">
                  <ShieldCheck class="w-6 h-6 group-hover:scale-110 transition-transform duration-300" />
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-2 group-hover:text-rose-600 transition-colors">Cam Kết Trách Nhiệm & Hậu Mãi Tận Tâm</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                  Phương châm "Chất lượng đi đôi với trách nhiệm". Bảo hành 100% – Hỗ trợ đổi trả trong 5 ngày và in lại miễn phí nhanh chóng nếu phát sinh lỗi từ phía nhà sản xuất.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Section 4: 4 Core Printing Product Categories (Interactive Category Cards) -->
      <div class="space-y-8">
        <div class="text-center max-w-2xl mx-auto">
          <span class="text-xs font-bold text-brand-gold uppercase tracking-wider block mb-1">
            DANH MỤC ẤN PHẨM CHỦ LỰC
          </span>
          <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
            Đáp Ứng Mọi Nhu Cầu In Ấn Toàn Diện
          </h2>
          <p class="text-xs sm:text-sm text-slate-500 mt-2">
            Từ ấn phẩm số lượng ít lấy ngay trong ngày đến các dự án sản xuất bao bì công nghiệp quy mô lớn.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch">
          <!-- Group 1 -->
          <div class="group h-full">
            <div class="h-full bg-white rounded-2xl p-6 border border-slate-200/90 shadow-sm flex flex-col justify-between group-hover:shadow-xl group-hover:-translate-y-1.5 group-hover:border-brand-blue/50 transition-all duration-300 transform-gpu">
              <div>
                <div class="text-brand-blue font-black text-lg mb-2 group-hover:text-brand-blue-dark transition-colors flex items-center justify-between">
                  <span>1. In Ấn Quảng Cáo</span>
                  <span class="text-xs font-bold text-slate-400 group-hover:text-brand-blue transition-colors">→</span>
                </div>
                <p class="text-xs text-slate-500 mb-4">Giải pháp truyền thông sự kiện, điểm bán và chiến dịch tiếp thị.</p>
                <ul class="space-y-1.5 text-xs text-slate-700 font-medium">
                  <li class="flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform"><Check class="w-3.5 h-3.5 text-brand-blue" /> Tờ rơi, Tờ gấp (Brochure)</li>
                  <li class="flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform"><Check class="w-3.5 h-3.5 text-brand-blue" /> Catalogue, Profile công ty</li>
                  <li class="flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform"><Check class="w-3.5 h-3.5 text-brand-blue" /> Poster, Banner, Băng rôn Hiflex</li>
                  <li class="flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform"><Check class="w-3.5 h-3.5 text-brand-blue" /> Standee, Tranh Canvas, Decal lớn</li>
                  <li class="flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform"><Check class="w-3.5 h-3.5 text-brand-blue" /> Voucher, Phiếu quà tặng, Hashtag</li>
                </ul>
              </div>
              <router-link to="/danh-muc/an-pham-quang-cao" class="mt-5 text-xs font-bold text-brand-blue group-hover:text-brand-gold hover:underline inline-flex items-center gap-1 transition-colors">
                <span>Xem sản phẩm quảng cáo</span>
                <span class="group-hover:translate-x-1 transition-transform">→</span>
              </router-link>
            </div>
          </div>

          <!-- Group 2 -->
          <div class="group h-full">
            <div class="h-full bg-white rounded-2xl p-6 border border-slate-200/90 shadow-sm flex flex-col justify-between group-hover:shadow-xl group-hover:-translate-y-1.5 group-hover:border-brand-gold/50 transition-all duration-300 transform-gpu">
              <div>
                <div class="text-brand-blue font-black text-lg mb-2 group-hover:text-amber-600 transition-colors flex items-center justify-between">
                  <span>2. Bao Bì & Tem Nhãn</span>
                  <span class="text-xs font-bold text-slate-400 group-hover:text-brand-gold transition-colors">→</span>
                </div>
                <p class="text-xs text-slate-500 mb-4">Bảo vệ sản phẩm và gia tăng giá trị nhận diện thương hiệu.</p>
                <ul class="space-y-1.5 text-xs text-slate-700 font-medium">
                  <li class="flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform"><Check class="w-3.5 h-3.5 text-brand-blue" /> Tem decal giấy, decal nhựa, trong</li>
                  <li class="flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform"><Check class="w-3.5 h-3.5 text-brand-blue" /> Tem vỡ / Tem bảo hành, tem bạc</li>
                  <li class="flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform"><Check class="w-3.5 h-3.5 text-brand-blue" /> Túi giấy Kraft, quai hột xoài, ép kim</li>
                  <li class="flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform"><Check class="w-3.5 h-3.5 text-brand-blue" /> Hộp giấy carton, hộp thực phẩm</li>
                  <li class="flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform"><Check class="w-3.5 h-3.5 text-brand-blue" /> Sticker sheets bế demi theo mẫu</li>
                </ul>
              </div>
              <router-link to="/danh-muc/bao-bi-hop-giay" class="mt-5 text-xs font-bold text-brand-blue group-hover:text-brand-gold hover:underline inline-flex items-center gap-1 transition-colors">
                <span>Xem bao bì & tem nhãn</span>
                <span class="group-hover:translate-x-1 transition-transform">→</span>
              </router-link>
            </div>
          </div>

          <!-- Group 3 -->
          <div class="group h-full">
            <div class="h-full bg-white rounded-2xl p-6 border border-slate-200/90 shadow-sm flex flex-col justify-between group-hover:shadow-xl group-hover:-translate-y-1.5 group-hover:border-emerald-500/50 transition-all duration-300 transform-gpu">
              <div>
                <div class="text-brand-blue font-black text-lg mb-2 group-hover:text-emerald-600 transition-colors flex items-center justify-between">
                  <span>3. In Ấn Văn Phòng</span>
                  <span class="text-xs font-bold text-slate-400 group-hover:text-emerald-500 transition-colors">→</span>
                </div>
                <p class="text-xs text-slate-500 mb-4">Chuẩn mực chuyên nghiệp cho các hoạt động hành chính doanh nghiệp.</p>
                <ul class="space-y-1.5 text-xs text-slate-700 font-medium">
                  <li class="flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform"><Check class="w-3.5 h-3.5 text-brand-blue" /> Danh thiếp (Namecard chuẩn & bo góc)</li>
                  <li class="flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform"><Check class="w-3.5 h-3.5 text-brand-blue" /> Thẻ nhựa PVC (nhân viên, thành viên)</li>
                  <li class="flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform"><Check class="w-3.5 h-3.5 text-brand-blue" /> Phong bì thư A4, A5, A6</li>
                  <li class="flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform"><Check class="w-3.5 h-3.5 text-brand-blue" /> Giấy tiêu đề, kẹp hồ sơ (Folder)</li>
                  <li class="flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform"><Check class="w-3.5 h-3.5 text-brand-blue" /> Hoá đơn bán lẻ, Biểu mẫu Carbonless</li>
                </ul>
              </div>
              <router-link to="/danh-muc/an-pham-van-phong" class="mt-5 text-xs font-bold text-brand-blue group-hover:text-brand-gold hover:underline inline-flex items-center gap-1 transition-colors">
                <span>Xem ấn phẩm văn phòng</span>
                <span class="group-hover:translate-x-1 transition-transform">→</span>
              </router-link>
            </div>
          </div>

          <!-- Group 4 -->
          <div class="group h-full">
            <div class="h-full bg-white rounded-2xl p-6 border border-slate-200/90 shadow-sm flex flex-col justify-between group-hover:shadow-xl group-hover:-translate-y-1.5 group-hover:border-purple-500/50 transition-all duration-300 transform-gpu">
              <div>
                <div class="text-brand-blue font-black text-lg mb-2 group-hover:text-purple-600 transition-colors flex items-center justify-between">
                  <span>4. Sự Kiện & F&B</span>
                  <span class="text-xs font-bold text-slate-400 group-hover:text-purple-500 transition-colors">→</span>
                </div>
                <p class="text-xs text-slate-500 mb-4">Tạo ấn tượng trang trọng cho nhà hàng, quán cafe và hội nghị.</p>
                <ul class="space-y-1.5 text-xs text-slate-700 font-medium">
                  <li class="flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform"><Check class="w-3.5 h-3.5 text-brand-blue" /> Menu nhà hàng (cuốn, tờ rời, bồi format)</li>
                  <li class="flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform"><Check class="w-3.5 h-3.5 text-brand-blue" /> Menu nhựa chống nước, Table tent</li>
                  <li class="flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform"><Check class="w-3.5 h-3.5 text-brand-blue" /> Vé mời sự kiện, Thẻ đeo ban tổ chức</li>
                  <li class="flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform"><Check class="w-3.5 h-3.5 text-brand-blue" /> Vòng tay giấy sự kiện chống rách</li>
                  <li class="flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform"><Check class="w-3.5 h-3.5 text-brand-blue" /> Thiệp mời cao cấp ép kim, dập nổi</li>
                </ul>
              </div>
              <router-link to="/san-pham" class="mt-5 text-xs font-bold text-brand-blue group-hover:text-brand-gold hover:underline inline-flex items-center gap-1 transition-colors">
                <span>Xem ấn phẩm sự kiện</span>
                <span class="group-hover:translate-x-1 transition-transform">→</span>
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- Section 5: Quy trình đặt in 6 bước (Hiệu ứng Auto-Stepper Chuyển Tới & Mũi Tên) -->
      <div 
        class="bg-white rounded-3xl p-6 sm:p-10 lg:p-12 border border-slate-200/90 shadow-sm space-y-8 hover:shadow-lg transition-shadow overflow-hidden"
        @mouseenter="pauseWorkflow"
        @mouseleave="resumeWorkflow"
      >
        <!-- Banner Tiêu Đề WebP Lớn Rõ Nét -->
        <div class="text-center max-w-5xl mx-auto flex flex-col items-center">
          <img
            src="/quy-trinh/tieu-de.webp"
            alt="Quy trình đặt in tại In Nhanh Nhật Nguyên"
            class="w-full max-w-3xl sm:max-w-4xl lg:max-w-5xl h-auto object-contain mx-auto transition-transform duration-300 hover:scale-[1.01]"
            loading="lazy"
          />

          <!-- Thanh chỉ báo bước chuyển tiếp (Progress Dots) -->
          <div class="mt-4 flex items-center justify-center gap-2">
            <button
              v-for="(st, idx) in workflowSteps"
              :key="'about-dot-' + idx"
              @click="activeWorkflowStep = idx"
              class="group p-1 focus:outline-none"
              :aria-label="st.title"
            >
              <span
                class="h-2 rounded-full transition-all duration-500 block"
                :class="activeWorkflowStep === idx 
                  ? 'w-8 bg-brand-gold shadow-sm shadow-brand-gold/50' 
                  : 'w-2 bg-slate-200 group-hover:bg-slate-300'"
              ></span>
            </button>
          </div>
        </div>

        <!-- 6 Tấm WebP Quy Trình (Hiệu ứng Card Chuyển Tới Tuần Tự) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4 lg:gap-5 items-stretch relative">
          <div
            v-for="(st, idx) in workflowSteps"
            :key="idx"
            class="relative flex flex-col h-full group"
            @mouseenter="activeWorkflowStep = idx"
          >
            <div
              class="h-full rounded-2xl p-2 sm:p-3.5 border transition-all duration-500 flex flex-col items-center justify-center text-center cursor-pointer transform-gpu relative overflow-hidden"
              :class="activeWorkflowStep === idx 
                ? 'bg-gradient-to-b from-amber-50/50 via-white to-white border-brand-gold ring-2 ring-brand-gold/40 shadow-xl shadow-brand-gold/20 -translate-y-2' 
                : 'bg-white border-slate-200/80 hover:border-slate-300 hover:shadow-md hover:-translate-y-1 opacity-85 hover:opacity-100'"
            >
              <!-- Tia sáng quét qua (Shimmer Sweep Effect) khi active -->
              <div 
                v-if="activeWorkflowStep === idx"
                class="absolute inset-0 -translate-x-full animate-shimmer-sweep bg-gradient-to-r from-transparent via-white/60 to-transparent pointer-events-none"
              ></div>

              <!-- Badge bước -->
              <div 
                class="absolute top-2 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider transition-all duration-300 z-10"
                :class="activeWorkflowStep === idx 
                  ? 'bg-brand-gold text-slate-900 shadow-xs scale-105' 
                  : 'bg-slate-100 text-slate-400 group-hover:text-slate-600'"
              >
                Bước 0{{ idx + 1 }}
              </div>

              <!-- Khung ảnh cố định chiều cao đồng bộ chuẩn 1:1 -->
              <div class="w-full h-[185px] sm:h-[200px] lg:h-[205px] flex items-center justify-center pt-3">
                <img
                  :src="st.image"
                  :alt="st.title"
                  class="h-full w-auto max-w-full object-contain mx-auto transition-all duration-500 filter"
                  :class="activeWorkflowStep === idx ? 'scale-108 drop-shadow-md' : 'scale-100 drop-shadow-xs'"
                  loading="lazy"
                />
              </div>

              <!-- Vệt sáng chân card (Active Bottom Glow Bar) -->
              <div 
                class="absolute bottom-0 left-0 right-0 h-1 transition-all duration-500"
                :class="activeWorkflowStep === idx ? 'bg-gradient-to-r from-brand-gold/20 via-brand-gold to-brand-gold/20 opacity-100' : 'opacity-0'"
              ></div>
            </div>

            <!-- Mũi tên động "chuyển tới" bước kế tiếp (Hiển thị từ bước 1 -> 5 trên desktop) -->
            <div 
              v-if="idx < workflowSteps.length - 1"
              class="hidden lg:flex absolute -right-3.5 top-1/2 -translate-y-1/2 z-20 pointer-events-none items-center justify-center w-7 h-7 rounded-full bg-white border shadow-xs transition-all duration-500"
              :class="activeWorkflowStep === idx 
                ? 'border-brand-gold text-brand-gold shadow-md shadow-brand-gold/30 translate-x-1 scale-110' 
                : 'border-slate-200 text-slate-300'"
            >
              <svg 
                xmlns="http://www.w3.org/2000/svg" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2.5" 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                class="w-3.5 h-3.5 transition-transform duration-300"
                :class="activeWorkflowStep === idx ? 'animate-move-forward' : ''"
              >
                <path d="m9 18 6-6-6-6"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Section 6: Direct Workshop Contact & Sales Team with Shimmering Gradient -->
      <div class="rounded-3xl bg-gradient-to-r from-brand-blue-dark via-brand-blue to-slate-900 text-white p-8 sm:p-12 shadow-2xl space-y-8 relative overflow-hidden group">
        <!-- Decorative Ambient Glow -->
        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-brand-gold/20 rounded-full blur-3xl pointer-events-none group-hover:scale-125 transition-transform duration-700"></div>

        <div class="max-w-2xl relative z-10">
          <span class="text-xs font-bold text-brand-gold uppercase tracking-wider block mb-2">
            ĐỘI NGŨ TƯ VẤN TRỰC TIẾP TẠI XƯỞNG
          </span>
          <h2 class="text-2xl sm:text-3xl font-black tracking-tight">
            Bạn Cần Tư Vấn Báo Giá Hoặc Test Mẫu In?
          </h2>
          <p class="text-sm text-slate-200 mt-2">
            Liên hệ ngay với nhân viên kinh doanh phụ trách để được hỗ trợ kiểm tra file in miễn phí và nhận báo giá xưởng tốt nhất trong 5 phút.
          </p>
        </div>

        <!-- Sales Representatives with Glassmorphism Hover -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 relative z-10">
          <div class="bg-white/10 hover:bg-white/15 backdrop-blur-md rounded-2xl p-5 border border-white/20 hover:border-brand-gold/50 flex items-center justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-lg group/sales">
            <div>
              <span class="text-xs text-brand-gold font-bold uppercase tracking-wider block">Tư vấn đặt hàng & Báo giá</span>
              <div class="text-lg font-black mt-0.5 flex items-center gap-2">
                <span>Minh Nguyên</span>
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
              </div>
              <div class="text-xs text-slate-300 mt-0.5">{{ siteSettings.workingHours }}</div>
            </div>
            <a
              :href="'tel:' + siteSettings.hotlines[0].number.replace(/\s+/g, '')"
              class="px-4 py-2.5 rounded-xl bg-brand-gold text-slate-950 font-black text-sm hover:bg-brand-gold-hover transition-all duration-300 shrink-0 shadow-md hover:scale-105 active:scale-95 flex items-center gap-1.5"
            >
              <PhoneCall class="w-4 h-4 animate-bounce" />
              <span>{{ siteSettings.hotlines[0].number }}</span>
            </a>
          </div>

          <div class="bg-white/10 hover:bg-white/15 backdrop-blur-md rounded-2xl p-5 border border-white/20 hover:border-brand-gold/50 flex items-center justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-lg group/sales">
            <div>
              <span class="text-xs text-brand-gold font-bold uppercase tracking-wider block">Tư vấn kỹ thuật & File in</span>
              <div class="text-lg font-black mt-0.5 flex items-center gap-2">
                <span>Trường Hưng</span>
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
              </div>
              <div class="text-xs text-slate-300 mt-0.5">{{ siteSettings.workingHours }}</div>
            </div>
            <a
              :href="'tel:' + siteSettings.hotlines[1].number.replace(/\s+/g, '')"
              class="px-4 py-2.5 rounded-xl bg-brand-gold text-slate-950 font-black text-sm hover:bg-brand-gold-hover transition-all duration-300 shrink-0 shadow-md hover:scale-105 active:scale-95 flex items-center gap-1.5"
            >
              <PhoneCall class="w-4 h-4 animate-bounce" />
              <span>{{ siteSettings.hotlines[1].number }}</span>
            </a>
          </div>
        </div>

        <!-- Workshop Address Info -->
        <div class="pt-6 border-t border-white/15 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-300 relative z-10">
          <div class="space-y-1 text-center sm:text-left">
            <div><strong>Xưởng sản xuất:</strong> {{ siteSettings.address }}</div>
            <div><strong>Văn phòng giao dịch:</strong> 167 Hà Huy Giáp, Phường An Phú Đông, Quận 12, TP.HCM</div>
          </div>
          <div class="flex items-center gap-3">
            <router-link
              to="/yeu-cau-bao-gia"
              class="px-5 py-2.5 rounded-xl bg-white text-slate-900 font-bold hover:bg-brand-gold hover:text-slate-950 transition-all duration-300 shadow-md hover:-translate-y-0.5"
            >
              Gửi yêu cầu báo giá
            </router-link>
            <router-link
              to="/lien-he"
              class="px-5 py-2.5 rounded-xl bg-white/10 text-white font-bold hover:bg-white/20 border border-white/20 transition-all duration-300 hover:-translate-y-0.5"
            >
              Bản đồ xưởng &rarr;
            </router-link>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import {
  Award,
  Printer,
  BadgePercent,
  Sparkles,
  ShieldCheck,
  Check,
  PhoneCall
} from 'lucide-vue-next'
import { siteSettings } from '../data/siteSettings'

const activeWorkflowStep = ref(0)
let workflowTimer = null
const isWorkflowPaused = ref(false)

const workflowSteps = [
  {
    step: '01',
    title: '1. Tiếp Nhận Yêu Cầu',
    image: '/quy-trinh/buoc-1.webp'
  },
  {
    step: '02',
    title: '2. Tư Vấn & Báo Giá',
    image: '/quy-trinh/buoc-2.webp'
  },
  {
    step: '03',
    title: '3. Thiết Kế & Duyệt File',
    image: '/quy-trinh/buoc-3.webp'
  },
  {
    step: '04',
    title: '4. Tiến Hành In Ấn',
    image: '/quy-trinh/buoc-4.webp'
  },
  {
    step: '05',
    title: '5. Hỗ Trợ Gửi Giao Hàng',
    image: '/quy-trinh/buoc-5.webp'
  },
  {
    step: '06',
    title: '6. Hỗ Trợ Sau In',
    image: '/quy-trinh/buoc-6.webp'
  }
]

const startWorkflowAutoPlay = () => {
  stopWorkflowAutoPlay()
  workflowTimer = setInterval(() => {
    if (!isWorkflowPaused.value) {
      activeWorkflowStep.value = (activeWorkflowStep.value + 1) % workflowSteps.length
    }
  }, 2200)
}

const stopWorkflowAutoPlay = () => {
  if (workflowTimer) {
    clearInterval(workflowTimer)
    workflowTimer = null
  }
}

const pauseWorkflow = () => {
  isWorkflowPaused.value = true
}

const resumeWorkflow = () => {
  isWorkflowPaused.value = false
}

onMounted(() => {
  startWorkflowAutoPlay()
})

onBeforeUnmount(() => {
  stopWorkflowAutoPlay()
})
</script>

<style scoped>
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fadeInUp 0.6s ease-out forwards;
}

@keyframes moveForward {
  0%, 100% {
    transform: translateX(0);
  }
  50% {
    transform: translateX(3px);
  }
}

.animate-move-forward {
  animation: moveForward 0.9s ease-in-out infinite;
}

@keyframes shimmerSweep {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(200%);
  }
}

.animate-shimmer-sweep {
  animation: shimmerSweep 1.2s ease-out forwards;
}
</style>
