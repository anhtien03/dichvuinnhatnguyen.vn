<template>
  <section v-if="hasShowcaseContent" class="py-12 lg:py-16 bg-white border-b border-slate-200/80">
    <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      
      <!-- Section Header with Root Industry Group Tabs (Cấp 1) -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
          <span class="text-xs font-bold text-brand-gold uppercase tracking-wider block mb-1">
            DANH MỤC THIẾT KẾ & IN ẤN THEO NGÀNH
          </span>
          <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
            Bộ Sưu Tập Ấn Phẩm Chuyên Nghiệp
          </h2>
        </div>

        <!-- Root Category Tabs (Cấp 1: Ấn Phẩm Văn Phòng, Tiếp Thị & Quảng Cáo...) -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
          <button
            v-for="cat in rootCategories"
            :key="cat.id"
            @click="activeRootSlug = cat.slug"
            class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all whitespace-nowrap cursor-pointer"
            :class="activeRootSlug === cat.slug 
              ? 'bg-brand-blue text-white shadow-sm' 
              : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900'"
          >
            {{ cat.name }}
          </button>
        </div>
      </div>

      <!-- Main Showcase Grid: Hiển thị các Danh Mục Cha (Cấp 2) thuộc Nhóm Ngành đang chọn -->
      <div v-if="currentShowcaseItems.length > 0" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
        <router-link
          v-for="item in currentShowcaseItems"
          :key="item.key"
          :to="item.link"
          class="group rounded-2xl bg-white border border-slate-200/90 overflow-hidden hover:shadow-xl hover:border-brand-blue/40 transition-all flex flex-col"
        >
          <div class="aspect-[4/3] bg-slate-100 overflow-hidden relative">
            <img
              :src="item.image || '/logo.png'"
              :alt="item.name"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              loading="lazy"
            />
            <span
              v-if="item.badge"
              class="absolute top-2.5 left-2.5 px-2.5 py-0.5 rounded-md bg-brand-gold text-slate-950 font-black text-[10px] shadow-xs"
            >
              {{ item.badge }}
            </span>
            <span
              v-if="item.isCategory && item.childCount > 0"
              class="absolute bottom-2.5 right-2.5 px-2.5 py-0.5 rounded-full bg-slate-950/80 text-white font-bold text-[10px] backdrop-blur-xs"
            >
              {{ item.childCount }} mẫu quy cách
            </span>
          </div>

          <div class="p-4 flex-1 flex flex-col justify-between gap-2">
            <div>
              <h4 class="text-sm sm:text-base font-black text-slate-900 group-hover:text-brand-blue line-clamp-1">
                {{ item.name }}
              </h4>
              <!-- Hiển thị danh sách các ấn phẩm con tiêu biểu bên trong Danh mục cha -->
              <div v-if="item.childProducts && item.childProducts.length > 0" class="mt-1.5 flex flex-wrap gap-1">
                <span
                  v-for="cp in item.childProducts.slice(0, 3)"
                  :key="cp.id"
                  class="inline-block px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-semibold truncate max-w-full"
                >
                  {{ cp.name }}
                </span>
                <span
                  v-if="item.childProducts.length > 3"
                  class="inline-block px-1.5 py-0.5 rounded-md bg-blue-50 text-brand-blue text-[10px] font-bold"
                >
                  +{{ item.childProducts.length - 3 }}
                </span>
              </div>
              <p v-else-if="item.description" class="text-[11px] text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                {{ item.description }}
              </p>
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs font-black text-brand-blue">
              <span>{{ item.priceLabel }}</span>
              <span class="text-[11px] font-bold text-slate-400 group-hover:text-brand-blue group-hover:translate-x-0.5 transition-all">
                {{ item.isCategory ? 'Xem các gói in →' : 'Chi tiết →' }}
              </span>
            </div>
          </div>
        </router-link>
      </div>

      <!-- Empty state for tab -->
      <div v-else class="py-10 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs text-slate-500">
        Đang cập nhật danh mục ấn phẩm cho nhóm ngành này.
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { api } from '../../services/api'

