import { spawn, execFileSync } from 'node:child_process';
import { mkdtempSync, rmSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join, resolve } from 'node:path';

const root = resolve('.');
const tempDir = mkdtempSync(join(tmpdir(), 'seo-load-'));
const dbPath = join(tempDir, 'load.sqlite');
writeFileSync(dbPath, '');

const phpEnv = {
  ...process.env,
  APP_ENV: 'testing',
  DB_CONNECTION: 'sqlite',
  DB_DATABASE: dbPath,
  SEO_SNAPSHOTS_ENABLED: 'true',
  SEO_FALLBACK_HTML_ON_INVALID: 'true',
  ...(process.platform !== 'win32' ? { PHP_CLI_SERVER_WORKERS: '4' } : {}),
};

function percentile(sorted, p) {
  if (!sorted.length) return 0;
  const idx = Math.min(sorted.length - 1, Math.ceil((p / 100) * sorted.length) - 1);
  return Number(sorted[idx].toFixed(2));
}

async function runBurst(baseUrl, routes, concurrency) {
  const latencies = [];
  let ok = 0;
  let err5xx = 0;
  let other = 0;
  const startAll = performance.now();

  await Promise.all(
    Array.from({ length: concurrency }, async (_, i) => {
      const route = routes[i % routes.length];
      const t0 = performance.now();
      try {
        const res = await fetch(baseUrl + route, { signal: AbortSignal.timeout(60000) });
        await res.text();
        const dt = performance.now() - t0;
        latencies.push(dt);
        if (res.status >= 200 && res.status < 300) ok++;
        else if (res.status >= 500) err5xx++;
        else other++;
      } catch {
        latencies.push(performance.now() - t0);
        err5xx++;
      }
    })
  );

  const totalMs = performance.now() - startAll;
  latencies.sort((a, b) => a - b);
  return {
    concurrency,
    ok,
    err5xx,
    other,
    totalMs: Number(totalMs.toFixed(1)),
    rps: Number(((concurrency / totalMs) * 1000).toFixed(1)),
    p50Ms: percentile(latencies, 50),
    p95Ms: percentile(latencies, 95),
    p99Ms: percentile(latencies, 99),
  };
}

let server;
try {
  // Prepare migrated SQLite DB + valid snapshots
  execFileSync('php', ['backend/artisan', 'migrate', '--force'], { cwd: root, env: phpEnv, stdio: 'pipe' });
  const seedCode = `
    require 'backend/vendor/autoload.php';
    $app = require 'backend/bootstrap/app.php';
    $app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap();
    $cat = App\\Models\\Category::create(['name'=>'Load Cat','slug'=>'load-cat','is_active'=>true]);
    App\\Models\\Product::create(['category_id'=>$cat->id,'name'=>'Load Prod','slug'=>'load-prod','base_price'=>120000,'is_active'=>true]);
    App\\Models\\Post::create(['title'=>'Load Post','slug'=>'load-post','content'=>'Body','is_published'=>true,'published_at'=>now()]);
    foreach (['/', '/san-pham/load-prod', '/gioi-thieu', '/tin-tuc/load-post'] as $r) {
      $rev = App\\Services\\PublicSeoSnapshot::routeRevision($r);
      $canon = $r === '/' ? 'https://dichvuinnhatnguyen.vn/' : 'https://dichvuinnhatnguyen.vn'.$r;
      App\\Services\\PublicSeoSnapshot::write($r, $rev, [
        'html'=>'<main><h1>Load Test '.$r.'</h1></main>',
        'title'=>'Load Test | In Ấn Nhật Nguyên',
        'description'=>'Load test description',
        'canonical'=>$canon,
        'metadata'=>[],
        'schemas'=>[],
      ]);
    }
  `;
  execFileSync('php', ['-r', seedCode], { cwd: root, env: phpEnv, stdio: 'pipe' });

  const port = 8129;
  const baseUrl = `http://127.0.0.1:${port}`;
  server = spawn('php', ['-S', `127.0.0.1:${port}`, 'scripts/seo-http-router.php'], {
    cwd: root,
    env: phpEnv,
    stdio: 'ignore',
  });

  // Wait for server readiness
  for (let i = 0; i < 30; i++) {
    try {
      const r = await fetch(baseUrl + '/gioi-thieu');
      if (r.ok) break;
    } catch {
      await new Promise((r) => setTimeout(r, 150));
    }
  }

  const warmRoutes = ['/', '/san-pham/load-prod', '/gioi-thieu', '/tin-tuc/load-post'];
  const r10 = await runBurst(baseUrl, warmRoutes, 10);
  const r50 = await runBurst(baseUrl, warmRoutes, 50);
  const r100 = await runBurst(baseUrl, warmRoutes, 100);

  // Concurrent price mutation during 50-request burst
  const mutationPromise = new Promise((resolveMut) => {
    setTimeout(() => {
      execFileSync(
        'php',
        [
          '-r',
          "require 'backend/vendor/autoload.php'; $app=require 'backend/bootstrap/app.php'; $app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap(); $p=App\\Models\\Product::where('slug','load-prod')->first(); $p->base_price=195000; $p->save();",
        ],
        { cwd: root, env: phpEnv, stdio: 'pipe' }
      );
      resolveMut();
    }, 20);
  });
  const [burstDuringUpdate] = await Promise.all([runBurst(baseUrl, ['/san-pham/load-prod', '/gioi-thieu'], 50), mutationPromise]);

  const rssMb = Number((process.memoryUsage().rss / (1024 * 1024)).toFixed(1));
  console.log(
    JSON.stringify(
      {
        environment: 'isolated-local-sqlite-php-cli-server',
        rssMb,
        warmBursts: [r10, r50, r100],
        concurrentPriceUpdateBurst: burstDuringUpdate,
      },
      null,
      2
    )
  );
} finally {
  if (server) {
    try { server.kill('SIGKILL'); } catch {}
  }
  rmSync(join(root, 'backend/storage/app/private/seo-snapshots'), { recursive: true, force: true });
  rmSync(tempDir, { recursive: true, force: true });
}
process.exit(0);
