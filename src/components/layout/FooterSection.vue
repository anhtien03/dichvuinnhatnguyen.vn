<template>
  <footer class="bg-slate-50 text-slate-600 pt-16 pb-24 md:pb-12 border-t-4 border-brand-gold">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
        
        <!-- Col 1: Brand Info & Logo Trực Tiếp Trên Nền Sáng -->
        <div class="space-y-4 text-left">
          <router-link to="/" class="inline-block hover:opacity-90 transition-opacity">
            <img src="/logo.png" alt="In Ấn Nhật Nguyên" class="h-16 sm:h-18 lg:h-20 w-auto object-contain" />
          </router-link>
          <p class="text-xs text-slate-500 leading-relaxed font-normal">
            Hơn 8 năm kinh nghiệm chuyên nghiệp trong lĩnh vực in ấn bao bì, ấn phẩm văn phòng và quảng cáo. Cam kết chất lượng chuẩn màu, đúng hẹn, giá tận xưởng không qua trung gian.
          </p>
          <div class="pt-1 text-xs text-slate-500 space-y-1">
            <div><strong class="text-slate-700">Mã số thuế:</strong> 0317208945</div>
            <div><strong class="text-slate-700">Giấy phép KD:</strong> Sở Kế Hoạch & Đầu Tư TP.HCM</div>
          </div>
        </div>

        <!-- Col 2: Services / Categories -->
        <div class="text-left">
          <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider border-l-3 border-brand-gold pl-2.5 mb-4">
            Dịch Vụ In Thế Mạnh
          </h3>
          <ul class="space-y-2 text-xs">
            <li v-for="cat in categories" :key="cat.id">
              <router-link
                :to="'/danh-muc/' + cat.slug"
                class="text-slate-600 hover:text-brand-blue font-medium transition-colors flex items-center gap-1.5"
              >
                <span class="text-brand-gold font-bold">›</span>
                <span>{{ cat.name }}</span>
              </router-link>
            </li>
            <li class="pt-1">
              <router-link to="/yeu-cau-bao-gia" class="text-brand-blue font-bold hover:underline">
                › Đặt in theo quy cách riêng &rarr;
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Col 3: Customer Care & Policies -->
        <div class="text-left">
          <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider border-l-3 border-brand-gold pl-2.5 mb-4">
            Hỗ Trợ & Chính Sách
          </h3>
          <ul class="space-y-2 text-xs">
            <li>
              <router-link to="/gioi-thieu" class="text-slate-600 hover:text-brand-blue font-medium transition-colors flex items-center gap-1.5">
                <span class="text-brand-gold font-bold">›</span>
                <span>Năng lực xưởng & Máy móc</span>
              </router-link>
            </li>
            <li>
              <router-link to="/tin-tuc" class="text-slate-600 hover:text-brand-blue font-medium transition-colors flex items-center gap-1.5">
                <span class="text-brand-gold font-bold">›</span>
                <span>Quy chuẩn xuất file in (CMYK, 300DPI)</span>
              </router-link>
            </li>
            <li>
              <router-link to="/tin-tuc" class="text-slate-600 hover:text-brand-blue font-medium transition-colors flex items-center gap-1.5">
                <span class="text-brand-gold font-bold">›</span>
                <span>Chính sách in test & bảo hành màu</span>
              </router-link>
            </li>
            <li>
              <router-link to="/lien-he" class="text-slate-600 hover:text-brand-blue font-medium transition-colors flex items-center gap-1.5">
                <span class="text-brand-gold font-bold">›</span>
                <span>Quy trình giao hàng & Thanh toán</span>
              </router-link>
            </li>
          </ul>

          <!-- Box cam kết hoàn tiền trên nền sáng -->
          <div class="mt-4 p-3.5 rounded-2xl bg-amber-50/80 border border-amber-200/80 shadow-2xs">
            <div class="text-[11px] font-black text-amber-900 mb-0.5 flex items-center gap-1">
              <span class="w-1.5 h-1.5 rounded-full bg-brand-gold"></span>
              Cam Kết Hoàn Tiền 100%
            </div>
            <p class="text-[11px] text-amber-800/90 leading-snug">Nếu sản phẩm in sai file duyệt, lỗi kỹ thuật gia công hoặc trễ hẹn cam kết.</p>
          </div>
        </div>

        <!-- Col 4: Contact & Factory -->
        <div class="text-left">
          <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider border-l-3 border-brand-gold pl-2.5 mb-4">
            Xưởng Sản Xuất
          </h3>
          <ul class="space-y-3 text-xs">
            <li class="flex items-start gap-2.5">
              <MapPin class="w-4 h-4 text-brand-gold shrink-0 mt-0.5" />
              <span class="text-slate-700 leading-snug font-medium">{{ siteSettings.address }}</span>
            </li>
            <li class="flex items-center gap-2.5">
              <Phone class="w-4 h-4 text-brand-gold shrink-0" />
              <div>
                <a
                  v-for="(h, idx) in siteSettings.hotlines"
                  :key="idx"
                  :href="'tel:' + h.number.replace(/\s+/g, '')"
                  class="block font-black text-brand-blue hover:underline"
                >
                  {{ h.number }} ({{ h.nvkd }})
                </a>
              </div>
            </li>
            <li class="flex items-center gap-2.5">
              <Mail class="w-4 h-4 text-brand-gold shrink-0" />
              <a :href="'mailto:' + siteSettings.email" class="text-slate-600 hover:text-brand-blue font-medium">
                {{ siteSettings.email }}
              </a>
            </li>
            <li class="flex items-center gap-2.5">
              <Clock class="w-4 h-4 text-brand-gold shrink-0" />
              <span class="text-slate-600 font-medium">{{ siteSettings.workingHours }}</span>
            </li>
          </ul>
        </div>

      </div>

      <!-- Copyright bar -->
      <div class="mt-12 pt-6 border-t border-slate-200/90 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-3">
        <p>© 2026 In Ấn Nhật Nguyên. Tất cả các quyền được bảo lưu.</p>
        <p class="flex items-center gap-2">
          <span class="text-brand-blue font-bold">dichvuinnhatnguyen.vn</span>
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { MapPin, Phone, Mail, Clock } from 'lucide-vue-next'
import { categories } from '../../data/categories'
import { siteSettings } from '../../data/siteSettings'
</script>
