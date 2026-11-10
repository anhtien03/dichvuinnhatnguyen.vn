<template>
  <div :class="[isDarkMode ? 'dark bg-[#1A222C] text-[#DEE4EE]' : 'bg-[#F1F5F9] text-[#64748B]']" class="min-h-screen flex font-sans transition-colors duration-200">
    <!-- Mobile Sidebar Backdrop -->
    <div
      v-if="isMobileMenuOpen"
      @click="isMobileMenuOpen = false"
      class="fixed inset-0 z-40 bg-black/50 lg:hidden backdrop-blur-sm"
    ></div>

    <!-- Sidebar (TailAdmin Styled) -->
    <aside
      :class="[
        isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-white border-slate-200',
        isSidebarCollapsed ? 'w-20' : 'w-64 sm:w-72',
        isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
      ]"
      class="fixed inset-y-0 left-0 z-50 flex flex-col border-r transition-all duration-300 ease-in-out shrink-0"
    >
      <!-- Sidebar Header & Brand -->
      <div class="h-20 px-6 flex items-center justify-between border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
        <router-link to="/he-thong-admin/dashboard" class="flex items-center gap-3 overflow-hidden">
          <img
            src="/logo-icon.png"
            alt="Nhật Nguyên"
            class="w-10 h-10 object-contain rounded-xl shrink-0"
          />
          <div v-if="!isSidebarCollapsed" class="truncate">
            <div class="text-base font-black tracking-wider leading-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
              NHẬT NGUYÊN
            </div>
          </div>
        </router-link>

        <!-- Desktop collapse toggle button -->
        <button
          @click="toggleCollapse"
          class="hidden lg:flex p-1.5 rounded-lg text-slate-400 hover:text-white transition-colors cursor-pointer"
          :class="isDarkMode ? 'hover:bg-[#2E3A47]' : 'hover:bg-slate-100 hover:text-slate-900'"
          :title="isSidebarCollapsed ? 'Mở rộng sidebar' : 'Thu nhỏ sidebar'"
        >
          <ChevronLeft v-if="!isSidebarCollapsed" class="w-4 h-4" />
          <ChevronRight v-else class="w-4 h-4" />
        </button>

        <!-- Mobile close button -->
        <button
          @click="isMobileMenuOpen = false"
          class="lg:hidden p-2 text-slate-400 hover:text-white"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Navigation Menu Groups (TailAdmin structure) -->
      <nav class="flex-1 px-4 py-6 space-y-6 overflow-y-auto custom-scrollbar">
        <!-- Group 1: MENU CHÍNH -->
        <div v-if="visiblePrimaryNav.length > 0">
          <h3
            v-if="!isSidebarCollapsed"
            class="text-[11px] uppercase font-bold tracking-wider mb-2.5 px-3"
            :class="isDarkMode ? 'text-slate-500' : 'text-slate-400'"
          >
            MENU CHÍNH
          </h3>
          <ul class="space-y-1.5">
            <li v-for="item in visiblePrimaryNav" :key="item.path">
              <router-link
                :to="item.path"
                @click="isMobileMenuOpen = false"
                class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-xs font-semibold transition-all group"
                :class="[
                  isItemActive(item.path)
                    ? (isDarkMode ? 'bg-[#333A48] text-white font-bold shadow-sm' : 'bg-brand-gold text-slate-950 font-bold shadow-sm')
                    : (isDarkMode ? 'text-[#8A99AD] hover:bg-[#333A48]/60 hover:text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900')
                ]"
                :title="isSidebarCollapsed ? item.label : ''"
              >
                <div class="flex items-center gap-3">
                  <component
                    :is="item.icon"
                    class="w-4 h-4 shrink-0 transition-transform group-hover:scale-110"
                    :class="isItemActive(item.path) ? 'text-brand-gold' : ''"
                  />
                  <span v-if="!isSidebarCollapsed">{{ item.label }}</span>
                </div>

                <!-- Pending quotes badge -->
                <span
                  v-if="item.badge && pendingCount > 0 && !isSidebarCollapsed"
                  class="px-2 py-0.5 text-[10px] font-black rounded-full"
                  :class="isItemActive(item.path) ? 'bg-amber-400 text-slate-950' : 'bg-red-500 text-white animate-pulse'"
                >
                  {{ pendingCount }}
                </span>
                <span
                  v-if="item.badge && pendingCount > 0 && isSidebarCollapsed"
                  class="w-2 h-2 rounded-full bg-red-500"
                ></span>
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Group: QUẢN LÝ VẬN HÀNH & IN ẤN (ERP) -->
        <div v-if="visibleErpNav.length > 0">
          <h3
            v-if="!isSidebarCollapsed"
            class="text-[11px] uppercase font-black tracking-wider mb-2.5 px-3 text-brand-gold"
          >
            VẬN HÀNH & IN ẤN (ERP)
          </h3>
          <ul class="space-y-1.5">
            <li v-for="item in visibleErpNav" :key="item.path">
              <router-link
                :to="item.path"
                @click="isMobileMenuOpen = false"
                class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-xs font-semibold transition-all group"
                :class="[
                  isItemActive(item.path)
                    ? (isDarkMode ? 'bg-[#333A48] text-white font-bold' : 'bg-brand-gold text-slate-950 font-bold')
                    : (isDarkMode ? 'text-[#8A99AD] hover:bg-[#333A48]/60 hover:text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900')
                ]"
                :title="isSidebarCollapsed ? item.label : ''"
              >
                <div class="flex items-center gap-3">
                  <component
                    :is="item.icon"
                    class="w-4 h-4 shrink-0 transition-transform group-hover:scale-110"
                    :class="isItemActive(item.path) ? 'text-brand-gold' : ''"
                  />
                  <span v-if="!isSidebarCollapsed">{{ item.label }}</span>
                </div>
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Group 2: QUẢN LÝ DỮ LIỆU -->
        <div v-if="visibleDataNav.length > 0">
          <h3
            v-if="!isSidebarCollapsed"
            class="text-[11px] uppercase font-bold tracking-wider mb-2.5 px-3"
            :class="isDarkMode ? 'text-slate-500' : 'text-slate-400'"
          >
            QUẢN LÝ DỮ LIỆU
          </h3>
          <ul class="space-y-1.5">
            <li v-for="item in visibleDataNav" :key="item.path">
              <router-link
                :to="item.path"
                @click="isMobileMenuOpen = false"
                class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-xs font-semibold transition-all group"
                :class="[
                  isItemActive(item.path)
                    ? (isDarkMode ? 'bg-[#333A48] text-white font-bold' : 'bg-brand-gold text-slate-950 font-bold')
                    : (isDarkMode ? 'text-[#8A99AD] hover:bg-[#333A48]/60 hover:text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900')
                ]"
                :title="isSidebarCollapsed ? item.label : ''"
              >
                <div class="flex items-center gap-3">
                  <component
                    :is="item.icon"
                    class="w-4 h-4 shrink-0 transition-transform group-hover:scale-110"
                    :class="isItemActive(item.path) ? 'text-brand-gold' : ''"
                  />
                  <span v-if="!isSidebarCollapsed">{{ item.label }}</span>
                </div>

                <!-- Pending quotes badge -->
                <span
                  v-if="item.badge && pendingCount > 0 && !isSidebarCollapsed"
                  class="px-2 py-0.5 text-[10px] font-black rounded-full"
                  :class="isItemActive(item.path) ? 'bg-amber-400 text-slate-950' : 'bg-red-500 text-white animate-pulse'"
                >
                  {{ pendingCount }}
                </span>
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Group 3: CÀI ĐẶT & HỆ THỐNG -->
        <div v-if="visibleSystemNav.length > 0">
          <h3
            v-if="!isSidebarCollapsed"
            class="text-[11px] uppercase font-bold tracking-wider mb-2.5 px-3"
            :class="isDarkMode ? 'text-slate-500' : 'text-slate-400'"
          >
            HỆ THỐNG
          </h3>
          <ul class="space-y-1.5">
            <li v-for="item in visibleSystemNav" :key="item.path">
              <router-link
                :to="item.path"
                @click="isMobileMenuOpen = false"
                class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-xs font-semibold transition-all group"
                :class="[
                  isItemActive(item.path)
                    ? (isDarkMode ? 'bg-[#333A48] text-white font-bold' : 'bg-brand-gold text-slate-950 font-bold')
                    : (isDarkMode ? 'text-[#8A99AD] hover:bg-[#333A48]/60 hover:text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900')
                ]"
                :title="isSidebarCollapsed ? item.label : ''"
              >
                <div class="flex items-center gap-3">
                  <component
                    :is="item.icon"
                    class="w-4 h-4 shrink-0 transition-transform group-hover:scale-110"
                    :class="isItemActive(item.path) ? 'text-brand-gold' : ''"
                  />
                  <span v-if="!isSidebarCollapsed">{{ item.label }}</span>
                </div>
              </router-link>
            </li>
          </ul>
        </div>
      </nav>

      <!-- Sidebar Bottom Trust Bar -->
      <div class="p-4 border-t" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
        <a
          href="/"
          target="_blank"
          class="flex items-center justify-center gap-2 w-full py-2.5 px-3 rounded-lg text-xs font-semibold transition-all"
          :class="isDarkMode ? 'bg-[#24303F] hover:bg-[#333A48] text-[#DEE4EE]' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
        >
          <ExternalLink class="w-3.5 h-3.5 text-brand-gold" />
          <span v-if="!isSidebarCollapsed">Xem Website Trang Chủ</span>
        </a>
      </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div
      :class="[isSidebarCollapsed ? 'lg:pl-20' : 'lg:pl-72']"
      class="flex-1 flex flex-col min-w-0 transition-all duration-300 ease-in-out"
    >
      <!-- Top Header (TailAdmin Styled) -->
      <header
        class="h-20 sticky top-0 z-30 px-4 sm:px-6 lg:px-8 flex items-center justify-between border-b backdrop-blur-md transition-colors"
        :class="isDarkMode ? 'bg-[#1C2434]/90 border-[#2E3A47]' : 'bg-white/90 border-slate-200'"
      >
        <!-- Left: Hamburger toggle & Breadcrumb / Title -->
        <div class="flex items-center gap-4">
          <!-- Mobile Hamburger -->
          <button
            @click="isMobileMenuOpen = true"
            class="lg:hidden p-2 rounded-lg border text-slate-400 hover:text-white"
            :class="isDarkMode ? 'border-[#2E3A47] bg-[#24303F]' : 'border-slate-200 bg-slate-50'"
          >
            <Menu class="w-5 h-5" />
          </button>

          <!-- Search Bar (TailAdmin style) -->
          <div class="hidden sm:flex items-center relative w-64 lg:w-80">
            <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
            <input
              v-model="globalSearch"
              @keydown.enter="handleGlobalSearch"
              type="text"
              placeholder="Tìm nhanh đơn hàng, SĐT... (Enter)"
              class="w-full text-xs rounded-xl pl-9 pr-4 py-2 border transition-all focus:outline-none focus:ring-1 focus:ring-brand-gold"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-white placeholder-slate-500' : 'bg-slate-50 border-slate-200 text-slate-800 placeholder-slate-400'"
            />
          </div>
        </div>

        <!-- Right: Actions & User Menu -->
        <div class="flex items-center gap-3 sm:gap-4">
          <!-- Dark / Light Mode Switch (TailAdmin Slider Style) -->
          <button
            @click="toggleTheme"
            class="relative flex items-center p-1.5 rounded-full transition-colors cursor-pointer border"
            :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47]' : 'bg-slate-100 border-slate-200'"
            :title="isDarkMode ? 'Chuyển sang giao diện Sáng' : 'Chuyển sang giao diện Tối'"
          >
            <div class="flex items-center gap-1.5">
              <div
                class="w-6 h-6 rounded-full flex items-center justify-center transition-all"
                :class="!isDarkMode ? 'bg-brand-gold text-slate-950 shadow-sm' : 'text-slate-400'"
              >
                <Sun class="w-3.5 h-3.5" />
              </div>
              <div
                class="w-6 h-6 rounded-full flex items-center justify-center transition-all"
                :class="isDarkMode ? 'bg-[#3C50E0] text-white shadow-sm' : 'text-slate-400'"
              >
                <Moon class="w-3.5 h-3.5" />
              </div>
            </div>
          </button>

          <!-- Notification Dropdown with Pulse Badge -->
          <div class="relative">
            <button
              @click="isNotificationOpen = !isNotificationOpen"
              class="relative w-10 h-10 rounded-full flex items-center justify-center border transition-colors cursor-pointer"
              :class="isDarkMode ? 'bg-[#24303F] border-[#2E3A47] text-slate-300 hover:text-white' : 'bg-slate-100 border-slate-200 text-slate-600 hover:text-slate-900'"
            >
              <Bell class="w-4 h-4" />
              <span
                v-if="pendingCount > 0"
                class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500 ring-2 ring-white animate-pulse"
              ></span>
            </button>

            <!-- Notification Popover -->
            <div
              v-if="isNotificationOpen"
              class="absolute right-0 mt-3 w-80 rounded-2xl border shadow-2xl p-4 z-50 text-xs"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
            >
              <div class="flex items-center justify-between pb-3 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
                <span class="font-bold">Thông Báo Mới</span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-brand-gold text-slate-950 font-bold">
                  {{ pendingCount }} đơn chờ
                </span>
              </div>
              <div class="py-3 space-y-2">
                <p v-if="pendingCount === 0" class="text-slate-400 text-center py-2">
                  Chưa có thông báo đơn mới nào
                </p>
                <router-link
                  v-else
                  to="/he-thong-admin/quotes"
                  @click="isNotificationOpen = false"
                  class="block p-2.5 rounded-xl transition-colors hover:bg-brand-gold/10 border border-brand-gold/20"
                >
                  <div class="font-bold text-brand-gold">Có {{ pendingCount }} đơn báo giá mới!</div>
                  <div class="text-[11px] text-slate-400 mt-0.5">Khách hàng đang đợi xưởng kiểm tra file và báo giá.</div>
                </router-link>
              </div>
              <router-link
                to="/he-thong-admin/quotes"
                @click="isNotificationOpen = false"
                class="block text-center text-brand-gold hover:underline font-semibold pt-2 text-[11px]"
              >
                Xem tất cả báo giá →
              </router-link>
            </div>
          </div>

          <!-- User Profile Dropdown (TailAdmin Styled) -->
          <div class="relative">
            <button
              @click="isUserMenuOpen = !isUserMenuOpen"
              class="flex items-center gap-3 p-1.5 rounded-xl transition-colors cursor-pointer group"
            >
              <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-brand-gold to-amber-500 text-slate-950 font-black text-sm flex items-center justify-center shadow-md shadow-brand-gold/20">
                AD
              </div>
              <div class="hidden sm:block text-left">
                <div class="text-xs font-bold leading-tight" :class="isDarkMode ? 'text-white' : 'text-slate-900'">
                  {{ user?.name || 'Quản Trị Viên' }}
                </div>
                <div class="text-[10px] text-slate-400 font-medium">
                  {{ user?.role?.name || 'Super Admin' }}
                </div>
              </div>
              <ChevronDown class="w-4 h-4 text-slate-400 group-hover:text-white transition-transform" :class="{ 'rotate-180': isUserMenuOpen }" />
            </button>

            <!-- User Menu Popover -->
            <div
              v-if="isUserMenuOpen"
              class="absolute right-0 mt-3 w-56 rounded-2xl border shadow-2xl p-2 z-50 text-xs space-y-1"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
            >
              <div class="px-3 py-2 border-b" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-100'">
                <div class="font-bold truncate">{{ user?.name || 'Quản Trị Viên' }}</div>
                <div class="text-[10px] text-slate-400 truncate">{{ user?.email || 'admin@innhatnguyen.vn' }}</div>
              </div>

              <router-link
                v-if="can('users.view')"
                to="/he-thong-admin/users"
                @click="isUserMenuOpen = false"
                class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition-colors"
                :class="isDarkMode ? 'hover:bg-[#24303F]' : 'hover:bg-slate-100'"
              >
                <UserCheck class="w-4 h-4 text-slate-400" />
                <span>Quản Lý Nhân Sự</span>
              </router-link>

              <router-link
                v-if="can('users.manage')"
                to="/he-thong-admin/roles"
                @click="isUserMenuOpen = false"
                class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition-colors"
                :class="isDarkMode ? 'hover:bg-[#24303F]' : 'hover:bg-slate-100'"
              >
                <ShieldCheck class="w-4 h-4 text-slate-400" />
                <span>Ma Trận Phân Quyền</span>
              </router-link>

              <router-link
                v-if="can('settings.manage')"
                to="/he-thong-admin/settings"
                @click="isUserMenuOpen = false"
                class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition-colors"
                :class="isDarkMode ? 'hover:bg-[#24303F]' : 'hover:bg-slate-100'"
              >
                <Settings class="w-4 h-4 text-slate-400" />
                <span>Cài Đặt Hệ Thống & SEO</span>
              </router-link>

              <a
                href="/"
                target="_blank"
                class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition-colors"
                :class="isDarkMode ? 'hover:bg-[#24303F]' : 'hover:bg-slate-100'"
              >
                <ExternalLink class="w-4 h-4 text-slate-400" />
                <span>Xem Trang Chủ Website</span>
              </a>

              <button
                @click="handleLogout"
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-red-400 hover:bg-red-500/10 transition-colors text-left cursor-pointer"
              >
                <LogOut class="w-4 h-4" />
                <span>Đăng Xuất Khỏi Hệ Thống</span>
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Body Container -->
      <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
  LayoutDashboard,
  ClipboardList,
  Package,
  Settings,
  ExternalLink,
  LogOut,
  ChevronLeft,
  ChevronRight,
  Menu,
  X,
  Search,
  Sun,
  Moon,
  Bell,
  ChevronDown,
  Tags,
  Users,
  UserCheck,
  ShieldCheck,
  Layers,
  Image as ImageIcon,
  Newspaper,
  ShoppingBag,
  Wallet,
  Building2,
  Receipt,
  TrendingUp,
  HeartHandshake,
  Calculator
} from 'lucide-vue-next'
import { api, getAdminUser, setAdminToken, setAdminUser, can } from '../../services/api'
import { useAdminTheme } from '../../composables/useAdminTheme'

