<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('seo:render {--origin=} {--force} {--concurrency=}', function () {
    $origin = (string) ($this->option('origin') ?: config('seo.render_origin', 'http://127.0.0.1:4173'));
    $parsedOrigin = parse_url($origin);
    $host = strtolower((string) ($parsedOrigin['host'] ?? ''));
    if (!in_array($host, ['127.0.0.1', 'localhost', '[::1]'], true) || !empty($parsedOrigin['user']) || !empty($parsedOrigin['pass'])) {
        $this->error('Invalid SEO render origin (must be isolated loopback URL): '.$origin);
        return 1;
    }

    $concurrency = max(1, min(4, (int) ($this->option('concurrency') ?: config('seo.render_concurrency', 2))));
    $timeoutSec = max(10, (int) config('seo.render_timeout_seconds', 25));
    $maxRetries = max(0, min(4, (int) config('seo.render_max_retries', 2)));
    $force = (bool) $this->option('force');

    $lock = fopen(storage_path('app/seo-render.lock'), 'c');
    if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
        if ($lock) {
            fclose($lock);
        }
        $this->error('Renderer already running');
        return 1;
    }
    try {
        $xml = simplexml_load_string(app(\App\Http\Controllers\Api\SitemapController::class)->index()->getContent());
        $jobs = [];
        foreach ($xml->url as $url) {
            $route = parse_url((string) $url->loc, PHP_URL_PATH) ?: '/';
            if (!$force) {
                $resolved = \App\Services\PublicSeoSnapshot::resolve($route);
                if ($resolved['state'] === \App\Services\PublicSeoSnapshot::STATE_FRESH) {
                    continue;
                }
            }
            $jobs[] = [
                'route' => $route,
                'revision' => \App\Services\PublicSeoSnapshot::routeRevision($route),
            ];
        }

        if ($jobs === []) {
            $this->info('All public SEO snapshots are already FRESH.');
            return 0;
        }

        $payload = implode("\n", array_map(static fn (array $j): string => json_encode($j, JSON_THROW_ON_ERROR), $jobs))."\n";
        $process = new \Symfony\Component\Process\Process(
            ['node', 'scripts/render-public-page.mjs', '--batch'],
            base_path('..'),
            [
                'RENDER_ORIGIN' => $origin,
                'RENDER_CONCURRENCY' => (string) $concurrency,
                'RENDER_TIMEOUT_MS' => (string) ($timeoutSec * 1000),
                'RENDER_MAX_RETRIES' => (string) $maxRetries,
            ]
        );
        $process->setInput($payload);
        $process->setTimeout(max(60, count($jobs) * $timeoutSec));
        $process->run();

        $failed = 0;
        $logFile = storage_path('logs/seo-render.log');
        foreach (explode("\n", trim($process->getOutput())) as $line) {
            if ($line === '') {
                continue;
            }
            $item = json_decode($line, true);
            if (!is_array($item) || empty($item['route'])) {
                $failed++;
                continue;
            }
            if (!empty($item['ok']) && is_array($item['page'] ?? null)) {
                try {
                    \App\Services\PublicSeoSnapshot::write($item['route'], (string) $item['revision'], $item['page']);
                    $this->info('Rendered '.$item['route']);
                } catch (\Throwable $e) {
                    $failed++;
                    $msg = '['.date('c').'] '.$item['route'].' — '.$e->getMessage()."\n";
                    @file_put_contents($logFile, $msg, FILE_APPEND);
                    $this->error('Write failed: '.$item['route'].' — '.$e->getMessage());
                }
            } else {
                $failed++;
                $err = (string) ($item['error'] ?? 'Unknown render error');
                $msg = '['.date('c').'] '.$item['route'].' — '.$err."\n";
                @file_put_contents($logFile, $msg, FILE_APPEND);
                $this->error('Render failed: '.$item['route'].' — '.$err);
            }
        }

        if (!$process->isSuccessful() && $process->getErrorOutput() !== '') {
            $this->error(trim($process->getErrorOutput()));
            $failed = max(1, $failed);
        }

        return $failed ? 1 : 0;
    } finally {
        flock($lock, LOCK_UN);
        fclose($lock);
    }
})->purpose('Generate versioned public HTML snapshots outside request handling');

\Illuminate\Support\Facades\Schedule::command('seo:render')
    ->everyMinute()->withoutOverlapping(60)
    ->when(fn () => config('seo.snapshots') && config('seo.auto_refresh'));

Artisan::command('seo:health {--json}', function () {
    $counts = [
        \App\Services\PublicSeoSnapshot::STATE_FRESH => 0,
        \App\Services\PublicSeoSnapshot::STATE_STALE => 0,
        \App\Services\PublicSeoSnapshot::STATE_INVALID => 0,
        \App\Services\PublicSeoSnapshot::STATE_MISSING => 0,
    ];
    $dbReachable = true;
    try {
        $xml = simplexml_load_string(app(\App\Http\Controllers\Api\SitemapController::class)->index()->getContent());
        foreach ($xml->url as $url) {
            $route = parse_url((string) $url->loc, PHP_URL_PATH) ?: '/';
            $state = \App\Services\PublicSeoSnapshot::resolve($route)['state'];
            $counts[$state] = ($counts[$state] ?? 0) + 1;
        }
    } catch (\Throwable $e) {
        $dbReachable = false;
    }

    $manifest = \App\Services\PublicSeoSnapshot::readManifest();
    $snapshotDir = storage_path('app/private/seo-snapshots');
    $report = [
        'db_reachable' => $dbReachable,
        'snapshots_enabled' => (bool) config('seo.snapshots'),
        'auto_refresh_enabled' => (bool) config('seo.auto_refresh'),
        'fallback_html_on_invalid' => (bool) config('seo.fallback_html_on_invalid'),
        'render_origin' => (string) config('seo.render_origin'),
        'dist_index_exists' => is_file(base_path('../dist/index.html')),
        'snapshot_dir_writable' => is_dir($snapshotDir) ? is_writable($snapshotDir) : is_writable(dirname($snapshotDir)),
        'global_rev' => (int) ($manifest['global_rev'] ?? 1),
        'total_sitemap_routes' => array_sum($counts),
        'states' => $counts,
    ];

    if ($this->option('json')) {
        $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    } else {
        $this->info('SEO Snapshot Health Report');
        foreach ($report as $k => $v) {
            $this->line($k.': '.(is_array($v) ? json_encode($v) : var_export($v, true)));
        }
    }

    return $report['dist_index_exists'] && $report['snapshot_dir_writable'] ? 0 : 1;
})->purpose('Inspect SEO Snapshot V2 configuration, storage permissions, and route states');
