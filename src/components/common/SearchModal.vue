<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="close"></div>

    <!-- Modal dialog -->
    <div class="flex min-h-full items-start justify-center p-4 sm:p-6 md:p-20">
      <div class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
        <!-- Search input header -->
        <div class="relative border-b border-slate-100 flex items-center px-4 sm:px-6">
          <Search class="w-5 h-5 text-slate-400 mr-3 shrink-0" />
          <input
            ref="inputRef"
            v-model="query"
            type="text"
            placeholder="Tìm theo tên ấn phẩm (bao thư, danh thiếp, namecard, tờ rơi, decal...)"
            class="w-full py-4 text-base sm:text-lg bg-transparent text-slate-800 placeholder-slate-400 focus:outline-none"
            @keydown.esc="close"
          />
          <Loader2 v-if="isLoading" class="w-4 h-4 text-brand-gold animate-spin mr-2" />
          <button
            v-if="query"
            @click="query = ''"
            class="p-1 text-slate-400 hover:text-slate-600 rounded-full"
          >
            <X class="w-4 h-4" />
          </button>
          <button
            @click="close"
            class="ml-3 px-2 py-1 text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 rounded"
          >
            ESC
          </button>
        </div>

        <!-- Search Content -->
        <div class="max-h-[60vh] overflow-y-auto p-4 sm:p-6">
          <!-- Live results -->
          <div v-if="query.trim().length > 0">
            <div v-if="results.length > 0">
              <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">
                Ấn phẩm phù hợp ({{ results.length }})
              </h3>
              <div class="space-y-2">
                <router-link
                  v-for="item in results"
                  :key="item.id"
                  :to="'/san-pham/' + item.slug"
                  class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-brand-blue-light/50 transition-colors group"
                  @click="onSelect(item)"
                >
                  <img
                    :src="item.image || '/logo-icon.png'"
                    :alt="item.name"
                    class="w-12 h-12 object-cover rounded-lg border border-slate-200 shrink-0 bg-slate-100"
                  />
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                      <span class="font-semibold text-slate-800 group-hover:text-brand-blue truncate">
                        {{ item.name }}
                      </span>
                      <span v-if="item.tag" class="text-[10px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 font-medium shrink-0">
                        {{ item.tag }}
                      </span>
                    </div>
                    <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-2">
                      <span>{{ item.category?.name || item.categoryName || 'Ấn phẩm in' }}</span>
                      <span>•</span>
                      <span class="text-brand-accent font-semibold">
                        Từ {{ formatCurrency(item.base_price || item.basePrice || 0) }} / {{ item.unit || 'cái' }}
                      </span>
                    </div>
                  </div>
                  <ChevronRight class="w-4 h-4 text-slate-300 group-hover:text-brand-blue transition-colors shrink-0" />
                </router-link>
              </div>
            </div>
            <div v-else class="text-center py-10">
              <PackageSearch class="w-12 h-12 text-slate-300 mx-auto mb-3" />
              <p class="text-sm text-slate-600 font-medium">Không tìm thấy ấn phẩm phù hợp với "{{ query }}"</p>
              <p class="text-xs text-slate-400 mt-1">Vui lòng thử tìm với từ khóa: bao thư, danh thiếp, namecard, tờ rơi, decal...</p>
            </div>
          </div>

          <!-- Empty state: Recent & Popular -->
          <div v-else>
            <!-- Popular keywords chips -->
            <div class="mb-6">
              <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                  <Sparkles class="w-3.5 h-3.5 text-brand-gold" />
                  <span>Ấn phẩm được tìm nhiều</span>
                </h3>
              </div>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="chip in popularChips"
                  :key="chip"
                  @click="query = chip"
                  class="px-3 py-1.5 text-xs rounded-lg bg-slate-100 hover:bg-brand-blue-light hover:text-brand-blue font-medium text-slate-700 transition-colors"
                >
                  {{ chip }}
                </button>
              </div>
            </div>

            <!-- Categories quick link -->
            <div>
              <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">
                Danh mục dịch vụ in
              </h3>
              <div class="grid grid-cols-2 gap-2">
                <router-link
                  v-for="cat in displayCategories"
                  :key="cat.id"
                  :to="'/danh-muc/' + cat.slug"
                  class="p-2.5 rounded-lg border border-slate-100 hover:border-brand-blue hover:bg-slate-50 transition-colors flex items-center justify-between group"
                  @click="close"
                >
                  <span class="text-xs font-medium text-slate-700 group-hover:text-brand-blue">{{ cat.name }}</span>
                  <ArrowUpRight class="w-3.5 h-3.5 text-slate-300 group-hover:text-brand-blue" />
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer hotline note -->
        <div class="bg-slate-50 px-4 py-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
          <span>Cần in kích thước riêng hoặc số lượng lớn?</span>
          <router-link to="/yeu-cau-bao-gia" class="text-brand-blue font-semibold hover:underline" @click="close">
            Gửi yêu cầu báo giá xưởng &rarr;
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted } from 'vue'
import { Search, X, ChevronRight, PackageSearch, Sparkles, ArrowUpRight, Loader2 } from 'lucide-vue-next'
import { api } from '../../services/api'
import { categories as fallbackCategories } from '../../data/categories'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue'])

