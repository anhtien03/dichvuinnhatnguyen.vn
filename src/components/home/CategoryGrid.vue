<template>
  <section class="py-12 lg:py-16 bg-white border-b border-slate-100">
    <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
      
      <!-- Section header -->
      <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
          <!-- DANH MỤC DỊCH VỤ IN: Viết hoa + Màu vàng thương hiệu -->
          <h2 class="text-2xl sm:text-3xl font-black text-brand-gold tracking-tight uppercase">
            DANH MỤC DỊCH VỤ IN
          </h2>
          <!-- Bạn đang cần in ấn phẩm nào?: Nhỏ lại + Tương tác hover -->
          <div
            class="text-sm sm:text-base font-semibold transition-colors duration-200 flex items-center gap-2 mt-1.5 min-h-[26px]"
            :class="hoveredItem ? 'text-brand-blue font-bold' : 'text-slate-600'"
          >
            <span>{{ displaySubtitle }}</span>
            <span
              v-if="hoveredItem"
              class="inline-block w-2 h-2 rounded-full bg-brand-gold animate-ping"
            ></span>
          </div>
        </div>

        <router-link
          to="/san-pham"
          class="text-xs sm:text-sm font-bold text-brand-blue hover:text-brand-blue-dark flex items-center gap-1 shrink-0"
        >
          <span>Xem tất cả ấn phẩm</span>
          <ArrowRight class="w-4 h-4" />
        </router-link>
      </div>

      <!-- Categories 20-item Grid (10 cột x 2 hàng chuẩn theo thiết kế tham khảo) -->
      <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-7 lg:grid-cols-10 gap-3 sm:gap-4 lg:gap-5 xl:gap-6">
        <router-link
          v-for="item in printItems"
          :key="item.id"
          :to="item.link"
          @mouseenter="hoveredItem = item"
          @mouseleave="hoveredItem = null"
          class="flex flex-col items-center group cursor-pointer"
        >
          <!-- Khung vuông bo tròn mềm mại chứa Icon -->
          <div
            class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-slate-50/90 border border-slate-200/80 shadow-sm flex items-center justify-center text-slate-700 transition-all duration-300 group-hover:bg-brand-blue group-hover:text-white group-hover:border-brand-blue group-hover:shadow-md group-hover:-translate-y-1"
            :class="hoveredItem?.id === item.id ? 'bg-brand-blue text-white border-brand-blue shadow-md -translate-y-1' : ''"
          >
            <component :is="item.icon" class="w-7 h-7 sm:w-8 sm:h-8 stroke-[1.6]" />
          </div>

          <!-- Tên ấn phẩm bên dưới -->
          <span
            class="mt-2 text-[11px] sm:text-xs font-medium text-slate-700 text-center group-hover:text-brand-blue group-hover:font-bold transition-colors leading-tight line-clamp-1"
            :class="hoveredItem?.id === item.id ? 'text-brand-blue font-bold' : ''"
          >
            {{ item.name }}
          </span>
        </router-link>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue'
import {
  ArrowRight, Folder, Contact2, FileText, Mail, Ticket,
  Layers, Image, Files, BookOpen, Gift,
  Package, ShoppingBag, Stamp, Tag, ClipboardList,
  CreditCard, UtensilsCrossed, StickyNote, ShieldCheck, Heart
} from 'lucide-vue-next'

const hoveredItem = ref(null)

// 20 ấn phẩm chủ lực chuẩn theo hình mẫu: 10 cột x 2 hàng
const printItems = [
  // Hàng 1
  { id: 'bia-ho-so', name: 'Bìa đựng hồ sơ', icon: Folder, link: '/san-pham?cat=an-pham-van-phong' },
  { id: 'danh-thiep', name: 'Danh thiếp', icon: Contact2, link: '/danh-muc/in-the-danh-thiep' },
  { id: 'giay-tieu-de', name: 'Giấy tiêu đề', icon: FileText, link: '/san-pham?cat=an-pham-van-phong' },
  { id: 'bao-thu', name: 'Bao thư', icon: Mail, link: '/san-pham/bao-thu-nho' },
  { id: 've', name: 'Vé', icon: Ticket, link: '/san-pham?cat=tiep-thi-quang-cao' },
  { id: 'to-gap', name: 'Tờ gấp', icon: Layers, link: '/san-pham?cat=tiep-thi-quang-cao' },
  { id: 'poster', name: 'Poster', icon: Image, link: '/san-pham?cat=tiep-thi-quang-cao' },
  { id: 'to-roi', name: 'Tờ rơi', icon: Files, link: '/san-pham?cat=tiep-thi-quang-cao' },
  { id: 'catalogue', name: 'Catalogue', icon: BookOpen, link: '/san-pham?cat=tiep-thi-quang-cao' },
  { id: 'phieu-qua-tang', name: 'Phiếu Quà Tặng', icon: Gift, link: '/san-pham?cat=tiep-thi-quang-cao' },

  // Hàng 2
  { id: 'hop-giay', name: 'Hộp Giấy', icon: Package, link: '/san-pham?cat=bao-bi-tui-giay' },
  { id: 'tui-giay', name: 'Túi giấy', icon: ShoppingBag, link: '/san-pham?cat=bao-bi-tui-giay' },
  { id: 'nhan-dan', name: 'Nhãn Dán', icon: Stamp, link: '/san-pham?cat=tem-nhan-decal' },
  { id: 'mac-san-pham', name: 'Mác sản phẩm', icon: Tag, link: '/san-pham?cat=tem-nhan-decal' },
  { id: 'bieu-mau', name: 'Biểu Mẫu', icon: ClipboardList, link: '/san-pham?cat=an-pham-van-phong' },
  { id: 'the-nhua', name: 'Thẻ nhựa', icon: CreditCard, link: '/danh-muc/in-the-danh-thiep' },
  { id: 'thuc-don', name: 'Thực Đơn', icon: UtensilsCrossed, link: '/san-pham?cat=tiep-thi-quang-cao' },
  { id: 'giay-ghi-chu', name: 'Giấy ghi chú', icon: StickyNote, link: '/san-pham?cat=an-pham-van-phong' },
  { id: 'phieu-bao-hanh', name: 'Phiếu bảo hành', icon: ShieldCheck, link: '/san-pham?cat=an-pham-van-phong' },
  { id: 'thiep-moi', name: 'Thiệp Mời', icon: Heart, link: '/san-pham?cat=lich-so-tay' }
]

const displaySubtitle = computed(() => {
  if (!hoveredItem.value) {
    return 'Bạn đang cần in ấn phẩm nào?'
  }
  return `In ${hoveredItem.value.name} giá gốc tận xưởng`
})
</script>
