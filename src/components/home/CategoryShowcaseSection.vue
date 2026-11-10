<template>
  <section v-if="hasProducts" class="py-12 lg:py-16 bg-white border-b border-slate-200/80">
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
            v-for="cat in categories"
            :key="cat.id"
            @click="activeCategorySlug = cat.slug"
            class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all whitespace-nowrap cursor-pointer"
            :class="activeCategorySlug === cat.slug 
              ? 'bg-brand-blue text-white shadow-sm' 
              : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900'"
          >
            {{ cat.name }}
          </button>
        </div>
      </div>

      <!-- Main Showcase Grid -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        <router-link
          v-for="prod in currentCategoryProducts"
          :key="prod.id"
          :to="'/san-pham/' + (prod.slug || prod.id)"
          class="group rounded-2xl bg-white border border-slate-200/90 overflow-hidden hover:shadow-lg transition-all flex flex-col"
        >
          <div class="aspect-[4/3] bg-slate-100 overflow-hidden relative">
            <img
              :src="prod.image || '/logo.png'"
              :alt="prod.name"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              loading="lazy"
            />
            <span v-if="prod.tag" class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded-md bg-brand-gold text-slate-950 font-black text-[10px]">
              {{ prod.tag }}
            </span>
          </div>
          <div class="p-3.5 flex-1 flex flex-col justify-between">
            <h4 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-brand-blue line-clamp-2">
              {{ prod.name }}
            </h4>
            <div class="mt-2 text-xs font-black text-brand-blue">
              {{ prod.base_price ? formatCurrency(prod.base_price) : 'Liên hệ' }}
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
const activeCategorySlug = ref('')

function formatCurrency(val) {
  if (!val) return 'Liên hệ'
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val)
}

const hasProducts = computed(() => products.value.length > 0)

const currentCategoryProducts = computed(() => {
  if (!activeCategorySlug.value) return products.value.slice(0, 8)
  return products.value.filter(p => (p.category?.slug === activeCategorySlug.value || p.category_id === activeCategorySlug.value)).slice(0, 8)
})

onMounted(async () => {
  try {
    const [catRes, prodRes] = await Promise.all([
      api.getCategories().catch(() => null),
      api.getProducts().catch(() => null)
    ])
    if (catRes?.data?.length) {
      categories.value = catRes.data
      activeCategorySlug.value = catRes.data[0]?.slug || ''
    }
    if (prodRes?.data?.length) {
      products.value = prodRes.data
    }
  } catch (err) {
    products.value = []
  }
})
</script>
