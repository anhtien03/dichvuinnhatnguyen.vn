<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 24px; }
    .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
    .header { background: #0A192F; color: #ffffff; padding: 28px 32px; text-align: center; }
    .badge { display: inline-block; background: #F59E0B; color: #0A192F; font-size: 11px; font-weight: 800; padding: 4px 12px; border-radius: 20px; text-transform: uppercase; margin-bottom: 8px; }
    .title { font-size: 20px; font-weight: 800; margin: 0; color: #ffffff; }
    .body { padding: 32px; font-size: 14px; line-height: 1.6; color: #334155; }
    .code-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 16px; text-align: center; margin: 20px 0; }
    .code { font-size: 20px; font-weight: 900; color: #1e3a8a; letter-spacing: 1px; }
    .hotline-btn { display: inline-block; background: #F59E0B; color: #0A192F !important; padding: 12px 24px; border-radius: 12px; font-weight: 800; text-decoration: none; margin-top: 16px; font-size: 14px; }
    .contact-info { background: #f8fafc; border-radius: 12px; padding: 16px; margin-top: 24px; font-size: 13px; color: #64748b; line-height: 1.6; }
    .footer { text-align: center; font-size: 12px; color: #94a3b8; padding: 20px; }
  </style>
</head>
<body>
  <div class="card">
    <div class="header">
      <div class="badge">XÁC NHẬN YÊU CẦU</div>
      <div class="title">XƯỞNG IN ẤN NHẬT NGUYÊN</div>
    </div>
    <div class="body">
      <p>Kính chào Quý khách <strong>{{ $quote->customer_name }}</strong>,</p>
      <p>Chúng tôi đã tiếp nhận yêu cầu báo giá của Quý khách cho ấn phẩm <strong>{{ $quote->product_name ?? 'In ấn quy cách riêng' }}</strong>.</p>
      
      <div class="code-box">
        <div style="font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 4px;">MÃ TRA CỨU ĐƠN CỦA BẠN</div>
        <div class="code">{{ $quote->code }}</div>
      </div>

      <p>Kỹ thuật viên xưởng in Nhật Nguyên sẽ chủ động liên hệ qua Zalo hoặc số điện thoại <strong>{{ $quote->phone }}</strong> trong ít phút để gửi bảng báo giá tối ưu và duyệt mẫu in cùng Quý khách.</p>

      <div style="text-align: center;">
        <a href="https://zalo.me/0359005469" class="hotline-btn">
          💬 Nhắn Zalo Trực Tiếp: 0359 005 469
        </a>
      </div>

      <div class="contact-info">
        <strong>XƯỞNG IN ẤN NHẬT NGUYÊN</strong><br>
        📍 Xưởng: Đông Hưng Thuận 40, P. Đông Hưng Thuận, Quận 12, TP.HCM<br>
        📍 VP: 167 Hà Huy Giáp, P. An Phú Đông, Quận 12, TP.HCM<br>
        📞 Hotline/Zalo: 0359 005 469 - 076 759 8368<br>
        🌐 Website: https://dichvuinnhatnguyen.vn
      </div>
    </div>
  </div>
  <div class="footer">
    Cảm ơn Quý khách đã tin tưởng đồng hành cùng Xưởng In Nhật Nguyên!
  </div>
</body>
</html>
