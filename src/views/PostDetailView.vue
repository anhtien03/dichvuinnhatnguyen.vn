<template>
  <div class="py-8 lg:py-14 bg-slate-50 min-h-screen relative">
    <!-- Reading Progress Bar (Fixed on top) -->
    <div
      class="fixed top-0 left-0 right-0 h-1 bg-slate-200 z-50 pointer-events-none"
      aria-hidden="true"
    >
      <div
        class="h-full bg-gradient-to-r from-amber-500 via-orange-500 to-brand-gold transition-all duration-150 ease-out"
        :style="{ width: `${readingProgress}%` }"
      ></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium overflow-x-auto pb-1">
        <router-link to="/" class="hover:text-brand-blue flex items-center gap-1 shrink-0">
          <span>Trang chủ</span>
        </router-link>
        <span class="text-slate-400">&rsaquo;</span>
        <router-link to="/tin-tuc" class="hover:text-brand-blue shrink-0">
          Cẩm nang in ấn
        </router-link>
        <span class="text-slate-400">&rsaquo;</span>
        <span class="text-slate-900 truncate max-w-xs font-semibold">
          {{ post?.title || 'Bài viết' }}
        </span>
      </nav>

      <!-- Loading State -->
      <div v-if="isLoading" class="py-24 text-center space-y-4">
        <Loader2 class="w-10 h-10 animate-spin mx-auto text-brand-gold" />
        <p class="text-sm font-medium text-slate-500">Đang tải nội dung bài viết chuyên sâu...</p>
      </div>

      <!-- Post Not Found -->
      <div v-else-if="!post" class="text-center py-20 bg-white rounded-3xl p-8 border border-slate-200 shadow-sm max-w-2xl mx-auto">
        <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-500 mx-auto flex items-center justify-center mb-4">
          <HelpCircle class="w-8 h-8" />
        </div>
        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 mb-2">Không tìm thấy bài viết</h2>
        <p class="text-sm text-slate-500 mb-6 max-w-md mx-auto">
          Bài viết có thể đã được cập nhật đường dẫn mới hoặc tạm thời không khả dụng.
        </p>
        <router-link
          to="/tin-tuc"
          class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-brand-blue text-white font-bold text-sm shadow-md hover:bg-blue-700 transition-all"
        >
          <ArrowLeft class="w-4 h-4" />
          <span>Quay về danh sách cẩm nang</span>
        </router-link>
      </div>

      <!-- Article Main 2-Column Grid -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- LEFT COLUMN: Main Content (8 cols) -->
        <main class="lg:col-span-8 space-y-8 min-w-0">
          <article class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200/90 shadow-sm space-y-8">
            
            <!-- Article Header Info -->
            <header class="space-y-4 border-b border-slate-100 pb-6">
              <!-- Meta Badges -->
              <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs">
                <span class="px-3 py-1 rounded-full font-bold bg-brand-blue/10 text-brand-blue flex items-center gap-1">
                  <Tag class="w-3.5 h-3.5" />
                  {{ post.category || 'Cẩm nang in ấn' }}
                </span>
                <span class="text-slate-300">•</span>
                <span class="text-slate-500 flex items-center gap-1">
                  <Calendar class="w-3.5 h-3.5 text-slate-400" />
                  {{ formatDate(post.published_at || post.created_at) }}
                </span>
                <span class="text-slate-300">•</span>
                <span class="text-slate-500 flex items-center gap-1">
                  <Clock class="w-3.5 h-3.5 text-slate-400" />
                  {{ readingTime }} phút đọc
                </span>
                <span class="text-slate-300">•</span>
                <span class="text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full font-semibold flex items-center gap-1">
                  <ShieldCheck class="w-3.5 h-3.5" />
                  Đã kiểm duyệt chuyên môn
                </span>
              </div>

              <!-- Main Title H1 -->
              <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 leading-tight tracking-tight">
                {{ post.title }}
              </h1>

              <!-- Author Sub-bar -->
              <div class="flex items-center justify-between pt-2">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-brand-blue to-blue-500 text-white font-bold flex items-center justify-center text-sm shadow-sm ring-2 ring-blue-100">
                    NN
                  </div>
                  <div>
                    <p class="text-xs sm:text-sm font-bold text-slate-800">
                      {{ post.author_name || 'Ban Biên Tập Xưởng In Nhật Nguyên' }}
                    </p>
                    <p class="text-xs text-slate-500">Chuyên viên Kỹ thuật In ấn & Bao bì</p>
                  </div>
                </div>

                <!-- Quick Action Buttons -->
                <div class="flex items-center gap-2">
                  <button
                    @click="copyLink"
                    class="p-2 sm:px-3 sm:py-1.5 rounded-xl border border-slate-200 text-slate-600 hover:text-brand-blue hover:border-brand-blue text-xs font-semibold flex items-center gap-1.5 transition-all"
                    :title="isCopied ? 'Đã sao chép link' : 'Sao chép liên kết'"
                  >
                    <Check v-if="isCopied" class="w-3.5 h-3.5 text-emerald-600" />
                    <Copy v-else class="w-3.5 h-3.5" />
                    <span class="hidden sm:inline">{{ isCopied ? 'Đã chép link' : 'Chia sẻ' }}</span>
                  </button>
                  <button
                    @click="printArticle"
                    class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:text-slate-900 text-xs hidden sm:flex items-center"
                    title="In bài viết"
                  >
                    <Printer class="w-3.5 h-3.5" />
                  </button>
                </div>
              </div>

              <!-- Excerpt / Key Takeaways Box -->
              <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-amber-50/70 via-orange-50/40 to-blue-50/50 border border-amber-200/60 space-y-2">
                <div class="flex items-center gap-2 text-xs font-bold text-amber-900 tracking-wide uppercase">
                  <Sparkles class="w-4 h-4 text-amber-600" />
                  <span>Tóm Tắt Nhanh & Lời Khuyên Từ Xưởng In</span>
                </div>
                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed italic font-medium">
                  {{ post.excerpt || 'Bài viết phân tích chi tiết quy cách, ưu thế công nghệ và hướng dẫn đặt in tối ưu chi phí trực tiếp tại Xưởng In Ấn Nhật Nguyên.' }}
                </p>
              </div>
            </header>

            <!-- Featured Hero Image -->
            <div class="aspect-[16/9] rounded-2xl overflow-hidden bg-slate-100 shadow-md border border-slate-200/80 relative group">
              <img
                :src="post.thumbnail || '/banner-chinh.jpg'"
                :alt="post.title"
                class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-500"
              />
              <div class="absolute bottom-0 inset-x-0 p-3 bg-gradient-to-t from-slate-950/80 via-slate-950/40 to-transparent text-white text-xs flex items-center justify-between">
                <span class="font-medium truncate">Mẫu ấn phẩm hoàn thiện trực tiếp tại Xưởng In Nhật Nguyên</span>
                <span class="text-amber-300 font-bold shrink-0 text-[11px]">Cam kết sắc nét 100%</span>
              </div>
            </div>

            <!-- Mobile Table of Contents (Shown only on small screens) -->
            <div v-if="tocItems.length > 0" class="lg:hidden rounded-2xl border border-slate-200 bg-slate-50/80 p-4 space-y-3">
              <button
                @click="isMobileTocOpen = !isMobileTocOpen"
                class="w-full flex items-center justify-between font-bold text-sm text-slate-900"
              >
                <div class="flex items-center gap-2 text-brand-blue">
                  <ListTree class="w-4 h-4 text-brand-gold" />
                  <span>Mục Lục Bài Viết ({{ tocItems.length }} mục)</span>
                </div>
                <ChevronRight
                  class="w-4 h-4 text-slate-400 transition-transform duration-200"
                  :class="{ 'rotate-90': isMobileTocOpen }"
                />
              </button>

              <nav v-show="isMobileTocOpen" class="space-y-1.5 pt-2 border-t border-slate-200/60 text-xs">
                <a
                  v-for="(item, index) in tocItems"
                  :key="index"
                  :href="`#${item.id}`"
                  @click.prevent="scrollToSection(item.id)"
                  class="block py-1.5 px-2 rounded-lg text-slate-700 hover:bg-white hover:text-brand-blue font-medium transition-colors"
                  :class="{ 'text-brand-blue font-bold bg-white shadow-2xs': activeTocId === item.id }"
                >
                  {{ item.text }}
                </a>
              </nav>
            </div>

            <!-- Dynamic Rich Body Content -->
            <div
              class="prose prose-slate max-w-none text-slate-800 text-sm sm:text-base leading-relaxed space-y-6 post-content"
              v-html="renderedContent"
            ></div>

            <!-- In-Article Consultation CTA Box -->
            <div class="rounded-3xl p-6 sm:p-8 bg-gradient-to-br from-brand-blue via-blue-800 to-slate-950 text-white shadow-xl relative overflow-hidden space-y-4">
              <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-brand-gold/20 rounded-full blur-2xl pointer-events-none"></div>

              <div class="flex flex-wrap items-center justify-between gap-4 relative z-10">
                <div class="space-y-1 max-w-lg">
                  <span class="px-2.5 py-0.5 rounded-full bg-brand-gold text-slate-950 font-black text-[11px] uppercase tracking-wider">
                    Tư vấn trực tiếp 24/7
                  </span>
                  <h3 class="text-xl sm:text-2xl font-black text-white">
                    Cần In Decal / Ấn Phẩm Theo Yêu Cầu?
                  </h3>
                  <p class="text-xs sm:text-sm text-blue-100/90 leading-relaxed">
                    Xưởng In Nhật Nguyên nhận in mẫu thử, hỗ trợ chỉnh file thiết kế chuẩn in trong 15 phút.
                  </p>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto shrink-0">
                  <a
                    href="https://zalo.me/0767598368"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="w-full sm:w-auto px-5 py-3 rounded-full bg-brand-gold hover:bg-amber-400 text-slate-950 font-black text-xs sm:text-sm shadow-lg text-center transition-all flex items-center justify-center gap-2"
                  >
                    <MessageSquare class="w-4 h-4" />
                    <span>Chat Zalo Báo Giá</span>
                  </a>
                  <a
                    href="tel:0767598368"
                    class="w-full sm:w-auto px-5 py-3 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold text-xs sm:text-sm border border-white/20 text-center transition-all flex items-center justify-center gap-2"
                  >
                    <Phone class="w-4 h-4 text-brand-gold" />
                    <span>076.759.8368</span>
                  </a>
                </div>
              </div>
            </div>

            <!-- Share & Print Toolbar -->
            <div class="flex flex-wrap items-center justify-between gap-4 pt-6 border-t border-slate-100">
              <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                <Share2 class="w-4 h-4 text-slate-400" />
                <span>Chia sẻ bài viết này:</span>
              </div>

              <div class="flex items-center gap-2">
                <button
                  @click="shareZalo"
                  class="px-3 py-1.5 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs font-bold flex items-center gap-1.5 transition-colors"
                >
                  <span>Zalo</span>
                </button>
                <button
                  @click="shareFacebook"
                  class="px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 text-xs font-bold flex items-center gap-1.5 transition-colors"
                >
                  <span>Facebook</span>
                </button>
                <button
                  @click="copyLink"
                  class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold flex items-center gap-1.5 transition-colors"
                >
                  <Copy class="w-3.5 h-3.5" />
                  <span>{{ isCopied ? 'Đã chép!' : 'Chép link' }}</span>
                </button>
              </div>
            </div>

            <!-- Author Bio Card (E-E-A-T) -->
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row items-center sm:items-start gap-4">
              <div class="w-16 h-16 rounded-2xl bg-brand-blue text-white font-black text-xl flex items-center justify-center shrink-0 shadow-md">
                NN
              </div>
              <div class="space-y-1.5 text-center sm:text-left">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                  <h4 class="text-sm font-bold text-slate-900">Xưởng In Ấn Nhật Nguyên</h4>
                  <span class="text-[11px] bg-brand-blue/10 text-brand-blue font-bold px-2 py-0.5 rounded-md">
                    Chuyên Gia In Ấn 10+ Năm
                  </span>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                  Nhật Nguyên là xưởng sản xuất trực tiếp tại TP.HCM chuyên in decal DTF, tem nhãn cuộn, bao bì hộp giấy, danh thiếp và ấn phẩm quảng cáo. Sở hữu dàn máy in kỹ thuật số công nghiệp hiện đại, nhận in mọi số lượng với giá gốc tại xưởng.
                </p>
                <div class="pt-1 flex items-center justify-center sm:justify-start gap-4 text-xs font-bold text-brand-blue">
                  <a href="tel:0767598368" class="hover:underline flex items-center gap-1">
                    <Phone class="w-3 h-3 text-brand-gold" />
                    <span>076.759.8368</span>
                  </a>
                  <router-link to="/gioi-thieu" class="hover:underline flex items-center gap-1 text-slate-600">
                    <span>Tìm hiểu về xưởng &rsaquo;</span>
                  </router-link>
                </div>
              </div>
            </div>

          </article>

          <!-- Related Posts Section (Lưới bài viết liên quan) -->
          <section v-if="relatedPosts.length > 0" class="space-y-4 pt-4">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <Bookmark class="w-5 h-5 text-brand-gold" />
                <span>Bài Viết Cùng Chuyên Mục</span>
              </h3>
              <router-link to="/tin-tuc" class="text-xs font-bold text-brand-blue hover:underline flex items-center gap-1">
                <span>Xem tất cả</span>
                <ChevronRight class="w-3.5 h-3.5" />
              </router-link>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <router-link
                v-for="relPost in relatedPosts"
                :key="relPost.id"
                :to="`/tin-tuc/${relPost.slug}`"
                class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-brand-blue/40 transition-all flex gap-3 group"
              >
                <div class="w-20 h-20 rounded-xl overflow-hidden bg-slate-100 shrink-0">
                  <img
                    :src="relPost.thumbnail || '/banner-chinh.jpg'"
                    :alt="relPost.title"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                  />
                </div>
                <div class="flex flex-col justify-between py-0.5 min-w-0">
                  <h4 class="text-xs sm:text-sm font-bold text-slate-900 line-clamp-2 group-hover:text-brand-blue transition-colors leading-snug">
                    {{ relPost.title }}
                  </h4>
                  <span class="text-[11px] text-slate-400 flex items-center gap-1">
                    <Calendar class="w-3 h-3" />
                    {{ formatDate(relPost.published_at || relPost.created_at) }}
                  </span>
                </div>
              </router-link>
            </div>
          </section>
        </main>

        <!-- RIGHT COLUMN: Sticky Sidebar (4 cols) -->
        <aside class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
          
          <!-- Widget 1: Interactive Table of Contents (TOC) -->
          <div v-if="tocItems.length > 0" class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-4 hidden lg:block">
            <div class="flex items-center gap-2 font-black text-sm text-slate-900 pb-3 border-b border-slate-100">
              <ListTree class="w-4 h-4 text-brand-gold" />
              <span>Mục Lục Bài Viết</span>
            </div>

            <nav class="space-y-1 max-h-[380px] overflow-y-auto pr-1 text-xs">
              <a
                v-for="(item, idx) in tocItems"
                :key="idx"
                :href="`#${item.id}`"
                @click.prevent="scrollToSection(item.id)"
                class="block py-2 px-3 rounded-xl transition-all font-medium leading-snug"
                :class="activeTocId === item.id
                  ? 'bg-brand-blue/10 text-brand-blue font-bold shadow-2xs translate-x-1'
                  : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
              >
                <div class="flex items-start gap-2">
                  <span
                    class="w-4 h-4 rounded-full text-[10px] flex items-center justify-center shrink-0 mt-0.5"
                    :class="activeTocId === item.id ? 'bg-brand-blue text-white' : 'bg-slate-100 text-slate-500'"
                  >
                    {{ idx + 1 }}
                  </span>
                  <span class="line-clamp-2">{{ item.text }}</span>
                </div>
              </a>
            </nav>
          </div>

          <!-- Widget 2: Fast Quote / Zalo Consultation -->
          <div class="bg-gradient-to-br from-brand-blue to-blue-900 text-white rounded-3xl p-6 shadow-md space-y-4 relative overflow-hidden">
            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-brand-gold mb-2">
              <Phone class="w-6 h-6" />
            </div>

            <div class="space-y-1">
              <h4 class="font-black text-base text-white">Bạn Cần Báo Giá In Nhanh?</h4>
              <p class="text-xs text-blue-100/90 leading-relaxed">
                Gửi kích thước và file in qua Zalo để nhận báo giá chi tiết trong 5 - 15 phút.
              </p>
            </div>

            <div class="p-3 bg-white/10 rounded-2xl space-y-1">
              <div class="text-[11px] text-blue-200">Hotline & Zalo Kỹ Thuật:</div>
              <div class="text-lg font-black text-brand-gold tracking-wider">076.759.8368</div>
            </div>

            <div class="space-y-2 pt-1">
              <a
                href="https://zalo.me/0767598368"
                target="_blank"
                rel="noopener noreferrer"
                class="w-full py-3 rounded-xl bg-brand-gold hover:bg-amber-400 text-slate-950 font-black text-xs text-center block shadow transition-all"
              >
                Nhắn Zalo Ngay →
              </a>
              <router-link
                to="/yeu-cau-bao-gia"
                class="w-full py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs text-center block transition-all"
              >
                Gửi Form Báo Giá
              </router-link>
            </div>
          </div>

          <!-- Widget 3: Featured Products (Sản phẩm in ấn liên quan) -->
          <div v-if="relatedProducts.length > 0" class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
              <h4 class="font-black text-sm text-slate-900 flex items-center gap-1.5">
                <Layers class="w-4 h-4 text-brand-gold" />
                <span>Dịch Vụ In Nổi Bật</span>
              </h4>
              <router-link to="/san-pham" class="text-[11px] font-bold text-brand-blue hover:underline">
                Xem tất cả
              </router-link>
            </div>

            <div class="space-y-3">
              <router-link
                v-for="prod in relatedProducts"
                :key="prod.id"
                :to="`/san-pham/${prod.slug}`"
                class="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-slate-50 transition-colors group border border-transparent hover:border-slate-100"
              >
                <div class="w-14 h-14 rounded-xl overflow-hidden bg-slate-100 shrink-0 border border-slate-100">
                  <img
                    :src="prod.image || '/logo.png'"
                    :alt="prod.name"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                  />
                </div>
                <div class="min-w-0 flex-1">
                  <h5 class="text-xs font-bold text-slate-800 group-hover:text-brand-blue truncate">
                    {{ prod.name }}
                  </h5>
                  <p class="text-[11px] text-slate-500 truncate">
                    Thời gian: {{ prod.turnaround || '24h' }}
                  </p>
                  <p class="text-xs font-black text-amber-600 mt-0.5">
                    {{ formatCurrency(prod.base_price) }}
                  </p>
                </div>
                <ChevronRight class="w-4 h-4 text-slate-300 group-hover:text-brand-blue group-hover:translate-x-0.5 transition-all" />
              </router-link>
            </div>
          </div>

          <!-- Widget 4: Gold Trust Guarantees -->
          <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-3">
            <h4 class="font-black text-xs uppercase tracking-wider text-slate-400">
              Cam Kết Từ Nhật Nguyên
            </h4>

            <ul class="space-y-2.5 text-xs text-slate-700">
              <li class="flex items-start gap-2">
                <CheckCircle2 class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                <span><strong>Giá gốc tận xưởng:</strong> Tiết kiệm đến 20-30% chi phí trung gian.</span>
              </li>
              <li class="flex items-start gap-2">
                <CheckCircle2 class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                <span><strong>Test màu miễn phí:</strong> Duyệt file mẫu trước khi bấm in số lượng lớn.</span>
              </li>
              <li class="flex items-start gap-2">
                <CheckCircle2 class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                <span><strong>Đổi trả 100%:</strong> Cam kết hoàn tiền hoặc in lại nếu sai lỗi xưởng.</span>
              </li>
              <li class="flex items-start gap-2">
                <CheckCircle2 class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                <span><strong>Giao hàng hỏa tốc:</strong> Hỗ trợ ship nhanh toàn quốc và nội thành TP.HCM.</span>
              </li>
            </ul>
          </div>

        </aside>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import {
  Calendar, Loader2, ArrowLeft, Clock, Share2, Copy, Check,
  Phone, MessageSquare, ChevronRight, Bookmark,
  ShieldCheck, Printer, ListTree, Sparkles, Tag, HelpCircle,
  CheckCircle2, Layers
} from 'lucide-vue-next'
import { api } from '../services/api'
import { setMetaTags } from '../composables/useSEO'

