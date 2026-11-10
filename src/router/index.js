import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import { getAdminToken, can } from '../services/api'
import { usePageLoader } from '../composables/usePageLoader'

const { startNavLoading, finishNavLoading } = usePageLoader()

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
      meta: { title: 'Trang chủ - In Ấn Nhật Nguyên' }
    },
    {
      path: '/san-pham',
      name: 'products',
      component: () => import('../views/ProductsView.vue'),
      meta: { title: 'Tất cả sản phẩm & Dịch vụ in' }
    },
    {
      path: '/danh-muc/:categorySlug',
      name: 'category-products',
      component: () => import('../views/ProductsView.vue'),
      meta: { title: 'Danh mục ấn phẩm in' }
    },
    {
      path: '/san-pham/:slug',
      name: 'product-detail',
      component: () => import('../views/ProductDetailView.vue'),
      meta: { title: 'Chi tiết ấn phẩm & Tính giá' }
    },
    {
      path: '/yeu-cau-bao-gia',
      name: 'quote-request',
      component: () => import('../views/QuoteRequestView.vue'),
      meta: { title: 'Gửi yêu cầu báo giá nhanh' }
    },
    {
      path: '/gioi-thieu',
      name: 'about',
      component: () => import('../views/AboutView.vue'),
      meta: { title: 'Giới thiệu xưởng in Nhật Nguyên - Nhanh, Đẹp, Giá Tốt' }
    },
    {
      path: '/tin-tuc',
      name: 'news',
      component: () => import('../views/NewsView.vue'),
      meta: { title: 'Cẩm nang & Kiến thức ngành in' }
    },
    {
      path: '/tin-tuc/:slug',
      name: 'post-detail',
      component: () => import('../views/PostDetailView.vue'),
      meta: { title: 'Bài viết cẩm nang in ấn' }
    },
    {
      path: '/lien-he',
      name: 'contact',
      component: () => import('../views/ContactView.vue'),
      meta: { title: 'Liên hệ xưởng in Nhật Nguyên' }
    },

    // Admin Routes
    {
      path: '/admin/login',
      name: 'admin-login',
      component: () => import('../views/admin/AdminLoginView.vue'),
      meta: { title: 'Đăng Nhập Quản Trị' }
    },
    {
      path: '/admin',
      component: () => import('../views/admin/AdminLayout.vue'),
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          redirect: '/admin/dashboard'
        },
        {
          path: 'manage',
          redirect: '/admin/dashboard'
        },
        {
          path: 'dashboard',
          name: 'admin-dashboard',
          component: () => import('../views/admin/AdminDashboardView.vue'),
          meta: { title: 'Bảng Điều Khiển Biểu Đồ & KPI', requiresAuth: true }
        },
        // ERP Modules
        {
          path: 'orders',
          name: 'admin-erp-orders',
          component: () => import('../views/admin/erp/AdminErpOrdersView.vue'),
          meta: { title: 'Quản Lý Đơn Hàng & Sản Xuất', requiresAuth: true, permission: 'orders.view' }
        },
        {
          path: 'orders/create',
          name: 'admin-erp-orders-create',
          component: () => import('../views/admin/erp/AdminErpOrderFormView.vue'),
          meta: { title: 'Tạo Đơn Hàng In Ấn Mới', requiresAuth: true, permission: 'orders.create' }
        },
        {
          path: 'orders/:id/edit',
          name: 'admin-erp-orders-edit',
          component: () => import('../views/admin/erp/AdminErpOrderFormView.vue'),
          meta: { title: 'Chỉnh Sửa Đơn Hàng', requiresAuth: true, permission: 'orders.edit' }
        },
        {
          path: 'clients',
          name: 'admin-erp-clients',
          component: () => import('../views/admin/erp/AdminErpClientsView.vue'),
          meta: { title: 'Quản Lý Khách Hàng & CRM Zalo', requiresAuth: true, permission: 'clients.view' }
        },
        {
          path: 'cashbook',
          name: 'admin-erp-cashbook',
          component: () => import('../views/admin/erp/AdminErpCashbookView.vue'),
          meta: { title: 'Sổ Quỹ Thu Chi Nội Bộ', requiresAuth: true, permission: 'cashbook.view' }
        },
        {
          path: 'suppliers',
          name: 'admin-erp-suppliers',
          component: () => import('../views/admin/erp/AdminErpSuppliersView.vue'),
          meta: { title: 'Xưởng In & Công Nợ Nhà Cung Cấp', requiresAuth: true, permission: 'suppliers.view' }
        },
        {
          path: 'invoices',
          name: 'admin-erp-invoices',
          component: () => import('../views/admin/erp/AdminErpInvoicesView.vue'),
          meta: { title: 'Quản Lý Hóa Đơn VAT', requiresAuth: true, permission: 'cashbook.view' }
        },
        {
          path: 'finance',
          name: 'admin-erp-finance',
          component: () => import('../views/admin/erp/AdminErpFinanceView.vue'),
          meta: { title: 'Tài Chính & Chia Lãi 50/50', requiresAuth: true, permission: 'finance.dashboard' }
        },
        {
          path: 'quotes',
          name: 'admin-quotes',
          component: () => import('../views/admin/AdminQuotesView.vue'),
          meta: { title: 'Quản Lý Yêu Cầu Báo Giá', requiresAuth: true, permission: 'quotes.view' }
        },
        {
          path: 'products',
          name: 'admin-products',
          component: () => import('../views/admin/AdminProductsView.vue'),
          meta: { title: 'Quản Lý Ấn Phẩm In', requiresAuth: true, permission: 'products.manage' }
        },
        {
          path: 'pricing',
          name: 'admin-pricing',
          component: () => import('../views/admin/AdminPricingView.vue'),
          meta: { title: 'Cấu Hình Bảng Giá Từng Sản Phẩm', requiresAuth: true, permission: 'pricing.manage' }
        },
        {
          path: 'products/create',
          name: 'admin-products-create',
          component: () => import('../views/admin/AdminProductFormView.vue'),
          meta: { title: 'Thêm Mới Ấn Phẩm In', requiresAuth: true, permission: 'products.manage' }
        },
        {
          path: 'products/:id/edit',
          name: 'admin-products-edit',
          component: () => import('../views/admin/AdminProductFormView.vue'),
          meta: { title: 'Chỉnh Sửa Ấn Phẩm In', requiresAuth: true, permission: 'products.manage' }
        },
        {
          path: 'categories',
          name: 'admin-categories',
          component: () => import('../views/admin/AdminCategoriesView.vue'),
          meta: { title: 'Quản Lý Danh Mục Nhóm In', requiresAuth: true, permission: 'categories.manage' }
        },
        {
          path: 'categories/create',
          name: 'admin-categories-create',
          component: () => import('../views/admin/AdminCategoryFormView.vue'),
          meta: { title: 'Thêm Mới Danh Mục Nhóm In', requiresAuth: true, permission: 'categories.manage' }
        },
        {
          path: 'categories/:id/edit',
          name: 'admin-categories-edit',
          component: () => import('../views/admin/AdminCategoryFormView.vue'),
          meta: { title: 'Chỉnh Sửa Danh Mục Nhóm In', requiresAuth: true, permission: 'categories.manage' }
        },
        {
          path: 'banners',
          name: 'admin-banners',
          component: () => import('../views/admin/AdminBannersView.vue'),
          meta: { title: 'Quản Lý Banner & Slider', requiresAuth: true, permission: 'banners.manage' }
        },
        {
          path: 'banners/create',
          name: 'admin-banners-create',
          component: () => import('../views/admin/AdminBannerFormView.vue'),
          meta: { title: 'Thêm Mới Banner & Thẻ Quảng Cáo', requiresAuth: true, permission: 'banners.manage' }
        },
        {
          path: 'banners/:id/edit',
          name: 'admin-banners-edit',
          component: () => import('../views/admin/AdminBannerFormView.vue'),
          meta: { title: 'Chỉnh Sửa Banner & Thẻ Quảng Cáo', requiresAuth: true, permission: 'banners.manage' }
        },
        {
          path: 'posts',
          name: 'admin-posts',
          component: () => import('../views/admin/AdminPostsView.vue'),
          meta: { title: 'Quản Lý Bài Viết & Cẩm Nang', requiresAuth: true, permission: 'posts.manage' }
        },
        {
          path: 'posts/create',
          name: 'admin-posts-create',
          component: () => import('../views/admin/AdminPostFormView.vue'),
          meta: { title: 'Đăng Bài Viết Mới', requiresAuth: true, permission: 'posts.manage' }
        },
        {
          path: 'posts/:id/edit',
          name: 'admin-posts-edit',
          component: () => import('../views/admin/AdminPostFormView.vue'),
          meta: { title: 'Chỉnh Sửa Bài Viết', requiresAuth: true, permission: 'posts.manage' }
        },
        {
          path: 'settings',
          name: 'admin-settings',
          component: () => import('../views/admin/AdminSettingsView.vue'),
          meta: { title: 'Cài Đặt Hệ Thống & SEO', requiresAuth: true, permission: 'settings.manage' }
        },
        {
          path: 'users',
          name: 'admin-users',
          component: () => import('../views/admin/AdminUsersView.vue'),
          meta: { title: 'Quản Lý Nhân Sự & Tài Khoản', requiresAuth: true, permission: 'users.view' }
        },
        {
          path: 'roles',
          name: 'admin-roles',
          component: () => import('../views/admin/AdminRolesView.vue'),
          meta: { title: 'Ma Trận Phân Quyền & Vai Trò', requiresAuth: true, permission: 'users.manage' }
        }
      ]
    },

    // Fallback 404
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('../views/NotFoundView.vue'),
      meta: { title: '404 - Trang không tồn tại' }
    }
  ],
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

