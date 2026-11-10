<template>
  <div class="py-10 lg:py-16 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-2xl mx-auto mb-12">
        <span class="text-xs font-bold text-brand-gold uppercase tracking-wider block mb-1">
          KẾT NỐI VỚI CHÚNG TÔI
        </span>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">
          Thông Tin Liên Hệ & Địa Chỉ Xưởng In
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-2">
          Quý khách có thể ghé trực tiếp xưởng in để xem mẫu giấy, duyệt bản test màu hoặc liên hệ trực tuyến qua Hotline/Zalo.
        </p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left: Contact Details & Hotlines -->
        <div class="lg:col-span-5 space-y-6">
          <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-6">
            <h2 class="text-lg font-black text-slate-900 border-b border-slate-100 pb-3">
              Xưởng In Ấn Nhật Nguyên
            </h2>

            <div class="space-y-4 text-xs sm:text-sm text-slate-600">
              <div class="flex items-start gap-3">
                <MapPin class="w-5 h-5 text-brand-gold shrink-0 mt-0.5" />
                <div class="space-y-1">
                  <strong class="text-slate-900 block font-bold mb-0.5">Địa chỉ cơ sở & xưởng in:</strong>
                  <div class="text-xs text-slate-700">
                    <span class="font-semibold text-brand-blue">Xưởng sản xuất:</span> {{ siteSettings.address }}
                  </div>
                  <div class="text-xs text-slate-700">
                    <span class="font-semibold text-brand-blue">Văn phòng giao dịch:</span> 167 Hà Huy Giáp, Phường An Phú Đông, Quận 12, TP.HCM
                  </div>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <Phone class="w-5 h-5 text-brand-gold shrink-0 mt-0.5" />
                <div>
                  <strong class="text-slate-900 block font-bold mb-0.5">Hotline kinh doanh & kỹ thuật:</strong>
                  <div class="space-y-1">
                    <a
                      v-for="(h, idx) in siteSettings.hotlines"
                      :key="idx"
                      :href="'tel:' + h.number.replace(/\s+/g, '')"
                      class="block font-bold text-brand-blue hover:underline"
                    >
                      {{ h.number }} ({{ h.nvkd }})
                    </a>
                  </div>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <Mail class="w-5 h-5 text-brand-gold shrink-0 mt-0.5" />
                <div>
                  <strong class="text-slate-900 block font-bold mb-0.5">Hòm thư điện tử:</strong>
                  <a :href="'mailto:' + siteSettings.email" class="text-brand-blue hover:underline">
                    {{ siteSettings.email }}
                  </a>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <Clock class="w-5 h-5 text-brand-gold shrink-0 mt-0.5" />
                <div>
                  <strong class="text-slate-900 block font-bold mb-0.5">Thời gian làm việc:</strong>
                  <span>{{ siteSettings.workingHours }}</span>
                </div>
              </div>
            </div>

            <!-- Fast Zalo Button -->
            <div class="pt-2">
              <a
                :href="siteSettings.zalo"
                target="_blank"
                rel="noopener"
                class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md transition-colors"
              >
                <MessageSquare class="w-4 h-4" />
                <span>Nhắn tin Zalo trực tiếp với xưởng</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Right: Fast Feedback Message Form -->
        <div class="lg:col-span-7">
          <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-6">
            <h2 class="text-lg font-black text-slate-900 border-b border-slate-100 pb-3">
              Gửi Lời Nhắn / Phản Hồi Cho Ban Giám Đốc
            </h2>

            <div v-if="submitted" class="p-6 rounded-2xl bg-emerald-50 text-emerald-800 text-center space-y-2">
              <CheckCircle class="w-10 h-10 text-emerald-600 mx-auto" />
              <h3 class="font-bold text-base">Cảm ơn lời nhắn của quý khách!</h3>
              <p class="text-xs text-emerald-700">Chúng tôi đã tiếp nhận và sẽ phản hồi sớm nhất.</p>
            </div>

            <form v-else @submit.prevent="submitMessage" class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-slate-700 mb-1">Họ tên *</label>
                  <input
                    v-model="msgForm.name"
                    type="text"
                    required
                    placeholder="Nguyễn Văn A"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 focus:outline-none focus:border-brand-blue"
                  />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-700 mb-1">Số điện thoại *</label>
                  <input
                    v-model="msgForm.phone"
                    type="tel"
                    required
                    placeholder="0912 345 678"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 focus:outline-none focus:border-brand-blue"
                  />
                </div>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nội dung trao đổi</label>
                <textarea
                  v-model="msgForm.content"
                  rows="4"
                  required
                  placeholder="Quý khách cần hỗ trợ vấn đề gì..."
                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 focus:outline-none focus:border-brand-blue"
                ></textarea>
              </div>

              <button
                type="submit"
                class="px-6 py-3 rounded-xl bg-brand-gold hover:bg-brand-gold-hover text-slate-950 font-bold text-xs shadow-md transition-all"
              >
                Gửi lời nhắn &rarr;
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { MapPin, Phone, Mail, Clock, MessageSquare, CheckCircle } from 'lucide-vue-next'
import { siteSettings } from '../data/siteSettings'

const submitted = ref(false)
const msgForm = ref({
  name: '',
  phone: '',
  content: ''
})

function submitMessage() {
  submitted.value = true
}
</script>