const route = useRoute()
const post = ref(null)
const isLoading = ref(true)
const isCopied = ref(false)
const readingProgress = ref(0)
const isMobileTocOpen = ref(false)
const activeTocId = ref('')

const relatedPosts = ref([])
const relatedProducts = ref([])

function formatDate(dateStr) {
  if (!dateStr) return '—'
  const d = new Date(dateStr)
  return d.toLocaleDateString('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

function formatCurrency(val) {
  if (!val || val <= 0) return 'Liên hệ báo giá'
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val)
}

// Estimated reading time
const readingTime = computed(() => {
  const content = post.value?.content || ''
  const words = content.replace(/<[^>]*>/g, '').trim().split(/\s+/).length
  return Math.max(2, Math.ceil(words / 200))
})

// Transform plain text or raw text into magazine-grade structured HTML
function formatPlainTextToHtml(text) {
  if (!text) return ''

  // 1. Remove duplicate raw "MỤC LỤC\n1. ...\n2. ..." block if present, since our dynamic TOC handles it
  let clean = text.replace(/MỤC LỤC\s*\n(\d+\.\s+[^\n]+\n*)+/gi, '')

  // 2. Split into lines
  const lines = clean.split('\n')
  const htmlParts = []
  let inList = false

  lines.forEach((line) => {
    const trimmed = line.trim()
    if (!trimmed) {
      if (inList) {
        htmlParts.push('</ul>')
        inList = false
      }
      return
    }

    // Numbered Main Heading e.g. "1. Giới thiệu dịch vụ in decal DTF"
    const headingMatch = trimmed.match(/^(\d+)\.\s+(.*)$/)
    if (headingMatch && trimmed.length < 80) {
      if (inList) {
        htmlParts.push('</ul>')
        inList = false
      }
      const num = headingMatch[1]
      const title = headingMatch[2]
      const anchorId = `muc-${num}-${slugify(title)}`
      htmlParts.push(`
        <h2 id="${anchorId}" class="article-heading-2">
          <span class="heading-badge">${num}</span>
          <span>${title}</span>
        </h2>
      `)
      return
    }

    // Step Cards e.g. "Bước 1: Tiếp nhận file thiết kế..."
    const stepMatch = trimmed.match(/^Bước\s*(\d+)[:\s]+(.*)$/i)
    if (stepMatch) {
      if (inList) {
        htmlParts.push('</ul>')
        inList = false
      }
      const stepNum = stepMatch[1]
      const stepText = stepMatch[2]
      htmlParts.push(`
        <div class="article-step-card">
          <div class="step-badge">Bước ${stepNum}</div>
          <div class="step-content">${stepText}</div>
        </div>
      `)
      return
    }

    // Bullet points / Feature lines e.g. "Màu sắc sắc nét, chân thực: ..." or "- ..."
    const isBullet = trimmed.startsWith('- ') || trimmed.startsWith('• ') || /^[A-ZÀ-Ỹa-zà-ỹ\s]+:\s+[A-ZÀ-Ỹa-zà-ỹ]/.test(trimmed)
    if (isBullet && !trimmed.startsWith('Bước') && trimmed.includes(':')) {
      if (!inList) {
        htmlParts.push('<ul class="article-feature-list">')
        inList = true
      }
      const parts = trimmed.split(':')
      const label = parts[0].replace(/^[-•]\s*/, '').trim()
      const desc = parts.slice(1).join(':').trim()
      htmlParts.push(`
        <li class="article-feature-item">
          <span class="feature-label">${label}:</span>
          <span class="feature-desc">${desc}</span>
        </li>
      `)
      return
    }

    // Normal paragraph
    if (inList) {
      htmlParts.push('</ul>')
      inList = false
    }

    // If it mentions Hotline / Zalo contact, make it an attractive contact note
    if (trimmed.includes('076.759.8368') || trimmed.includes('Hotline')) {
      htmlParts.push(`
        <div class="article-contact-box">
          <div class="contact-icon">📞</div>
          <div>${trimmed}</div>
        </div>
      `)
      return
    }

    htmlParts.push(`<p class="article-paragraph">${trimmed}</p>`)
  })

  if (inList) {
    htmlParts.push('</ul>')
  }

  return htmlParts.join('\n')
}

// Generate slug for anchor
function slugify(text) {
  return (text || '')
    .toString()
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[đĐ]/g, 'd')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
    .slice(0, 40)
}

