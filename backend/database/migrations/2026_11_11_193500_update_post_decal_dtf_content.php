<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $content = <<<'HTML'
<p>Công nghệ <strong>in decal DTF (Direct to Film)</strong> và <strong>decal DTF UV nổi</strong> hiện đang là bước đột phá hàng đầu trong ngành in ấn bao bì, may mặc và quà tặng doanh nghiệp tại Việt Nam. Phương pháp này giải quyết triệt để các hạn chế của in lụa truyền thống và in chuyển nhiệt cũ: không giới hạn số lượng màu sắc, không cần làm khuôn in tốn kém và ép được trên hầu như mọi bề mặt chất liệu.</p>

<p>Tại <strong>Xưởng In Ấn Nhật Nguyên</strong>, chúng tôi đầu tư hệ thống máy in kỹ thuật số đầu phun kép công nghiệp thế hệ mới, nhận in decal DTF theo mét hoặc bế rời thành phẩm với mức giá gốc trực tiếp từ xưởng, phục vụ từ các đơn hàng cá nhân số lượng ít đến các xưởng may gia công hàng chục nghìn sản phẩm.</p>

<h2>1. Giới thiệu dịch vụ in decal DTF và DTF UV nổi</h2>
<p><strong>Decal DTF (Direct to Film)</strong> là công nghệ in kỹ thuật số trực tiếp lên màng phim PET chuyên dụng, sau đó được phủ một lớp bột keo chuyển nhiệt TPU và sấy khô tự động qua buồng sấy nhiệt. Khi thành phẩm, người dùng chỉ cần dùng máy ép nhiệt (hoặc bàn ủi) ở nhiệt độ khoảng <strong>150°C - 160°C</strong> trong <strong>12 - 15 giây</strong> là hình in sẽ bám chặt vĩnh viễn vào sợi vải.</p>

<p>Bên cạnh DTF vải, Nhật Nguyên còn phát triển dòng <strong>Decal DTF UV nổi</strong>: công nghệ in phủ mực UV cao cấp tạo hiệu ứng 3D nổi bóng bẩy, có thể dán trực tiếp lên các bề mặt cứng như ly thủy tinh, bình giữ nhiệt, mũ bảo hiểm, đồ da, hộp quà mà không cần ép nhiệt.</p>

<h2>2. Ưu điểm vượt trội của công nghệ in decal DTF</h2>
<ul class="article-feature-list">
  <li class="article-feature-item">
    <span class="feature-label">Màu sắc sắc nét, chuẩn hệ màu CMYK:</span>
    <span class="feature-desc">Không giới hạn số lượng màu sắc, thể hiện hoàn hảo các chi tiết chuyển màu gradient, hiệu ứng khói, bóng đổ 3D phức tạp mà in lụa không thể làm được.</span>
  </li>
  <li class="article-feature-item">
    <span class="feature-label">Độ bền vượt trội 50+ lần giặt:</span>
    <span class="feature-desc">Màng keo TPU cao cấp dẻo dai, chịu lực co giãn 4 chiều cực tốt, không bị nứt chân chim hay phai màu sau nhiều lần giặt máy.</span>
  </li>
  <li class="article-feature-item">
    <span class="feature-label">Tương thích mọi chất liệu vải:</span>
    <span class="feature-desc">Bám dính siêu chắc trên 100% Cotton, Poly, Kaki, Jean, Canvas, Nỉ, Vải dù, Thun lạnh và cả vải không dệt khó tính.</span>
  </li>
  <li class="article-feature-item">
    <span class="feature-label">Mỏng nhẹ, thoáng khí khi mặc:</span>
    <span class="feature-desc">Lớp mực và keo mỏng mịn, không tạo cảm giác bí bách hay cộm cứng như các loại decal cao su dày thông thường.</span>
  </li>
  <li class="article-feature-item">
    <span class="feature-label">In nhanh lấy ngay, không tốn phí khuôn:</span>
    <span class="feature-desc">In trực tiếp từ file đồ họa máy tính, đáp ứng đơn hàng lấy gấp trong 2h - 4h, tối ưu cho cả đơn hàng từ 1 mét đến hàng nghìn mét.</span>
  </li>
</ul>

<h2>3. Ứng dụng thực tế của decal DTF trong đời sống & kinh doanh</h2>
<p>Nhờ tính linh hoạt và độ bám dính cao, decal DTF tại Nhật Nguyên được ứng dụng rộng rãi trong nhiều lĩnh vực:</p>
<ul>
  <li><strong>Áo thun đồng phục, áo lớp, áo nhóm:</strong> Ép logo công ty, slogan, hình ảnh sự kiện sắc nét, chuyên nghiệp.</li>
  <li><strong>Thương hiệu thời trang Local Brand:</strong> In nhãn size cổ áo, hình in nghệ thuật cao cấp trên áo hoodie, sweater, jacket.</li>
  <li><strong>Túi vải canvas, balo, túi tote:</strong> Làm quà tặng quảng cáo thương hiệu, quà tặng hội nghị sang trọng.</li>
  <li><strong>Đồ thể thao, đồ tập gym:</strong> In số áo, tên cầu thủ trên chất liệu thun mè, spandex co giãn mạnh.</li>
  <li><strong>Nón, mũ lưỡi trai, khẩu trang vải:</strong> Điểm xuyết logo nhỏ gọn, tạo điểm nhấn thương hiệu tinh tế.</li>
  <li><strong>Vật phẩm quà tặng với Decal DTF UV:</strong> Dán logo nổi 3D lên bình giữ nhiệt, cốc sứ, sổ tay da, ốp lưng điện thoại.</li>
