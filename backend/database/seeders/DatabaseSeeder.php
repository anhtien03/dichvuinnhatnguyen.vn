<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\QuoteRequest;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@innhatnguyen.vn'],
            [
                'name' => 'Quản Trị Viên Nhật Nguyên',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Seed Categories
        $categoriesData = [
            [
                'slug' => 'an-pham-van-phong',
                'name' => 'Ấn Phẩm Văn Phòng',
                'description' => 'Namecard, bao thư, tiêu đề thư, kẹp file hồ sơ chuyên nghiệp chuẩn nhận diện thương hiệu.',
                'icon' => 'Briefcase',
                'badge' => 'In nhanh',
                'sort_order' => 1,
            ],
            [
                'slug' => 'tiep-thi-quang-cao',
                'name' => 'Tiếp Thị & Quảng Cáo',
                'description' => 'Tờ rơi, brochure, catalogue, voucher, menu bảng giá sắc nét, thu hút khách hàng.',
                'icon' => 'Megaphone',
                'badge' => 'Bán chạy',
                'sort_order' => 2,
            ],
            [
                'slug' => 'tem-nhan-decal',
                'name' => 'Tem Nhãn & Decal',
                'description' => 'Decal giấy, decal nhựa chống nước, tem bảo hành bể, tem xi bạc bế khuôn theo yêu cầu.',
                'icon' => 'Tag',
                'badge' => 'Chống nước',
                'sort_order' => 3,
            ],
            [
                'slug' => 'bao-bi-tui-giay',
                'name' => 'Bao Bì & Hộp Giấy',
                'description' => 'Hộp cứng cao cấp, hộp giấy duplex, túi giấy kraft thân thiện môi trường in offset.',
                'icon' => 'Package',
                'badge' => 'Giá xưởng',
                'sort_order' => 4,
            ],
            [
                'slug' => 'su-kien-trien-lam',
                'name' => 'Sự Kiện & Triển Lãm',
                'description' => 'Standee chữ X, standee cuốn nhôm, băng rôn hiflex, backdrop hội nghị lấy liền.',
                'icon' => 'Flag',
                'badge' => 'Lấy ngay',
                'sort_order' => 5,
            ],
            [
                'slug' => 'lich-so-tay',
                'name' => 'Lịch & Quà Tặng',
                'description' => 'Lịch để bàn, lịch bloc, sổ tay bìa da, bao lì xì doanh nghiệp thiết kế độc quyền.',
                'icon' => 'Calendar',
                'badge' => 'Quà tặng',
                'sort_order' => 6,
            ],
        ];

        $categoryMap = [];
        foreach ($categoriesData as $cat) {
            $created = Category::updateOrCreate(['slug' => $cat['slug']], $cat);
            $categoryMap[$cat['slug']] = $created->id;
        }

        // 3. Seed Products
        $productsData = [
            [
                'slug' => 'in-tui-giay-cao-cap',
                'name' => 'In Túi Giấy Cao Cấp',
                'category_slug' => 'bao-bi-tui-giay',
                'tag' => 'Số lượng ít',
                'base_price' => 5000,
                'unit' => 'Cái',
                'turnaround' => '2 - 4 ngày',
                'min_quantity' => 50,
                'description' => 'Túi giấy shop thời trang, mỹ phẩm, quà tặng doanh nghiệp. Đáy bồi dày chịu lực tốt, in màu chuẩn sắc nét.',
                'image' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=800&q=80',
                'is_popular' => true,
                'sort_order' => 1,
                'specs' => [
                    'sizes' => [
                        ['label' => 'Size Nhỏ (15 x 20 x 7 cm)', 'value' => 'small', 'extra' => 0],
                        ['label' => 'Size Vừa (22 x 30 x 10 cm)', 'value' => 'medium', 'extra' => 1500],
                        ['label' => 'Size Lớn (30 x 40 x 12 cm)', 'value' => 'large', 'extra' => 3500],
                    ],
                    'materials' => [
                        ['label' => 'Giấy Kraft nâu 170gsm thân thiện môi trường', 'value' => 'kraft', 'extra' => 0],
                        ['label' => 'Giấy Ivory 250gsm cứng cáp, sang trọng', 'value' => 'ivory', 'extra' => 1200],
                        ['label' => 'Giấy Couche 250gsm cán màng mờ', 'value' => 'c250', 'extra' => 1000],
                    ],
                    'finishings' => [
                        ['label' => 'Quai dây dù tròn xỏ khuy kim loại', 'value' => 'rope', 'extra' => 0],
                        ['label' => 'Quai ruy băng lụa cao cấp', 'value' => 'ribbon', 'extra' => 800],
                        ['label' => 'Ép kim vàng / bạc logo 1 mặt', 'value' => 'foil', 'extra' => 1500],
                    ],
                    'quantities' => [
                        ['label' => '50 cái (In nhanh)', 'count' => 50, 'pricePerUnit' => 8500],
                        ['label' => '200 cái (Tiết kiệm)', 'count' => 200, 'pricePerUnit' => 6500],
                        ['label' => '500 cái (Giá xưởng)', 'count' => 500, 'pricePerUnit' => 5000],
                        ['label' => '1.000 cái (Giá sỉ)', 'count' => 1000, 'pricePerUnit' => 4200],
                    ],
                ],
                'features' => [
                    'Chịu tải trọng tốt lên tới 3 - 5kg nhờ đáy bồi bìa cứng',
                    'Đa dạng loại quai: dây dù, quai lụa, quai giấy gấp',
                    'In Offset chuẩn màu theo mã Pantone hoặc nhận diện thương hiệu',
                    'Hỗ trợ duyệt mẫu in thực tế trước khi chạy hàng loạt',
                ],
            ],
            [
                'slug' => 'in-hop-giay-hop-cung',
                'name' => 'In Hộp Giấy - Hộp Cứng',
                'category_slug' => 'bao-bi-tui-giay',
                'tag' => 'Giá xưởng',
                'base_price' => 600,
                'unit' => 'Hộp',
                'turnaround' => '3 - 5 ngày',
                'min_quantity' => 100,
                'description' => 'Hộp giấy đựng son môi, mỹ phẩm, thức ăn, hộp quà Tết. Bế khuôn chính xác, gia công ép kim cao cấp.',
                'image' => 'https://images.unsplash.com/photo-1530587191325-3db32d826c18?auto=format&fit=crop&w=800&q=80',
                'is_popular' => true,
                'sort_order' => 2,
                'specs' => [
                    'sizes' => [
                        ['label' => 'Hộp son / mỹ phẩm mini (3x3x10 cm)', 'value' => 'mini', 'extra' => 0],
                        ['label' => 'Hộp kem / serum (6x6x12 cm)', 'value' => 'medium', 'extra' => 300],
                        ['label' => 'Hộp quà nắp nam châm theo yêu cầu', 'value' => 'rigid_custom', 'extra' => 15000],
                    ],
                    'materials' => [
                        ['label' => 'Giấy Ivory 300gsm trắng đẹp 2 mặt', 'value' => 'ivory300', 'extra' => 0],
                        ['label' => 'Giấy Duplex 350gsm mặt lưng xám tiết kiệm', 'value' => 'duplex350', 'extra' => -100],
                        ['label' => 'Carton cứng bồi giấy mỹ thuật sang trọng', 'value' => 'rigid_box', 'extra' => 18000],
                    ],
                    'finishings' => [
                        ['label' => 'Cán màng mờ + Bế dán thành phẩm', 'value' => 'matte_fold', 'extra' => 0],
                        ['label' => 'Ép kim logo + UV định hình cục bộ', 'value' => 'foil_uv', 'extra' => 1200],
                    ],
                    'quantities' => [
                        ['label' => '200 hộp', 'count' => 200, 'pricePerUnit' => 2500],
                        ['label' => '500 hộp', 'count' => 500, 'pricePerUnit' => 1200],
                        ['label' => '1.000 hộp (Khuyên dùng)', 'count' => 1000, 'pricePerUnit' => 850],
                        ['label' => '5.000 hộp (Giá gốc xuất xưởng)', 'count' => 5000, 'pricePerUnit' => 600],
                    ],
                ],
                'features' => [
                    'Bế gân chính xác, dễ dàng gấp dán không gãy mép',
                    'In offset nhiều màu sắc nét, không lem màu',
                    'Tùy chọn khay mút xốp hoặc khay giấy định hình bên trong',
                ],
            ],
            [
                'slug' => 'in-tag-treo-nhan-mac',
                'name' => 'In Tag Treo - Nhãn Mác',
                'category_slug' => 'tem-nhan-decal',
                'tag' => 'In nhanh 24h',
                'base_price' => 650,
                'unit' => 'Cái',
                'turnaround' => '24h',
                'min_quantity' => 200,
                'description' => 'Thẻ tag treo quần áo, tag cảm ơn khách hàng, mác giá phụ kiện. Đục lỗ xỏ dây sẵn, cắt mọi hình dáng.',
                'image' => 'https://images.unsplash.com/photo-1528459801416-a9e53bbf4e17?auto=format&fit=crop&w=800&q=80',
                'is_popular' => true,
                'sort_order' => 3,
                'specs' => [
                    'sizes' => [
                        ['label' => 'Hình chữ nhật chuẩn (4.5 x 8.5 cm)', 'value' => 'standard', 'extra' => 0],
                        ['label' => 'Hình vuông (5 x 5 cm)', 'value' => 'square', 'extra' => 0],
                        ['label' => 'Bế hình tròn / oval / bo góc / chuông', 'value' => 'die_cut', 'extra' => 150],
                    ],
                    'materials' => [
                        ['label' => 'Giấy Couche 300gsm cán màng mờ', 'value' => 'c300', 'extra' => 0],
                        ['label' => 'Giấy Kraft vintage nâu dày', 'value' => 'kraft', 'extra' => 100],
                        ['label' => 'Giấy Bồi 2 lớp dày 600gsm cao cấp', 'value' => 'thick600', 'extra' => 500],
                    ],
                    'finishings' => [
                        ['label' => 'Đục lỗ xỏ dây tròn 3mm hoặc 5mm', 'value' => 'punch_hole', 'extra' => 0],
                        ['label' => 'Đục lỗ + Tặng kèm dây dù đen gài sẵn', 'value' => 'with_string', 'extra' => 100],
                    ],
                    'quantities' => [
                        ['label' => '200 cái', 'count' => 200, 'pricePerUnit' => 1200],
                        ['label' => '500 cái', 'count' => 500, 'pricePerUnit' => 850],
                        ['label' => '1.000 cái', 'count' => 1000, 'pricePerUnit' => 650],
                        ['label' => '2.000 cái', 'count' => 2000, 'pricePerUnit' => 450],
                    ],
                ],
                'features' => [
                    'Cắt bế theo mọi hình dáng độc quyền',
                    'Đục lỗ sẵn tiện lợi cho việc xỏ dây thẻ treo',
                    'Nhận in số lượng ít chỉ từ 200 cái',
                ],
            ],
            [
                'slug' => 'in-danh-thiep-namecard',
                'name' => 'In Danh Thiếp - Namecard',
                'category_slug' => 'an-pham-van-phong',
                'tag' => 'Chuẩn màu Offset',
                'base_price' => 90000,
                'unit' => 'Hộp',
                'turnaround' => '24h - 48h',
                'min_quantity' => 2,
                'description' => 'Danh thiếp cá nhân, giám đốc, card visit công ty in ghép bài offset sắc nét, tặng kèm hộp nhựa mica.',
                'image' => 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=800&q=80',
                'is_popular' => true,
                'sort_order' => 4,
                'specs' => [
                    'sizes' => [
                        ['label' => 'Tiêu chuẩn (9.0 x 5.4 cm)', 'value' => 'standard', 'extra' => 0],
                        ['label' => 'Bo tròn 4 góc (+30k/hộp)', 'value' => 'rounded', 'extra' => 30000],
                    ],
                    'materials' => [
                        ['label' => 'Giấy Couche 300gsm cán màng mờ', 'value' => 'c300', 'extra' => 0],
                        ['label' => 'Giấy Mỹ Thuật sần sang trọng', 'value' => 'art', 'extra' => 70000],
                        ['label' => 'Nhựa Plastic xé không rách', 'value' => 'plastic', 'extra' => 120000],
                    ],
                    'finishings' => [
                        ['label' => 'Cán màng mờ 2 mặt bảo vệ', 'value' => 'matte', 'extra' => 0],
                        ['label' => 'Ép kim vàng / bạc logo 2 mặt', 'value' => 'foil', 'extra' => 120000],
                    ],
                    'quantities' => [
                        ['label' => '2 hộp (200 cái)', 'count' => 2, 'pricePerUnit' => 90000],
                        ['label' => '5 hộp (500 cái)', 'count' => 5, 'pricePerUnit' => 50000],
                        ['label' => '10 hộp (1.000 cái)', 'count' => 10, 'pricePerUnit' => 35000],
                    ],
                ],
                'features' => [
                    'Công nghệ in ghép bài Offset công nghiệp chuẩn màu',
                    'Đóng hộp mica trong suốt 100 cái/hộp',
                    'Miễn phí kiểm tra và sửa file',
                ],
            ],
            [
                'slug' => 'in-bao-thu-phong-bi',
                'name' => 'In Bao Thư - Phong Bì',
                'category_slug' => 'an-pham-van-phong',
                'tag' => 'Bản in sắc nét',
                'base_price' => 2600,
                'unit' => 'Cái',
                'turnaround' => '2 - 3 ngày',
                'min_quantity' => 200,
                'description' => 'Bao thư nhỏ (A6, A5), bao thư lớn (A4) gửi hợp đồng công ty. Có dán sẵn băng keo 2 mặt tiện lợi.',
                'image' => 'https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?auto=format&fit=crop&w=800&q=80',
                'is_popular' => true,
                'sort_order' => 5,
                'specs' => [
                    'sizes' => [
                        ['label' => 'Bao thư nhỏ A6 (12 x 22 cm)', 'value' => 'a6', 'extra' => 0],
                        ['label' => 'Bao thư trung A5 (16 x 23 cm)', 'value' => 'a5', 'extra' => 500],
                        ['label' => 'Bao thư lớn A4 (25 x 35 cm) đựng tài liệu', 'value' => 'a4', 'extra' => 1800],
                    ],
                    'materials' => [
                        ['label' => 'Giấy Fort 100gsm tiêu chuẩn', 'value' => 'fort100', 'extra' => 0],
                        ['label' => 'Giấy Fort 120gsm dày dặn, không lộ chữ', 'value' => 'fort120', 'extra' => 300],
                        ['label' => 'Giấy Kraft nâu Nhật cổ điển', 'value' => 'kraft', 'extra' => 400],
                    ],
                    'finishings' => [
                        ['label' => 'Dán sẵn keo nắp chờ bóc dán', 'value' => 'tape', 'extra' => 0],
                        ['label' => 'Cửa sổ kiếng trong suốt xem địa chỉ', 'value' => 'window', 'extra' => 600],
                    ],
                    'quantities' => [
                        ['label' => '200 cái', 'count' => 200, 'pricePerUnit' => 3500],
                        ['label' => '500 cái', 'count' => 500, 'pricePerUnit' => 2600],
                        ['label' => '1.000 cái', 'count' => 1000, 'pricePerUnit' => 1900],
                    ],
                ],
                'features' => [
                    'Dán sẵn băng keo nắp đóng gói cực nhanh',
                    'In logo, thông tin công ty sắc nét chuẩn màu thương hiệu',
                    'Giấy dầy không bị thấu chữ tài liệu bên trong',
                ],
            ],
            [
                'slug' => 'in-to-roi-brochure',
                'name' => 'In Tờ Rơi - Flyer - Brochure',
                'category_slug' => 'tiep-thi-quang-cao',
                'tag' => 'In nhanh',
                'base_price' => 450,
                'unit' => 'Tờ',
                'turnaround' => '1 - 2 ngày',
                'min_quantity' => 500,
                'description' => 'Tờ rơi quảng cáo A4, A5 in offset màu sắc tươi sáng, định lượng giấy đa dạng từ C100 đến C250.',
                'image' => 'https://images.unsplash.com/photo-1541701494587-cb58502866ab?auto=format&fit=crop&w=800&q=80',
                'is_popular' => false,
                'sort_order' => 6,
                'specs' => [
                    'sizes' => [
                        ['label' => 'Khổ A5 (14.8 x 21.0 cm)', 'value' => 'a5', 'extra' => 0],
                        ['label' => 'Khổ A4 (21.0 x 29.7 cm)', 'value' => 'a4', 'extra' => 450],
                    ],
                    'materials' => [
                        ['label' => 'Couche 150gsm (Phổ biến)', 'value' => 'c150', 'extra' => 0],
                        ['label' => 'Couche 200gsm (Dày dặn)', 'value' => 'c200', 'extra' => 200],
                    ],
                    'finishings' => [
                        ['label' => 'Không cán màng', 'value' => 'none', 'extra' => 0],
                        ['label' => 'Cấn 1 đường gấp đôi', 'value' => 'fold_1', 'extra' => 50],
                    ],
                    'quantities' => [
                        ['label' => '500 tờ', 'count' => 500, 'pricePerUnit' => 850],
                        ['label' => '1.000 tờ', 'count' => 1000, 'pricePerUnit' => 550],
                        ['label' => '2.000 tờ', 'count' => 2000, 'pricePerUnit' => 450],
                    ],
                ],
                'features' => [
                    'In offset 2 mặt màu sắc sống động',
                    'Mực in chống bay màu',
                ],
            ],
            [
                'slug' => 'in-tem-nhan-decal',
                'name' => 'In Tem Nhãn Decal - Sticker Bế Sẵn',
                'category_slug' => 'tem-nhan-decal',
                'tag' => 'Bế khuôn tự do',
                'base_price' => 150,
                'unit' => 'Con',
                'turnaround' => '24h - 48h',
                'min_quantity' => 500,
                'description' => 'Tem nhãn dán ly trà sữa, chai lọ mỹ phẩm, hộp thực phẩm. Cắt bế khuôn mọi hình dạng tròn, oval, chữ nhật.',
                'image' => 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675?auto=format&fit=crop&w=800&q=80',
                'is_popular' => false,
                'sort_order' => 7,
                'specs' => [
                    'sizes' => [
                        ['label' => 'Kích thước 3 x 3 cm', 'value' => '3x3', 'extra' => 0],
                        ['label' => 'Kích thước 5 x 5 cm', 'value' => '5x5', 'extra' => 80],
                    ],
                    'materials' => [
                        ['label' => 'Decal Giấy cán màng bóng', 'value' => 'paper_glossy', 'extra' => 0],
                        ['label' => 'Decal Nhựa PVC chống nước', 'value' => 'plastic_pvc', 'extra' => 120],
                    ],
                    'finishings' => [
                        ['label' => 'Cắt bế demi thành từng con dễ bóc', 'value' => 'kiss_cut', 'extra' => 0],
                    ],
                    'quantities' => [
                        ['label' => '500 con', 'count' => 500, 'pricePerUnit' => 300],
                        ['label' => '1.000 con', 'count' => 1000, 'pricePerUnit' => 180],
                        ['label' => '2.000 con', 'count' => 2000, 'pricePerUnit' => 150],
                    ],
                ],
                'features' => [
                    'Bế demi sẵn từng con tiện lợi bóc dán',
                    'Độ bám dính cực tốt trên mọi chất liệu nhựa, thủy tinh, giấy',
                ],
            ],
        ];

        foreach ($productsData as $prod) {
            $catSlug = $prod['category_slug'];
            unset($prod['category_slug']);
            $prod['category_id'] = $categoryMap[$catSlug] ?? null;
            Product::updateOrCreate(['slug' => $prod['slug']], $prod);
        }

        // 4. Seed Site Settings
        SiteSetting::set('site_name', 'In Ấn Nhật Nguyên', 'general');
        SiteSetting::set('brand_name', 'NHẬT NGUYÊN PRINTING', 'general');
        SiteSetting::set('slogan', 'Nhanh - Đẹp - Giá Tốt | Xưởng In Trực Tiếp Không Qua Trung Gian', 'general');
        SiteSetting::set('hotline_1', '0359 005 469', 'contact');
        SiteSetting::set('hotline_2', '076 759 8368', 'contact');
        SiteSetting::set('zalo', 'https://zalo.me/0359005469', 'contact');
        SiteSetting::set('email', 'nhatnguyenprinting@gmail.com', 'contact');
        SiteSetting::set('address_factory', 'Đông Hưng Thuận 40, P. Đông Hưng Thuận, Quận 12, TP.HCM', 'contact');
        SiteSetting::set('address_office', '167 Hà Huy Giáp, P. An Phú Đông, Quận 12, TP.HCM', 'contact');
        SiteSetting::set('working_hours', 'Thứ 2 - Chủ Nhật: 08:00 - 20:00 (Hỗ trợ tư vấn Zalo 24/7)', 'contact');
        SiteSetting::set('meta_title_default', 'Xưởng In Ấn Nhật Nguyên | In Offset & Kỹ Thuật Số Giá Gốc TPHCM', 'seo');
        SiteSetting::set('meta_description_default', 'Xưởng in trực tiếp Nhật Nguyên chuyên in túi giấy, hộp giấy, tem nhãn decal, catalogue, namecard giá gốc tại xưởng TPHCM. Hỗ trợ file và giao hàng nhanh.', 'seo');

        // 5. Seed Sample Quote Requests
        $sampleQuotes = [
            [
                'code' => 'NN-202611-001',
                'customer_name' => 'Nguyễn Minh Tuấn',
                'phone' => '0908 123 456',
                'email' => 'tuan.nguyen@brandv.vn',
                'company' => 'Công Ty CP Thương Mại BrandV',
                'product_name' => 'In Túi Giấy Cao Cấp',
                'quantity' => '500 cái',
                'specs_description' => 'Giấy Ivory 250gsm, quai dù tròn màu đen, cán màng mờ, ép kim logo màu vàng đồng 1 mặt. Cần nhận hàng trước thứ 6.',
                'status' => 'pending',
                'quoted_price' => null,
                'admin_notes' => 'Đã gọi tư vấn sơ bộ, khách đang gửi lại file vector logo.',
            ],
            [
                'code' => 'NN-202611-002',
                'customer_name' => 'Chị Mai Lan',
                'phone' => '0938 789 012',
                'email' => 'lanmai.spa@gmail.com',
                'company' => 'Lan Spa & Cosmetics',
                'product_name' => 'In Hộp Giấy - Hộp Cứng',
                'quantity' => '1.000 hộp',
                'specs_description' => 'Hộp đựng serum 30ml, giấy Ivory 350gsm bế nắp khóa gài, dập nổi tên thương hiệu.',
                'status' => 'contacted',
                'quoted_price' => 1250000,
                'admin_notes' => 'Đã gửi báo giá qua Zalo 1.250.000đ cho 1000 hộp. Chờ khách duyệt mẫu.',
            ],
            [
                'code' => 'NN-202611-003',
                'customer_name' => 'Anh Hoàng Bách',
                'phone' => '0919 654 321',
                'email' => 'bach@startupvn.com',
                'company' => 'Startup Studio',
                'product_name' => 'In Danh Thiếp - Namecard',
                'quantity' => '5 hộp',
                'specs_description' => 'Giấy mỹ thuật sần, bo tròn 4 góc cho 2 nhân sự.',
                'status' => 'approved',
                'quoted_price' => 350000,
                'admin_notes' => 'Khách đã cọc 50%, đang ghép bài in offset.',
            ],
        ];

        foreach ($sampleQuotes as $quote) {
            QuoteRequest::updateOrCreate(['code' => $quote['code']], $quote);
        }

        // 6. Seed Default Banners
        $sampleBanners = [
            [
                'title' => 'Xưởng In Nhật Nguyên - Bạn Cần In Có Ngay Trong Ngày',
                'subtitle' => 'In nhanh 24h, In Offset & Kỹ Thuật Số sắc nét chuẩn màu xưởng trực tiếp',
                'badge' => 'IN NHANH CHUYÊN NGHIỆP',
                'image_url' => '/banner-chinh.jpg',
                'mobile_image_url' => null,
                'link_url' => '/yeu-cau-bao-gia',
                'button_text' => 'Nhận Báo Giá Ngay',
                'extra_text' => 'Hỗ trợ thiết kế & kiểm tra file miễn phí',
                'position' => 'home_main',
                'bg_color' => 'blue',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'IN NHANH',
                'subtitle' => 'online',
                'badge' => 'CÔNG NGHỆ KTS 24H',
                'image_url' => null,
                'mobile_image_url' => null,
                'link_url' => '/yeu-cau-bao-gia',
                'button_text' => 'Đặt ngay →',
                'extra_text' => 'Báo giá tức thì 24/7',
                'position' => 'home_sub_1',
                'bg_color' => 'blue',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'MẪU TEM IN HỘT TRẮNG',
                'subtitle' => 'SUPER BUBBLE - Decal nhựa, xi bạc, tem bế demi bóc dán',
                'badge' => 'MẪU TEM HOT',
                'image_url' => null,
                'mobile_image_url' => null,
                'link_url' => '/san-pham/in-tem-nhan-decal-be-thanh-pham',
                'button_text' => 'Xem mẫu →',
                'extra_text' => 'Bế hình theo yêu cầu',
                'position' => 'home_sub_2',
                'bg_color' => 'amber',
                'sort_order' => 1,
                'is_active' => true,
            ],
        ];

        // Clear existing and re-seed
        Banner::truncate();
        foreach ($sampleBanners as $banner) {
            Banner::create($banner);
        }

        // 7. Seed Initial Posts
        $samplePosts = [
            [
                'title' => 'Phân biệt giấy Ford, Couche, Ivory, Bristol khi đặt in',
                'slug' => 'phan-biet-giay-ford-couche-ivory-bristol-khi-dat-in',
                'category' => 'Cẩm nang in ấn',
                'excerpt' => 'Hướng dẫn chi tiết cách nhận biết định lượng GSM và đặc tính bề mặt của các loại giấy in phổ biến nhất hiện nay trên thị trường.',
                'content' => '<h3>1. Giấy Couche (C) - Bóng mịn và chuẩn màu</h3><p>Giấy Couche là loại giấy được tráng phủ cao lanh cả hai mặt, bề mặt láng mịn, bắt sáng tốt. Rất phù hợp để in catalogue, tờ rơi, brochure, namecard.</p><h3>2. Giấy Ivory - Cứng cáp, chịu lực cho bao bì hộp</h3><p>Giấy Ivory có một mặt láng bóng và một mặt nhám sần nhẹ. Độ đanh và độ xốp cao, là lựa chọn số 1 cho in hộp mỹ phẩm, hộp thuốc, túi giấy cao cấp.</p><h3>3. Giấy Ford - Thấm mực tự nhiên, viết tay dễ dàng</h3><p>Giấy Ford không tráng phủ, bề mặt nhám như giấy photocopy thông thường. Chuyên dùng in bao thư, tiêu đề thư, sổ tay, hóa đơn bán lẻ.</p>',
                'thumbnail' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=800&q=80',
                'is_published' => true,
                'published_at' => now()->subDays(2),
                'meta_title' => 'Phân Biệt Giấy Ford, Couche, Ivory, Bristol Chi Tiết Từ Xưởng In',
                'meta_description' => 'Tìm hiểu chi tiết ưu nhược điểm và ứng dụng của giấy Couche, Ivory, Ford, Bristol giúp doanh nghiệp tối ưu chi phí in ấn tại Nhật Nguyên.',
            ],
            [
                'title' => 'Cách xuất file in chuẩn hệ màu CMYK và chừa lề cắt xén 2mm',
                'slug' => 'cach-xuat-file-in-chuan-he-mau-cmyk-va-chua-le-cat-xen-2mm',
                'category' => 'Kỹ thuật xuất file',
                'excerpt' => 'Tránh lỗi lệch màu và cắt phạm vào chữ khi in ấn với các quy tắc vàng thiết lập bleed và chuyển đổi Profile màu CMYK trong Adobe Illustrator/Photoshop.',
                'content' => '<h3>Tại sao phải chừa tràn lề (Bleed) tối thiểu 2mm?</h3><p>Khi gia công cắt xén thành phẩm hàng loạt bằng máy xén tự động, dao xén có thể xê dịch từ 0.5 - 1mm. Việc chừa tràn viền 2mm mỗi cạnh giúp thành phẩm sau khi cắt không bị viền trắng ngoài ý muốn.</p><h3>Hệ màu CMYK so với RGB</h3><p>Màn hình hiển thị ánh sáng quang học RGB, trong khi máy in offset và KTS sử dụng hạt mực phản xạ ánh sáng CMYK. Luôn chuyển hệ màu sang CMYK trước khi gửi xưởng in để màu sắc chuẩn xác nhất.</p>',
                'thumbnail' => 'https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=800&q=80',
                'is_published' => true,
                'published_at' => now()->subDays(5),
                'meta_title' => 'Cách Xuất File In Chuẩn Hệ Màu CMYK & Bleed 2mm Không Lỗi',
                'meta_description' => 'Hướng dẫn kỹ thuật viên và designer xuất file in vector PDF/AI chuẩn hệ màu CMYK và chừa tràn lề cắt bế chuẩn xác xưởng in Nhật Nguyên.',
            ],
            [
                'title' => 'Bí quyết chọn kích thước Namecard và Catalogue chuẩn phong thủy',
                'slug' => 'bi-quyet-chon-kich-thuoc-namecard-va-catalogue-chuan-phong-thuy',
                'category' => 'Xu hướng thiết kế',
                'excerpt' => 'Kích thước tiêu chuẩn quốc tế cho danh thiếp và hồ sơ năng lực giúp doanh nghiệp tạo ấn tượng chuyên nghiệp trong mắt đối tác kinh doanh.',
                'content' => '<h3>Kích thước namecard chuẩn tại Việt Nam</h3><p>Kích thước chuẩn nhất là 9.0 x 5.4 cm hoặc 9.0 x 5.0 cm. Kích thước này vừa vặn với mọi loại ví tiền và hộp đựng card visit chuẩn quốc tế.</p><h3>Kích thước Catalogue tối ưu chi phí</h3><p>Kích thước A4 (21 x 29.7 cm) và A5 (14.8 x 21 cm) là tối ưu chi phí nhất vì vừa khổ khổ giấy in tiêu chuẩn, không bị hao giấy thừa.</p>',
                'thumbnail' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=800&q=80',
                'is_published' => true,
                'published_at' => now()->subDays(8),
                'meta_title' => 'Kích Thước Namecard & Catalogue Tiêu Chuẩn Doanh Nghiệp',
                'meta_description' => 'Cẩm nang tra cứu kích thước danh thiếp card visit, catalogue A4, A5 chuẩn quy cách và kinh nghiệm in ấn tiết kiệm chi phí.',
            ],
        ];

        foreach ($samplePosts as $post) {
            Post::updateOrCreate(['slug' => $post['slug']], $post);
        }
    }
}