const route = useRoute()
const router = useRouter()
const user = ref(getAdminUser())
const pendingCount = ref(0)

const {
  isDarkMode,
  isSidebarCollapsed,
  toggleTheme,
  toggleCollapse
} = useAdminTheme()

const isMobileMenuOpen = ref(false)
const isNotificationOpen = ref(false)
const isUserMenuOpen = ref(false)
const globalSearch = ref('')

const primaryNav = [
  { path: '/he-thong-admin/dashboard', label: 'Bảng Điều Khiển & KPI', icon: LayoutDashboard }
]

const erpNav = [
  { path: '/he-thong-admin/orders', label: '1. Đơn Hàng & Sản Xuất', icon: ShoppingBag, permission: 'orders.view' },
  { path: '/he-thong-admin/clients', label: '2. Khách Hàng & CRM Zalo', icon: Users, permission: 'clients.view' },
  { path: '/he-thong-admin/cashbook', label: '3. Sổ Quỹ Thu Chi', icon: Wallet, permission: 'cashbook.view' },
  { path: '/he-thong-admin/suppliers', label: '4. Xưởng In & Công Nợ', icon: Building2, permission: 'suppliers.view' },
  { path: '/he-thong-admin/invoices', label: '5. Quản Lý Hóa Đơn VAT', icon: Receipt, permission: 'cashbook.view' },
  { path: '/he-thong-admin/finance', label: '6. Tài Chính & Chia Lãi', icon: TrendingUp, permission: 'finance.dashboard' }
]

