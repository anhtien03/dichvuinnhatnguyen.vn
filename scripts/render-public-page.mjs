import { chromium } from 'playwright';
import readline from 'node:readline';

const originRaw = process.env.RENDER_ORIGIN || 'http://127.0.0.1:4173';
let origin;
try {
  origin = new URL(originRaw);
} catch {
  console.error('Invalid RENDER_ORIGIN URL: ' + originRaw);
  process.exit(2);
}

if (!['127.0.0.1', 'localhost', '[::1]'].includes(origin.hostname) || origin.username || origin.password) {
  console.error('Renderer requires isolated loopback origin without credentials');
  process.exit(2);
}

const isBatch = process.argv.includes('--batch');
const timeoutMs = Math.max(5000, Number(process.env.RENDER_TIMEOUT_MS || 20000));
const maxRetries = Math.max(0, Math.min(4, Number(process.env.RENDER_MAX_RETRIES || 2)));
const concurrency = Math.max(1, Math.min(4, Number(process.env.RENDER_CONCURRENCY || 2)));
const maxRssMb = Math.max(256, Number(process.env.RENDER_MAX_RSS_MB || 768));

const STATIC_PATHS = new Set(['/', '/san-pham', '/tin-tuc', '/gioi-thieu', '/lien-he', '/yeu-cau-bao-gia']);
const SENSITIVE_KEY_RE = /(key|secret|token|password|credential|private)/i;

function validatePublicPath(path) {
  if (!STATIC_PATHS.has(path) && !/^\/(san-pham|danh-muc|tin-tuc)\/[\p{L}\p{N}_%-]+$/u.test(path)) {
    throw new Error('Invalid public render path: ' + path);
  }
}

function sanitizeBootstrapValue(endpointPath, payload) {
  if (!payload || typeof payload !== 'object') return null;
  if (endpointPath === '/settings') {
    const src = payload.data && typeof payload.data === 'object' ? payload.data : payload;
    if (Array.isArray(src)) {
      return src.filter((item) => item && !SENSITIVE_KEY_RE.test(String(item.key || '')));
    }
    const clean = {};
    for (const [k, v] of Object.entries(src)) {
      if (!SENSITIVE_KEY_RE.test(k)) clean[k] = v;
    }
    return payload.data ? { ...payload, data: clean } : clean;
  }
  return payload;
}

