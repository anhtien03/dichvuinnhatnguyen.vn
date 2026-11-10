# PROMPT: XÂY DỰNG WEBSITE IN ẤN CHUYÊN NGHIỆP VỚI LARAVEL + VUE.JS

## 1. Vai trò và mục tiêu

Bạn là **Senior Full-stack Developer, UI/UX Designer và Solution
Architect**, có kinh nghiệm xây dựng website thương mại dịch vụ in ấn.

Hãy phân tích, thiết kế và phát triển một website giới thiệu dịch vụ in
ấn chuyên nghiệp, hiện đại, có khả năng mở rộng thành nền tảng tiếp nhận
yêu cầu báo giá và quản lý đơn hàng.

**Mục tiêu chính:** - Tạo website có giao diện hiện đại, chỉn chu, đáng
tin cậy và tối ưu chuyển đổi. - Trình bày rõ các dịch vụ, sản phẩm in,
quy trình đặt in, năng lực sản xuất và thông tin liên hệ. - Giúp khách
hàng nhanh chóng tìm được dịch vụ phù hợp, xem thông tin và gửi yêu cầu
báo giá. - Xây dựng bằng mã nguồn thực tế, có cấu trúc rõ ràng, dễ bảo
trì và mở rộng. - Không sử dụng website builder hoặc giao diện kéo-thả.
Ưu tiên triển khai bằng code.

## 2. Công nghệ bắt buộc

### Backend

-   Laravel phiên bản ổn định, tương thích với môi trường PHP được chọn.
-   Laravel xử lý API, xác thực, phân quyền, nghiệp vụ, dữ liệu và quản
    trị.
-   Thiết kế RESTful API có cấu trúc nhất quán.
-   Validation dữ liệu ở phía server.
-   Sử dụng Eloquent ORM và Migration/Seeder.
-   Có cơ chế xử lý lỗi, logging và bảo vệ các endpoint phù hợp.

### Frontend

-   Vue.js 3.
-   Composition API và `<script setup>`.
-   Vue Router cho điều hướng.
-   Pinia khi cần quản lý state dùng chung.
-   Axios hoặc giải pháp tương đương để gọi API.
-   Ưu tiên component hóa, tái sử dụng và tách biệt logic giao diện.

### Giao diện và công cụ

-   HTML5, CSS3, JavaScript/TypeScript tùy mức độ phù hợp.
-   Có thể sử dụng Tailwind CSS hoặc một hệ thống CSS nhất quán; không
    trộn nhiều framework UI thiếu kiểm soát.
-   Thiết kế responsive cho desktop, tablet và mobile.
-   Có thể sử dụng Docker Compose để chuẩn hóa môi trường phát triển nếu
    dự án phù hợp.

### Cổng chạy dự án

**Website phải chạy tại: `http://localhost:8090`**

Yêu cầu: - Cấu hình port `8090` rõ ràng trong Docker Compose hoặc cấu
hình chạy dự án được lựa chọn. - Không mặc định sử dụng port 8000, 8080
hoặc 5173 làm địa chỉ truy cập chính của website. - Nếu frontend và
backend chạy ở các service/port nội bộ khác nhau, phải cấu hình reverse
proxy hoặc cơ chế tương đương để người dùng truy cập website qua
`http://localhost:8090`. - Tránh xung đột port với các dự án khác. -
Cung cấp lệnh khởi chạy, dừng và kiểm tra trạng thái dự án. - Ghi rõ
port nội bộ và port được expose ra máy host.

## 3. Định hướng thiết kế UI/UX

Thiết kế theo phong cách **Modern Printing Service / Professional B2B &
B2C**.

### Cảm giác thương hiệu

-   Chuyên nghiệp, hiện đại, đáng tin cậy.
-   Có tính thương mại nhưng không quá rối mắt.
-   Thể hiện được năng lực sản xuất, chất lượng thành phẩm và sự chính
    xác trong in ấn.
-   Tránh giao diện cũ, bố cục dày đặc, quá nhiều màu hoặc hiệu ứng
    không cần thiết.

### Nguyên tắc thiết kế

-   Phân cấp thông tin rõ ràng.
-   Khoảng trắng hợp lý, typography dễ đọc.
-   Hình ảnh sản phẩm in chất lượng cao, đúng ngữ cảnh.
-   CTA nổi bật nhưng không gây khó chịu.
-   Màu sắc nhất quán theo bộ nhận diện; nếu chưa có thương hiệu, hãy đề
    xuất palette phù hợp với lĩnh vực in ấn.
-   Hiệu ứng chuyển động nhẹ, có mục đích; không lạm dụng animation.
-   Tối ưu trải nghiệm trên màn hình nhỏ.
-   Không đặt bảng giá, biểu mẫu hoặc thành phần nổi đè lên nội dung
    banner theo cách làm vỡ bố cục.

