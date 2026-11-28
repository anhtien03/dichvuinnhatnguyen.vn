import { spawn, execFileSync } from 'node:child_process';
import { mkdtempSync, rmSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join, resolve } from 'node:path';

const root = resolve('.');
const tempDir = mkdtempSync(join(tmpdir(), 'seo-36-'));
const dbPath = join(tempDir, 'real36.sqlite');
const seedJsonPath = join(tempDir, 'seed.json');
writeFileSync(dbPath, '');

const phpEnv = {
  ...process.env,
  APP_ENV: 'testing',
  DB_CONNECTION: 'sqlite',
  DB_DATABASE: dbPath,
  SEO_SNAPSHOTS_ENABLED: 'false',
  SEO_FALLBACK_HTML_ON_INVALID: 'true',
};

let server;
try {
  // 1. Read-only fetch of real catalog from production API
  const [catsRes, prodsRes, postsRes] = await Promise.all([
    fetch('https://dichvuinnhatnguyen.vn/api/v1/categories').then((r) => r.json()),
    fetch('https://dichvuinnhatnguyen.vn/api/v1/products').then((r) => r.json()),
    fetch('https://dichvuinnhatnguyen.vn/api/v1/posts').then((r) => r.json()),
  ]);

  writeFileSync(
    seedJsonPath,
    JSON.stringify({
      categories: catsRes.data || [],
      products: prodsRes.data || [],
      posts: postsRes.data || [],
    })
  );

  // 2. Migrate & seed isolated SQLite DB
  execFileSync('php', ['backend/artisan', 'migrate', '--force'], { cwd: root, env: phpEnv, stdio: 'pipe' });
  const seedPhp = `
    require 'backend/vendor/autoload.php';
    $app = require 'backend/bootstrap/app.php';
    $app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap();
    $seed = json_decode(file_get_contents(${JSON.stringify(seedJsonPath)}), true);
    $catMap = [];
    foreach ($seed['categories'] as $c) {
      $cat = App\\Models\\Category::create([
        'name' => $c['name'],
        'slug' => $c['slug'],
        'description' => $c['description'] ?? null,
        'meta_title' => $c['meta_title'] ?? null,
        'meta_description' => $c['meta_description'] ?? null,
        'canonical_url' => $c['canonical_url'] ?? null,
        'is_active' => true,
      ]);
      $catMap[$c['id']] = $cat->id;
    }
    foreach ($seed['products'] as $p) {
      App\\Models\\Product::create([
        'category_id' => $catMap[$p['category_id']] ?? reset($catMap),
        'name' => $p['name'],
        'slug' => $p['slug'],
        'description' => $p['description'] ?? null,
        'base_price' => $p['base_price'] ?? 0,
        'meta_title' => $p['meta_title'] ?? null,
        'meta_description' => $p['meta_description'] ?? null,
        'canonical_url' => $p['canonical_url'] ?? null,
        'is_active' => true,
      ]);
    }
    foreach ($seed['posts'] as $post) {
      App\\Models\\Post::create([
        'title' => $post['title'],
        'slug' => $post['slug'],
        'excerpt' => $post['excerpt'] ?? null,
        'content' => $post['content'] ?? '<p>Nội dung</p>',
        'meta_title' => $post['meta_title'] ?? null,
        'meta_description' => $post['meta_description'] ?? null,
        'canonical_url' => $post['canonical_url'] ?? null,
        'is_published' => true,
        'published_at' => now(),
      ]);
    }
    App\\Models\\SiteSetting::create(['key' => 'site_name', 'value' => 'In Ấn Nhật Nguyên']);
    App\\Models\\SiteSetting::create(['key' => 'google_indexing_service_account_json', 'value' => '{"private_key":"-----BEGIN PRIVATE KEY-----SECRET"}']);
    App\\Models\\SiteSetting::create(['key' => 'google_indexing_auto_publish', 'value' => '1']);
  `;
  execFileSync('php', ['-r', seedPhp], { cwd: root, env: phpEnv, stdio: 'pipe' });

  const port = 8131;
  const baseUrl = `http://127.0.0.1:${port}`;
  server = spawn('php', ['-S', `127.0.0.1:${port}`, '-t', 'dist', 'scripts/seo-http-router.php'], {
    cwd: root,
    env: phpEnv,
    stdio: 'ignore',
  });

  for (let i = 0; i < 30; i++) {
    try {
      const r = await fetch(baseUrl + '/sitemap.xml');
      if (r.ok) break;
    } catch {
      await new Promise((r) => setTimeout(r, 150));
    }
  }

  // 3. Verify P0 Settings Allowlist
  const settingsRes = await fetch(baseUrl + '/api/v1/settings');
  const settingsText = await settingsRes.text();
  const settingsJson = JSON.parse(settingsText);
  const leakedKeys = Object.keys(settingsJson.data || {}).filter((k) =>
    /service_account|private|secret|token|auto_publish/i.test(k)
  );
  if (leakedKeys.length > 0 || settingsText.includes('BEGIN PRIVATE KEY')) {
    throw new Error('P0 LEAK DETECTED in /api/v1/settings: ' + leakedKeys.join(','));
  }

  // 4. Parse all 36 URLs from sitemap.xml
  const sitemapXml = await (await fetch(baseUrl + '/sitemap.xml')).text();
  const locs = [...sitemapXml.matchAll(/<loc>([^<]+)<\/loc>/g)].map((m) => m[1]);

  const titleCounts = new Map();
  const descCounts = new Map();
  const rows = [];

  for (const loc of locs) {
    const path = new URL(loc).pathname;
    const res = await fetch(baseUrl + path);
    const html = await res.text();
    if (html.includes('BEGIN PRIVATE KEY') || html.includes('google_indexing_service_account_json')) {
      throw new Error('P0 LEAK DETECTED in HTML for ' + path);
    }
    const title = (html.match(/<title>([^<]*)<\/title>/i)?.[1] || '').trim();
    const desc = (html.match(/<meta\s+name="description"\s+content="([^"]*)"/i)?.[1] || '').trim();
    const canonical = (html.match(/<link\s+rel="canonical"\s+href="([^"]*)"/i)?.[1] || '').trim();
    const robots = (html.match(/<meta\s+name="robots"\s+content="([^"]*)"/i)?.[1] || '').trim();
    titleCounts.set(title, (titleCounts.get(title) || 0) + 1);
    descCounts.set(desc, (descCounts.get(desc) || 0) + 1);
    rows.push({ path, status: res.status, title, titleLen: title.length, descLen: desc.length, canonicalOk: canonical === loc, robots });
  }

  // 5. Verify extra edge URLs (404, noindex query, noindex admin, og-banner, llms.txt)
  const extraChecks = {
    notFound404: (await fetch(baseUrl + '/san-pham/khong-ton-tai-seo-test')).status,
    quoteQueryRobots: (await (await fetch(baseUrl + '/yeu-cau-bao-gia?product=in-name-card')).text()).match(/<meta\s+name="robots"\s+content="([^"]*)"/i)?.[1],
    adminRobots: (await (await fetch(baseUrl + '/he-thong-admin/login')).text()).match(/<meta\s+name="robots"\s+content="([^"]*)"/i)?.[1],
    ogBannerStatus: (await fetch(baseUrl + '/og-banner.jpg')).status,
    llmsStatus: (await fetch(baseUrl + '/llms.txt')).status,
  };

  const dupTitles = [...titleCounts.entries()].filter(([, c]) => c > 1).map(([t]) => t);
  const dupDescs = [...descCounts.entries()].filter(([, c]) => c > 1).map(([d]) => d);
  const badStatus = rows.filter((r) => r.status !== 200);
  const badCanonical = rows.filter((r) => !r.canonicalOk);

  console.log(JSON.stringify({
    totalSitemapUrls: rows.length,
    leakedKeys,
    badStatusCount: badStatus.length,
    badCanonicalCount: badCanonical.length,
    duplicateTitles: dupTitles,
    duplicateDescriptionsCount: dupDescs.length,
    extraChecks,
    sampleRows: rows,
  }, null, 2));
} finally {
  if (server) {
    try { server.kill('SIGKILL'); } catch {}
  }
  rmSync(tempDir, { recursive: true, force: true });
}
process.exit(0);
