// Central API client for In Ấn Nhật Nguyên

const API_BASE = '/api/v1'

export function getAdminToken() {
  return localStorage.getItem('admin_token') || ''
}

export function setAdminToken(token) {
  if (token) {
    localStorage.setItem('admin_token', token)
  } else {
    localStorage.removeItem('admin_token')
  }
}

export function getAdminUser() {
  const user = localStorage.getItem('admin_user')
  return user ? JSON.parse(user) : null
}

export function can(permissionSlug) {
  const user = getAdminUser()
  if (!user) return false
  if (user.is_super_admin) return true
  if (Array.isArray(user.permissions)) {
    if (permissionSlug.includes('|')) {
      return permissionSlug.split('|').some(p => user.permissions.includes(p.trim()))
    }
    return user.permissions.includes(permissionSlug)
  }
  return false
}

export function setAdminUser(user) {
  if (user) {
    localStorage.setItem('admin_user', JSON.stringify(user))
  } else {
    localStorage.removeItem('admin_user')
  }
}

export async function apiRequest(endpoint, options = {}) {
  const url = `${API_BASE}${endpoint}`
  const headers = {
    'Accept': 'application/json',
    ...(options.headers || {})
  }

  // Include Bearer token if available
  const token = getAdminToken()
  if (token) {
    headers['Authorization'] = `Bearer ${token}`
  }

  // If body is not FormData, ensure Content-Type is json
  if (options.body && !(options.body instanceof FormData)) {
    headers['Content-Type'] = 'application/json'
    options.body = JSON.stringify(options.body)
  }

  const response = await fetch(url, {
    ...options,
    headers
  })

  // Handle Unauthorized
  if (response.status === 401 && !endpoint.includes('/auth/login')) {
    setAdminToken(null)
    setAdminUser(null)
    if (window.location.pathname.startsWith('/he-thong-admin') && !window.location.pathname.includes('/login')) {
      window.location.href = '/he-thong-admin/login'
    }
  }

  const data = await response.json().catch(() => ({}))

  if (!response.ok) {
    const errorMsg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Có lỗi xảy ra')
    const err = new Error(errorMsg)
    err.status = response.status
    err.data = data
    throw err
  }

  // Clear client cache after any write/mutation request
  const method = (options.method || 'GET').toUpperCase()
  if (method !== 'GET') {
    publicCache.clear()
  }

  return data
}

// Client In-Memory Cache (TTL 90s for public homepage catalog data)
const publicCache = new Map()
const CACHE_TTL = 90 * 1000 // 90 giây

function cachedPublicRequest(endpoint, params = {}) {
  const query = new URLSearchParams(params).toString()
  const fullPath = `${endpoint}${query ? '?' + query : ''}`
  const now = Date.now()
  const hit = publicCache.get(fullPath)
  if (hit && (now - hit.time < CACHE_TTL)) {
    return Promise.resolve(hit.data)
  }
  return apiRequest(fullPath).then(res => {
    publicCache.set(fullPath, { time: Date.now(), data: res })
    return res
  })
}

