    <?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SeoAiController extends Controller
{
    /**
     * Optimize SEO metadata and intro/H2 suggestions using Google Gemini (Method A).
     */
    public function optimize(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'in:product,post,category'],
            'focus_keyword' => ['nullable', 'string', 'max:120'],
            'meta_title' => ['nullable', 'string', 'max:160'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'slug' => ['nullable', 'string', 'max:160'],
            'content' => ['nullable', 'string', 'max:100000'],
        ]);

        $apiKey = (string) config('services.gemini.api_key', '');
        if (trim($apiKey) === '') {
            return response()->json([
                'message' => 'Chưa cấu hình GEMINI_API_KEY trong backend/.env.',
            ], 503);
        }

        $primaryModel = (string) config('services.gemini.seo_model', 'gemini-3.8-flash');
        $fallbackModel = (string) config('services.gemini.seo_fallback_model', 'gemini-3.5-flash-lite');
        $dailyLimit = max(0, (int) config('services.gemini.seo_daily_limit', 100));
        $baseUrl = rtrim((string) config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');

        $nowVn = now('Asia/Ho_Chi_Minh');
        $dailyKey = 'seo_ai_daily_count:' . $nowVn->format('Y-m-d');
        $cache = Cache::store('file');
        $currentCount = (int) $cache->get($dailyKey, 0);

        if ($dailyLimit > 0 && $currentCount >= $dailyLimit) {
            return response()->json([
                'message' => "Đã đạt hạn mức tối ưu AI trong ngày ({$currentCount}/{$dailyLimit} lượt). Vui lòng thử lại vào ngày mai hoặc dùng Tối Ưu 1 Chạm cục bộ.",
                'quota' => [
                    'used_today' => $currentCount,
                    'daily_limit' => $dailyLimit,
                    'remaining_today' => 0,
                ],
            ], 429);
        }

        $prompt = $this->buildSeoPrompt($validated);

        $modelsToTry = array_values(array_unique(array_filter([$primaryModel, $fallbackModel])));
        $lastError = null;
        $lastStatus = 502;

        foreach ($modelsToTry as $index => $model) {
            $result = $this->callGemini($baseUrl, $model, $apiKey, $prompt);

            if ($result['ok']) {
                $usedToday = $this->incrementDailyCount($dailyKey, $nowVn);

                return response()->json([
                    'success' => true,
                    'model' => $model,
                    'fallback_used' => $index > 0,
                    'usage' => $result['usage'] ?? null,
                    'quota' => [
                        'used_today' => $usedToday,
                        'daily_limit' => $dailyLimit,
                        'remaining_today' => $dailyLimit > 0 ? max(0, $dailyLimit - $usedToday) : null,
                    ],
                    'data' => $this->normalizeSeoPayload($result['data'], $validated),
                ]);
            }

            $lastError = $result['message'];
            $lastStatus = $result['status'];

            // Retry with fallback model on rate limit, overload, unavailable model, or malformed JSON
            if (!in_array($result['status'], [404, 429, 500, 502, 503, 504], true)) {
                break;
            }
        }

        return response()->json([
            'message' => $lastError ?: 'Không thể kết nối Gemini API lúc này.',
        ], $lastStatus >= 400 && $lastStatus < 600 ? $lastStatus : 502);
    }

    /**
     * Build a structured Vietnamese SEO prompt aligned with RankMathSeoBox checks.
     */
    private function buildSeoPrompt(array $input): string
    {
        $typeLabel = match ($input['type'] ?? 'product') {
            'post' => 'Bài viết tin tức / blog',
            'category' => 'Danh mục sản phẩm',
            default => 'Sản phẩm / dịch vụ in ấn',
        };

        $plainContent = html_entity_decode(strip_tags((string) ($input['content'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plainContent = preg_replace('/\s+/u', ' ', trim($plainContent)) ?? '';
        $currentWords = $plainContent !== '' ? count(preg_split('/\s+/u', $plainContent, -1, PREG_SPLIT_NO_EMPTY) ?: []) : 0;
        $minWordsTarget = match ($input['type'] ?? 'product') {
            'post' => 500,
            'category' => 120,
            default => 200,
        };
        $wordsNeeded = max(0, $minWordsTarget - $currentWords);
        $excerpt = mb_substr($plainContent, 0, 2200);

        $title = trim((string) $input['title']);
        $focusKeyword = trim((string) ($input['focus_keyword'] ?? ''));
        $currentSlug = trim((string) ($input['slug'] ?? ''));
        $currentYear = date('Y');

        $expansionInstruction = $wordsNeeded > 0
            ? "7. \"expansion_html\": Nội dung hiện tại mới có {$currentWords} từ (cần thêm khoảng " . ($wordsNeeded + 60) . " từ để vượt mốc {$minWordsTarget} từ và đưa mật độ từ khóa về mức 1.0% - 2.5%). Hãy viết 1 đoạn <p>...</p> và 1 danh sách <ul><li>...</li></ul> khoảng " . max(100, min(220, $wordsNeeded + 70)) . " từ đặt bên dưới h2_heading, mô tả quy trình in ấn, kiểm tra file, gia công thành phẩm và giao hàng phù hợp với sản phẩm/bài viết này. QUAN TRỌNG: KHÔNG lặp lại y nguyên cụm focus_keyword quá 1 lần trong expansion_html (hãy dùng từ đồng nghĩa/ngữ cảnh tự nhiên) để tránh vượt mật độ từ khóa."
            : '7. "expansion_html": Trả về chuỗi rỗng "" vì nội dung hiện tại đã đủ độ dài.';

        return <<<PROMPT
Bạn là chuyên gia SEO tiếng Việt cho website xưởng in "In Nhật Nguyên" (dichvuinnhatnguyen.vn, chuyên in ấn phẩm tại TP.HCM & giao toàn quốc).

Hãy phân tích dữ liệu dưới đây và trả về JSON tối ưu chuẩn Rank Math SEO cho:
- Loại nội dung: {$typeLabel}
- Tên / Tiêu đề gốc: {$title}
- Từ khóa mục tiêu hiện tại (nếu có): {$focusKeyword}
- Slug hiện tại (nếu có): {$currentSlug}
- Nội dung hiện có ({$currentWords} từ, tóm tắt): {$excerpt}

YÊU CẦU NGHIÊM NGẶT:
1. "focus_keyword": 2-5 từ tiếng Việt tự nhiên, chữ thường, sát với tên sản phẩm/bài viết (giữ nguyên nếu từ khóa hiện tại đã tốt).
2. "meta_title": Độ dài BẮT BUỘC từ 45 đến 64 ký tự. Chứa từ khóa mục tiêu nằm trong 20 ký tự đầu tiên, có con số hoặc năm ({$currentYear}, 24h, 4h...) và từ khóa thu hút click phù hợp ngành in (Giá Xưởng, Tận Xưởng, Chuẩn Màu, Lấy Nhanh, TPHCM...), ưu tiên kết thúc bằng "| In Nhật Nguyên" nếu đủ độ dài.
3. "meta_description": Độ dài BẮT BUỘC từ 135 đến 160 ký tự. Chứa từ khóa mục tiêu tự nhiên, nêu bật lợi ích đúng với sản phẩm/bài viết này và lời kêu gọi hành động.
4. "slug": URL tiếng Việt không dấu dạng kebab-case, ngắn gọn <= 75 ký tự, chứa toàn bộ từ khóa mục tiêu.
5. "intro_html": Một thẻ <p>...</p> (2-3 câu, khoảng 45-70 từ) mở đầu tự nhiên, có chứa <strong>từ khóa mục tiêu</strong>.
6. "h2_heading": Một thẻ <h2>...</h2> tự nhiên, hấp dẫn, chứa từ khóa mục tiêu.
{$expansionInstruction}

QUY TẮC CHỐNG BỊA ĐẶT:
- TUYỆT ĐỐI KHÔNG tự bịa giá tiền, định lượng giấy (gsm), kích thước cụ thể, phần trăm giảm giá hoặc chính sách bảo hành nếu thông tin đó không có trong "Tên" hoặc "Nội dung hiện có".
- Chỉ trả về JSON hợp lệ theo schema.
PROMPT;
    }

    /**
     * Call Gemini generateContent endpoint and parse structured JSON output.
     */
    private function callGemini(string $baseUrl, string $model, string $apiKey, string $prompt): array
    {
        $url = "{$baseUrl}/models/{$model}:generateContent";

        try {
            $response = Http::timeout(25)
                ->withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($url, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.4,
                        'maxOutputTokens' => 3072,
                        'responseMimeType' => 'application/json',
                        'responseSchema' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'focus_keyword' => ['type' => 'STRING'],
                                'meta_title' => ['type' => 'STRING'],
                                'meta_description' => ['type' => 'STRING'],
                                'slug' => ['type' => 'STRING'],
                                'intro_html' => ['type' => 'STRING'],
                                'h2_heading' => ['type' => 'STRING'],
                                'expansion_html' => ['type' => 'STRING'],
                            ],
                            'required' => [
                                'focus_keyword',
                                'meta_title',
                                'meta_description',
                                'slug',
                                'intro_html',
                                'h2_heading',
                                'expansion_html',
                            ],
                        ],
                    ],
                ]);
        } catch (\Throwable $e) {
            Log::warning('Gemini SEO request exception', ['model' => $model, 'error' => $e->getMessage()]);

            return [
                'ok' => false,
                'status' => 504,
                'message' => 'Kết nối tới Gemini API bị gián đoạn hoặc quá thời gian chờ.',
            ];
        }

        if (!$response->successful()) {
            $status = $response->status();
            $apiMsg = (string) data_get($response->json(), 'error.message', '');
            Log::warning('Gemini SEO API error', ['model' => $model, 'status' => $status, 'message' => $apiMsg]);

            $friendlyMessage = match ($status) {
                400 => 'Yêu cầu gửi tới Gemini không hợp lệ.',
                401, 403 => 'GEMINI_API_KEY không hợp lệ hoặc không có quyền truy cập.',
                404 => "Model {$model} chưa khả dụng trên tài khoản Gemini.",
                429 => 'Gemini Free Tier đang chạm giới hạn tốc độ/ngày (429). Vui lòng thử lại sau ít phút.',
                default => 'Gemini API gặp sự cố tạm thời. Vui lòng thử lại sau.',
            };

            return [
                'ok' => false,
                'status' => $status,
                'message' => $friendlyMessage,
            ];
        }

        $payload = $response->json();
        $rawText = (string) data_get($payload, 'candidates.0.content.parts.0.text', '');
        $finishReason = (string) data_get($payload, 'candidates.0.finishReason', '');
        $decoded = $this->decodeGeminiJson($rawText);

        if (!is_array($decoded)) {
            Log::warning('Gemini SEO invalid JSON output', [
                'model' => $model,
                'finish_reason' => $finishReason,
                'raw' => mb_substr($rawText, 0, 500),
            ]);

            return [
                'ok' => false,
                'status' => 502,
                'message' => 'Gemini trả về dữ liệu không đúng định dạng JSON.',
            ];
        }

        $usageMeta = data_get($payload, 'usageMetadata');
        $usage = is_array($usageMeta) ? [
            'prompt_tokens' => (int) ($usageMeta['promptTokenCount'] ?? 0),
            'output_tokens' => (int) ($usageMeta['candidatesTokenCount'] ?? 0),
            'total_tokens' => (int) ($usageMeta['totalTokenCount'] ?? 0),
        ] : null;

        return [
            'ok' => true,
            'status' => 200,
            'usage' => $usage,
            'data' => $decoded,
        ];
    }

    /**
     * Decode JSON robustly even if the model wraps it in markdown code fences.
     */
    private function decodeGeminiJson(string $rawText): ?array
    {
        $trimmed = trim($rawText);
        if ($trimmed === '') {
            return null;
        }

        $decoded = json_decode($trimmed, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/i', $trimmed, $matches)) {
            $decoded = json_decode(trim($matches[1]), true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        $start = strpos($trimmed, '{');
        $end = strrpos($trimmed, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $decoded = json_decode(substr($trimmed, $start, $end - $start + 1), true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Atomically increment today's AI SEO usage counter until midnight Asia/Ho_Chi_Minh.
     */
    private function incrementDailyCount(string $dailyKey, \Illuminate\Support\Carbon $nowVn): int
    {
        $cache = Cache::store('file');
        $expiresAt = $nowVn->copy()->endOfDay()->addMinutes(5);
        if (!$cache->has($dailyKey)) {
            $cache->put($dailyKey, 0, $expiresAt);
        }

        return (int) $cache->increment($dailyKey);
    }

    /**
     * Sanitize and normalize AI output to safe HTML and valid SEO lengths.
     */
    private function normalizeSeoPayload(array $data, array $input): array
    {
        $focusKeyword = mb_strtolower(trim((string) ($data['focus_keyword'] ?? $input['focus_keyword'] ?? '')));
        $metaTitle = trim(preg_replace('/\s+/u', ' ', strip_tags((string) ($data['meta_title'] ?? ''))) ?? '');
        $metaDescription = trim(preg_replace('/\s+/u', ' ', strip_tags((string) ($data['meta_description'] ?? ''))) ?? '');

        $rawSlug = trim((string) ($data['slug'] ?? ''));
        $slug = Str::slug($rawSlug !== '' ? $rawSlug : ($focusKeyword !== '' ? $focusKeyword : (string) $input['title']));
        if (mb_strlen($slug) > 75) {
            $slug = rtrim(mb_substr($slug, 0, 75), '-');
        }

        $introText = strip_tags((string) ($data['intro_html'] ?? ''), '<p><strong><em>');
        $introText = trim($introText);
        if ($introText !== '' && !str_starts_with($introText, '<p')) {
            $introText = "<p>{$introText}</p>";
        }

        $h2Text = strip_tags((string) ($data['h2_heading'] ?? ''), '<h2><h3><strong>');
        $h2Text = trim($h2Text);
        if ($h2Text !== '' && !preg_match('/^<h[23]\b/i', $h2Text)) {
            $h2Text = "<h2>{$h2Text}</h2>";
        }

        $expansionText = strip_tags((string) ($data['expansion_html'] ?? ''), '<p><ul><li><strong><em><h3>');
        $expansionText = trim($expansionText);

        return [
            'focus_keyword' => $focusKeyword,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'slug' => $slug,
            'intro_html' => $introText,
            'h2_heading' => $h2Text,
            'expansion_html' => $expansionText,
        ];
    }
}
