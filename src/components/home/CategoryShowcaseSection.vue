<template>
  <section class="py-12 lg:py-16 bg-white border-b border-slate-200/80">
    <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      
      <!-- Section Header with Category Tabs -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
          <span class="text-xs font-bold text-brand-gold uppercase tracking-wider block mb-1">
            DANH MỤC THIẾT KẾ & IN ẤN THEO NGÀNH
          </span>
          <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
            Bộ Sưu Tập Ấn Phẩm Chuyên Nghiệp
          </h2>
        </div>

        <!-- Category Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
          <button
            v-for="tab in showcaseTabs"
            :key="tab.id"
            @click="activeTabId = tab.id"
            class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all whitespace-nowrap"
            :class="activeTabId === tab.id 
              ? 'bg-brand-blue text-white shadow-sm' 
              : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900'"
          >
            {{ tab.tabName }}
          </button>
        </div>
      </div>

      <!-- Main Showcase Block (Banner Xanh Bên Trái + Lưới 8 Sản Phẩm Bên Phải) -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
        
        <!-- CỘT TRÁI (Banner Xanh Dọc Lớn: ẤN PHẨM VĂN PHÒNG / TIẾP THỊ) -->
        <div class="lg:col-span-3 xl:col-span-3 rounded-3xl bg-gradient-to-br from-brand-blue via-blue-700 to-blue-900 text-white p-7 sm:p-8 flex flex-col justify-between shadow-md relative overflow-hidden group">
          <!-- Background Decoration -->
          <div class="absolute -right-16 -top-16 w-52 h-52 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
          <div class="absolute -left-16 -bottom-16 w-52 h-52 bg-brand-gold/15 rounded-full blur-2xl pointer-events-none"></div>

          <!-- Dynamic Background Image with Gradient Blend -->
          <img
            v-if="currentShowcaseBanner?.image_url"
            :src="currentShowcaseBanner.image_url"
            :alt="currentShowcaseBanner.title || currentTab.tabName"
            class="absolute inset-0 w-full h-full object-cover object-center opacity-25 group-hover:opacity-40 group-hover:scale-105 transition-all duration-700 pointer-events-none"
          />
          <div v-if="currentShowcaseBanner?.image_url" class="absolute inset-0 bg-gradient-to-t from-blue-950 via-brand-blue/85 to-blue-900/90 pointer-events-none"></div>

          <div class="relative z-10 space-y-4 text-left">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-[11px] font-bold uppercase tracking-wider text-blue-100 border border-white/20">
              <span class="w-1.5 h-1.5 rounded-full bg-brand-gold animate-ping"></span>
              {{ currentShowcaseBanner?.badge || 'XƯỞNG IN NHẬT NGUYÊN' }}
            </span>

            <!-- Tiêu đề lớn: Có thể cập nhật từ Banner Admin hoặc mặc định tab -->
            <h3 class="text-3xl sm:text-4xl xl:text-5xl font-black uppercase tracking-tight leading-none text-white">
              {{ currentShowcaseBanner?.title || (currentTab.titleLine1 + ' ' + currentTab.titleLine2) }}
            </h3>

            <p class="text-xs sm:text-sm text-blue-100/90 leading-relaxed font-normal pt-2">
              {{ currentShowcaseBanner?.subtitle || currentTab.description }}
            </p>

            <!-- 3 Cam kết nhanh -->
            <div class="space-y-2 pt-2 text-xs font-semibold text-blue-50">
              <div class="flex items-center gap-2">
                <CheckCircle2 class="w-4 h-4 text-brand-gold shrink-0" />
                <span>Hỗ trợ thiết kế & xuất file in</span>
              </div>
              <div class="flex items-center gap-2">
                <CheckCircle2 class="w-4 h-4 text-brand-gold shrink-0" />
                <span>In chuẩn màu sắc nhận diện</span>
              </div>
              <div class="flex items-center gap-2">
                <CheckCircle2 class="w-4 h-4 text-brand-gold shrink-0" />
                <span>Nhận in số lượng ít & số lượng lớn</span>
              </div>
            </div>
          </div>

          <!-- Featured Mockup / Image Box (Cập nhật từ Admin Banner) -->
          <div v-if="currentShowcaseBanner?.image_url" class="relative z-10 py-3 my-2">
            <div class="rounded-2xl overflow-hidden border border-white/25 shadow-xl bg-black/20 backdrop-blur-xs p-1 transform group-hover:scale-102 transition-transform duration-300">
              <img
                :src="currentShowcaseBanner.image_url"
                :alt="currentShowcaseBanner.title || currentTab.tabName"
                class="w-full h-36 sm:h-44 object-cover rounded-xl"
                loading="lazy"
              />
            </div>
          </div>

          <!-- Nút hành động chân banner -->
          <div class="relative z-10 pt-4 text-left">
            <router-link
              :to="currentShowcaseBanner?.link_url || currentTab.categoryLink"
              class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white text-brand-blue hover:bg-brand-gold hover:text-slate-950 font-black text-xs sm:text-sm shadow-xl transition-all duration-300 transform group-hover:scale-103"
            >
              <span>{{ currentShowcaseBanner?.button_text || ('Xem tất cả ' + currentTab.tabName) }}</span>
              <ArrowRight class="w-4 h-4" />
            </router-link>
          </div>
        </div>

        <!-- CỘT PHẢI (Lưới 8 Sản Phẩm: 4 Cột x 2 Hàng Chuẩn Theo Ảnh Mẫu) -->
        <div class="lg:col-span-9 xl:col-span-9 grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 lg:gap-5">
          <router-link
            v-for="item in currentTab.items"
            :key="item.id"
            :to="item.link"
            class="flex flex-col items-center group cursor-pointer"
          >
            <!-- Khung hình sản phẩm vuông bo góc lớn (Background xám nhạt như hình) -->
            <div class="w-full aspect-square rounded-2xl bg-[#F4F5F7] border border-slate-200/70 p-3 sm:p-4 flex items-center justify-center overflow-hidden transition-all duration-300 group-hover:bg-white group-hover:border-brand-blue group-hover:shadow-xl group-hover:-translate-y-1.5 relative">
              
              <!-- Subtle badge nếu có -->
              <span v-if="item.badge" class="absolute top-2 left-2 z-10 px-2 py-0.5 rounded-md bg-white/90 text-brand-blue text-[9px] font-bold shadow-2xs border border-slate-200/50">
                {{ item.badge }}
              </span>

              <!-- Mockup Image -->
              <img
                :src="item.image"
                :alt="item.name"
                class="w-full h-full object-contain transform group-hover:scale-108 transition-transform duration-500 drop-shadow-sm"
                loading="lazy"
              />

              <!-- Overlay ánh sáng mờ khi hover -->
              <div class="absolute inset-0 bg-brand-blue/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none rounded-2xl"></div>
            </div>

            <!-- Tên sản phẩm bên dưới ảnh -->
            <span class="mt-2.5 text-xs sm:text-[13px] font-bold text-slate-700 text-center group-hover:text-brand-blue transition-colors leading-tight line-clamp-1">
              {{ item.name }}
            </span>
          </router-link>
        </div>

      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { ArrowRight, CheckCircle2 } from 'lucide-vue-next'
