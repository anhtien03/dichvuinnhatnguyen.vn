<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
      <div>
        <div class="flex items-center gap-2">
          <h1 class="text-xl sm:text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
            Ma Trận Phân Quyền & Vai Trò
          </h1>
          <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-brand-gold/15 text-amber-700 border border-brand-gold/30">
            30 Quyền Hạn Chi Tiết
          </span>
        </div>
        <p class="text-xs sm:text-sm mt-1" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
          Tùy biến quyền truy cập và thao tác cho từng bộ phận trong Xưởng In Nhật Nguyên
        </p>
      </div>

      <div class="flex items-center gap-2.5 flex-wrap">
        <router-link
          to="/he-thong-admin/users"
          class="px-4 py-2.5 rounded-xl border text-xs font-bold transition-all flex items-center gap-2 cursor-pointer shadow-xs"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200 hover:bg-[#24303F]' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50'"
        >
          <ArrowLeft class="w-4 h-4" />
          <span>Danh Sách Nhân Viên</span>
        </router-link>

        <button
          @click="openCreateRoleModal"
          class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-black text-xs sm:text-sm shadow-md shadow-purple-600/20 transition-all flex items-center gap-2 cursor-pointer"
        >
          <Plus class="w-4 h-4" />
          <span>Tạo Vai Trò Mới</span>
        </button>
      </div>
    </div>

    <!-- Main 2-Column Matrix Layout -->
    <div v-if="isLoading" class="p-16 text-center">
      <Loader2 class="w-8 h-8 animate-spin mx-auto text-brand-gold mb-2" />
      <span class="text-xs text-slate-400">Đang tải cấu hình ma trận phân quyền...</span>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
      <!-- Left Column: Role Selector Cards (4 cols) -->
      <div class="lg:col-span-4 space-y-3">
        <label class="block text-xs font-black uppercase tracking-wider text-slate-400 px-1">
          Danh Sách Các Vai Trò (Roles):
        </label>

        <div
          v-for="role in roles"
          :key="role.id"
          @click="selectRole(role)"
          class="p-4 rounded-2xl border transition-all cursor-pointer relative"
          :class="[
            selectedRole?.id === role.id
              ? (isDarkMode ? 'bg-[#24303F] border-brand-gold ring-1 ring-brand-gold' : 'bg-blue-50/50 border-brand-blue ring-1 ring-brand-blue shadow-md')
              : (isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] hover:border-slate-500' : 'bg-white border-slate-200 hover:border-slate-300 shadow-xs')
          ]"
        >
          <div class="flex items-center justify-between gap-2 mb-1">
            <div class="flex items-center gap-2">
              <span
                class="w-3 h-3 rounded-full"
                :class="getRoleDotColor(role.color)"
              ></span>
              <span class="font-black text-sm" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                {{ role.name }}
              </span>
            </div>
            <span
              v-if="role.is_system"
              class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400"
            >
              Hệ thống
            </span>
          </div>

          <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
            {{ role.description || 'Chưa có mô tả' }}
          </p>

          <div class="mt-3 pt-2.5 border-t flex items-center justify-between text-[11px]" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <span class="text-slate-400">
              Nhân sự: <strong class="text-slate-700 dark:text-slate-200">{{ role.users_count || 0 }} người</strong>
            </span>
            <span class="font-bold text-brand-blue dark:text-purple-400">
              {{ role.slug === 'super_admin' ? 'Toàn quyền (30/30)' : `${role.permissions?.length || 0} quyền hạn` }}
            </span>
          </div>
        </div>
      </div>

      <!-- Right Column: Permission Matrix For Selected Role (8 cols) -->
      <div class="lg:col-span-8 space-y-6">
        <div
          v-if="selectedRole"
          class="rounded-3xl border shadow-sm p-6 space-y-6"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200/90'"
        >
          <!-- Selected Role Banner -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <div>
              <div class="flex items-center gap-2.5">
                <h3 class="text-lg font-black" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                  Thiết Lập Quyền: {{ selectedRole.name }}
                </h3>
                <span
                  class="px-2.5 py-0.5 rounded-full text-xs font-bold border"
                  :class="getRoleBadgeClasses(selectedRole.color)"
                >
                  {{ selectedRole.slug }}
                </span>
              </div>
              <p class="text-xs text-slate-400 mt-1">
                Tích chọn các quyền mà nhân sự thuộc vai trò này được phép truy cập và thực thi
              </p>
            </div>

            <!-- Super Admin Notice or Save Button -->
            <div v-if="selectedRole.slug === 'super_admin'" class="px-3.5 py-2 rounded-xl bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 border border-purple-200 text-xs font-bold flex items-center gap-2">
              <ShieldAlert class="w-4 h-4 shrink-0" />
              <span>Super Admin luôn có 100% quyền hạn</span>
            </div>

            <button
              v-else
              type="button"
              @click="saveRolePermissions"
              :disabled="isSaving"
              class="px-5 py-2.5 rounded-xl bg-brand-gold hover:bg-brand-gold-hover text-slate-950 font-black text-xs sm:text-sm shadow-md transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50"
            >
              <Loader2 v-if="isSaving" class="w-4 h-4 animate-spin" />
              <Save v-else class="w-4 h-4" />
              <span>Lưu Cấu Hình Quyền</span>
            </button>
          </div>

          <!-- Grouped Permissions Accordions / Checkbox List -->
          <div class="space-y-6">
            <div
              v-for="(groupData, groupKey) in permissionsGrouped"
              :key="groupKey"
              class="rounded-2xl border p-4 sm:p-5 transition-all"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47]' : 'bg-slate-50/70 border-slate-200/80'"
            >
              <!-- Group Header with Select All button -->
              <div class="flex items-center justify-between pb-3 border-b mb-3.5" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
                <div class="flex items-center gap-2">
                  <component :is="getGroupIcon(groupKey)" class="w-4 h-4 text-brand-gold" />
                  <h4 class="font-black text-sm" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                    {{ groupData.label }}
                  </h4>
                  <span class="text-[11px] text-slate-400">
                    ({{ getGroupSelectedCount(groupData.items) }}/{{ groupData.items.length }})
                  </span>
                </div>

                <div v-if="selectedRole.slug !== 'super_admin'" class="flex items-center gap-2">
                  <button
                    type="button"
                    @click="toggleGroup(groupData.items, true)"
                    class="text-[11px] font-bold text-blue-600 hover:underline cursor-pointer"
                  >
                    Chọn tất cả
                  </button>
                  <span class="text-slate-300">•</span>
                  <button
                    type="button"
                    @click="toggleGroup(groupData.items, false)"
                    class="text-[11px] font-bold text-slate-400 hover:text-slate-600 cursor-pointer"
                  >
                    Bỏ chọn
                  </button>
                </div>
              </div>

              <!-- Permission Items Grid -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <label
                  v-for="item in groupData.items"
                  :key="item.id"
                  class="flex items-start gap-3 p-3 rounded-xl border transition-all cursor-pointer"
                  :class="[
                    selectedPermissionIds.includes(item.id)
                      ? (isDarkMode ? 'bg-[#24303F] border-purple-500/50' : 'bg-white border-brand-blue/40 shadow-xs')
                      : (isDarkMode ? 'bg-[#1C2434] border-transparent opacity-60' : 'bg-white/60 border-slate-200/60 opacity-70')
                  ]"
                >
                  <input
                    type="checkbox"
                    :value="item.id"
                    v-model="selectedPermissionIds"
                    :disabled="selectedRole.slug === 'super_admin'"
                    class="w-4 h-4 mt-0.5 rounded text-brand-gold focus:ring-brand-gold disabled:opacity-50"
                  />
                  <div class="min-w-0">
                    <div class="font-extrabold text-xs" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                      {{ item.name }}
                    </div>
                    <div class="text-[10px] text-slate-400 leading-snug mt-0.5">
                      {{ item.description }}
                    </div>
                    <div class="text-[9px] font-mono text-slate-400 mt-1">
                      code: {{ item.slug }}
                    </div>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <!-- Bottom Save Button -->
          <div v-if="selectedRole.slug !== 'super_admin'" class="pt-4 border-t flex items-center justify-between" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <span class="text-xs text-slate-400">
              Đã chọn: <strong class="text-brand-blue font-black">{{ selectedPermissionIds.length }}</strong> quyền hạn.
            </span>
            <button
              type="button"
              @click="saveRolePermissions"
              :disabled="isSaving"
              class="px-6 py-2.5 rounded-xl bg-brand-gold hover:bg-brand-gold-hover text-slate-950 font-black text-xs sm:text-sm shadow-md transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50"
            >
              <Loader2 v-if="isSaving" class="w-4 h-4 animate-spin" />
              <Save v-else class="w-4 h-4" />
              <span>Lưu Cấu Hình Quyền Cho {{ selectedRole.name }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Create Custom Role -->
    <div
      v-if="isRoleModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
    >
      <div
        class="w-full max-w-md rounded-3xl border shadow-2xl overflow-hidden"
        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <div class="px-6 py-4 border-b flex items-center justify-between" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
          <h3 class="text-base font-black" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
            Tạo Vai Trò Tùy Chỉnh Mới
          </h3>
          <button @click="isRoleModalOpen = false" class="p-1.5 text-slate-400 hover:text-white rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="createCustomRole" class="p-6 space-y-4 text-xs">
          <div>
            <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
              Tên Vai Trò (Role Name) <span class="text-red-500">*</span>
            </label>
            <input
              v-model="newRole.name"
              type="text"
              required
              placeholder="Ví dụ: Trợ Lý Giám Đốc, Giao Nhận Hàng..."
              class="w-full px-3.5 py-2.5 rounded-xl border focus:outline-none"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white focus:border-brand-gold' : 'bg-slate-50 border-slate-200 text-slate-800 focus:border-brand-blue'"
            />
          </div>

          <div>
            <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
              Mô tả trách nhiệm
            </label>
            <textarea
              v-model="newRole.description"
              rows="3"
              placeholder="Quyền hạn và nhiệm vụ của vai trò này trong xưởng..."
              class="w-full px-3.5 py-2.5 rounded-xl border focus:outline-none"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white focus:border-brand-gold' : 'bg-slate-50 border-slate-200 text-slate-800 focus:border-brand-blue'"
            ></textarea>
          </div>

          <div>
            <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
              Màu đại diện
            </label>
            <div class="flex items-center gap-3">
              <label v-for="c in ['blue', 'amber', 'emerald', 'purple', 'pink']" :key="c" class="flex items-center gap-1.5 cursor-pointer">
                <input type="radio" v-model="newRole.color" :value="c" class="w-4 h-4 text-brand-gold" />
                <span class="w-4 h-4 rounded-full" :class="getRoleDotColor(c)"></span>
              </label>
            </div>
          </div>

          <div class="pt-4 border-t flex items-center justify-end gap-2.5" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <button
              type="button"
              @click="isRoleModalOpen = false"
              class="px-4 py-2.5 rounded-xl border font-bold text-slate-400 hover:text-white"
              :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200 text-slate-600'"
            >
              Hủy
            </button>
            <button
              type="submit"
              :disabled="isSaving"
              class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-black shadow-md cursor-pointer disabled:opacity-50"
            >
              Tạo Vai Trò
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import {
  ShieldCheck,
  ShieldAlert,
  ArrowLeft,
  Plus,
  Save,
  Loader2,
  X,
  ShoppingBag,
  Users,
  Wallet,
  TrendingUp,
  ClipboardList,
  Package,
  Settings
} from 'lucide-vue-next'
import { api } from '../../services/api'
import { useAdminTheme } from '../../composables/useAdminTheme'

const { isDarkMode } = useAdminTheme()

const roles = ref([])
const permissionsGrouped = ref({})
const allPermissionsList = ref([])
const selectedRole = ref(null)
const selectedPermissionIds = ref([])

const isLoading = ref(true)
const isSaving = ref(false)
const isRoleModalOpen = ref(false)

const newRole = ref({
  name: '',
  description: '',
  color: 'blue'
})

function getRoleDotColor(color) {
  switch (color) {
    case 'purple': return 'bg-purple-500'
    case 'blue': return 'bg-blue-500'
    case 'amber': return 'bg-amber-500'
    case 'emerald': return 'bg-emerald-500'
    case 'pink': return 'bg-pink-500'
    case 'indigo': return 'bg-indigo-500'
    default: return 'bg-slate-400'
  }
}

function getRoleBadgeClasses(color) {
  switch (color) {
    case 'purple': return 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-900/30 dark:border-purple-800 dark:text-purple-300'
    case 'blue': return 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:border-blue-800 dark:text-blue-300'
    case 'amber': return 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-900/30 dark:border-amber-800 dark:text-amber-300'
    case 'emerald': return 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:border-emerald-800 dark:text-emerald-300'
    case 'pink': return 'bg-pink-50 text-pink-700 border-pink-200 dark:bg-pink-900/30 dark:border-pink-800 dark:text-pink-300'
    case 'indigo': return 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-900/30 dark:border-indigo-800 dark:text-indigo-300'
    default: return 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-300'
  }
}

function getGroupIcon(groupKey) {
  switch (groupKey) {
    case 'orders': return ShoppingBag
    case 'clients': return Users
    case 'cashbook': return Wallet
    case 'finance': return TrendingUp
    case 'quotes': return ClipboardList
    case 'catalog': return Package
    case 'system': return Settings
    default: return ShieldCheck
  }
}

function selectRole(role) {
  selectedRole.value = role
  if (role.slug === 'super_admin') {
    selectedPermissionIds.value = allPermissionsList.value.map(p => p.id)
  } else {
    selectedPermissionIds.value = (role.permissions || []).map(p => p.id)
  }
}

function getGroupSelectedCount(items) {
  const itemIds = items.map(i => i.id)
  return itemIds.filter(id => selectedPermissionIds.value.includes(id)).length
}

function toggleGroup(items, selectAll) {
  const itemIds = items.map(i => i.id)
  if (selectAll) {
    const current = new Set(selectedPermissionIds.value)
    itemIds.forEach(id => current.add(id))
    selectedPermissionIds.value = Array.from(current)
  } else {
    selectedPermissionIds.value = selectedPermissionIds.value.filter(id => !itemIds.includes(id))
  }
}

async function loadData() {
  isLoading.value = true
  try {
    const [rolesRes, permsRes] = await Promise.all([
      api.getAdminRoles(),
      api.getAdminPermissions()
    ])
    roles.value = rolesRes.data || []
    permissionsGrouped.value = permsRes.data?.grouped || {}
    allPermissionsList.value = permsRes.data?.list || []

    if (roles.value.length > 0) {
      // Default select Sales or first
      const defaultRole = roles.value.find(r => r.slug === 'sales') || roles.value[0]
      selectRole(defaultRole)
    }
  } catch (err) {
    alert('Lỗi tải dữ liệu quyền hạn: ' + (err.message || 'Thử lại sau.'))
  } finally {
    isLoading.value = false
  }
}

async function saveRolePermissions() {
  if (!selectedRole.value || selectedRole.value.slug === 'super_admin') return

  isSaving.value = true
  try {
    await api.updateRolePermissions(selectedRole.value.id, selectedPermissionIds.value)
    // Update local role's permissions
    selectedRole.value.permissions = allPermissionsList.value.filter(p => selectedPermissionIds.value.includes(p.id))
    alert(`Đã lưu thành công ${selectedPermissionIds.value.length} quyền cho vai trò [${selectedRole.value.name}]!`)
  } catch (err) {
    alert('Lỗi lưu quyền: ' + (err.message || 'Thử lại sau.'))
  } finally {
    isSaving.value = false
  }
}

function openCreateRoleModal() {
  newRole.value = {
    name: '',
    description: '',
    color: 'blue'
  }
  isRoleModalOpen.value = true
}

async function createCustomRole() {
  isSaving.value = true
  try {
    const res = await api.createAdminRole(newRole.value)
    isRoleModalOpen.value = false
    await loadData()
    if (res.data) {
      selectRole(res.data)
    }
  } catch (err) {
    alert('Không thể tạo vai trò: ' + (err.message || 'Kiểm tra lại dữ liệu.'))
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  loadData()
})
</script>



