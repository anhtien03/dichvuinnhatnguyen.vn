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
     * Seed clean production database for In Ấn Nhật Nguyên.
     */
    public function run(): void
    {
        // 1. Setup Roles & Permissions first
        $this->call(RolePermissionSeeder::class);

        // 2. Create/Update Super Admin User
        $superAdminRole = \App\Models\Role::where('slug', 'super_admin')->first();
        $admin = User::updateOrCreate(
            ['email' => 'admin@innhatnguyen.vn'],
            [
                'name' => 'Quản Trị Viên Nhật Nguyên',
                'password' => Hash::make('admin123'),
                'phone' => '0359 005 469',
                'role_id' => $superAdminRole?->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 3. Seed Standard Categories (Khung danh mục chuẩn của xưởng in)
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

        foreach ($categoriesData as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 4. Seed Official Site Settings (Thông tin xưởng in chuẩn)
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

        // 5. Seed Real Banner with Local Asset
        Banner::truncate();
        Banner::create([
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
        ]);
        Banner::create([
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
        ]);
        Banner::create([
            'title' => 'MẪU TEM IN HỘT TRẮNG',
            'subtitle' => 'SUPER BUBBLE - Decal nhựa, xi bạc, tem bế demi bóc dán',
            'badge' => 'MẪU TEM HOT',
            'image_url' => null,
            'mobile_image_url' => null,
            'link_url' => '/san-pham',
            'button_text' => 'Xem mẫu →',
            'extra_text' => 'Bế hình theo yêu cầu',
            'position' => 'home_sub_2',
            'bg_color' => 'amber',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Clean any dummy test data safely without foreign key conflicts
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        QuoteRequest::truncate();
        Product::truncate();
        Post::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}