async function renderSingleRoute(browserRef, routePath) {
  validatePublicPath(routePath);

  let lastError = null;
  for (let attempt = 0; attempt <= maxRetries; attempt++) {
    if (!browserRef.current || !browserRef.current.isConnected()) {
      browserRef.current = await chromium.launch({ headless: true, timeout: 15000 });
    }

    let context = null;
    let timer = null;
    try {
      context = await browserRef.current.newContext({
        locale: 'vi-VN',
        timezoneId: 'Asia/Ho_Chi_Minh',
        serviceWorkers: 'block',
      });
      const page = await context.newPage();

      const deadlinePromise = new Promise((_, reject) => {
        timer = setTimeout(() => reject(new Error(`Render deadline exceeded (${timeoutMs}ms)`)), timeoutMs);
      });

      const renderPromise = (async () => {
        const errors = [];
        const pending = new Set();
        const responses = new Map();
        const bootstrapResponses = {};
        const categoryReads = [];
        const isApi = (url) => new URL(url).pathname.startsWith('/api/');

        page.on('pageerror', (e) => errors.push(e.message));
        page.on('request', (r) => {
          if (isApi(r.url())) pending.add(r);
        });
        page.on('requestfinished', (r) => pending.delete(r));
        page.on('requestfailed', (r) => {
          pending.delete(r);
          if (isApi(r.url())) errors.push('API request failed');
        });
        page.on('response', async (r) => {
          if (!isApi(r.url())) return;
          const u = new URL(r.url());
          responses.set(u.pathname, r.status());
          if (!r.ok()) {
            errors.push(`API HTTP ${r.status()} ${u.pathname}`);
            return;
          }
          if (u.pathname === '/api/v1/categories') {
            categoryReads.push(r.json().catch(() => ({})));
          }
          const relApiPath = u.pathname.replace(/^\/api\/v1/, '') + u.search;
          try {
            const json = await r.json();
            const safe = sanitizeBootstrapValue(u.pathname.replace(/^\/api\/v1/, ''), json);
            if (safe !== null) bootstrapResponses[relApiPath] = safe;
          } catch {
            // Non-JSON responses are ignored for bootstrap
          }
        });

        await page.route('**/*', (route) => {
          const req = route.request();
          const url = new URL(req.url());
          if (url.origin !== origin.origin || !['GET', 'HEAD'].includes(req.method())) {
            return route.abort();
          }
          if (
            isApi(req.url()) &&
            (!/^\/api\/v1\/(products|categories|posts|settings|banners)(\/[^/]+)?$/.test(url.pathname) ||
              url.searchParams.has('all'))
          ) {
            return route.abort();
          }
          return route.continue();
        });

        const response = await page.goto(new URL(routePath, origin).href, {
          waitUntil: 'domcontentloaded',
          timeout: Math.min(timeoutMs, 15000),
        });
        if (!response?.ok()) {
          throw new Error(`Public page HTTP failure (${response?.status() ?? 'no-response'})`);
        }

        await page.waitForFunction(
          () => {
            const h = document.querySelectorAll('main h1');
            return h.length === 1 && h[0].textContent.trim() && document.querySelector('link[rel="canonical"]');
          },
          null,
          { timeout: Math.min(timeoutMs, 12000) }
        );

        await page.waitForLoadState('networkidle', { timeout: Math.min(timeoutMs, 12000) });

        const detail = routePath.match(/^\/(san-pham|danh-muc|tin-tuc)\/(.+)$/);
        if (detail) {
          const endpoint = { 'san-pham': 'products', 'danh-muc': 'categories', 'tin-tuc': 'posts' }[detail[1]];
          const expected = `/api/v1/${endpoint}/${detail[2]}`;
          if (detail[1] === 'danh-muc') {
            const slug = decodeURIComponent(detail[2]);
            const lists = await Promise.all(categoryReads);
            const category = lists
              .flatMap((r) => (Array.isArray(r.data) ? r.data : []))
              .find((c) => c.slug === slug && c.is_active !== false && c.is_active !== 0);
            if (!category || responses.get('/api/v1/products') !== 200) {
              throw new Error('Missing category or product list data: ' + slug);
            }
            const rendered = await page.evaluate(() => ({
              h1: document.querySelector('main h1')?.textContent.trim(),
              schemas: [...document.querySelectorAll('script[type="application/ld+json"]')].map((s) =>
                JSON.parse(s.textContent)
              ),
            }));
            if (
              rendered.h1 !== category.name ||
              !rendered.schemas.some(
                (s) => s['@type'] === 'CollectionPage' && s.url === 'https://dichvuinnhatnguyen.vn/danh-muc/' + slug
              )
            ) {
              throw new Error('Category content or CollectionPage schema mismatch: ' + slug);
            }
          } else if (
            ![...responses].some(
              ([p, status]) => decodeURIComponent(p) === decodeURIComponent(expected) && status === 200
            )
          ) {
            throw new Error('Missing successful detail API response: ' + expected);
          }
        }

        if (errors.length || pending.size) {
          throw new Error('Incomplete public data: ' + errors.join('; '));
        }

        const result = await page.evaluate(() => {
          const app = document.querySelector('#app');
          const canonical = document.querySelector('link[rel="canonical"]');
          if (!app || !canonical || app.querySelectorAll('main h1').length !== 1) {
            throw new Error('Incomplete render');
          }
          const clone = app.cloneNode(true);
          clone.querySelectorAll('script,iframe,object,embed').forEach((e) => e.remove());
          clone.querySelectorAll('*').forEach((e) => {
            for (const a of [...e.attributes]) {
              if (/^on/i.test(a.name)) e.removeAttribute(a.name);
              if (/^(href|src|action)$/i.test(a.name) && /^\s*javascript:/i.test(a.value)) {
                e.removeAttribute(a.name);
              }
            }
            if (e.matches('.reveal-on-scroll,.reveal-left,.reveal-right')) e.classList.add('is-revealed');
          });
          return {
            html: clone.innerHTML,
            title: document.title,
            description: document.querySelector('meta[name="description"]')?.content,
            canonical: canonical.href,
            metadata: [
              ...document.querySelectorAll('meta[property^="og:"],meta[name^="twitter:"],meta[name="robots"]'),
            ].map((e) => ({ name: e.getAttribute('name'), property: e.getAttribute('property'), content: e.content })),
            schemas: [...document.querySelectorAll('script[type="application/ld+json"]')].map((e) =>
              JSON.parse(e.textContent)
            ),
          };
        });

        if (!result.description || !result.title || !result.canonical.startsWith('https://dichvuinnhatnguyen.vn/')) {
          throw new Error('Incomplete metadata');
        }

        result.bootstrap = {
          route: routePath,
          generatedAt: Date.now(),
          ttlMs: 300000,
          responses: bootstrapResponses,
        };

        return result;
      })();

      return await Promise.race([renderPromise, deadlinePromise]);
    } catch (err) {
      lastError = err;
      if (browserRef.current && !browserRef.current.isConnected()) {
        browserRef.current = null;
      }
      if (attempt < maxRetries) {
        await new Promise((r) => setTimeout(r, 250 * (attempt + 1)));
      }
    } finally {
      if (timer) clearTimeout(timer);
      if (context) {
        await context.close().catch(() => {});
      }
    }
  }

  throw lastError || new Error('Unknown render failure');
}

const browserRef = { current: null };

try {
  browserRef.current = await chromium.launch({ headless: true, timeout: 15000 });

  if (!isBatch) {
    const path = process.argv[2] || '/';
    const result = await renderSingleRoute(browserRef, path);
    process.stdout.write(JSON.stringify(result));
  } else {
    const rl = readline.createInterface({ input: process.stdin, crlfDelay: Infinity });
    const jobs = [];
    for await (const line of rl) {
      const trimmed = line.trim();
      if (!trimmed) continue;
      jobs.push(JSON.parse(trimmed));
    }

    let cursor = 0;
    const worker = async () => {
      while (cursor < jobs.length) {
        const job = jobs[cursor++];
        const rssMb = process.memoryUsage().rss / (1024 * 1024);
        if (rssMb > maxRssMb && browserRef.current) {
          await browserRef.current.close().catch(() => {});
          browserRef.current = null;
        }
        try {
          const page = await renderSingleRoute(browserRef, job.route);
          process.stdout.write(JSON.stringify({ ok: true, route: job.route, revision: job.revision, page }) + '\n');
        } catch (err) {
          process.stdout.write(
            JSON.stringify({ ok: false, route: job.route, revision: job.revision, error: err?.message || String(err) }) + '\n'
          );
        }
      }
    };

    await Promise.all(Array.from({ length: Math.min(concurrency, Math.max(1, jobs.length)) }, () => worker()));
  }
} finally {
  if (browserRef.current) {
    await browserRef.current.close().catch(() => {});
  }
}
