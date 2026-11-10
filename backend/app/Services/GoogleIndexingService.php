<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleIndexingService
{
    /**
     * Publish URL notification to Google Indexing API
     *
     * @param string $url
     * @param string $type 'URL_UPDATED' or 'URL_DELETED'
     * @return array
     */
    public function publishUrl(string $url, string $type = 'URL_UPDATED'): array
    {
        $credentials = $this->getCredentials();
        if (!$credentials) {
            return [
                'success' => false,
                'message' => 'Chưa cấu hình Google Service Account JSON trong Cài đặt Website.',
            ];
        }

        $accessToken = $this->getAccessToken($credentials);
        if (!$accessToken) {
            return [
                'success' => false,
                'message' => 'Không thể tạo Access Token từ Google Service Account. Vui lòng kiểm tra lại private_key và quyền hạn API.',
            ];
        }

        try {
            $response = Http::withToken($accessToken)
                ->timeout(15)
                ->post('https://indexing.googleapis.com/v3/urlNotifications:publish', [
                    'url' => $url,
                    'type' => $type,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message' => 'Đã gửi yêu cầu Index tới Google thành công!',
                    'url' => $url,
                    'type' => $type,
                    'notifyTime' => $data['urlNotificationMetadata']['latestUpdate']['notifyTime'] ?? now()->toIso8601String(),
                    'raw' => $data,
                ];
            }

            $errorData = $response->json();
            $errorMessage = $errorData['error']['message'] ?? $response->body();

            return [
                'success' => false,
                'message' => 'Lỗi từ Google: ' . $errorMessage,
                'status' => $response->status(),
                'raw' => $errorData,
            ];
        } catch (\Throwable $e) {
            Log::error('Google Indexing API Exception: ' . $e->getMessage(), ['url' => $url]);
            return [
                'success' => false,
                'message' => 'Lỗi kết nối tới Google Indexing API: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get URL notification metadata from Google Indexing API
     *
     * @param string $url
     * @return array
     */
    public function getUrlMetadata(string $url): array
    {
        $credentials = $this->getCredentials();
        if (!$credentials) {
            return ['success' => false, 'message' => 'Chưa cấu hình Google Service Account JSON.'];
        }

        $accessToken = $this->getAccessToken($credentials);
        if (!$accessToken) {
            return ['success' => false, 'message' => 'Không thể lấy Access Token từ Google.'];
        }

        try {
            $response = Http::withToken($accessToken)
                ->timeout(10)
                ->get('https://indexing.googleapis.com/v3/urlNotifications/metadata', [
                    'url' => $url,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['error']['message'] ?? 'Không tìm thấy dữ liệu.',
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get OAuth2 Access Token using Service Account JWT assertion
     */
    public function getAccessToken(?array $credentials = null): ?string
    {
        $credentials = $credentials ?? $this->getCredentials();
        if (!$credentials) {
            return null;
        }

        $clientEmail = $credentials['client_email'] ?? null;
        $privateKey = $credentials['private_key'] ?? null;

        if (!$clientEmail || !$privateKey) {
            return null;
        }

        $now = time();
        $jwtHeader = $this->base64UrlEncode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ]));

        $jwtClaim = $this->base64UrlEncode(json_encode([
            'iss' => $clientEmail,
            'scope' => 'https://www.googleapis.com/auth/indexing',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now,
        ]));

        $signatureInput = $jwtHeader . '.' . $jwtClaim;
        $signature = '';

        $keyResource = openssl_pkey_get_private($privateKey);
        if (!$keyResource) {
            Log::error('Invalid Google Service Account private key.');
            return null;
        }

        $success = openssl_sign($signatureInput, $signature, $keyResource, OPENSSL_ALGO_SHA256);
        if (!$success) {
            Log::error('Failed to sign JWT with private key.');
            return null;
        }

        $jwt = $signatureInput . '.' . $this->base64UrlEncode($signature);

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post('https://oauth2.googleapis.com/token', [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ]);

            if ($response->successful()) {
                return $response->json()['access_token'] ?? null;
            }

            Log::error('Google OAuth token request failed: ' . $response->body());
            return null;
        } catch (\Throwable $e) {
            Log::error('Exception requesting Google OAuth token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieve parsed credentials from settings or local file
     */
    public function getCredentials(): ?array
    {
        $raw = SiteSetting::get('google_indexing_service_account_json');
        if (empty($raw)) {
            $filePath = storage_path('app/google-service-account.json');
            if (file_exists($filePath)) {
                $raw = file_get_contents($filePath);
            }
        }

        if (empty($raw)) {
            return null;
        }

        if (is_array($raw)) {
            return $raw;
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : null;
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

