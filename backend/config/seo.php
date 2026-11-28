<?php

return [
    'snapshots' => filter_var(env('SEO_SNAPSHOTS_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
    'auto_refresh' => filter_var(env('SEO_AUTO_REFRESH', false), FILTER_VALIDATE_BOOLEAN),
    'render_origin' => env('SEO_RENDER_ORIGIN', env('RENDER_ORIGIN', 'http://127.0.0.1:4173')),
    'stale_ttl_seconds' => max(60, (int) env('SEO_STALE_TTL_SECONDS', 3600)),
    'fresh_ttl_seconds' => max(300, (int) env('SEO_FRESH_TTL_SECONDS', 86400)),
    'fallback_html_on_invalid' => filter_var(env('SEO_FALLBACK_HTML_ON_INVALID', false), FILTER_VALIDATE_BOOLEAN),
    'render_concurrency' => max(1, min(4, (int) env('SEO_RENDER_CONCURRENCY', 2))),
    'render_timeout_seconds' => max(10, (int) env('SEO_RENDER_TIMEOUT_SECONDS', 35)),
    'render_max_retries' => max(0, min(3, (int) env('SEO_RENDER_MAX_RETRIES', 2))),
];
