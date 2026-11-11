# Checklist Cải Thiện Website In Ấn Nhật Nguyên

> Ngày tạo: 03/11/2026
> Phạm vi: Đánh giá hiện trạng website và đối chiếu với inantrangia.vn

---

## 1. Tổng Quan

Website hiện tại đã có nhiều chức năng cần thiết cho một xưởng in: danh mục sản phẩm, trang chi tiết, gửi yêu cầu báo giá, công cụ tính giá, quy trình đặt in và nội dung hướng dẫn.

**Hướng cải thiện chính:** Không phải thêm thật nhiều mục mới, mà là làm rõ năng lực thật, giảm trùng lặp và giúp khách dễ chọn sản phẩm, hiểu giá, rồi gửi yêu cầu.

---

## 2. Phân Tích Website Hiện Tại

### 2.1. Cấu trúc trang chủ

```text
Hero Banner → Lưới Danh Mục → Bộ Sưu Tập Ấn Phẩm → Sản Phẩm Phổ Biến
→ Báo Giá Nhanh → Quy Trình → Cẩm Nang
```

Nền tảng khá đầy đủ, nhưng hai khu vực danh mục và trưng bày sản phẩm có thể tạo cảm giác lặp lại trước khi khách đến được phần báo giá.

### 2.2. Các điểm cụ thể

| # | Khu vực | File | Vấn đề | Gợi ý |
|---|---|---|---|---|
| 1 | Hero | `src/components/home/HeroBanner.vue` | Chia hero thành menu, nội dung chính và banner phụ nên có nhiều thông tin ngay vùng đầu trang | Ưu tiên một thông điệp rõ, ảnh sản phẩm in thực tế và nút CTA báo giá |
| 2 | Danh mục | `src/components/home/CategoryGrid.vue` | Trình bày tới 20 danh mục trên lưới rộng; tên sản phẩm khó đọc khi bị giới hạn một dòng | Giữ lưới gọn, cho phép xem thêm thay vì đưa toàn bộ 20 mục lên cùng vùng |
| 3 | Trưng bày sản phẩm | `CategoryShowcaseSection.vue` và `PopularProductsSection.vue` | Cùng giới thiệu sản phẩm theo cách khác nhau nên có thể trùng lặp | Phân vai rõ hoặc gộp lại để giảm độ dài trang |
| 4 | Ảnh sản phẩm | `src/data/products.js` | Dùng ảnh Unsplash, chưa thể hiện rõ thành phẩm thực tế | Dùng ảnh sản phẩm thật, ảnh cận chất liệu và ảnh thành phẩm |
| 5 | Số liệu cam kết | `src/data/siteSettings.js` | Các số liệu như “30 Phút”, “15,000+”, “100% đổi trả” cần đối chiếu năng lực thực tế | Chỉ hiển thị con số có thể chứng minh |
| 6 | Giao diện | Toàn bộ | Nhiều màu thương hiệu, bo góc, nhãn và hiệu ứng trang trí | Tiết chế để sản phẩm, thông số và CTA dễ nhìn hơn |

---

## 3. Đối Chiếu Với Inantrangia.vn

### Điểm đáng tham khảo về cấu trúc

- Bao phủ nội dung theo hành trình khách hàng: danh mục theo mục đích sử dụng, gợi ý sản phẩm, kiến thức in ấn, quy trình, dự án thực tế, thông tin xưởng và biểu mẫu báo giá.
- Nội dung hướng dẫn giúp khách hiểu các yếu tố ảnh hưởng đến kết quả in như giấy, màu và gia công.
- Form báo giá hỏi những thông tin quan trọng như sản phẩm, số lượng và thời gian cần nhận.

> Chỉ nên tham khảo cấu trúc, không sao chép nguyên thiết kế hay nội dung. Giá trị khác biệt của Nhật Nguyên cần dựa trên dữ liệu thật: loại máy, quy trình sản xuất, thời gian xử lý, khả năng nhận đơn gấp, địa bàn giao hàng, mẫu thành phẩm và phản hồi khách hàng đã xác thực.

---

## 4. Hướng Đề Xuất

### 4.1. Đầu trang (Hero)

- Dùng một tiêu đề ngắn nêu rõ Nhật Nguyên in gì và phục vụ ai.
- Dùng ảnh thành phẩm hoặc ảnh xưởng thật.
- Đặt nút chính **“Nhận báo giá”**, nút phụ gọi điện hoặc Zalo.
- Tránh để quá nhiều thông tin tranh sự chú ý trong hero.