const isOpen = computed(() => props.modelValue)
const query = ref('')
const inputRef = ref(null)
const isLoading = ref(false)

const allProducts = ref([])
const allCategories = ref([])

const popularChips = [
  'Bao thư', 'Danh thiếp', 'Namecard', 'Tờ rơi', 'Tem decal', 'Túi giấy', 'Catalogue'
]

// Chuẩn hóa loại bỏ dấu tiếng Việt để tìm kiếm không phân biệt có dấu / không dấu
function removeVietnameseTones(str) {
  if (!str) return ''
  str = String(str).toLowerCase()
  str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, 'a')
  str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, 'e')
  str = str.replace(/ì|í|ị|ỉ|ĩ/g, 'i')
  str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, 'o')
  str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, 'u')
  str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, 'y')
  str = str.replace(/đ/g, 'd')
  str = str.replace(/\u0300|\u0301|\u0303|\u0309|\u0323/g, '')
  str = str.replace(/\u02C6|\u0306|\u031B/g, '')
  return str.trim()
}

async function loadData() {
  if (allProducts.value.length === 0) {
    isLoading.value = true
    try {
      const [resProd, resCat] = await Promise.allSettled([
        api.getProducts(),
        api.getCategories()
      ])
      if (resProd.status === 'fulfilled' && resProd.value?.data) {
        allProducts.value = Array.isArray(resProd.value.data) ? resProd.value.data : (resProd.value.data.data || [])
      }
      if (resCat.status === 'fulfilled' && resCat.value?.data) {
        allCategories.value = Array.isArray(resCat.value.data) ? resCat.value.data : (resCat.value.data.data || [])
      }
    } catch (err) {
      console.error('Failed to load search data', err)
    } finally {
      isLoading.value = false
    }
  }
}

const displayCategories = computed(() => {
  return allCategories.value.length > 0 ? allCategories.value : fallbackCategories
})

const results = computed(() => {
  if (!query.value.trim()) return []
  const rawQ = query.value.trim()
  const cleanQ = removeVietnameseTones(rawQ)
  const qWords = cleanQ.split(/\s+/).filter(Boolean)

  return allProducts.value.filter(p => {
    const name = p.name || ''
    const desc = p.description || ''
    const catName = p.category?.name || p.categoryName || ''
    const tag = p.tag || ''
    const slug = p.slug || ''

    // 1. Khớp chuỗi có dấu
    const rawTarget = `${name} ${desc} ${catName} ${tag} ${slug}`.toLowerCase()
    if (rawTarget.includes(rawQ.toLowerCase())) return true

    // 2. Khớp chuỗi không dấu (ví dụ: "bao thu" khớp với "Bao Thư Nhỏ")
    const cleanTarget = removeVietnameseTones(rawTarget)
    if (cleanTarget.includes(cleanQ)) return true

    // 3. Khớp từng từ khóa trong câu (ví dụ: "nho bao thu" cũng khớp)
    if (qWords.length > 1 && qWords.every(w => cleanTarget.includes(w))) return true

    return false
  })
})

watch(isOpen, async (val) => {
  if (val) {
    loadData()
    await nextTick()
    inputRef.value?.focus()
  } else {
    query.value = ''
  }
})

onMounted(() => {
  loadData()
})

function close() {
  emit('update:modelValue', false)
}

function onSelect(item) {
  close()
}

function formatCurrency(val) {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val)
}
</script>