const dataNav = [
  { path: '/he-thong-admin/quotes', label: '1. Đơn Báo Giá', icon: ClipboardList, badge: true, permission: 'quotes.view' },
  { path: '/he-thong-admin/products', label: '2. Ấn Phẩm In Ấn', icon: Package, permission: 'products.manage' },
  { path: '/he-thong-admin/pricing', label: '3. Cấu Hình Bảng Giá', icon: Calculator, permission: 'pricing.manage' },
  { path: '/he-thong-admin/categories', label: '4. Danh Mục Nhóm In', icon: Tags, permission: 'categories.manage' },
  { path: '/he-thong-admin/banners', label: '5. Banner & Slider', icon: ImageIcon, permission: 'banners.manage' },
  { path: '/he-thong-admin/posts', label: '6. Bài Viết & Cẩm Nang', icon: Newspaper, permission: 'posts.manage' }
]

const systemNav = [
  { path: '/he-thong-admin/users', label: '1. Nhân Sự & Tài Khoản', icon: UserCheck, permission: 'users.view' },
  { path: '/he-thong-admin/roles', label: '2. Ma Trận Phân Quyền', icon: ShieldCheck, permission: 'users.manage' },
  { path: '/he-thong-admin/settings', label: '3. Cài Đặt Xưởng & SEO', icon: Settings, permission: 'settings.manage' }
]