// Extract Table of Contents items and ensure headings have IDs
const tocItems = ref([])

const renderedContent = computed(() => {
  let raw = post.value?.content || ''
  if (!raw.trim()) return '<p>Bài viết đang cập nhật nội dung...</p>'

  const isHtml = /<[a-z][\s\S]*>/i.test(raw)
  let html = isHtml ? raw : formatPlainTextToHtml(raw)

  // Parse headings into TOC
  const items = []
  let counter = 0

  html = html.replace(/<h([23])([^>]*)>(.*?)<\/h\1>/gi, (match, level, attrs, text) => {
    counter++
    const cleanText = text.replace(/<[^>]*>/g, '').trim()
    
    // Extract existing id or generate one
    let id = ''
    const idMatch = attrs.match(/id="([^"]*)"/i)
    if (idMatch) {
      id = idMatch[1]
    } else {
      id = `heading-${counter}-${slugify(cleanText)}`
      attrs = `${attrs} id="${id}"`
    }

    items.push({
      id,
      text: cleanText,
      level: parseInt(level, 10)
    })

    return `<h${level}${attrs}>${text}</h${level}>`
  })

  tocItems.value = items
  return html
})

function scrollToSection(id) {
  const el = document.getElementById(id)
  if (el) {
    const yOffset = -90
    const y = el.getBoundingClientRect().top + window.pageYOffset + yOffset
    window.scrollTo({ top: y, behavior: 'smooth' })
    activeTocId.value = id
    isMobileTocOpen.value = false
  }
}