router.beforeEach((to, from, next) => {
  // Start top progress bar if navigating between different routes
  if (to.path !== from.path) {
    startNavLoading()
  }

  // Update document title
  document.title = to.meta.title ? `${to.meta.title} | In Ấn Nhật Nguyên` : 'In Ấn Nhật Nguyên'

  // Security: Prevent search engines and crawlers from indexing admin pages
  let robotsMeta = document.querySelector('meta[name="robots"]')
  if (!robotsMeta) {
    robotsMeta = document.createElement('meta')
    robotsMeta.name = 'robots'
    document.head.appendChild(robotsMeta)
  }
  if (to.path.startsWith('/admin')) {
    robotsMeta.content = 'noindex, nofollow, noarchive, nosnippet'
  } else {
    robotsMeta.content = 'index, follow'
  }

  // Admin authentication guard
  const token = getAdminToken()
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!token) {
      finishNavLoading()
      return next({ path: '/admin/login', query: { redirect: to.fullPath } })
    }

    // Permission authorization check
    const matchedRecord = to.matched.slice().reverse().find(record => record.meta.permission)
    if (matchedRecord && matchedRecord.meta.permission) {
      if (!can(matchedRecord.meta.permission)) {
        finishNavLoading()
        alert(`Bạn không có quyền truy cập trang này (${matchedRecord.meta.permission}).`)
        return next('/admin/dashboard')
      }
    }
  } else if (to.path === '/admin/login' && token) {
    finishNavLoading()
    return next('/admin/dashboard')
  }

  next()
})

router.afterEach(() => {
  finishNavLoading()
})

export default router