const visiblePrimaryNav = computed(() => primaryNav.filter(item => !item.permission || can(item.permission)))
const visibleErpNav = computed(() => erpNav.filter(item => !item.permission || can(item.permission)))
const visibleDataNav = computed(() => dataNav.filter(item => !item.permission || can(item.permission)))
const visibleSystemNav = computed(() => systemNav.filter(item => !item.permission || can(item.permission)))

function isItemActive(path) {
  if (path === '/he-thong-admin/dashboard') {
    return route.path === '/he-thong-admin/dashboard'
  }
  return route.path.startsWith(path)
}

function handleGlobalSearch() {
  if (globalSearch.value.trim()) {
    router.push({ path: '/he-thong-admin/quotes', query: { search: globalSearch.value.trim() } })
    globalSearch.value = ''
  }
}

async function fetchStats() {
  try {
    const res = await api.getDashboardStats()
    if (res.data?.quotes?.pending !== undefined) {
      pendingCount.value = res.data.quotes.pending
    }
  } catch (err) {
    console.error('Failed to load pending quotes count', err)
  }
}

async function handleLogout() {
  try {
    await api.logout().catch(() => {})
  } finally {
    setAdminToken(null)
    setAdminUser(null)
    router.push('/he-thong-admin/login')
  }
}

onMounted(async () => {
  fetchStats()
  try {
    const res = await api.getMe()
    if (res?.user) {
      setAdminUser(res.user)
      user.value = res.user
    }
  } catch (err) {
    // Session expired or unauthenticated
  }
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(148, 163, 184, 0.2);
  border-radius: 4px;
}
</style>

