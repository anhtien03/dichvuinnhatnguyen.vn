import { defineStore } from 'pinia'
import { ref } from 'vue'
import { mockApi } from '../services/mockApi'

export const useQuoteStore = defineStore('quote', () => {
  const currentQuote = ref({
    productId: null,
    productName: '',
    specs: {},
    customerName: '',
    phone: '',
    email: '',
    notes: '',
    files: [],
    estimatedPrice: 0
  })

  const loading = ref(false)
  const lastSubmittedQuote = ref(null)

  function setProductQuote(product, specs, estimatedPrice) {
    currentQuote.value.productId = product.id
    currentQuote.value.productName = product.name
    currentQuote.value.specs = specs
    currentQuote.value.estimatedPrice = estimatedPrice
  }

  async function submitQuote(formData) {
    loading.value = true
    try {
      const payload = {
        ...currentQuote.value,
        ...formData
      }
      const res = await mockApi.submitQuote(payload)
      lastSubmittedQuote.value = {
        ...payload,
        id: res.quoteId
      }
      return res
    } finally {
      loading.value = false
    }
  }

  function resetQuote() {
    currentQuote.value = {
      productId: null,
      productName: '',
      specs: {},
      customerName: '',
      phone: '',
      email: '',
      notes: '',
      files: [],
      estimatedPrice: 0
    }
  }

  return {
    currentQuote,
    loading,
    lastSubmittedQuote,
    setProductQuote,
    submitQuote,
    resetQuote
  }
})