const categories = ref([])
const products = ref([])
const activeRootSlug = ref('')

function formatCurrency(val) {
  if (!val) return 'Liên hệ'
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val)
}

// Cấp 1: Các Nhóm Ngành Lớn (không có parent_id)
const rootCategories = computed(() => {
  return categories.value.filter(c => !c.parent_id && c.is_active !== false)
})

const activeRootCategory = computed(() => {
  return rootCategories.value.find(c => c.slug === activeRootSlug.value) || rootCategories.value[0] || null
})

// Cấp 2: Các Danh Mục Cha thuộc Nhóm Ngành Lớn đang chọn (VD: "In thẻ & danh thiếp" thuộc "Ấn Phẩm Văn Phòng")
const currentShowcaseItems = computed(() => {
  const rootCat = activeRootCategory.value
  if (!rootCat) return []

  // 1. Lấy các Danh Mục Cha (Cấp 2) có parent_id = rootCat.id
  const subCategories = categories.value.filter(
    c => Number(c.parent_id) === Number(rootCat.id) && c.is_active !== false
  )

  const items = subCategories.map(subCat => {
    const childProds = products.value.filter(
      p => Number(p.category_id) === Number(subCat.id) || p.category?.slug === subCat.slug
    )
    const minPrice = childProds.reduce((min, p) => {
      const price = Number(p.base_price || 0)
      return price > 0 && (min === null || price < min) ? price : min
    }, null)

    return {
      key: `cat-${subCat.id}`,
      isCategory: true,
      name: subCat.name,
      description: subCat.description,
      image: subCat.image || childProds[0]?.image || rootCat.image || '/logo.png',
      badge: subCat.badge || 'Danh mục in',
      link: `/danh-muc/${subCat.slug}`,
      childCount: childProds.length,
      childProducts: childProds,
      priceLabel: minPrice ? `Giá từ ${formatCurrency(minPrice)}` : 'Xem báo giá chi tiết'
    }
  })

  // 2. Nếu có ấn phẩm nào đang gán trực tiếp vào Nhóm Ngành Lớn (chưa qua Danh mục Cấp 2), vẫn hiển thị kèm để không bị sót
  const directProducts = products.value.filter(
    p => Number(p.category_id) === Number(rootCat.id) || p.category?.slug === rootCat.slug
  )

  directProducts.forEach(prod => {
    items.push({
      key: `prod-${prod.id}`,
      isCategory: false,
      name: prod.name,
      description: prod.description,
      image: prod.image || '/logo.png',
      badge: prod.tag || '',
      link: `/san-pham/${prod.slug || prod.id}`,
      childCount: 0,
      childProducts: [],
      priceLabel: prod.base_price ? formatCurrency(prod.base_price) : 'Liên hệ'
    })
  })

  return items.slice(0, 8)
})

const hasShowcaseContent = computed(() => {
  return rootCategories.value.length > 0 && (categories.value.some(c => c.parent_id) || products.value.length > 0)
})

onMounted(async () => {
  try {
    const [catRes, prodRes] = await Promise.all([
      api.getCategories().catch(() => null),
      api.getProducts().catch(() => null)
    ])
    if (catRes?.data?.length) {
      categories.value = catRes.data
    }
    if (prodRes?.data?.length) {
      products.value = prodRes.data
    }

    // Ưu tiên chọn tab Nhóm Ngành Lớn đầu tiên có Danh Mục Cha hoặc có Ấn Phẩm
    if (rootCategories.value.length > 0) {
      const firstWithContent = rootCategories.value.find(rc => {
        const hasSubCat = categories.value.some(c => Number(c.parent_id) === Number(rc.id))
        const hasDirectProd = products.value.some(p => Number(p.category_id) === Number(rc.id) || p.category?.slug === rc.slug)
        return hasSubCat || hasDirectProd
      })
      activeRootSlug.value = (firstWithContent || rootCategories.value[0]).slug
    }
  } catch (err) {
    products.value = []
  }
})
</script>
