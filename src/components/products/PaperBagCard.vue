<template>
  <!-- Khung Thẻ Card: Kích thước dọc (300px - 350px), bo góc 12px, đổ bóng nhẹ -->
  <div class="w-full max-w-[340px] bg-white rounded-[12px] border border-slate-200/90 shadow-[0_4px_20px_-2px_rgba(10,25,47,0.08)] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col overflow-hidden group select-none">
    
    <!-- 1. Header (Banner) -->
    <div class="bg-[#0A192F] text-white px-3.5 py-2.5 flex items-center justify-between">
      <div class="flex items-center gap-1.5 text-[11px] font-bold tracking-wider uppercase">
        <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B] animate-pulse"></span>
        <span>{{ brandName }}</span>
      </div>
      <!-- Badge Số lượng ít ở góc phải -->
      <span class="bg-[#F59E0B] text-[#0A192F] text-[10px] font-black px-2 py-0.5 rounded shadow-xs uppercase tracking-tight">
        {{ tag }}
      </span>
    </div>

    <!-- 2. Body (Khu vực hình ảnh) -->
    <div class="relative w-full h-[220px] bg-slate-100 overflow-hidden">
      <!-- Ảnh sản phẩm -->
      <img
        :src="image"
        :alt="title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
        loading="lazy"
      />

      <!-- Đè góc trên trái: 3 khối tính năng nhỏ (Nền --bg-cream #FFFBEB, icon navy, chữ navy) -->
      <div class="absolute top-2.5 left-2.5 flex flex-col gap-1.5 z-10">
        <div class="bg-[#FFFBEB] text-[#0A192F] text-[10px] font-bold px-2 py-0.5 rounded border border-amber-300/40 shadow-xs flex items-center gap-1.5 backdrop-blur-xs">
          <Sparkles class="w-3 h-3 text-[#0A192F]" />
          <span>Ấn tượng</span>
        </div>
        <div class="bg-[#FFFBEB] text-[#0A192F] text-[10px] font-bold px-2 py-0.5 rounded border border-amber-300/40 shadow-xs flex items-center gap-1.5 backdrop-blur-xs">
          <MessageCircle class="w-3 h-3 text-[#0A192F]" />
          <span>Tư vấn</span>
        </div>
        <div class="bg-[#FFFBEB] text-[#0A192F] text-[10px] font-bold px-2 py-0.5 rounded border border-amber-300/40 shadow-xs flex items-center gap-1.5 backdrop-blur-xs">
          <Truck class="w-3 h-3 text-[#0A192F]" />
          <span>Giao hàng</span>
        </div>
      </div>

      <!-- Đè góc dưới phải: Nền --gold-accent, chữ navy "Giao hàng nhanh chóng" -->
      <div class="absolute bottom-2.5 right-2.5 bg-[#F59E0B] text-[#0A192F] text-[10px] font-black px-2.5 py-1 rounded-full shadow-md flex items-center gap-1 z-10">
        <Zap class="w-3 h-3 fill-current" />
        <span>{{ deliveryTag }}</span>
      </div>
    </div>

    <!-- 3. Footer (Thông tin, Giá & Nút CTA) -->
    <div class="p-4 bg-white flex flex-col gap-3">
      <div>
        <!-- Tiêu đề: Font to, đậm, màu --navy-main -->
        <h3 class="text-lg font-black text-[#0A192F] leading-snug">
          {{ title }}
        </h3>
        <!-- Mô tả: 2 dòng text màu xám -->
        <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed mt-1 font-normal">
          {{ description }}
        </p>
      </div>

      <!-- Giá: "Chỉ từ 5.000đ / Cái" (Số màu --gold-dark in đậm) -->
      <div class="flex items-baseline gap-1 pt-2 border-t border-slate-100">
        <span class="text-xs text-gray-400 font-medium">Chỉ từ</span>
        <span class="text-xl font-black text-[#D97706]">{{ price }}</span>
        <span class="text-xs text-gray-400 font-medium">/ {{ unit }}</span>
      </div>

      <!-- Nút CTA: "Xem quy cách", viền nét đứt (dashed) màu --navy-light, chữ --navy-main -->
      <router-link
        v-if="link"
        :to="link"
        class="w-full py-2.5 px-3.5 rounded-lg bg-white border-[1.5px] border-dashed border-[#1E3A8A] text-[#0A192F] font-bold text-xs flex items-center justify-between hover:bg-slate-50 transition-colors group/btn cursor-pointer"
      >
        <span>Xem quy cách</span>
        <!-- Hình tròn nhỏ nền navy chứa icon mũi tên màu trắng -->
        <span class="w-5 h-5 rounded-full bg-[#0A192F] text-white flex items-center justify-center transition-transform group-hover/btn:translate-x-1">
          <ArrowRight class="w-3 h-3" />
        </span>
      </router-link>

      <button
        v-else
        type="button"
        class="w-full py-2.5 px-3.5 rounded-lg bg-white border-[1.5px] border-dashed border-[#1E3A8A] text-[#0A192F] font-bold text-xs flex items-center justify-between hover:bg-slate-50 transition-colors group/btn cursor-pointer"
      >
        <span>Xem quy cách</span>
        <span class="w-5 h-5 rounded-full bg-[#0A192F] text-white flex items-center justify-center transition-transform group-hover/btn:translate-x-1">
          <ArrowRight class="w-3 h-3" />
        </span>
      </button>
    </div>

  </div>
</template>

<script setup>
import { Sparkles, MessageCircle, Truck, Zap, ArrowRight } from 'lucide-vue-next'

withDefaults(defineProps(), {
  title: 'In túi giấy',
  description: 'Túi giấy kraft, ivory quai xách chắc chắn, đáy bồi chịu lực tốt, màu in chuẩn sắc nét cho shop và doanh nghiệp.',
  price: '5.000đ',
  unit: 'Cái',
  brandName: 'Xưởng In Nhật Nguyên',
  tag: 'Số lượng ít',
  deliveryTag: 'Giao hàng nhanh chóng',
  image: 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=600&q=80',
  link: '/san-pham/in-tui-giay-kraft-couche-gia-xuong'
})
</script>

