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
            placeholder="Tìm theo tên ấn phẩm (namecard, tờ rơi, tem decal, bao bì...)"
            class="w-full py-4 text-base sm:text-lg bg-transparent text-slate-800 placeholder-slate-400 focus:outline-none"
            @keydown.esc="close"
          />
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
                    :src="item.image"
                    :alt="item.name"
                    class="w-12 h-12 object-cover rounded-lg border border-slate-200 shrink-0"
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
                      <span>{{ item.categoryName }}</span>
                      <span>•</span>
                      <span class="text-brand-accent font-semibold">
                        Từ {{ formatCurrency(item.basePrice) }} / {{ item.unit }}
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
              <p class="text-xs text-slate-400 mt-1">Vui lòng thử tìm với từ khóa: namecard, tờ rơi, decal, túi giấy...</p>
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
                  v-for="cat in categories"
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
import { ref, computed, watch, nextTick } from 'vue'
import { Search, X, ChevronRight, PackageSearch, Sparkles, ArrowUpRight } from 'lucide-vue-next'
import { products } from '../../data/products'
import { categories } from '../../data/categories'

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

const popularChips = [
  'Namecard', 'Tờ rơi A5', 'Tem decal', 'Túi giấy', 'Standee', 'Catalogue', 'Decal chống nước'
]

const results = computed(() => {
  if (!query.value.trim()) return []
  const q = query.value.toLowerCase().trim()
  return products.filter(p => 
    p.name.toLowerCase().includes(q) || 
    p.categoryName.toLowerCase().includes(q) ||
    p.description.toLowerCase().includes(q)
  )
})

watch(isOpen, async (val) => {
  if (val) {
    await nextTick()
    inputRef.value?.focus()
  } else {
    query.value = ''
  }
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

