<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 24px; }
    .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
    .header { background: #0A192F; color: #ffffff; padding: 24px 32px; }
    .badge { display: inline-block; background: #F59E0B; color: #0A192F; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; margin-bottom: 8px; }
    .title { font-size: 20px; font-weight: 800; margin: 0; color: #ffffff; }
    .body { padding: 32px; }
    .row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #e2e8f0; font-size: 14px; }
    .label { color: #64748b; font-weight: 600; width: 140px; }
    .value { color: #0f172a; font-weight: 700; flex: 1; text-align: right; }
    .desc-box { background: #f1f5f9; padding: 16px; border-radius: 12px; margin-top: 16px; font-size: 13px; line-height: 1.6; color: #334155; }
    .btn { display: block; text-align: center; background: #1E3A8A; color: #ffffff !important; padding: 14px 20px; border-radius: 12px; font-weight: 800; text-decoration: none; margin-top: 24px; font-size: 14px; }
    .footer { text-align: center; font-size: 12px; color: #94a3b8; padding: 20px; }
  </style>
</head>
<body>
  <div class="card">
    <div class="header">
      <div class="badge">YÊU CẦU BÁO GIÁ MỚI</div>
      <div class="title">Đơn Yêu Cầu #{{ $quote->code }}</div>
    </div>
    <div class="body">
      <div class="row">
        <span class="label">Khách hàng:</span>
        <span class="value">{{ $quote->customer_name }}</span>
      </div>
      <div class="row">
        <span class="label">Số điện thoại:</span>
        <span class="value" style="color: #d97706; font-size: 16px;">
          <a href="tel:{{ $quote->phone }}" style="color: #d97706; text-decoration: none;">{{ $quote->phone }}</a>
        </span>
      </div>
      @if($quote->email)
      <div class="row">
        <span class="label">Email khách:</span>
        <span class="value">{{ $quote->email }}</span>
      </div>
      @endif
      @if($quote->company)
      <div class="row">
        <span class="label">Công ty / Đơn vị:</span>
        <span class="value">{{ $quote->company }}</span>
      </div>
      @endif
      <div class="row">
        <span class="label">Ấn phẩm cần in:</span>
        <span class="value" style="color: #1E3A8A;">{{ $quote->product_name ?? 'Theo quy cách riêng' }}</span>
      </div>
      @if($quote->quantity)
      <div class="row">
        <span class="label">Số lượng:</span>
        <span class="value">{{ $quote->quantity }}</span>
      </div>
      @endif
      @if($quote->file_path)
      <div class="row">
        <span class="label">File đính kèm:</span>
        <span class="value">
          <a href="{{ url($quote->file_path) }}" target="_blank" style="color: #2563eb; font-weight: 700;">
            📎 Tải file in ({{ $quote->file_name ?? 'Xem file' }})
          </a>
        </span>
      </div>
      @endif

      @if($quote->specs_description)
      <div style="margin-top: 20px;">
        <strong style="font-size: 13px; color: #475569;">Ghi chú quy cách của khách:</strong>
        <div class="desc-box">
          {!! nl2br(e($quote->specs_description)) !!}
        </div>
      </div>
      @endif

      <a href="{{ url('/admin/quotes') }}" class="btn">
        Mở Quản Trị Đơn Báo Giá &rarr;
      </a>
    </div>
  </div>
  <div class="footer">
    Email tự động gửi từ website Xưởng In Ấn Nhật Nguyên (dichvuinnhatnguyen.vn)
  </div>
</body>
</html>