function handleScroll() {
  // Reading progress
  const total = document.documentElement.scrollHeight - window.innerHeight
  if (total > 0) {
    readingProgress.value = Math.min(100, Math.max(0, (window.scrollY / total) * 100))
  }

  // Active TOC section
  if (!tocItems.value.length) return
  const headings = tocItems.value
    .map(item => document.getElementById(item.id))
    .filter(Boolean)

  const scrollPos = window.scrollY + 120
  for (let i = headings.length - 1; i >= 0; i--) {
    const el = headings[i]
    if (el.offsetTop <= scrollPos) {
      activeTocId.value = el.id
      break
    }
  }
}

function copyLink() {
  navigator.clipboard.writeText(window.location.href)
  isCopied.value = true
  setTimeout(() => {
    isCopied.value = false
  }, 2500)
}

function shareFacebook() {
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}`, '_blank', 'width=600,height=400')
}

function shareZalo() {
  window.open(`https://sp.zalo.me/share_inline?link=${encodeURIComponent(window.location.href)}`, '_blank', 'width=600,height=400')
}

function printArticle() {
  window.print()
}

async function loadSidebarData() {
  try {
    const [postsRes, prodsRes] = await Promise.allSettled([
      api.getPosts({ limit: 6 }),
      api.getProducts({ limit: 4 })
    ])

    if (postsRes.status === 'fulfilled' && postsRes.value?.data) {
      relatedPosts.value = postsRes.value.data
        .filter(p => p.slug !== route.params.slug)
        .slice(0, 4)
    }

    if (prodsRes.status === 'fulfilled' && prodsRes.value?.data) {
      relatedProducts.value = prodsRes.value.data.slice(0, 4)
    }
  } catch (err) {
    console.warn('Could not load sidebar recommendations', err)
  }
}

