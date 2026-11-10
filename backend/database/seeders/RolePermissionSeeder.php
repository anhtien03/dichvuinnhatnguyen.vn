<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Define all permissions
        $permissions = [
            // Đơn hàng & Sản xuất (orders)
            [
                'slug' => 'orders.view',
                'name' => 'Xem danh sách & chi tiết đơn hàng',
                'group' => 'orders',
                'group_label' => 'Đơn Hàng & Sản Xuất',
                'description' => 'Xem danh sách đơn hàng, xem tiến độ sản xuất, tìm kiếm, in phiếu giao hàng.'
            ],
            [
                'slug' => 'orders.create',
                'name' => 'Tạo đơn hàng mới',
                'group' => 'orders',
                'group_label' => 'Đơn Hàng & Sản Xuất',
                'description' => 'Nhập đơn mới từ khách lẻ/đại lý, chọn quy cách, tính tiền, tạo lịch hẹn giao.'
            ],
            [
                'slug' => 'orders.edit',
                'name' => 'Chỉnh sửa đơn hàng',
                'group' => 'orders',
                'group_label' => 'Đơn Hàng & Sản Xuất',
                'description' => 'Sửa thông số in, đổi chất liệu, đổi địa chỉ nhận, điều chỉnh số tiền đơn hàng.'
            ],
            [
                'slug' => 'orders.stage',
                'name' => 'Cập nhật tiến độ sản xuất',
                'group' => 'orders',
                'group_label' => 'Đơn Hàng & Sản Xuất',
                'description' => 'Chuyển trạng thái: Chờ in -> Đang in -> Gia công -> Đã giao.'
            ],
            [
                'slug' => 'orders.payment',
                'name' => 'Thu tiền & Tạo mã VietQR',
                'group' => 'orders',
                'group_label' => 'Đơn Hàng & Sản Xuất',
                'description' => 'Ghi nhận khách cọc/thanh toán, tạo mã VietQR động để khách quét.'
            ],
            [
                'slug' => 'orders.delete',
                'name' => 'Hủy & Xóa đơn hàng (Nhạy cảm)',
                'group' => 'orders',
                'group_label' => 'Đơn Hàng & Sản Xuất',
                'description' => 'Xóa hẳn đơn hàng hoặc duyệt yêu cầu hủy đơn khỏi hệ thống.'
            ],

            // Khách hàng & CRM (clients)
            [
                'slug' => 'clients.view',
                'name' => 'Xem danh bạ khách hàng',
                'group' => 'clients',
                'group_label' => 'Khách Hàng & CRM Zalo',
                'description' => 'Xem danh sách khách, số điện thoại, lịch sử đặt in và công nợ.'
            ],
            [
                'slug' => 'clients.manage',
                'name' => 'Thêm & Sửa khách hàng',
                'group' => 'clients',
                'group_label' => 'Khách Hàng & CRM Zalo',
                'description' => 'Tạo khách hàng mới, cập nhật địa chỉ, phân loại khách sỉ/lẻ, Import Excel.'
            ],
            [
                'slug' => 'clients.care',
                'name' => 'Chăm sóc khách Zalo (Daily Care)',
                'group' => 'clients',
                'group_label' => 'Khách Hàng & CRM Zalo',
                'description' => 'Xem danh sách Khách cần chăm sóc hôm nay, click nhắn Zalo trực tiếp.'
            ],
            [
                'slug' => 'clients.delete',
                'name' => 'Xóa hồ sơ khách hàng',
                'group' => 'clients',
                'group_label' => 'Khách Hàng & CRM Zalo',
                'description' => 'Xóa hồ sơ khách hàng khỏi hệ thống.'
            ],

            // Sổ quỹ & Công nợ xưởng (cashbook)
            [
                'slug' => 'cashbook.view',
                'name' => 'Xem sổ quỹ thu chi',
                'group' => 'cashbook',
                'group_label' => 'Sổ Quỹ & Công Nợ Xưởng',
                'description' => 'Xem dòng tiền thực tế, số dư tiền mặt/ngân hàng, thống kê ngày/tháng.'
            ],
            [
                'slug' => 'cashbook.create',
                'name' => 'Lập phiếu thu / chi tiền',
                'group' => 'cashbook',
                'group_label' => 'Sổ Quỹ & Công Nợ Xưởng',
                'description' => 'Tạo phiếu thu tiền ngoài, lập phiếu chi tiền giấy, mực, điện, mặt bằng.'
            ],
            [
                'slug' => 'cashbook.manage',
                'name' => 'Sửa / Hủy phiếu thu chi',
                'group' => 'cashbook',
                'group_label' => 'Sổ Quỹ & Công Nợ Xưởng',
                'description' => 'Điều chỉnh hoặc hủy phiếu thu chi sai.'
            ],
            [
                'slug' => 'suppliers.view',
                'name' => 'Xem xưởng phụ & công nợ',
                'group' => 'cashbook',
                'group_label' => 'Sổ Quỹ & Công Nợ Xưởng',
                'description' => 'Xem danh sách xưởng gia công ngoài, nhà cung cấp giấy/mực và số nợ.'
            ],
            [
                'slug' => 'suppliers.manage',
                'name' => 'Thanh toán công nợ xưởng ngoài',
                'group' => 'cashbook',
                'group_label' => 'Sổ Quỹ & Công Nợ Xưởng',
                'description' => 'Ghi nhận chi phí gia công, lập phiếu xuất tiền trả nợ xưởng ngoài.'
            ],

            // Tài chính & Chia lãi (finance)
            [
                'slug' => 'finance.dashboard',
                'name' => 'Báo cáo tổng quan kinh doanh',
                'group' => 'finance',
                'group_label' => 'Tài Chính & Lợi Nhuận',
                'description' => 'Xem biểu đồ doanh thu, tổng chi phí vốn và lợi nhuận gộp.'
            ],
            [
                'slug' => 'finance.profit_sharing',
                'name' => 'Bảng chia lãi & Cổ tức (Tối mật)',
                'group' => 'finance',
                'group_label' => 'Tài Chính & Lợi Nhuận',
                'description' => 'Xem và cấu hình bảng tính chia lợi nhuận ròng giữa các thành viên góp vốn.'
            ],

            // Đơn báo giá trực tuyến (quotes)
            [
                'slug' => 'quotes.view',
                'name' => 'Xem danh sách yêu cầu báo giá',
                'group' => 'quotes',
                'group_label' => 'Yêu Cầu Báo Giá Website',
                'description' => 'Xem danh sách khách đặt yêu cầu từ website, xem số Zalo/Hotline khách.'
            ],
            [
                'slug' => 'quotes.process',
                'name' => 'Xử lý báo giá & Tải file in',
                'group' => 'quotes',
                'group_label' => 'Yêu Cầu Báo Giá Website',
                'description' => 'Tải file thiết kế khách đính kèm, cập nhật trạng thái Đã liên hệ / Đã báo giá.'
            ],
            [
                'slug' => 'quotes.delete',
                'name' => 'Xóa yêu cầu báo giá spam',
                'group' => 'quotes',
                'group_label' => 'Yêu Cầu Báo Giá Website',
                'description' => 'Xóa các yêu cầu báo giá rác, kiểm tra file rác.'
            ],

            // Sản phẩm, Bảng giá & Website (catalog)
            [
                'slug' => 'products.manage',
                'name' => 'Quản lý ấn phẩm in ấn',
                'group' => 'catalog',
                'group_label' => 'Sản Phẩm & Website',
                'description' => 'Thêm, sửa, xóa sản phẩm, tải ảnh mẫu, cấu hình bài viết chuẩn SEO.'
            ],
            [
                'slug' => 'pricing.manage',
                'name' => 'Cấu hình Pricing Studio',
                'group' => 'catalog',
                'group_label' => 'Sản Phẩm & Website',
                'description' => 'Bật/tắt chế độ tính giá tự động, ẩn bảng giá (chuyển sang báo giá riêng).'
            ],
            [
                'slug' => 'categories.manage',
                'name' => 'Quản lý danh mục in ấn',
                'group' => 'catalog',
                'group_label' => 'Sản Phẩm & Website',
                'description' => 'Thêm, sửa nhóm ấn phẩm (Bao bì, Tờ rơi, Decal...).'
            ],
            [
                'slug' => 'banners.manage',
                'name' => 'Quản lý Banner & Slider',
                'group' => 'catalog',
                'group_label' => 'Sản Phẩm & Website',
                'description' => 'Thay đổi banner trang chủ, kéo thả vị trí hiển thị.'
            ],
            [
                'slug' => 'posts.manage',
                'name' => 'Bài viết & Cẩm nang',
                'group' => 'catalog',
                'group_label' => 'Sản Phẩm & Website',
                'description' => 'Viết bài chia sẻ kỹ thuật in, tin tức ngành in, xuất bản bài viết.'
            ],
            [
                'slug' => 'media.manage',
                'name' => 'Thư viện Media & Tải ảnh',
                'group' => 'catalog',
                'group_label' => 'Sản Phẩm & Website',
                'description' => 'Upload ảnh, tạo thư mục lưu trữ file trên server.'
            ],
            [
                'slug' => 'seo_indexing.manage',
                'name' => 'Google Indexing API',
                'group' => 'catalog',
                'group_label' => 'Sản Phẩm & Website',
                'description' => 'Gửi URL sản phẩm / bài viết mới lên Google để index lấy top tìm kiếm.'
            ],

            // Hệ thống & Nhân sự (system)
            [
                'slug' => 'users.view',
                'name' => 'Xem danh sách nhân sự',
                'group' => 'system',
                'group_label' => 'Hệ Thống & Phân Quyền',
                'description' => 'Xem danh sách tài khoản nhân viên trong xưởng, email, vai trò.'
            ],
            [
                'slug' => 'users.manage',
                'name' => 'Quản lý Nhân sự & Phân quyền',
                'group' => 'system',
                'group_label' => 'Hệ Thống & Phân Quyền',
                'description' => 'Tạo nhân viên mới, phân quyền, đổi mật khẩu, kích hoạt/khóa tài khoản.'
            ],
            [
                'slug' => 'settings.manage',
                'name' => 'Cài đặt Xưởng & Ngân hàng',
                'group' => 'system',
                'group_label' => 'Hệ Thống & Phân Quyền',
                'description' => 'Sửa Hotline, Zalo, địa chỉ xưởng, số tài khoản ngân hàng nhận tiền QR.'
            ],
        ];

        $permissionMap = [];
        foreach ($permissions as $p) {
            $created = Permission::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'name' => $p['name'],
                    'group' => $p['group'],
                    'group_label' => $p['group_label'],
                    'description' => $p['description'],
                ]
            );
            $permissionMap[$p['slug']] = $created->id;
        }

        // 2. Define 5 Standard Roles
        $roles = [
            [
                'name' => 'Super Admin (Giám Đốc)',
                'slug' => 'super_admin',
                'color' => 'purple',
                'description' => 'Toàn quyền kiểm soát và vận hành hệ thống xưởng in.',
                'is_system' => true,
                'permissions' => array_keys($permissionMap), // All permissions
            ],
            [
                'name' => 'Kinh Doanh / CSKH',
                'slug' => 'sales',
                'color' => 'blue',
                'description' => 'Tư vấn báo giá, tiếp nhận khách hàng và lên đơn đặt in.',
                'is_system' => true,
                'permissions' => [
                    'orders.view', 'orders.create', 'orders.edit', 'orders.payment',
                    'clients.view', 'clients.manage', 'clients.care',
                    'quotes.view', 'quotes.process',
                    'products.manage', 'categories.manage'
                ],
            ],
            [
                'name' => 'Kỹ Thuật Xưởng / Sản Xuất',
                'slug' => 'production',
                'color' => 'amber',
                'description' => 'Kiểm tra file in, điều phối máy in, cập nhật tiến độ gia công.',
                'is_system' => true,
                'permissions' => [
                    'orders.view', 'orders.stage',
                    'quotes.view', 'quotes.process',
                    'suppliers.view'
                ],
            ],
            [
                'name' => 'Kế Toán / Thủ Quỹ',
                'slug' => 'accountant',
                'color' => 'emerald',
                'description' => 'Quản lý thu chi dòng tiền, công nợ xưởng phụ, xác nhận thanh toán đơn.',
                'is_system' => true,
                'permissions' => [
                    'orders.view', 'orders.payment',
                    'clients.view',
                    'cashbook.view', 'cashbook.create', 'cashbook.manage',
                    'suppliers.view', 'suppliers.manage',
                    'finance.dashboard'
                ],
            ],
            [
                'name' => 'Biên Tập / Content Marketing',
                'slug' => 'content',
                'color' => 'pink',
                'description' => 'Quản trị nội dung website, viết cẩm nang in, đăng ảnh banner và SEO.',
                'is_system' => true,
                'permissions' => [
                    'products.manage', 'pricing.manage', 'categories.manage',
                    'banners.manage', 'posts.manage', 'media.manage', 'seo_indexing.manage'
                ],
            ],
            [
                'name' => 'Cộng Tác Viên Viết Bài (Writer)',
                'slug' => 'writer',
                'color' => 'indigo',
                'description' => 'Thuê ngoài viết bài SEO/Sales. Chỉ được viết & quản lý bài viết và tải ảnh minh họa. Bị ẩn 100% sản phẩm, bảng giá, banner, đơn hàng, khách hàng và hệ thống.',
                'is_system' => true,
                'permissions' => [
                    'posts.manage', 'media.manage'
                ],
            ],
        ];

        foreach ($roles as $r) {
            $perms = $r['permissions'];
            unset($r['permissions']);

            $role = Role::updateOrCreate(
                ['slug' => $r['slug']],
                $r
            );

            // Sync permissions
            $permIds = [];
            foreach ($perms as $pSlug) {
                if (isset($permissionMap[$pSlug])) {
                    $permIds[] = $permissionMap[$pSlug];
                }
            }
            $role->permissions()->sync($permIds);
        }

        // 3. Assign existing user 1 to Super Admin
        $superAdminRole = Role::where('slug', 'super_admin')->first();
        if ($superAdminRole) {
            $user = User::first();
            if ($user && !$user->role_id) {
                $user->role_id = $superAdminRole->id;
                $user->phone = '0359 005 469';
                $user->is_active = true;
                $user->save();
            }
        }
    }
}

