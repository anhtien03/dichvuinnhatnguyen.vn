<template>
  <section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-10">
        <span class="text-xs font-bold text-brand-gold uppercase tracking-wider block mb-1">
          GIẢI ĐÁP THẮC MẮC
        </span>
        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
          Câu Hỏi Thường Gặp Khi Đặt In
        </h2>
        <p class="text-sm text-slate-500 mt-2">
          Những điều cần biết về file thiết kế, thời gian giao hàng và chính sách bảo hành in ấn.
        </p>
      </div>

      <div class="space-y-3">
        <div
          v-for="(faq, idx) in faqs"
          :key="idx"
          class="border border-slate-200 rounded-2xl overflow-hidden transition-colors"
        >
          <button
            @click="activeIdx = activeIdx === idx ? -1 : idx"
            class="w-full px-5 py-4 text-left flex items-center justify-between gap-4 bg-slate-50/50 hover:bg-slate-50 transition-colors"
          >
            <span class="font-bold text-slate-800 text-sm sm:text-base">{{ faq.q }}</span>
            <ChevronDown
              :class="['w-5 h-5 text-slate-400 shrink-0 transition-transform duration-300', activeIdx === idx ? 'rotate-180 text-brand-blue' : '']"
            />
          </button>
          <div
            v-show="activeIdx === idx"
            class="px-5 py-4 bg-white text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100"
          >
            {{ faq.a }}
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import { ChevronDown } from 'lucide-vue-next'

const activeIdx = ref(0)

const faqs = [
  {
    q: 'File in cần định dạng gì và hệ màu như thế nào?',
    a: 'Xưởng in Nhật Nguyên khuyến khích gửi file định dạng Vector như PDF, AI, CDR hoặc PSD/TIFF có độ phân giải tối thiểu 300 DPI. File phải được thiết kế trên hệ màu CMYK (không dùng RGB) và chừa lề cắt xén (bleed) 2mm mỗi cạnh để tránh bị phạm vào nội dung khi cắt bế.'
  },
  {
    q: 'Xưởng có hỗ trợ in test mẫu trước khi in số lượng lớn không?',
    a: 'Có. Đối với các đơn hàng in catalogue, hộp giấy hoặc đơn hàng lớn trên 5 triệu đồng, xưởng in hỗ trợ in test mẫu (proof màu) để khách hàng ký duyệt trực tiếp trước khi xuất kẽm in hàng loạt.'
  },
  {
    q: 'Thời gian hoàn thành đơn hàng mất bao lâu?',
    a: 'Đối với in nhanh kỹ thuật số (Namecard số lượng ít, Standee, Tờ rơi gấp), thời gian chỉ từ 2 giờ đến 24 giờ. Đối với đơn hàng in Offset ghép bài, thời gian trung bình từ 2 đến 3 ngày làm việc.'
  },
  {
    q: 'Chính sách bảo hành và xử lý lỗi in ấn ra sao?',
    a: 'Nếu thành phẩm in bị lỗi sai màu nghiêm trọng so với file duyệt, sai nội dung, sai quy cách giấy hoặc trễ hẹn cam kết, In Nhật Nguyên cam kết in lại 100% miễn phí hoặc hoàn tiền theo yêu cầu của quý khách.'
  },
  {
    q: 'Công ty có xuất hóa đơn giá trị gia tăng (VAT) không?',
    a: 'Có. In Nhật Nguyên là doanh nghiệp có tư cách pháp nhân đầy đủ, hỗ trợ xuất hóa đơn điện tử VAT (thuế suất 8% hoặc 10% tùy loại mặt hàng) gửi qua email ngay sau khi hoàn tất giao hàng.'
  }
]
</script>

