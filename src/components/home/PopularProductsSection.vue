<template>
  <section class="py-14 lg:py-20 bg-gradient-to-b from-white via-slate-50/60 to-white border-b border-slate-200/80">
    <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
      
      <!-- Section Header -->
      <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
          <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200/60 text-brand-gold text-xs font-bold uppercase tracking-wider mb-2">
            <span class="w-1.5 h-1.5 rounded-full bg-brand-gold animate-ping"></span>
            ẤN PHẨM PHỔ BIẾN
          </div>
          <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">
            Ấn Phẩm Bán Chạy
          </h2>
          <p class="text-xs sm:text-sm text-slate-500 mt-1.5">
            Các dòng ấn phẩm in sắc nét, chuẩn màu, hỗ trợ kiểm tra file và nhận in số lượng linh hoạt tại TPHCM.
          </p>
        </div>
        
        <router-link
          to="/san-pham"
          class="text-xs sm:text-sm font-bold text-brand-blue hover:text-brand-blue-dark flex items-center gap-1.5 shrink-0 px-4 py-2 rounded-xl bg-blue-50/70 hover:bg-blue-100/70 transition-colors"
        >
          <span>Xem tất cả 20+ ấn phẩm</span>
          <ArrowRight class="w-4 h-4" />
        </router-link>
      </div>

      <!-- 5-Column Modern Product Cards Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 lg:gap-5 xl:gap-6">
        <div
          v-for="item in popularProducts"
          :key="item.id"
          class="w-full bg-white rounded-[12px] border border-slate-200/90 shadow-[0_4px_20px_-2px_rgba(10,25,47,0.08)] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between overflow-hidden group select-none cursor-pointer"
        >
          <!-- 1. Header (Banner) Navy -->
          <div class="bg-[#0A192F] text-white px-3.5 py-2 flex items-center justify-center shrink-0">
            <div class="flex items-center gap-1.5 text-[11px] font-bold tracking-wider uppercase">
              <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B] animate-pulse"></span>
              <span>Xưởng In Nhật Nguyên</span>
            </div>
          </div>

          <!-- 2. Body (Khu vực hình ảnh) -->
          <router-link :to="item.link" class="block w-full flex-1 flex flex-col justify-between">
            <div class="relative w-full h-[200px] sm:h-[220px] bg-slate-100 overflow-hidden">
              <!-- Ảnh sản phẩm -->
              <img
                :src="item.image"
                :alt="item.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                loading="lazy"
              />

              <!-- Đè góc dưới phải: Nút nền --gold-accent, chữ navy -->
              <div class="absolute bottom-2.5 right-2.5 bg-[#F59E0B] text-[#0A192F] text-[10px] font-black px-2.5 py-1 rounded-full shadow-md flex items-center gap-1 z-10">
                <Zap class="w-3 h-3 fill-current" />
                <span>{{ item.deliveryTag }}</span>
              </div>
            </div>

            <!-- 3. Footer (Thông tin & Giá) -->
            <div class="p-4 bg-white flex flex-col justify-between flex-1 gap-3">
              <div>
                <!-- Tiêu đề: Font to, đậm, màu --navy-main -->
                <h3 class="text-base sm:text-lg font-black text-[#0A192F] leading-snug group-hover:text-[#1E3A8A] transition-colors line-clamp-1">
                  {{ item.name }}
                </h3>
                <!-- Mô tả: 2 dòng text màu xám -->
                <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed mt-1 font-normal">
                  {{ item.description }}
                </p>
              </div>

              <!-- Giá: Chỉ từ x.xxxđ / Đơn vị (Số màu --gold-dark in đậm) -->
              <div class="flex items-baseline gap-1 pt-2 border-t border-slate-100">
                <span class="text-xs text-gray-400 font-medium">Chỉ từ</span>
                <span class="text-xl font-black text-[#D97706]">{{ item.price }}</span>
                <span class="text-xs text-gray-400 font-medium">/ {{ item.unit }}</span>
              </div>
            </div>
          </router-link>

          <!-- Nút CTA viền nét đứt: Nền trắng, viền --navy-light dashed, mũi tên trắng trong vòng tròn navy -->
          <div class="px-4 pb-4 pt-1 shrink-0">
            <router-link
              :to="item.link"
              class="w-full py-2.5 px-3.5 rounded-lg bg-white border-[1.5px] border-dashed border-[#1E3A8A] text-[#0A192F] font-bold text-xs flex items-center justify-between hover:bg-slate-50 transition-colors group/btn cursor-pointer"
            >
              <span>Xem quy cách</span>
              <span class="w-5 h-5 rounded-full bg-[#0A192F] text-white flex items-center justify-center transition-transform group-hover/btn:translate-x-1">
                <ArrowRight class="w-3 h-3" />
              </span>
            </router-link>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { Zap, ArrowRight } from 'lucide-vue-next'

