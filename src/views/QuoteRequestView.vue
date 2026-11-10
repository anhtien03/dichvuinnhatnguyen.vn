<template>
  <div class="py-10 lg:py-16 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Breadcrumb & Title -->
      <div class="text-center max-w-2xl mx-auto mb-10">
        <span class="text-xs font-bold text-brand-gold uppercase tracking-wider block mb-1">
          BÁO GIÁ NHANH TRỰC TIẾP TỪ XƯỞNG
        </span>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">
          Yêu Cầu Báo Giá & Tư Vấn Đặt In
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-2">
          Điền thông tin và yêu cầu in ấn của bạn. Xưởng in Nhật Nguyên cam kết phản hồi bảng giá chi tiết trong vòng <strong>15 - 30 phút</strong>.
        </p>
      </div>

      <!-- Success Card (When submitted) -->
      <div
        v-if="isSubmitted"
        class="bg-white rounded-3xl p-8 sm:p-12 border border-emerald-200 shadow-xl text-center space-y-6"
      >
        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto">
          <CheckCircle2 class="w-10 h-10" />
        </div>

        <div>
          <h2 class="text-2xl font-black text-slate-900">Gửi Yêu Cầu Báo Giá Thành Công!</h2>
          <p class="text-sm text-slate-600 mt-2 max-w-md mx-auto">
            Mã yêu cầu của bạn là: <strong class="text-brand-blue font-mono text-base">{{ submittedQuoteId }}</strong>.
            Kỹ thuật viên xưởng in đang kiểm tra quy cách và sẽ gọi điện/nhắn tin Zalo báo giá ngay cho bạn.
          </p>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 max-w-md mx-auto text-xs text-left space-y-1.5">
          <div><strong>Khách hàng:</strong> {{ form.customerName }}</div>
          <div><strong>Số điện thoại:</strong> {{ form.phone }}</div>
          <div v-if="form.productName"><strong>Ấn phẩm:</strong> {{ form.productName }}</div>
          <div v-if="form.quantity"><strong>Số lượng:</strong> {{ form.quantity }}</div>
          <div v-if="form.files.length > 0"><strong>Tệp đính kèm:</strong> {{ form.files.length }} tệp</div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-4">
          <router-link
            to="/"
            class="px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs"
          >
            Quay về trang chủ
          </router-link>
          <a
            :href="siteSettings.zalo"
            target="_blank"
            rel="noopener"
            class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md flex items-center gap-2"
          >
            <MessageSquare class="w-4 h-4" />
            <span>Chat Zalo nhắc xưởng xử lý gấp</span>
          </a>
        </div>
      </div>

      <!-- Main Form -->
      <form
        v-else
        @submit.prevent="handleSubmit"
        class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200/90 shadow-xl space-y-8"
      >
        <!-- Section 1: Customer Contact -->
        <div>
          <h3 class="text-sm font-bold text-brand-blue uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3 mb-4">
            <User class="w-4 h-4 text-brand-gold" />
            <span>1. Thông Tin Người Liên Hệ</span>
          </h3>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                Họ và tên quý khách <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.customerName"
                type="text"
                required
                placeholder="Ví dụ: Nguyễn Văn A"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                Số điện thoại / Zalo <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.phone"
                type="tel"
                required
                placeholder="Ví dụ: 0912 345 678"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                Email nhận báo giá (tùy chọn)
              </label>
              <input
                v-model="form.email"
                type="email"
                placeholder="email@congty.com"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                Tên công ty / Cửa hàng (nếu có)
              </label>
              <input
                v-model="form.company"
                type="text"
                placeholder="Tên doanh nghiệp để xuất hóa đơn VAT"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue"
              />
            </div>

            <!-- Anti-bot Honeypot field (hidden from real users) -->
            <div style="opacity: 0; position: absolute; top: 0; left: -9999px; height: 0; width: 0; z-index: -1;">
              <input
                v-model="form.company_fax"
                type="text"
                name="company_fax"
                tabindex="-1"
                autocomplete="off"
              />
            </div>
          </div>
        </div>

        <!-- Section 2: Printing Requirements -->
        <div>
          <h3 class="text-sm font-bold text-brand-blue uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3 mb-4">
            <Package class="w-4 h-4 text-brand-gold" />
            <span>2. Yêu Cầu & Quy Cách In Ấn</span>
          </h3>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                Sản phẩm / Dịch vụ in cần báo giá
              </label>
              <select
                v-model="form.productName"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 bg-white focus:outline-none focus:border-brand-blue"
              >
                <option value="">-- Chọn ấn phẩm in hoặc nhập mục khác --</option>
                <option v-for="p in products" :key="p.id" :value="p.name">
                  {{ p.name }} ({{ p.categoryName }})
                </option>
                <option value="Sản phẩm đặc thù khác">Sản phẩm đặc thù khác</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                Số lượng dự kiến
              </label>
              <input
                v-model="form.quantity"
                type="text"
                placeholder="Ví dụ: 5 hộp, 1000 tờ, 500 con tem..."
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 focus:outline-none focus:border-brand-blue"
              />
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                Kích thước, chất liệu hoặc gia công mong muốn (nếu biết)
              </label>
              <textarea
                v-model="form.specsDescription"
                rows="2"
                placeholder="Ví dụ: In giấy C300 cán màng mờ, kích thước 9x5.4cm, bo tròn 4 góc, cần giao trước thứ 6..."
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 focus:outline-none focus:border-brand-blue"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Section 3: File Upload -->
        <div>
          <h3 class="text-sm font-bold text-brand-blue uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3 mb-4">
            <FileUp class="w-4 h-4 text-brand-gold" />
            <span>3. Tải Lên File Thiết Kế / Ảnh Mẫu (Nếu Có)</span>
          </h3>

          <FileUploadBox v-model="form.files" />
          <p class="text-[11px] text-slate-400 mt-2">
            * Nếu bạn chưa có file thiết kế, xưởng in Nhật Nguyên có đội ngũ thiết kế đồ họa hỗ trợ thiết kế hoặc chỉnh sửa file miễn phí theo yêu cầu.
          </p>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
          <span class="text-xs text-slate-500">
            Cam kết bảo mật 100% file thiết kế và thông tin cá nhân.
          </span>

          <button
            type="submit"
            :disabled="isSubmitting"
            class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-brand-gold hover:bg-brand-gold-hover text-slate-950 font-black text-sm shadow-lg shadow-brand-gold/30 transition-all transform hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-60 flex items-center justify-center gap-2"
          >
            <Send v-if="!isSubmitting" class="w-4 h-4" />
            <span v-if="!isSubmitting">Gửi Yêu Cầu Báo Giá Xưởng</span>
            <span v-else class="flex items-center gap-2">
              <span class="w-4 h-4 border-2 border-slate-900 border-t-transparent rounded-full animate-spin"></span>
              Đang gửi dữ liệu...
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { User, Package, FileUp, Send, CheckCircle2, MessageSquare } from 'lucide-vue-next'
import { products } from '../data/products'
import { siteSettings } from '../data/siteSettings'
import { api } from '../services/api'
import { useQuoteStore } from '../stores/quote'
import FileUploadBox from '../components/products/FileUploadBox.vue'

