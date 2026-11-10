<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\GoogleIndexingService;
use Illuminate\Http\Request;

class GoogleIndexingController extends Controller
{
    protected GoogleIndexingService $indexingService;

    public function __construct(GoogleIndexingService $indexingService)
    {
        $this->indexingService = $indexingService;
    }

    /**
     * Get Google Indexing status and configurations
     */
    public function getConfig()
    {
        $credentials = $this->indexingService->getCredentials();
        $autoPublish = (bool) SiteSetting::get('google_indexing_auto_publish', true);

        return response()->json([
            'success' => true,
            'data' => [
                'is_configured' => !empty($credentials),
                'client_email' => $credentials['client_email'] ?? null,
                'project_id' => $credentials['project_id'] ?? null,
                'auto_publish' => $autoPublish,
                'has_private_key' => !empty($credentials['private_key']),
            ],
        ]);
    }

    /**
     * Save Service Account JSON & Settings
     */
    public function saveConfig(Request $request)
    {
        $validated = $request->validate([
            'service_account_json' => 'nullable|string',
            'auto_publish' => 'nullable|boolean',
        ]);

        if (isset($validated['service_account_json'])) {
            $jsonStr = trim($validated['service_account_json']);
            if (!empty($jsonStr)) {
                $decoded = json_decode($jsonStr, true);
                if (json_last_error() !== JSON_ERROR_NONE || empty($decoded['client_email']) || empty($decoded['private_key'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nội dung JSON không hợp lệ! Vui lòng đảm bảo file JSON chứa client_email và private_key từ Google Cloud Service Account.',
                    ], 422);
                }
                SiteSetting::set('google_indexing_service_account_json', $jsonStr, 'seo');
            } else {
                SiteSetting::set('google_indexing_service_account_json', '', 'seo');
            }
        }

        if (isset($validated['auto_publish'])) {
            SiteSetting::set('google_indexing_auto_publish', (bool) $validated['auto_publish'], 'seo');
        }

        return $this->getConfig();
    }

    /**
     * Test connection to Google OAuth2 token endpoint
     */
    public function testConnection()
    {
        $credentials = $this->indexingService->getCredentials();
        if (!$credentials) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa dán nội dung Service Account JSON key.',
            ], 400);
        }

        $token = $this->indexingService->getAccessToken($credentials);
        if ($token) {
            return response()->json([
                'success' => true,
                'message' => 'Kết nối thành công! Service Account đã được Google xác thực hợp lệ.',
                'client_email' => $credentials['client_email'] ?? '',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Xác thực thất bại! Google từ chối khóa bảo mật (private_key). Vui lòng kiểm tra lại file JSON.',
        ], 400);
    }

    /**
     * Publish single URL to Google
     */
    public function publish(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'type' => 'nullable|in:URL_UPDATED,URL_DELETED',
        ]);

        $url = $request->input('url');
        $type = $request->input('type', 'URL_UPDATED');

        $result = $this->indexingService->publishUrl($url, $type);

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 400);
    }

    /**
     * Get URL metadata from Google
     */
    public function getMetadata(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $result = $this->indexingService->getUrlMetadata($request->input('url'));

        return response()->json($result);
    }
}