### Tham khảo cách trình bày

Có thể nghiên cứu các website dịch vụ in ấn hiện đại để học hỏi cách tổ
chức thông tin, nhưng **không sao chép nguyên mẫu**. Hãy tạo hệ thống
thiết kế riêng, phù hợp với thương hiệu và nội dung dự án.

Nếu tôi cung cấp ảnh chụp giao diện hoặc đường dẫn website tham khảo: 1.
Phân tích bố cục tổng thể. 2. Phân tích header, navigation, hero/banner,
nội dung, CTA, footer. 3. Phân tích màu sắc, font chữ, khoảng cách, kích
thước và tỷ lệ. 4. Xác định thành phần nên kế thừa về mặt trải nghiệm và
thành phần cần cải tiến. 5. Chỉ ra các vấn đề về responsive, khả năng
đọc, điều hướng và chuyển đổi. 6. Đề xuất layout mới trước khi triển
khai. 7. Không tự ý sao chép logo, hình ảnh, nội dung có bản quyền hoặc
nhận diện của website tham khảo.

## 4. Cấu trúc website đề xuất

Hãy đánh giá và triển khai các trang phù hợp với quy mô thực tế. Bộ
trang cơ bản gồm:

### 4.1. Trang chủ

-   Header có logo, menu, thông tin liên hệ và CTA yêu cầu báo giá.
-   Hero section giới thiệu năng lực/dịch vụ in ấn, có thông điệp rõ
    ràng.
-   Khu vực dịch vụ hoặc danh mục sản phẩm nổi bật.
-   Lý do khách hàng nên lựa chọn đơn vị in.
-   Quy trình tiếp nhận và sản xuất đơn hàng.
-   Sản phẩm/dự án tiêu biểu.
-   Năng lực máy móc, công nghệ hoặc quy trình kiểm soát chất lượng (chỉ
    hiển thị dữ liệu có thật).
-   Đánh giá khách hàng nếu có dữ liệu xác thực.
-   FAQ.
-   CTA liên hệ/báo giá.
-   Footer đầy đủ thông tin doanh nghiệp.

### 4.2. Trang danh mục dịch vụ

-   Danh sách dịch vụ theo nhóm.
-   Bộ lọc hoặc phân loại nếu số lượng dịch vụ đủ lớn.
-   Thẻ dịch vụ có ảnh, tên, mô tả ngắn và liên kết chi tiết.
-   Có trạng thái rỗng khi không có dữ liệu phù hợp.

### 4.3. Trang chi tiết dịch vụ/sản phẩm in

-   Tên dịch vụ/sản phẩm.
-   Ảnh đại diện và thư viện ảnh.
-   Mô tả, đặc điểm, ứng dụng.
-   Chất liệu, kích thước, quy cách, kỹ thuật gia công nếu có.
-   Các tùy chọn đặt in phù hợp với từng sản phẩm.
-   Sản phẩm/dịch vụ liên quan.
-   FAQ theo dịch vụ.
-   CTA gửi yêu cầu báo giá.

Có thể bổ sung các nhóm nội dung phù hợp với ngành in như danh thiếp,
catalogue, brochure, tờ rơi, tem nhãn, bao bì, decal, băng rôn, vòng tay
sự kiện và các sản phẩm khác. Không tạo danh mục giả định nếu dự án
không kinh doanh các sản phẩm đó.

### 4.4. Trang báo giá / yêu cầu tư vấn

-   Form nhập thông tin liên hệ.
-   Chọn dịch vụ hoặc sản phẩm cần in.
-   Số lượng, kích thước, chất liệu, quy cách và thời gian mong muốn nếu
    có.
-   Cho phép mô tả yêu cầu và gửi file thiết kế/tham khảo nếu nghiệp vụ
    hỗ trợ.
-   Kiểm tra dữ liệu đầu vào, giới hạn dung lượng và loại file tải lên.
-   Thông báo gửi thành công/thất bại rõ ràng.
-   Lưu yêu cầu vào hệ thống quản trị.
-   Không tự đưa ra giá chính xác nếu chưa có công thức tính giá và dữ
    liệu đầu vào đáng tin cậy.

### 4.5. Trang giới thiệu

-   Giới thiệu doanh nghiệp.
-   Năng lực sản xuất.
-   Quy trình làm việc.
-   Cam kết chất lượng.
-   Thông tin liên hệ.

### 4.6. Trang tin tức / kiến thức in ấn

-   Danh sách bài viết.
-   Danh mục bài viết.
-   Trang chi tiết bài viết.
-   Bài viết liên quan.
-   Metadata SEO đầy đủ.

### 4.7. Trang liên hệ

-   Thông tin doanh nghiệp.
-   Địa chỉ, số điện thoại, email, giờ làm việc.
-   Bản đồ nếu có địa chỉ thực tế.
-   Form liên hệ.
-   Liên kết các kênh hỗ trợ.