async function fetchPost() {
  try {
    isLoading.value = true
    const slug = route.params.slug
    const res = await api.getPostBySlug(slug)
    if (res.data) {
      post.value = res.data

      // Apply dynamic SEO
      setMetaTags({
        title: post.value.meta_title || `${post.value.title} - Cẩm Nang In Ấn`,
        description: post.value.meta_description || post.value.excerpt || 'Cẩm nang hướng dẫn kỹ thuật xuất file in và chọn chất liệu in ấn tại Xưởng In Nhật Nguyên.',
        image: post.value.thumbnail || 'https://dichvuinnhatnguyen.vn/logo.png',
        url: window.location.href,
        type: 'article',
        schema: {
          '@context': 'https://schema.org',
          '@type': 'Article',
          headline: post.value.title,
          description: post.value.excerpt || post.value.meta_description,
          image: post.value.thumbnail || 'https://dichvuinnhatnguyen.vn/logo.png',
          datePublished: post.value.published_at || post.value.created_at,
          author: {
            '@type': 'Organization',
            name: 'Xưởng In Ấn Nhật Nguyên'
          }
        }
      })
    }
  } catch (err) {
    console.error('Failed to load post', err)
    post.value = null
  } finally {
    isLoading.value = false
  }
}

watch(() => route.params.slug, () => {
  fetchPost()
  loadSidebarData()
})

