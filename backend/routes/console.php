<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('seo:render {--origin=http://127.0.0.1:4173}', function () {
    $lock = fopen(storage_path('app/seo-render.lock'), 'c');
    if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
        $this->error('Renderer already running');
        return 1;
    }
    try {
        $version = \App\Services\PublicSeoSnapshot::version();
        $xml = simplexml_load_string(app(\App\Http\Controllers\Api\SitemapController::class)->index()->getContent());
        $failed = 0;
        foreach ($xml->url as $url) {
            $route = parse_url((string) $url->loc, PHP_URL_PATH);
            try {
                $process = new \Symfony\Component\Process\Process(
                    ['node', 'scripts/render-public-page.mjs', $route],
                    base_path('..'), ['RENDER_ORIGIN' => $this->option('origin')]
                );
                $process->setTimeout(45);
                $process->mustRun();
                $page = json_decode($process->getOutput(), true, 512, JSON_THROW_ON_ERROR);
                \App\Services\PublicSeoSnapshot::write($route, $version, $page);
                $this->info('Rendered '.$route);
            } catch (\Throwable $e) {
                $failed++;
                $this->error('Render failed: '.$route);
                report($e);
            }
        }
        return $failed ? 1 : 0;
    } finally {
        flock($lock, LOCK_UN);
        fclose($lock);
    }
})->purpose('Generate versioned public HTML snapshots outside request handling');