## 5. Chức năng quản trị

Xây dựng khu vực quản trị riêng, có xác thực và phân quyền. Tùy phạm vi
dự án, tối thiểu cần:

-   Đăng nhập/đăng xuất.
-   Quản lý tài khoản và vai trò.
-   Quản lý danh mục dịch vụ.
-   Quản lý sản phẩm/dịch vụ in.
-   Quản lý thư viện hình ảnh.
-   Quản lý bài viết và danh mục bài viết.
-   Quản lý yêu cầu báo giá/liên hệ.
-   Quản lý thông tin website, logo, thông tin liên hệ.
-   Quản lý SEO: meta title, meta description, slug, canonical nếu cần.
-   Trạng thái bản nháp/đã xuất bản.
-   Tìm kiếm, lọc, phân trang.
-   Thông báo khi có yêu cầu báo giá mới.

Không xây dựng chức năng quản trị chỉ để làm giao diện demo. Các thao
tác tạo, xem, sửa, xóa phải kết nối dữ liệu thật và có kiểm tra quyền.

## 6. Cơ sở dữ liệu và API

Trước khi code: 1. Phân tích các thực thể dữ liệu. 2. Đề xuất quan hệ
giữa các bảng. 3. Xây dựng ERD hoặc mô tả quan hệ. 4. Tạo migrations,
models, factories/seeders phù hợp. 5. Xác định API endpoint và cấu trúc
request/response.

Các bảng có thể cần: - users - roles/permissions (nếu dùng phân quyền
chi tiết) - categories - services hoặc products - product_images -
posts - post_categories - quote_requests - quote_request_files -
site_settings

Đây chỉ là danh sách khởi đầu. Hãy điều chỉnh theo nghiệp vụ thực tế,
tránh tạo bảng dư thừa.

Yêu cầu API: - Quy ước URL và HTTP method nhất quán. - Phân trang, tìm
kiếm, lọc. - Validation và thông báo lỗi dễ hiểu. - Kiểm soát quyền truy
cập. - Không trả về dữ liệu nhạy cảm. - Có tài liệu API hoặc file mô tả
endpoint.

## 7. SEO và hiệu năng

### SEO

-   URL thân thiện, slug rõ ràng.
-   Meta title và meta description riêng cho từng trang.
-   Heading H1-H2-H3 có cấu trúc hợp lý.
-   Alt text cho hình ảnh.
-   Canonical phù hợp.
-   Open Graph/Twitter metadata.
-   XML sitemap.
-   Robots.txt.
-   Schema.org phù hợp với dữ liệu thật.
-   Breadcrumb.
-   Không tạo nội dung trùng lặp hàng loạt.
-   Có trang 404 và xử lý redirect khi thay đổi slug.

### Hiệu năng

-   Tối ưu kích thước và định dạng ảnh.
-   Lazy loading ảnh ngoài vùng nhìn thấy đầu tiên.
-   Không lazy-load ảnh hero quan trọng nếu ảnh đó cần hiển thị ngay.
-   Hạn chế JavaScript/CSS không cần thiết.
-   Tối ưu truy vấn database và tránh N+1.
-   Cache những dữ liệu phù hợp.
-   Phân trang dữ liệu lớn.
-   Đảm bảo giao diện không bị layout shift nghiêm trọng.

## 8. Bảo mật và chất lượng mã nguồn

-   Sử dụng CSRF protection phù hợp.
-   Kiểm tra và làm sạch dữ liệu đầu vào.
-   Phân quyền ở backend, không chỉ ẩn nút ở frontend.
-   Bảo vệ chức năng upload file: MIME type, phần mở rộng, dung lượng,
    tên file và quyền truy cập.
-   Không lưu mật khẩu dạng plain text.
-   Không đưa secret/API key vào mã nguồn frontend.
-   Dùng `.env` cho cấu hình môi trường.
-   Không commit file `.env` thật.
-   Có xử lý lỗi và logging.
-   Viết test cho các luồng nghiệp vụ quan trọng.
-   Code dễ đọc, có quy ước đặt tên và cấu trúc thư mục rõ ràng.

## 9. Quy trình thực hiện bắt buộc

Không code toàn bộ ngay khi chưa phân tích. Thực hiện theo từng giai
đoạn:

### Giai đoạn 1 --- Khảo sát và phân tích

-   Xác định mục tiêu website và nhóm khách hàng.
-   Liệt kê trang, chức năng và luồng người dùng.
-   Xác định nội dung cần có và dữ liệu còn thiếu.
-   Nêu rõ giả định, không tự bịa thông tin doanh nghiệp.

**Đầu ra:** tài liệu yêu cầu chức năng, sitemap và user flow.