const popularProducts = [
  {
    id: 1,
    name: 'In túi giấy',
    tag: 'Số lượng ít',
    deliveryTag: 'Giao hàng nhanh chóng',
    price: '5.000đ',
    unit: 'Cái',
    description: 'Túi giấy kraft, ivory quai xách bền chắc, đáy bồi chịu lực tốt, màu in chuẩn sắc nét cho shop và doanh nghiệp.',
    image: 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=600&q=80',
    link: '/san-pham/in-tui-giay-kraft-couche-gia-xuong',
    features: ['Ấn tượng', 'Tư vấn', 'Giao hàng']
  },
  {
    id: 2,
    name: 'In hộp giấy',
    tag: 'Giá xưởng',
    deliveryTag: 'Theo kích thước',
    price: '600đ',
    unit: 'Hộp',
    description: 'Hộp giấy đựng son môi, mỹ phẩm, thức ăn, hộp quà Tết. Bế khuôn chính xác, gia công ép kim sang trọng.',
    image: 'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?auto=format&fit=crop&w=600&q=80',
    link: '/san-pham/in-hop-giay-hop-cung-cao-cap',
    features: ['Sắc nét', 'Thiết kế', 'Bền đẹp']
  },
  {
    id: 3,
    name: 'In tag treo - nhãn mác',
    tag: 'In nhanh 24h',
    deliveryTag: 'Lấy liền trong ngày',
    price: '650đ',
    unit: 'Cái',
    description: 'Thẻ tag treo quần áo, tag cảm ơn khách hàng, mác giá phụ kiện. Đục lỗ xỏ dây sẵn, cắt mọi hình dạng.',
    image: 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?auto=format&fit=crop&w=600&q=80',
    link: '/san-pham/in-tem-nhan-decal-be-thanh-pham',
    features: ['Đục lỗ', 'Cán màng', 'Bế khuôn']
  },
  {
    id: 4,
    name: 'In danh thiếp - namecard',
    tag: 'Chuẩn màu offset',
    deliveryTag: 'Tặng hộp mica',
    price: '90.000đ',
    unit: 'Hộp',
    description: 'Danh thiếp cá nhân, giám đốc, card visit công ty in ghép bài offset sắc nét, tặng kèm hộp nhựa trong suốt.',
    image: 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=600&q=80',
    link: '/san-pham/in-danh-thiep-name-card-lay-lien',
    features: ['C300 dày', 'Chuẩn màu', 'Hộp mica']
  },
  {
    id: 5,
    name: 'In bao thư - phong bì',
    tag: 'Bản in sắc nét',
    deliveryTag: 'Có sẵn keo nắp',
    price: '2.600đ',
    unit: 'Cái',
    description: 'Bao thư nhỏ (A6, A5), bao thư lớn (A4) gửi hợp đồng công ty. Có dán sẵn băng keo 2 mặt tiện lợi.',
    image: 'https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?auto=format&fit=crop&w=600&q=80',
    link: '/san-pham/in-bao-thu-phong-bi-thuong-hieu',
    features: ['Keo nắp', 'Giấy Fort', 'A4/A5/A6']
  }
]
</script>
