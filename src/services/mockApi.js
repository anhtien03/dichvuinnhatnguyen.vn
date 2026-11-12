import { products } from '../data/products'
import { categories } from '../data/categories'

export const mockApi = {
  // Get all products or by category
  async getProducts(categoryId = null) {
    await new Promise(r => setTimeout(r, 80))
    if (!categoryId || categoryId === 'all') return products
    return products.filter(p => p.categoryId === categoryId)
  },

  // Get product detail by slug
  async getProductBySlug(slug) {
    await new Promise(r => setTimeout(r, 60))
    const product = products.find(p => p.slug === slug)
    if (!product) throw new Error('Không tìm thấy sản phẩm')
    return product
  },

  // Get categories
  async getCategories() {
    await new Promise(r => setTimeout(r, 50))
    return categories
  },

  // Search products by name, tag, or desc
  async search(query) {
    if (!query || !query.trim()) return []
    const q = query.toLowerCase().trim()
    return products.filter(p =>
      p.name.toLowerCase().includes(q) ||
      p.categoryName.toLowerCase().includes(q) ||
      p.description.toLowerCase().includes(q) ||
      (p.tag && p.tag.toLowerCase().includes(q))
    )
  },

  // Calculate estimated price based on configuration
  calculateEstimate({ basePrice, selectedSize, selectedMaterial, selectedFinishing, selectedQuantity }) {
    let extra = 0
    if (selectedSize && selectedSize.extra) extra += Number(selectedSize.extra) || 0
    if (selectedMaterial && selectedMaterial.extra) extra += Number(selectedMaterial.extra) || 0
    if (selectedFinishing && selectedFinishing.extra) extra += Number(selectedFinishing.extra) || 0

    const qty = (selectedQuantity && selectedQuantity.count) ? Number(selectedQuantity.count) : 1
    const baseUnitPrice = (selectedQuantity && selectedQuantity.pricePerUnit !== undefined && selectedQuantity.pricePerUnit !== null)
      ? Number(selectedQuantity.pricePerUnit)
      : (Number(basePrice) || 0)

    // Phụ phí quy cách (kích thước, chất liệu, gia công) được cộng vào đơn giá trên mỗi đơn vị sản phẩm (cái/hộp/tờ) rồi nhân với số lượng
    const unitPrice = Math.max(0, baseUnitPrice + extra)
    const total = Math.max(0, unitPrice * qty)
    return {
      qty,
      baseUnitPrice,
      unitPrice,
      extra,
      total,
      formattedTotal: new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(isNaN(total) ? 0 : total)
    }
  },

  // Submit quote request
  async submitQuote(quoteData) {
    await new Promise(r => setTimeout(r, 500))
    // Save to local storage for demo persistence
    const existing = JSON.parse(localStorage.getItem('innhatnguyen_quotes') || '[]')
    const newQuote = {
      id: 'NN-' + Date.now().toString().slice(-6),
      createdAt: new Date().toISOString(),
      status: 'pending',
      ...quoteData
    }
    existing.unshift(newQuote)
    localStorage.setItem('innhatnguyen_quotes', JSON.stringify(existing))
    return {
      success: true,
      quoteId: newQuote.id,
      message: 'Yêu cầu báo giá của bạn đã được gửi thành công! Kỹ thuật viên sẽ liên hệ trong 15-30 phút.'
    }
  }
}