// API Endpoints
export const api = {
  // Public (Áp dụng cache tức thì khi chuyển trang qua lại, F5 sẽ xóa cache)
  getCategories: () => cachedPublicRequest('/categories'),
  getCategoryBySlug: (slug) => apiRequest(`/categories/${slug}`),
  getProducts: (params = {}) => cachedPublicRequest('/products', params),
  getProductBySlug: (slug) => apiRequest(`/products/${slug}`),
  getBanners: (params = {}) => cachedPublicRequest('/banners', params),
  getPosts: (params = {}) => cachedPublicRequest('/posts', params),
  getPostBySlug: (slug) => apiRequest(`/posts/${slug}`),
  submitQuote: (formData) => apiRequest('/quotes', { method: 'POST', body: formData }),
  getSettings: () => apiRequest('/settings'),

  // Auth
  login: (credentials) => apiRequest('/auth/login', { method: 'POST', body: credentials }),
  logout: () => apiRequest('/auth/logout', { method: 'POST' }),
  getMe: () => apiRequest('/auth/me'),

  // Admin Dashboard
  getDashboardStats: () => apiRequest('/admin/dashboard-stats'),

  // Admin Quotes
  getAdminQuotes: (params = {}) => {
    const query = new URLSearchParams(params).toString()
    return apiRequest(`/admin/quotes${query ? '?' + query : ''}`)
  },
  getAdminQuoteDetail: (id) => apiRequest(`/admin/quotes/${id}`),
  updateAdminQuote: (id, data) => apiRequest(`/admin/quotes/${id}`, { method: 'PUT', body: data }),
  deleteAdminQuote: (id) => apiRequest(`/admin/quotes/${id}`, { method: 'DELETE' }),

  // Admin Products
  createAdminProduct: (data) => apiRequest('/admin/products', { method: 'POST', body: data }),
  updateAdminProduct: (id, data) => apiRequest(`/admin/products/${id}`, { method: 'PUT', body: data }),
  deleteAdminProduct: (id) => apiRequest(`/admin/products/${id}`, { method: 'DELETE' }),

  // Admin Categories
  createAdminCategory: (data) => apiRequest('/admin/categories', { method: 'POST', body: data }),
  updateAdminCategory: (id, data) => apiRequest(`/admin/categories/${id}`, { method: 'PUT', body: data }),
  deleteAdminCategory: (id) => apiRequest(`/admin/categories/${id}`, { method: 'DELETE' }),

  // Admin Banners
  getAdminBanners: (params = {}) => {
    const query = new URLSearchParams(params).toString()
    return apiRequest(`/admin/banners${query ? '?' + query : ''}`)
  },
  getAdminBanner: (id) => apiRequest(`/admin/banners/${id}`),
  createAdminBanner: (data) => apiRequest('/admin/banners', { method: 'POST', body: data }),
  updateAdminBanner: (id, data) => apiRequest(`/admin/banners/${id}`, { method: 'PUT', body: data }),
  deleteAdminBanner: (id) => apiRequest(`/admin/banners/${id}`, { method: 'DELETE' }),
  toggleAdminBanner: (id) => apiRequest(`/admin/banners/${id}/toggle-active`, { method: 'POST' }),
  reorderAdminBanners: (orders) => apiRequest('/admin/banners/reorder', { method: 'POST', body: { orders } }),

  // Admin Posts & Articles
  getAdminPosts: (params = {}) => {
    const query = new URLSearchParams(params).toString()
    return apiRequest(`/admin/posts${query ? '?' + query : ''}`)
  },
  getAdminPost: (id) => apiRequest(`/admin/posts/${id}`),
  createAdminPost: (data) => apiRequest('/admin/posts', { method: 'POST', body: data }),
  updateAdminPost: (id, data) => apiRequest(`/admin/posts/${id}`, { method: 'PUT', body: data }),
  deleteAdminPost: (id) => apiRequest(`/admin/posts/${id}`, { method: 'DELETE' }),
  toggleAdminPost: (id) => apiRequest(`/admin/posts/${id}/toggle-publish`, { method: 'POST' }),

  // Admin Settings
  updateAdminSettings: (data) => apiRequest('/admin/settings', { method: 'POST', body: data }),

  // Admin Media Library
  getAdminMedia: (params = {}) => {
    const query = new URLSearchParams(params).toString()
    return apiRequest(`/admin/media${query ? '?' + query : ''}`)
  },
  uploadAdminMedia: (formData) => apiRequest('/admin/media/upload', { method: 'POST', body: formData }),
  deleteAdminMedia: (path) => apiRequest('/admin/media', { method: 'DELETE', body: { path } }),
  createAdminMediaFolder: (data) => apiRequest('/admin/media/folders', { method: 'POST', body: data }),
  deleteAdminMediaFolder: (folder) => apiRequest('/admin/media/folders', { method: 'DELETE', body: { folder } }),

  // ERP - Orders
  getErpOrders: (params = {}) => {
    const query = new URLSearchParams(params).toString()
    return apiRequest(`/admin/erp/orders${query ? '?' + query : ''}`)
  },
  getErpNextCode: (params = {}) => {
    const query = new URLSearchParams(params).toString()
    return apiRequest(`/admin/erp/orders/next-code${query ? '?' + query : ''}`)
  },
  getErpOrder: (id) => apiRequest(`/admin/erp/orders/${id}`),
  createErpOrder: (data) => apiRequest('/admin/erp/orders', { method: 'POST', body: data }),
  updateErpOrder: (id, data) => apiRequest(`/admin/erp/orders/${id}`, { method: 'PUT', body: data }),
  deleteErpOrder: (id) => apiRequest(`/admin/erp/orders/${id}`, { method: 'DELETE' }),
  updateErpOrderStage: (id, stage) => apiRequest(`/admin/erp/orders/${id}/stage`, { method: 'POST', body: { stage } }),
  addErpOrderPayment: (id, data) => apiRequest(`/admin/erp/orders/${id}/payments`, { method: 'POST', body: data }),
  requestErpOrderDelete: (id, reason) => apiRequest(`/admin/erp/orders/${id}/request-delete`, { method: 'POST', body: { reason } }),
  getErpOrderVietQR: (id, bank = 'acb') => apiRequest(`/admin/erp/orders/${id}/vietqr?bank=${bank}`),

  // ERP - Customers & CRM
  getErpCustomers: (params = {}) => {
    const query = new URLSearchParams(params).toString()
    return apiRequest(`/admin/erp/customers${query ? '?' + query : ''}`)
  },
  searchErpCustomerPhone: (phone) => apiRequest(`/admin/erp/customers/search?phone=${encodeURIComponent(phone)}`),
  getErpDailyCare: (days = 15) => apiRequest(`/admin/erp/customers/daily-care?days=${days}`),
  createErpCustomer: (data) => apiRequest('/admin/erp/customers', { method: 'POST', body: data }),
  getErpCustomer: (id) => apiRequest(`/admin/erp/customers/${id}`),
  getErpCustomerCommissionStatement: (params = {}) => {
    const query = new URLSearchParams(params).toString()
    return apiRequest(`/admin/erp/customers/commission-statement${query ? '?' + query : ''}`)
  },
  updateErpCustomer: (id, data) => apiRequest(`/admin/erp/customers/${id}`, { method: 'PUT', body: data }),
  deleteErpCustomer: (id) => apiRequest(`/admin/erp/customers/${id}`, { method: 'DELETE' }),
  importErpCustomers: (customers) => apiRequest('/admin/erp/customers/import', { method: 'POST', body: { customers } }),

  // ERP - Suppliers & Debt
  getErpSuppliers: (params = {}) => {
    const query = new URLSearchParams(params).toString()
    return apiRequest(`/admin/erp/suppliers${query ? '?' + query : ''}`)
  },
  createErpSupplier: (data) => apiRequest('/admin/erp/suppliers', { method: 'POST', body: data }),
  getErpSupplier: (id) => apiRequest(`/admin/erp/suppliers/${id}`),
  updateErpSupplier: (id, data) => apiRequest(`/admin/erp/suppliers/${id}`, { method: 'PUT', body: data }),
  deleteErpSupplier: (id) => apiRequest(`/admin/erp/suppliers/${id}`, { method: 'DELETE' }),
  payErpSupplier: (id, data) => apiRequest(`/admin/erp/suppliers/${id}/payments`, { method: 'POST', body: data }),

  // ERP - Cashbook
  getErpCashbook: (params = {}) => {
    const query = new URLSearchParams(params).toString()
    return apiRequest(`/admin/erp/cashbook${query ? '?' + query : ''}`)
  },
  createErpCashbook: (data) => apiRequest('/admin/erp/cashbook', { method: 'POST', body: data }),
  getErpCashbookStats: (params = {}) => {
    const query = new URLSearchParams(params).toString()
    return apiRequest(`/admin/erp/cashbook/statistics${query ? '?' + query : ''}`)
  },
  updateErpCashbook: (id, data) => apiRequest(`/admin/erp/cashbook/${id}`, { method: 'PUT', body: data }),
  deleteErpCashbook: (id) => apiRequest(`/admin/erp/cashbook/${id}`, { method: 'DELETE' }),

  // ERP - Finance & Profit
  getErpDashboard: () => apiRequest('/admin/erp/finance/dashboard'),
  getErpProfitSharing: (params = {}) => {
    const query = new URLSearchParams(params).toString()
    return apiRequest(`/admin/erp/finance/profit-sharing${query ? '?' + query : ''}`)
  },

  // Google Indexing API
  getGoogleIndexingConfig: () => apiRequest('/admin/google-indexing/config'),
  saveGoogleIndexingConfig: (data) => apiRequest('/admin/google-indexing/config', { method: 'POST', body: data }),
  testGoogleIndexingConnection: () => apiRequest('/admin/google-indexing/test', { method: 'POST' }),
  publishGoogleIndexingUrl: (url, type = 'URL_UPDATED') => apiRequest('/admin/google-indexing/publish', { method: 'POST', body: { url, type } }),
  getGoogleIndexingMetadata: (url) => apiRequest(`/admin/google-indexing/metadata?url=${encodeURIComponent(url)}`),

  // Users & Staff
  getAdminUsers: (params = {}) => {
    const query = new URLSearchParams(params).toString()
    return apiRequest(`/admin/users${query ? '?' + query : ''}`)
  },
  createAdminUser: (data) => apiRequest('/admin/users', { method: 'POST', body: data }),
  getAdminUserDetail: (id) => apiRequest(`/admin/users/${id}`),
  updateAdminUser: (id, data) => apiRequest(`/admin/users/${id}`, { method: 'PUT', body: data }),
  deleteAdminUser: (id) => apiRequest(`/admin/users/${id}`, { method: 'DELETE' }),
  toggleAdminUserActive: (id) => apiRequest(`/admin/users/${id}/toggle-active`, { method: 'POST' }),

  // Roles & Permissions
  getAdminRoles: () => apiRequest('/admin/roles'),
  createAdminRole: (data) => apiRequest('/admin/roles', { method: 'POST', body: data }),
  updateAdminRole: (id, data) => apiRequest(`/admin/roles/${id}`, { method: 'PUT', body: data }),
  deleteAdminRole: (id) => apiRequest(`/admin/roles/${id}`, { method: 'DELETE' }),
  getAdminPermissions: () => apiRequest('/admin/permissions'),
  updateRolePermissions: (roleId, permissionIds) => apiRequest(`/admin/roles/${roleId}/permissions`, { method: 'POST', body: { permission_ids: permissionIds } })
}