### Giai đoạn 2 --- Đề xuất UI/UX

-   Đề xuất cấu trúc từng trang.
-   Mô tả layout desktop/mobile.
-   Đề xuất màu sắc, typography, button, card, form và spacing.
-   Mô tả hành vi của menu, CTA, form báo giá và các thành phần tương
    tác.
-   Chỉ rõ cách xử lý banner và khu vực báo giá để không chồng lấn, phá
    vỡ bố cục.

**Đầu ra:** design direction, component list và mô tả wireframe.

### Giai đoạn 3 --- Kiến trúc kỹ thuật

-   Chốt cấu trúc Laravel + Vue.js.
-   Thiết kế database và API.
-   Xác định cách xác thực, phân quyền, upload file.
-   Thiết kế cấu hình môi trường và port `8090`.

**Đầu ra:** sơ đồ kiến trúc, ERD, API specification và cấu trúc thư mục.

### Giai đoạn 4 --- Phát triển

-   Khởi tạo dự án và cấu hình môi trường.
-   Xây dựng backend trước hoặc theo từng module có API rõ ràng.
-   Xây dựng frontend theo component.
-   Kết nối frontend với API thật.
-   Hoàn thiện responsive.
-   Không dùng dữ liệu giả để thay thế chức năng đã được yêu cầu triển
    khai.

### Giai đoạn 5 --- Kiểm thử và bàn giao

-   Kiểm tra các trang trên desktop, tablet và mobile.
-   Kiểm tra form, validation, upload, đăng nhập và phân quyền.
-   Kiểm tra các API và luồng dữ liệu.
-   Kiểm tra lỗi console, lỗi server và truy vấn database.
-   Kiểm tra website truy cập đúng tại `http://localhost:8090`.
-   Viết README hướng dẫn cài đặt, chạy, dừng, migrate, seed và build
    production.

## 10. Yêu cầu về cách làm việc với AI / lập trình viên

-   Làm việc theo hướng phân tích trước, triển khai sau.
-   Không tự ý thay đổi công nghệ bắt buộc Laravel + Vue.js.
-   Không tự đổi port `8090`.
-   Không tự ý xóa hoặc ghi đè mã nguồn hiện có khi chưa phân tích tác
    động.
-   Nếu đang làm trên một dự án có sẵn, hãy đọc cấu trúc thư mục,
    `composer.json`, `package.json`, cấu hình Docker và database trước
    khi chỉnh sửa.
-   Ưu tiên tái sử dụng phần code tốt, chỉ refactor khi có lý do rõ
    ràng.
-   Không tạo một giao diện chỉ đẹp ở ảnh chụp nhưng các nút và chức
    năng không hoạt động.
-   Không để link `#`, nút giả hoặc form không có xử lý.
-   Không hard-code dữ liệu nghiệp vụ có khả năng thay đổi.
-   Khi thiếu logo, nội dung, hình ảnh hoặc thông tin doanh nghiệp, hãy
    dùng placeholder có ghi chú rõ ràng để tôi thay thế.
-   Khi gặp vấn đề ảnh hưởng kiến trúc hoặc dữ liệu, hãy giải thích và
    đề xuất phương án trước khi thực hiện.

## 11. Tiêu chí nghiệm thu

Website chỉ được xem là hoàn thành khi đáp ứng:

-   [ ] Chạy được tại `http://localhost:8090`.
-   [ ] Backend Laravel và frontend Vue.js hoạt động đúng.
-   [ ] Giao diện hiện đại, nhất quán và responsive.
-   [ ] Các trang chính có nội dung và điều hướng đầy đủ.
-   [ ] Danh mục và dịch vụ/sản phẩm lấy dữ liệu từ backend.
-   [ ] Form báo giá gửi dữ liệu và lưu được vào database.
-   [ ] Khu vực quản trị có xác thực và phân quyền phù hợp.
-   [ ] Các chức năng CRUD chính hoạt động thực tế.
-   [ ] Có SEO cơ bản cho các trang nội dung.
-   [ ] Không có lỗi nghiêm trọng trên console/server.
-   [ ] Có README và hướng dẫn chạy dự án.
-   [ ] Có hướng dẫn cấu hình môi trường production.

## 12. Bắt đầu thực hiện

Trước tiên, hãy **chỉ thực hiện Giai đoạn 1 và Giai đoạn 2**: phân tích
yêu cầu, đề xuất sitemap, luồng người dùng và định hướng UI/UX.

Sau khi trình bày, hãy nêu các điểm cần tôi xác nhận. Không bắt đầu
triển khai code toàn bộ cho đến khi cấu trúc và định hướng giao diện
được thống nhất.

Khi được duyệt, tiếp tục lần lượt các giai đoạn còn lại, cập nhật tiến
độ và ghi rõ những file đã tạo hoặc chỉnh sửa.