</ul>

<h2>4. Báo giá và thông số nhận in decal DTF theo yêu cầu</h2>
<p>Nhật Nguyên cung cấp 2 hình thức gia công linh hoạt theo nhu cầu của quý khách:</p>
<ul>
  <li><strong>In theo mét (Khổ cuộn 60cm):</strong> Khách hàng tự dàn file (xếp hình) kín khổ in 58cm để tối ưu tối đa chi phí. Thích hợp cho xưởng may, shop đồ in số lượng lớn.</li>
  <li><strong>In bế rời từng con tem:</strong> Xưởng hỗ trợ cắt bế rời thành phẩm, khách hàng mua về chỉ cần đặt lên áo và bấm máy ép nhiệt là hoàn thiện ngay.</li>
</ul>

<blockquote>
  <strong>Lời khuyên từ thợ in Nhật Nguyên:</strong> Để bản in đạt độ chuẩn màu cao nhất, quý khách nên thiết kế file trên hệ màu <strong>CMYK</strong> với độ phân giải tối thiểu <strong>300 DPI</strong> và convert toàn bộ font chữ trước khi xuất file.
</blockquote>

<h2>5. Quy trình 5 bước đặt in decal DTF tại Xưởng In Nhật Nguyên</h2>
<div class="article-step-card">
  <div class="step-badge">Bước 1</div>
  <div class="step-content"><strong>Tiếp nhận yêu cầu & file thiết kế:</strong> Khách gửi file qua Zalo 076.759.8368 hoặc email kèm kích thước và số lượng mét cần in.</div>
</div>
<div class="article-step-card">
  <div class="step-badge">Bước 2</div>
  <div class="step-content"><strong>Kiểm tra file & tối ưu kỹ thuật:</strong> Kỹ thuật viên kiểm tra độ nét, hệ màu, lót mực trắng và hỗ trợ dàn file tối ưu diện tích màng in miễn phí.</div>
</div>
<div class="article-step-card">
  <div class="step-badge">Bước 3</div>
  <div class="step-content"><strong>Báo giá gốc tại xưởng:</strong> Gửi bảng báo giá chi tiết, xác nhận tiến độ giao hàng và quy cách đóng gói.</div>
</div>
<div class="article-step-card">
  <div class="step-badge">Bước 4</div>
  <div class="step-content"><strong>Tiến hành in & rải sấy keo tự động:</strong> In phun mực DTF chính hãng, phủ keo hạt TPU và sấy khô bằng máy sấy nhiệt băng chuyền công nghiệp.</div>
</div>
<div class="article-step-card">
  <div class="step-badge">Bước 5</div>
  <div class="step-content"><strong>KCS kiểm tra chất lượng & bàn giao:</strong> Kiểm tra từng mét phim in, cuộn bọc chống sốc cẩn thận và giao tận nơi toàn quốc hoặc nhận ngay tại xưởng.</div>
</div>

<h2>6. Liên hệ tư vấn và nhận báo giá in decal DTF tốt nhất</h2>
<p>Quý khách hàng, đối tác xưởng may và các thương hiệu thời trang tại TP.HCM hoặc trên toàn quốc đang tìm kiếm đơn vị gia công in decal DTF uy tín, giá tận gốc, in sắc nét và hỗ trợ kỹ thuật tận tâm – hãy liên hệ ngay với <strong>Xưởng In Ấn Nhật Nguyên</strong>:</p>

<div class="article-contact-box">
  <div class="contact-icon">📍</div>
  <div>
    <strong>Xưởng In Ấn Nhật Nguyên:</strong> Đồng hành cùng thương hiệu của bạn với giải pháp in ấn chuyên nghiệp.<br>
    <strong>Hotline / Zalo hỗ trợ 24/7:</strong> <a href="tel:0767598368" class="text-amber-600 font-black hover:underline">076.759.8368</a> (Gặp Kỹ thuật viên báo giá trong 5 phút).
  </div>
</div>
HTML;

        DB::table('posts')
            ->where('slug', 'nhan-decal-dtf-uv-noi')
            ->orWhere('id', 1)
            ->update([
                'title' => 'In Decal DTF – DTF UV Nổi Theo Yêu Cầu, Giá Xưởng TP.HCM',
                'excerpt' => 'Xưởng In Nhật Nguyên chuyên nhận in decal DTF và DTF UV nổi theo yêu cầu giá gốc tận xưởng tại TP.HCM. Công nghệ in chuyển nhiệt hiện đại, hình ảnh sắc nét, độ bền 50+ lần giặt, ép được trên mọi chất liệu vải và vật phẩm.',
                'meta_title' => 'In Decal DTF, DTF UV Nổi Theo Yêu Cầu Giá Rẻ Tại Xưởng TP.HCM | Nhật Nguyên',
                'meta_description' => 'Dịch vụ in decal DTF, DTF UV nổi chất lượng cao tại TP.HCM. Công nghệ in chuyển nhiệt sắc nét, bám dính cực tốt trên mọi loại vải, in nhanh lấy ngay trong ngày.',
                'content' => $content,
                'category' => 'Tem nhãn & Decal',
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revert needed
    }
};

