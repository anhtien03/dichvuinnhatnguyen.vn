<template>
  <section v-if="hasProducts" class="py-12 lg:py-16 bg-white border-b border-slate-200/80">
    <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      
      <!-- Section Header with Category Tabs (Chỉ hiện các danh mục đang có sản phẩm) -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
          <span class="text-xs font-bold text-brand-gold uppercase tracking-wider block mb-1">
            DANH MỤC THIẾT KẾ & IN ẤN THEO NGÀNH
          </span>
          <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
            Bộ Sưu Tập Ấn Phẩm Chuyên Nghiệp
          </h2>
        </div>

        <!-- Category Tabs: Nút Tất cả + Các Danh mục có sản phẩm -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
          <button
            type="button"
            @click="activeCategorySlug = 'all'"
            class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all whitespace-nowrap cursor-pointer"
            :class="activeCategorySlug === 'all'
              ? 'bg-brand-blue text-white shadow-sm'
              : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900'"
          >
            Tất cả ({{ products.length }})
          </button>

          <button
            v-for="cat in categoriesWithProducts"
            :key="cat.id"
            type="button"
            @click="activeCategorySlug = cat.slug"
            class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all whitespace-nowrap cursor-pointer"
            :class="activeCategorySlug === cat.slug 
              ? 'bg-brand-blue text-white shadow-sm' 
              : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900'"
          >
            {{ cat.name }} ({{ getCategoryProductCount(cat) }})
          </button>
        </div>
      </div>

      <!-- Main Showcase Grid: Hiển thị TRỰC TIẾP thẻ Ấn phẩm -> Bấm 1 click vào thẳng trang nội dung tính giá -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
        <router-link
          v-for="prod in currentCategoryProducts"
          :key="prod.id"
          :to="'/san-pham/' + (prod.slug || prod.id)"
          class="group rounded-2xl bg-white border border-slate-200/90 overflow-hidden hover:shadow-xl hover:border-brand-blue/40 transition-all flex flex-col"
        >
          <div class="aspect-[4/3] bg-slate-100 overflow-hidden relative">
            <img
              :src="prod.image || '/logo.png'"
              :alt="prod.name"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              loading="lazy"
            />
            <span
              v-if="prod.category?.name"
              class="absolute top-2.5 left-2.5 px-2.5 py-0.5 rounded-md bg-slate-950/75 backdrop-blur-xs text-white font-bold text-[10px]"
            >
              {{ prod.category.name }}
            </span>
            <span
              v-if="prod.tag"
              class="absolute top-2.5 right-2.5 px-2.5 py-0.5 rounded-md bg-brand-gold text-slate-950 font-black text-[10px] shadow-xs"
            >
              {{ prod.tag }}
            </span>
          </div>

          <div class="p-3.5 sm:p-4 flex-1 flex flex-col justify-between gap-2">
            <div>
              <h4 class="text-sm sm:text-base font-black text-slate-900 group-hover:text-brand-blue line-clamp-1">
                {{ prod.name }}
              </h4>
              <p v-if="prod.description" class="text-[11px] text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                {{ stripHtml(prod.description) }}
              </p>
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
              <div class="text-xs sm:text-sm font-black text-brand-blue">
                {{ prod.base_price ? formatCurrency(prod.base_price) : 'Liên hệ' }}
                <span v-if="prod.base_price && prod.unit" class="text-[10px] text-slate-400 font-normal">/{{ prod.unit }}</span>
              </div>
              <span class="text-[11px] font-bold text-brand-blue group-hover:translate-x-0.5 transition-transform">
                Tính giá &rarr;
              </span>
            </div>
          </div>
        </router-link>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { api } from '../../services/api'

const categories = ref([])
const products = ref([])
const activeCategorySlug = ref('all')

function formatCurrency(val) {
  if (!val) return 'Liên hệ'
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val)
}

function stripHtml(html) {
  if (!html) return ''
  return String(html).replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim()
}

const hasProducts = computed(() => products.value.length > 0)

function isProductMatchingCategory(prod, cat) {
  if (!cat) return false
  if (Number(prod.category_id) === Number(cat.id) || prod.category?.slug === cat.slug) {
    return true
  }
  // Nếu cat là Nhóm ngành cha, khớp luôn cả sản phẩm thuộc danh mục con của nó
  if (prod.category?.parent?.slug === cat.slug || Number(prod.category?.parent_id) === Number(cat.id)) {
    return true
  }
  return false
}

function getCategoryProductCount(cat) {
  return products.value.filter(p => isProductMatchingCategory(p, cat)).length
}

// Chỉ hiển thị các nút Tab Danh mục thực sự có sản phẩm (> 0) để khách bấm vào luôn thấy sản phẩm ngay
const categoriesWithProducts = computed(() => {
  return categories.value.filter(c => c.is_active !== false && getCategoryProductCount(c) > 0)
})

const currentCategoryProducts = computed(() => {
  if (!activeCategorySlug.value || activeCategorySlug.value === 'all') {
    return products.value.slice(0, 8)
  }
  const targetCat = categories.value.find(c => c.slug === activeCategorySlug.value)
  if (!targetCat) return products.value.slice(0, 8)
  return products.value.filter(p => isProductMatchingCategory(p, targetCat)).slice(0, 8)
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
    // Mặc định chọn "all" để vừa vào trang chủ khách thấy ngay toàn bộ sản phẩm chủ lực (Danh thiếp chuẩn, Bao thư nhỏ...)
    activeCategorySlug.value = 'all'
  } catch (err) {
    products.value = []
  }
})
</script>