const route = useRoute()
const quoteStore = useQuoteStore()

const isSubmitting = ref(false)
const isSubmitted = ref(false)
const submittedQuoteId = ref('')

const form = ref({
  customerName: '',
  phone: '',
  email: '',
  company: '',
  productName: '',
  quantity: '',
  specsDescription: '',
  files: []
})

onMounted(() => {
  if (route.query.product) {
    const prod = products.find(p => p.slug === route.query.product)
    if (prod) {
      form.value.productName = prod.name
      if (route.query.qty) {
        form.value.quantity = `${route.query.qty} ${prod.unit}`
      }
    }
  }

  // Pre-fill from store if available
  if (quoteStore.currentQuote.productName) {
    form.value.productName = quoteStore.currentQuote.productName
  }
  if (quoteStore.currentQuote.files && quoteStore.currentQuote.files.length > 0) {
    form.value.files = quoteStore.currentQuote.files
  }
})

async function handleSubmit() {
  isSubmitting.value = true
  try {
    const formData = new FormData()
    formData.append('customer_name', form.value.customerName)
    formData.append('phone', form.value.phone)
    if (form.value.email) formData.append('email', form.value.email)
    if (form.value.company) formData.append('company', form.value.company)
    if (form.value.company_fax) formData.append('company_fax', form.value.company_fax)
    if (form.value.productName) formData.append('product_name', form.value.productName)
    if (form.value.quantity) formData.append('quantity', form.value.quantity)
    if (form.value.specsDescription) formData.append('specs_description', form.value.specsDescription)

    if (form.value.files && form.value.files.length > 0) {
      const fileObj = form.value.files[0]
      if (fileObj instanceof File) {
        formData.append('file', fileObj)
      }
    }

    const res = await api.submitQuote(formData)
    submittedQuoteId.value = res.data?.code || 'NN-DAT-HANG'
    isSubmitted.value = true
    quoteStore.resetQuote()
  } catch (err) {
    alert('Có lỗi xảy ra: ' + (err.message || 'Vui lòng thử lại sau.'))
  } finally {
    isSubmitting.value = false
  }
}
</script>

