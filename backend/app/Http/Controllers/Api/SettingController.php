<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::all()->mapWithKeys(function ($item) {
            $decoded = json_decode($item->value, true);
            $val = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $item->value;
            return [$item->key => $val];
        })->toArray();

        // Nếu database chưa lưu mã google_site_verification, tự động đọc mã hiện tại từ file HTML
        // để trên giao diện Admin không bao giờ bị rỗng khi F5
        if (empty($settings['google_site_verification'])) {
            $targetFiles = [
                base_path('../dist/index.html'),
                base_path('../index.html'),
                public_path('index.html'),
            ];
            foreach ($targetFiles as $file) {
                if (file_exists($file)) {
                    $html = file_get_contents($file);
                    if (preg_match('/<meta\s+name=["\']google-site-verification["\']\s+content=["\']([^"\']+)["\']/i', $html, $m)) {
                        $settings['google_site_verification'] = $m[1];
                        break;
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->all();

        foreach ($data as $key => $value) {
            if ($key === '_token') continue;
            SiteSetting::set($key, $value);
        }

        // Tự động đồng bộ mã google_site_verification trực tiếp vào các file tĩnh HTML
        // để bot Google Search Console đọc được ngay mà không cần đợi JS SPA render
        if (isset($data['google_site_verification'])) {
            $code = trim($data['google_site_verification']);
            if (preg_match('/content=["\']([^"\']+)["\']/', $code, $matches)) {
                $code = $matches[1];
                SiteSetting::set('google_site_verification', $code);
            }

            $targetFiles = [
                base_path('../dist/index.html'),
                base_path('../index.html'),
                public_path('index.html'),
            ];

            foreach ($targetFiles as $filePath) {
                if (file_exists($filePath)) {
                    $html = file_get_contents($filePath);
                    if (str_contains($html, 'name="google-site-verification"')) {
                        $html = preg_replace(
                            '/<meta\s+name=["\']google-site-verification["\']\s+content=["\'][^"\']*["\']\s*\/?>/i',
                            '<meta name="google-site-verification" content="' . htmlspecialchars($code, ENT_QUOTES) . '" />',
                            $html
                        );
                    } elseif (!empty($code)) {
                        $html = str_replace(
                            '</head>',
                            '  <meta name="google-site-verification" content="' . htmlspecialchars($code, ENT_QUOTES) . '" />' . "\n</head>",
                            $html
                        );
                    }
                    @file_put_contents($filePath, $html);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật cấu hình website thành công',
            'data' => $this->index()->original['data'],
        ]);
    }
}