### 4.2. Lối vào danh mục

- Gom sản phẩm thành các nhóm dễ hiểu: **bao bì, tem nhãn, văn phòng, quảng cáo, in nhanh**.
- Giữ lưới danh mục gọn; cho phép xem thêm thay vì đưa toàn bộ 20 mục lên cùng một vùng.

### 4.3. Sản phẩm nổi bật

- Chỉ giữ một khu vực sản phẩm chủ lực.
- Dùng tên ngắn, giá “từ” có điều kiện rõ ràng, số lượng tối thiểu và thời gian dự kiến.
- Thay ảnh minh họa bằng ảnh thật của sản phẩm Nhật Nguyên.

### 4.4. Báo giá

- Đưa form hoặc lối tắt báo giá lên sớm.
- Hỏi đủ thông tin để tư vấn: sản phẩm, kích thước, chất liệu, số lượng, gia công, hạn nhận và file thiết kế.
- Nếu công cụ tính giá chỉ đưa mức tham khảo, cần ghi rõ để không tạo cảm giác đó là báo giá xác nhận.

### 4.5. Tăng độ tin cậy

- Thêm ảnh xưởng, máy móc, quy trình kiểm file/duyệt mẫu và dự án thật.
- Chỉ hiển thị chứng nhận, đánh giá, số liệu và cam kết có thể chứng minh.

### 4.6. Nội dung kiến thức

- Giữ các bài hướng dẫn có ích cho quyết định đặt in.
- Đặt phần kiến thức sau danh mục, sản phẩm và báo giá để không làm gián đoạn hành trình mua.

### 4.7. Giao diện và kỹ thuật

- Chuẩn hóa ảnh sản phẩm, kích thước tiêu đề, khoảng cách và kiểu nút.
- Kiểm tra trải nghiệm mobile, tốc độ tải ảnh và liên kết danh mục.
- Đảm bảo giá ở trang chủ nhất quán với giá ở trang chi tiết.

---

## 5. Đổi Tên Tiêu Đề Sản Phẩm

Tiêu đề **“Sản Phẩm Được Doanh Nghiệp Đặt In Nhiều Nhất”** dài và hơi gượng. Có thể đổi thành:

| Phương án | Khi nên dùng |
|---|---|
| **Ấn Phẩm Được Ưa Chuộng** | Muốn tiêu đề chuyên nghiệp, trung tính, dễ quét mắt |
| **Ấn Phẩm Bán Chạy** | Muốn nhấn mạnh sức mua và sự ngắn gọn |

**Khuyến nghị:** Dùng **“Ấn Phẩm Bán Chạy”** cho giao diện chính.

---

## 6. Kết Luận

Giữ các chức năng sẵn có, nhưng tinh gọn trang chủ theo thứ tự:

```text
Nhận diện năng lực → Chọn sản phẩm → Xem minh chứng → Yêu cầu báo giá
```

Website sẽ tập trung hơn vào nhu cầu khách và trông chuyên nghiệp nhờ nội dung, hình ảnh thực tế, thay vì chỉ tăng hiệu ứng giao diện.

---

## 7. Phạm Vi Phân Tích

- Đã khảo sát: `HomeView.vue`, các thành phần trang chủ, dữ liệu sản phẩm/cài đặt và nội dung trang chủ `inantrangia.vn`.
- Chưa kiểm tra sâu: các trang con của inantrangia.vn, hiệu năng và dữ liệu chuyển đổi.
- Chưa chỉnh sửa code trong lượt phân tích này.

## 8. NGUYÊN TẮC CẢI TIẾN VÀ BẢO TOÀN THIẾT KẾ HIỆN TẠI

### 8.1. Nguyên tắc chung

Quá trình cải thiện website Nhật Nguyên phải tuân thủ nguyên tắc:

**Không phá vỡ thiết kế hiện tại – Không tự ý thay đổi layout – Không viết lại code khi chưa thực sự cần thiết.**

Website đã có giao diện, cấu trúc component và hệ thống CSS được xây dựng trước đó. Vì vậy, nhiệm vụ cải tiến là nâng cấp những phần chưa tốt trên nền tảng hiện có, không phải thiết kế lại website từ đầu.

### 8.2. Những thành phần phải được bảo toàn