onMounted(() => {
  fetchPost()
  loadSidebarData()
  window.addEventListener('scroll', handleScroll, { passive: true })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style>
/* Typography Styling for Magazine Article Content */
.post-content {
  font-family: inherit;
  color: #1e293b;
}

.article-heading-2,
.post-content h2 {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1.4rem;
  font-weight: 800;
  color: #0f172a;
  margin-top: 2.25rem;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #f1f5f9;
  line-height: 1.35;
  scroll-margin-top: 90px;
}

@media (min-width: 640px) {
  .article-heading-2,
  .post-content h2 {
    font-size: 1.6rem;
  }
}

.heading-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  height: 2rem;
  border-radius: 9999px;
  background: linear-space(#0f3460, #001f3f);
  background-color: #0f3460;
  color: #f59e0b;
  font-size: 0.875rem;
  font-weight: 900;
  flex-shrink: 0;
  box-shadow: 0 2px 4px rgba(15, 52, 96, 0.2);
}

.post-content h3 {
  font-size: 1.2rem;
  font-weight: 700;
  color: #1e293b;
  margin-top: 1.75rem;
  margin-bottom: 0.75rem;
  scroll-margin-top: 90px;
}

.article-paragraph,
.post-content p {
  font-size: 0.975rem;
  line-height: 1.8;
  color: #334155;
  margin-bottom: 1.25rem;
}

/* Feature List Cards */
.article-feature-list {
  list-style: none !important;
  padding-left: 0 !important;
  display: grid;
  grid-template-columns: 1fr;
  gap: 0.75rem;
  margin: 1.5rem 0;
}

.article-feature-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  padding: 0.85rem 1rem;
  border-radius: 1rem;
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  position: relative;
  padding-left: 2.5rem;
}

.article-feature-item::before {
  content: "✓";
  position: absolute;
  left: 0.85rem;
  top: 0.85rem;
  width: 1.25rem;
  height: 1.25rem;
  border-radius: 9999px;
  background-color: #10b981;
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 900;
}

.feature-label {
  font-weight: 800;
  color: #0f172a;
  font-size: 0.95rem;
}

.feature-desc {
  font-size: 0.9rem;
  color: #475569;
  line-height: 1.6;
}

/* Step Process Cards */
.article-step-card {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1rem 1.25rem;
  margin-bottom: 0.75rem;
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-left: 4px solid #f59e0b;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.step-badge {
  background-color: #fef3c7;
  color: #92400e;
  font-weight: 800;
  font-size: 0.75rem;
  padding: 0.25rem 0.6rem;
  border-radius: 0.5rem;
  white-space: nowrap;
  flex-shrink: 0;
}

.step-content {
  font-size: 0.925rem;
  color: #1e293b;
  font-weight: 500;
  line-height: 1.6;
}

/* Contact / Notice Callout Box */
.article-contact-box {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.25rem;
  margin: 1.5rem 0;
  background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%);
  border: 1px solid #bfdbfe;
  border-radius: 1rem;
  font-size: 0.925rem;
  font-weight: 600;
  color: #1e3a8a;
}

.contact-icon {
  font-size: 1.5rem;
  flex-shrink: 0;
}

/* Images & Media in Article */
.post-content img {
  max-width: 100%;
  border-radius: 1.25rem;
  margin: 1.75rem auto;
  box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.08);
  border: 1px solid #f1f5f9;
}

/* Blockquotes */
.post-content blockquote {
  border-left: 4px solid #f59e0b;
  background-color: #fffbeb;
  padding: 1rem 1.25rem;
  border-radius: 0 1rem 1rem 0;
  margin: 1.5rem 0;
  font-style: italic;
  color: #78350f;
}
</style>