import { api } from '../../services/api'

const activeTabId = ref('van-phong')
const banners = ref([])

async function fetchBanners() {
  try {
    const res = await api.getBanners()
    if (res.data) {
      banners.value = res.data
    }
  } catch (err) {
    console.error('Failed to load showcase banners', err)
  }
}

onMounted(() => {
  fetchBanners()
})

const currentShowcaseBanner = computed(() => {
  if (activeTabId.value === 'van-phong') {
    return banners.value.find(b => b.position === 'home_showcase_1') || null
  } else if (activeTabId.value === 'quang-cao') {
    return banners.value.find(b => b.position === 'home_showcase_2') || null
  }
  return null
})

const showcaseTabs = [
  {
    id: 'van-phong',
    tabName: 'Ấn Phẩm Văn Phòng',
    titleLine1: 'ẤN PHẨM',
    titleLine2: 'VĂN PHÒNG',
    description: 'Trọn bộ ấn phẩm nhận diện văn phòng cho doanh nghiệp: namecard, phong bì, kẹp file, tài liệu hội nghị sắc nét.',
    categoryLink: '/san-pham?cat=an-pham-van-phong',
    items: [
      {
        id: 'standee',
        name: 'Standee',
        badge: 'Lấy ngay 2h',
        image: 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=500&q=80',
        link: '/san-pham'
      },
      {
        id: 'lich-de-ban',
        name: 'Bài post MXH / Lịch bàn',
        badge: 'Thiết kế đẹp',
        image: 'https://images.unsplash.com/photo-1506784365847-bbad939e9335?auto=format&fit=crop&w=500&q=80',
        link: '/san-pham'
      },
      {
        id: 'poster-chuan',
        name: 'Poster chuẩn',
        badge: 'In PP / Hiflex',
        image: 'https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?auto=format&fit=crop&w=500&q=80',
        link: '/san-pham'
      },
      {
        id: 'tui-canvas',
        name: 'Túi canvas',
        badge: 'Vải bố bền đẹp',
        image: 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=500&q=80',
        link: '/san-pham/in-tui-giay-kraft-couche-gia-xuong'
      },
      {
        id: 'hashtag-cam-tay',
        name: 'Hashtag cầm tay',
        badge: 'Fomex 5mm',
        image: 'https://images.unsplash.com/photo-1513151233558-d860c5398176?auto=format&fit=crop&w=500&q=80',
        link: '/san-pham'
      },
      {
        id: 'thu-moi',
        name: 'Thư mời',
        badge: 'Giấy mỹ thuật',
        image: 'https://images.unsplash.com/photo-1516962215378-7fa2e137ae93?auto=format&fit=crop&w=500&q=80',
        link: '/san-pham'
      },
      {
        id: 'thank-you-card',
        name: 'Thank you card',
        badge: 'Cán màng mờ',
        image: 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=500&q=80',
        link: '/san-pham/in-danh-thiep-name-card-lay-lien'
      },
      {
        id: 'nhan-dan',
        name: 'Nhãn dán',
        badge: 'Bế demi sẵn',
        image: 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?auto=format&fit=crop&w=500&q=80',
        link: '/san-pham/in-tem-nhan-decal-be-thanh-pham'
      }
    ]
  },
  {
    id: 'quang-cao',
    tabName: 'Tiếp Thị & Quảng Cáo',
    titleLine1: 'TIẾP THỊ &',
    titleLine2: 'QUẢNG CÁO',
    description: 'Ấn phẩm truyền thông thu hút khách hàng, tăng trưởng doanh số với brochure, catalogue, voucher, menu.',
    categoryLink: '/san-pham?cat=tiep-thi-quang-cao',
    items: [
      {
        id: 'catalogue',
        name: 'Catalogue công ty',
        image: 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=500&q=80',
        link: '/san-pham/in-catalogue-profile-cong-ty'
      },
      {
        id: 'to-roi-a5',
        name: 'Tờ rơi A5',
        image: 'https://images.unsplash.com/photo-1541701494587-cb58502866ab?auto=format&fit=crop&w=500&q=80',
        link: '/san-pham/in-to-roi-to-gap-gia-re-tphcm'
      },
      {
        id: 'brochure-gap-3',
        name: 'Brochure gấp 3',
        image: 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=500&q=80',
        link: '/san-pham/in-brochure-to-gap-quang-cao'
      },
      {
        id: 'voucher',
        name: 'Voucher giảm giá',
        image: 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=500&q=80',
        link: '/san-pham'
      },
      {
        id: 'menu-bia-cung',
        name: 'Menu bìa bồi cứng',
        image: 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=500&q=80',
        link: '/san-pham'
      },
      {
        id: 'folder-kep-file',
        name: 'Kẹp file hồ sơ',
        image: 'https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?auto=format&fit=crop&w=500&q=80',
        link: '/san-pham'
      },
      {
        id: 'phong-bi',
        name: 'Phong bì thư',
        image: 'https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?auto=format&fit=crop&w=500&q=80',
        link: '/san-pham/in-bao-thu-phong-bi-thuong-hieu'
      },
      {
        id: 'decal-quang-cao',
        name: 'Decal dán kính',
        image: 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?auto=format&fit=crop&w=500&q=80',
        link: '/san-pham/in-tem-nhan-decal-be-thanh-pham'
      }
    ]
  }
]

const currentTab = computed(() => {
  return showcaseTabs.find(t => t.id === activeTabId.value) || showcaseTabs[0]
})
</script>