- Giữ nguyên cấu trúc layout của các trang hiện tại.
- Giữ nguyên bố cục Header, Hero Banner, Navigation, Sidebar, Footer và các khu vực nội dung.
- Giữ nguyên cấu trúc DOM và hệ thống component Vue hiện có.
- Giữ nguyên class CSS, ID, CSS variables và các quy tắc thiết kế đang sử dụng.
- Giữ nguyên màu sắc thương hiệu, typography, khoảng cách, border-radius, shadow và hiệu ứng.
- Giữ nguyên responsive breakpoint và cách bố trí trên desktop, tablet, mobile.
- Không thay đổi logic nghiệp vụ, dữ liệu và chức năng đang hoạt động tốt.

### 8.3. Những thay đổi được phép

Chỉ thực hiện các thay đổi có mục tiêu cụ thể và không ảnh hưởng đến thiết kế tổng thể:

1. Điều chỉnh nội dung, tiêu đề, mô tả và CTA.
2. Tối ưu dữ liệu sản phẩm, danh mục và thông tin hiển thị.
3. Thay thế hình ảnh khi cần thiết nhưng phải giữ nguyên kích thước, tỷ lệ và vị trí hiển thị.
4. Sửa lỗi giao diện đã xác định rõ nguyên nhân.
5. Tối ưu SEO, accessibility và hiệu năng.
6. Cải thiện trải nghiệm người dùng bằng những điều chỉnh nhỏ, có kiểm soát.
7. Bổ sung chức năng mới bằng component độc lập hoặc phương án mở rộng không ảnh hưởng đến component hiện hữu.

### 8.4. Quy trình bắt buộc trước khi chỉnh sửa code

Trước khi thực hiện bất kỳ thay đổi nào, phải:

- Đọc và phân tích code hiện tại.
- Xác định chính xác component, file CSS và logic liên quan.
- Ghi nhận layout và giao diện hiện tại.
- Xác định phạm vi ảnh hưởng của thay đổi.
- Đề xuất phương án chỉnh sửa tối thiểu.
- Phân biệt rõ thay đổi nội dung, thay đổi giao diện và thay đổi chức năng.

Không được chỉnh sửa trực tiếp khi chưa xác định được phạm vi ảnh hưởng.

### 8.5. Quy tắc chỉnh sửa code

- Ưu tiên chỉnh sửa cục bộ thay vì viết lại toàn bộ file.
- Không thay thế toàn bộ component chỉ để thực hiện một thay đổi nhỏ.
- Không xóa code cũ nếu chưa xác định code đó không còn được sử dụng.
- Không tự ý cài thêm thư viện hoặc thay đổi dependency.
- Không tự ý thay đổi cấu trúc thư mục và kiến trúc dự án.
- Không chỉnh sửa các file không liên quan đến yêu cầu.
- Không tự ý gộp hoặc tách component.
- Không tạo CSS global có khả năng ảnh hưởng đến các trang khác.
- Ưu tiên CSS có phạm vi giới hạn trong component cần chỉnh sửa.

### 8.6. Quy trình kiểm tra sau chỉnh sửa

Sau mỗi lần cập nhật phải kiểm tra:

- Layout trước và sau chỉnh sửa.
- Giao diện desktop, tablet và mobile.
- Các component liên quan.
- Các chức năng hiện có.
- Console và lỗi runtime.
- CSS bị ghi đè hoặc xung đột.
- Những khu vực khác có bị ảnh hưởng ngoài phạm vi yêu cầu hay không.

Nếu phát hiện giao diện bị thay đổi ngoài ý muốn, phải khôi phục và xử lý lại trước khi hoàn thành.

### 8.7. Quy tắc phê duyệt

Nếu một đề xuất cải tiến bắt buộc phải thay đổi layout, cấu trúc component hoặc hệ thống CSS hiện tại:

**Phải trình bày lý do, vị trí ảnh hưởng, giao diện dự kiến và phương án thay thế để người dùng phê duyệt trước.**

Không tự ý triển khai.

### 8.8. Mục tiêu cuối cùng

Website Nhật Nguyên cần được cải thiện theo hướng:

- Giữ nguyên bản sắc thiết kế hiện tại.
- Nâng cao chất lượng nội dung và hình ảnh.
- Tăng khả năng tìm kiếm và lựa chọn sản phẩm.
- Cải thiện hành trình yêu cầu báo giá.
- Tối ưu hiệu năng và trải nghiệm người dùng.
- Hạn chế tối đa rủi ro phát sinh lỗi hoặc mất các thành phần giao diện đã xây dựng.

**Ưu tiên cải tiến có kiểm soát thay vì thay đổi toàn diện.**