<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
      <div>
        <div class="flex items-center gap-2">
          <h1 class="text-xl sm:text-2xl font-black tracking-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
            Quản Lý Nhân Sự & Tài Khoản
          </h1>
          <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-500/10 text-purple-500 border border-purple-500/20">
            {{ users.length }} Thành viên
          </span>
        </div>
        <p class="text-xs sm:text-sm mt-1" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
          Phân cấp nhân viên theo phòng ban: Kinh doanh, Kỹ thuật xưởng, Kế toán và Ban quản trị
        </p>
      </div>

      <div class="flex items-center gap-2.5 flex-wrap">
        <router-link
          to="/he-thong-admin/roles"
          class="px-4 py-2.5 rounded-xl border text-xs font-bold transition-all flex items-center gap-2 cursor-pointer shadow-xs"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200 hover:bg-[#24303F]' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50'"
        >
          <ShieldCheck class="w-4 h-4 text-purple-500" />
          <span>Ma Trận Phân Quyền (Roles)</span>
        </router-link>

        <button
          @click="openCreateModal"
          class="px-5 py-2.5 rounded-xl bg-brand-gold hover:bg-brand-gold-hover text-slate-950 font-black text-xs sm:text-sm shadow-md shadow-brand-gold/20 transition-all flex items-center gap-2 cursor-pointer transform hover:-translate-y-0.5"
        >
          <UserPlus class="w-4 h-4" />
          <span>Thêm Nhân Viên Mới</span>
        </button>
      </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
      <div
        v-for="(card, idx) in statCards"
        :key="idx"
        class="p-4 rounded-2xl border transition-all"
        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200/80 shadow-xs'"
      >
        <div class="flex items-center justify-between text-xs font-medium" :class="isDarkMode ? 'text-slate-400' : 'text-slate-500'">
          <span>{{ card.label }}</span>
          <component :is="card.icon" class="w-4 h-4" :class="card.color" />
        </div>
        <div class="text-2xl font-black mt-2" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
          {{ card.value }}
        </div>
      </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="p-4 rounded-2xl border flex flex-col sm:flex-row items-center justify-between gap-3" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200/80 shadow-xs'">
      <div class="relative w-full sm:w-80">
        <Search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
        <input
          v-model="filters.search"
          type="text"
          placeholder="Tìm theo tên, email, số điện thoại..."
          class="w-full pl-9 pr-4 py-2 rounded-xl text-xs border focus:outline-none focus:ring-1 transition-all"
          :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white focus:border-brand-gold focus:ring-brand-gold' : 'bg-slate-50 border-slate-200 text-slate-800 focus:border-brand-blue focus:ring-brand-blue'"
        />
      </div>

      <div class="flex items-center gap-2.5 w-full sm:w-auto">
        <!-- Role Filter -->
        <select
          v-model="filters.roleId"
          class="px-3 py-2 rounded-xl text-xs border focus:outline-none cursor-pointer"
          :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
        >
          <option value="">Tất cả vai trò</option>
          <option v-for="r in roles" :key="r.id" :value="r.id">
            {{ r.name }}
          </option>
        </select>

        <!-- Status Filter -->
        <select
          v-model="filters.status"
          class="px-3 py-2 rounded-xl text-xs border focus:outline-none cursor-pointer"
          :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
        >
          <option value="">Tất cả trạng thái</option>
          <option value="active">Đang hoạt động</option>
          <option value="inactive">Đã tạm khóa</option>
        </select>
      </div>
    </div>

    <!-- Users Table -->
    <div class="rounded-3xl border overflow-hidden shadow-sm" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200/90'">
      <div v-if="isLoading" class="p-12 text-center">
        <Loader2 class="w-8 h-8 animate-spin mx-auto text-brand-gold mb-2" />
        <span class="text-xs text-slate-400">Đang tải danh sách nhân sự...</span>
      </div>

      <div v-else-if="filteredUsers.length === 0" class="p-12 text-center">
        <Users class="w-10 h-10 mx-auto text-slate-400 mb-2 opacity-50" />
        <p class="text-sm font-bold" :class="isDarkMode ? 'text-white' : 'text-slate-800'">Không tìm thấy nhân viên nào</p>
        <p class="text-xs text-slate-400 mt-1">Thử thay đổi từ khóa tìm kiếm hoặc thêm nhân viên mới.</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="border-b uppercase tracking-wider font-extrabold text-[11px]" :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-600'">
            <tr>
              <th class="py-3.5 px-4">Nhân Viên</th>
              <th class="py-3.5 px-4">Số Điện Thoại / Zalo</th>
              <th class="py-3.5 px-4">Vai Trò (Role)</th>
              <th class="py-3.5 px-4 text-center">Trạng Thái</th>
              <th class="py-3.5 px-4">Ngày Tạo</th>
              <th class="py-3.5 px-4 text-right">Thao Tác</th>
            </tr>
          </thead>
          <tbody class="divide-y" :class="isDarkMode ? 'divide-[#2E3A47]' : 'divide-slate-100'">
            <tr
              v-for="u in filteredUsers"
              :key="u.id"
              class="transition-colors hover:bg-slate-50/50"
              :class="isDarkMode ? 'hover:bg-[#24303F]/50' : ''"
            >
              <!-- Name & Email with Avatar -->
              <td class="py-3.5 px-4">
                <div class="flex items-center gap-3">
                  <div
                    class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-xs shrink-0 shadow-xs"
                    :class="getRoleColorClasses(u.role?.color)"
                  >
                    {{ getInitials(u.name) }}
                  </div>
                  <div class="min-w-0">
                    <div class="font-extrabold text-sm truncate flex items-center gap-1.5" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                      <span>{{ u.name }}</span>
                      <span v-if="u.id === currentUser?.id" class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-500/10 text-amber-500 border border-amber-500/20">
                        Bạn
                      </span>
                    </div>
                    <div class="text-[11px] text-slate-400 truncate mt-0.5">
                      {{ u.email }}
                    </div>
                  </div>
                </div>
              </td>

              <!-- Phone -->
              <td class="py-3.5 px-4 font-medium" :class="isDarkMode ? 'text-slate-300' : 'text-slate-700'">
                {{ u.phone || '—' }}
              </td>

              <!-- Role Badge -->
              <td class="py-3.5 px-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                  :class="getRoleBadgeClasses(u.role?.color)"
                >
                  <ShieldCheck class="w-3 h-3" />
                  <span>{{ u.role?.name || 'Chưa gán vai trò' }}</span>
                </span>
              </td>

              <!-- Active Status Toggle -->
              <td class="py-3.5 px-4 text-center">
                <button
                  type="button"
                  @click="toggleUserActive(u)"
                  :disabled="u.id === currentUser?.id || u.id === 1"
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold cursor-pointer transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                  :class="u.is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-600 border border-red-200'"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="u.is_active ? 'bg-emerald-500' : 'bg-red-500'"></span>
                  <span>{{ u.is_active ? 'Hoạt động' : 'Tạm khóa' }}</span>
                </button>
              </td>

              <!-- Created At -->
              <td class="py-3.5 px-4 text-slate-400 text-[11px]">
                {{ u.created_at || '—' }}
              </td>

              <!-- Actions -->
              <td class="py-3.5 px-4 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button
                    @click="openEditModal(u)"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-brand-blue hover:bg-slate-100 transition-colors cursor-pointer"
                    :class="isDarkMode ? 'hover:bg-[#2E3A47]' : ''"
                    title="Chỉnh sửa tài khoản & mật khẩu"
                  >
                    <Edit3 class="w-4 h-4" />
                  </button>

                  <button
                    v-if="u.id !== currentUser?.id && u.id !== 1"
                    @click="confirmDeleteUser(u)"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors cursor-pointer"
                    :class="isDarkMode ? 'hover:bg-red-500/10' : ''"
                    title="Xóa nhân viên"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Create / Edit User -->
    <div
      v-if="isModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
    >
      <div
        class="w-full max-w-lg rounded-3xl border shadow-2xl overflow-hidden transition-all"
        :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200'"
      >
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b flex items-center justify-between" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
          <div>
            <h3 class="text-base font-black" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              {{ isEditing ? 'Chỉnh Sửa Thông Tin Nhân Viên' : 'Thêm Nhân Viên Mới' }}
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">
              {{ isEditing ? 'Cập nhật phòng ban, vai trò hoặc đổi mật khẩu' : 'Cấp tài khoản đăng nhập vào hệ thống xưởng in' }}
            </p>
          </div>
          <button @click="isModalOpen = false" class="p-1.5 text-slate-400 hover:text-white rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Form Body -->
        <form @submit.prevent="saveUser" class="p-6 space-y-4 text-xs">
          <!-- Full Name -->
          <div>
            <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
              Họ và tên nhân viên <span class="text-red-500">*</span>
            </label>
            <input
              v-model="userForm.name"
              type="text"
              required
              placeholder="Ví dụ: Nguyễn Văn Hoàng (Sales)"
              class="w-full px-3.5 py-2.5 rounded-xl border focus:outline-none"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white focus:border-brand-gold' : 'bg-slate-50 border-slate-200 text-slate-800 focus:border-brand-blue'"
            />
          </div>

          <!-- Email & Phone Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                Email đăng nhập <span class="text-red-500">*</span>
              </label>
              <input
                v-model="userForm.email"
                type="email"
                required
                placeholder="sales@dichvuinnhatnguyen.vn"
                class="w-full px-3.5 py-2.5 rounded-xl border focus:outline-none"
                :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white focus:border-brand-gold' : 'bg-slate-50 border-slate-200 text-slate-800 focus:border-brand-blue'"
              />
            </div>

            <div>
              <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
                Số điện thoại / Zalo
              </label>
              <input
                v-model="userForm.phone"
                type="tel"
                placeholder="0912 345 678"
                class="w-full px-3.5 py-2.5 rounded-xl border focus:outline-none"
                :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white focus:border-brand-gold' : 'bg-slate-50 border-slate-200 text-slate-800 focus:border-brand-blue'"
              />
            </div>
          </div>

          <!-- Role Selection -->
          <div>
            <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
              Phân Vai Trò (Role) <span class="text-red-500">*</span>
            </label>
            <select
              v-model="userForm.role_id"
              required
              class="w-full px-3.5 py-2.5 rounded-xl border focus:outline-none cursor-pointer"
              :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white focus:border-brand-gold' : 'bg-slate-50 border-slate-200 text-slate-800 focus:border-brand-blue'"
            >
              <option value="" disabled>-- Chọn vai trò nghiệp vụ --</option>
              <option v-for="r in roles" :key="r.id" :value="r.id">
                {{ r.name }} ({{ r.description }})
              </option>
            </select>
          </div>

          <!-- Password Field -->
          <div>
            <label class="block font-bold mb-1.5" :class="isDarkMode ? 'text-slate-200' : 'text-slate-700'">
              {{ isEditing ? 'Mật khẩu mới (bỏ trống nếu không đổi)' : 'Mật khẩu đăng nhập' }}
              <span v-if="!isEditing" class="text-red-500">*</span>
            </label>
            <div class="relative">
              <input
                v-model="userForm.password"
                :type="showPassword ? 'text' : 'password'"
                :required="!isEditing"
                placeholder="Tối thiểu 6 ký tự"
                class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border focus:outline-none"
                :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white focus:border-brand-gold' : 'bg-slate-50 border-slate-200 text-slate-800 focus:border-brand-blue'"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
              >
                <Eye v-if="!showPassword" class="w-4 h-4" />
                <EyeOff v-else class="w-4 h-4" />
              </button>
            </div>
          </div>

          <!-- Active checkbox -->
          <div class="pt-1">
            <label class="flex items-center gap-2.5 cursor-pointer">
              <input
                v-model="userForm.is_active"
                type="checkbox"
                class="w-4 h-4 rounded text-brand-gold focus:ring-brand-gold"
              />
              <span class="font-bold" :class="isDarkMode ? 'text-white' : 'text-slate-800'">
                Kích hoạt tài khoản ngay (Cho phép đăng nhập)
              </span>
            </label>
          </div>

          <!-- Modal Actions -->
          <div class="pt-4 border-t flex items-center justify-end gap-2.5" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
            <button
              type="button"
              @click="isModalOpen = false"
              class="px-4 py-2.5 rounded-xl border font-bold text-slate-400 hover:text-white transition-colors"
              :class="isDarkMode ? 'border-[#2E3A47] hover:bg-[#24303F]' : 'border-slate-200 hover:bg-slate-100 text-slate-600'"
            >
              Hủy Bỏ
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="px-6 py-2.5 rounded-xl bg-brand-gold hover:bg-brand-gold-hover text-slate-950 font-black shadow-md transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50"
            >
              <Loader2 v-if="isSubmitting" class="w-4 h-4 animate-spin" />
              <span v-else>{{ isEditing ? 'Cập Nhật Nhân Viên' : 'Tạo Tài Khoản' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  Users,
  UserPlus,
  ShieldCheck,
  Search,
  Edit3,
  Trash2,
  Eye,
  EyeOff,
  X,
  Loader2,
  CheckCircle2,
  UserCheck,
  Building,
  KeyRound
} from 'lucide-vue-next'
import { api, getAdminUser } from '../../services/api'
import { useAdminTheme } from '../../composables/useAdminTheme'
import { swalToast, swalError, swalConfirm, swalDelete } from '../../utils/swal'

const { isDarkMode } = useAdminTheme()
const currentUser = ref(getAdminUser())

const users = ref([])
const roles = ref([])
const isLoading = ref(true)
const isSubmitting = ref(false)
const isModalOpen = ref(false)
const isEditing = ref(false)
const editingUserId = ref(null)
const showPassword = ref(false)

const filters = ref({
  search: '',
  roleId: '',
  status: ''
})

const userForm = ref({
  name: '',
  email: '',
  phone: '',
  role_id: '',
  password: '',
  is_active: true
})

const statCards = computed(() => {
  const total = users.value.length
  const active = users.value.filter(u => u.is_active).length
  const salesCount = users.value.filter(u => u.role?.slug === 'sales').length
  const prodCount = users.value.filter(u => u.role?.slug === 'production').length

  return [
    { label: 'Tổng Nhân Viên', value: total, icon: Users, color: 'text-purple-500' },
    { label: 'Đang Hoạt Động', value: active, icon: UserCheck, color: 'text-emerald-500' },
    { label: 'Phòng Bán Hàng (Sales)', value: salesCount, icon: KeyRound, color: 'text-blue-500' },
    { label: 'Kỹ Thuật Xưởng', value: prodCount, icon: Building, color: 'text-amber-500' },
  ]
})

const filteredUsers = computed(() => {
  return users.value.filter(u => {
    // Search
    if (filters.value.search) {
      const q = filters.value.search.toLowerCase()
      const matchName = u.name?.toLowerCase().includes(q)
      const matchEmail = u.email?.toLowerCase().includes(q)
      const matchPhone = u.phone?.toLowerCase().includes(q)
      if (!matchName && !matchEmail && !matchPhone) return false
    }

    // Role
    if (filters.value.roleId && u.role?.id !== Number(filters.value.roleId)) {
      return false
    }

    // Status
    if (filters.value.status) {
      const wantActive = filters.value.status === 'active'
      if (u.is_active !== wantActive) return false
    }

    return true
  })
})

function getInitials(name) {
  if (!name) return 'NN'
  const parts = name.trim().split(' ')
  if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase()
  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
}

function getRoleColorClasses(color) {
  switch (color) {
    case 'purple': return 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300'
    case 'blue': return 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
    case 'amber': return 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300'
    case 'emerald': return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'
    case 'pink': return 'bg-pink-100 text-pink-700 dark:bg-pink-900/40 dark:text-pink-300'
    case 'indigo': return 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300'
    default: return 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
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

async function loadData() {
  isLoading.value = true
  try {
    const [usersRes, rolesRes] = await Promise.all([
      api.getAdminUsers(),
      api.getAdminRoles()
    ])
    users.value = usersRes.data || []
    roles.value = rolesRes.data || []
  } catch (err) {
    swalError('Lỗi tải dữ liệu nhân sự', err.message || 'Vui lòng thử lại.')
  } finally {
    isLoading.value = false
  }
}

function openCreateModal() {
  isEditing.value = false
  editingUserId.value = null
  showPassword.value = false
  userForm.value = {
    name: '',
    email: '',
    phone: '',
    role_id: roles.value[1]?.id || roles.value[0]?.id || '',
    password: '',
    is_active: true
  }
  isModalOpen.value = true
}

function openEditModal(u) {
  isEditing.value = true
  editingUserId.value = u.id
  showPassword.value = false
  userForm.value = {
    name: u.name,
    email: u.email,
    phone: u.phone || '',
    role_id: u.role?.id || '',
    password: '',
    is_active: u.is_active
  }
  isModalOpen.value = true
}

async function saveUser() {
  isSubmitting.value = true
  try {
    if (isEditing.value) {
      await api.updateAdminUser(editingUserId.value, userForm.value)
      swalToast('Đã cập nhật thông tin nhân viên')
    } else {
      await api.createAdminUser(userForm.value)
      swalToast('Đã thêm nhân viên mới')
    }
    isModalOpen.value = false
    await loadData()
  } catch (err) {
    swalError('Thao tác không thành công', err.message || 'Vui lòng kiểm tra lại.')
  } finally {
    isSubmitting.value = false
  }
}

async function toggleUserActive(u) {
  const confirmed = await swalConfirm({
    title: `${u.is_active ? 'Tạm khóa' : 'Mở khóa'} tài khoản?`,
    text: `Bạn có chắc muốn ${u.is_active ? 'tạm khóa' : 'mở khóa'} tài khoản [${u.name}]?`,
    icon: 'warning',
    confirmText: u.is_active ? 'Tạm khóa' : 'Mở khóa',
    danger: u.is_active
  })
  if (confirmed) {
    try {
      const res = await api.toggleAdminUserActive(u.id)
      u.is_active = res.is_active
      swalToast(`Đã ${u.is_active ? 'mở khóa' : 'tạm khóa'} tài khoản ${u.name}`)
    } catch (err) {
      swalError('Lỗi cập nhật trạng thái', err.message || 'Thử lại sau.')
    }
  }
}

async function confirmDeleteUser(u) {
  const confirmed = await swalDelete(`nhân viên [${u.name}] (${u.email})`)
  if (confirmed) {
    try {
      await api.deleteAdminUser(u.id)
      swalToast(`Đã xóa nhân viên ${u.name}`)
      await loadData()
    } catch (err) {
      swalError('Không thể xóa', err.message || 'Thử lại sau.')
    }
  }
}

onMounted(() => {
  loadData()
})
</script>



