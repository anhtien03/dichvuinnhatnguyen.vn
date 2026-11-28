import { chromium } from 'playwright';

const origin = new URL(process.env.RENDER_ORIGIN || 'http://127.0.0.1:4173');
const path = process.argv[2] || '/';
if (!/^\/(?:san-pham|danh-muc|tin-tuc|gioi-thieu|lien-he|yeu-cau-bao-gia)?(?:\/[\p{L}\p{N}_%-]+)?$/u.test(path) || path.startsWith('//')) {
  throw new Error('Invalid public render path');
}
const browser = await chromium.launch({ headless: true, timeout: 10000 });
const timer = setTimeout(() => { console.error('Render deadline exceeded'); process.exit(1); }, 25000);
try {
  const page = await browser.newPage({ locale: 'vi-VN', timezoneId: 'Asia/Ho_Chi_Minh' });
  await page.route('**/*', route => {
    const req = route.request();
    const url = new URL(req.url());
    if (url.origin !== origin.origin || !['GET','HEAD'].includes(req.method())) return route.abort();
    if (/^\/api\//.test(url.pathname) && !/^\/api\/v1\/(products|categories|posts|settings|banners)(\/|$)/.test(url.pathname)) return route.abort();
    return route.continue();
  });
  await page.goto(new URL(path, origin).href, {waitUntil:'domcontentloaded', timeout:10000});
  await page.waitForFunction(() => {
    const h = document.querySelectorAll('main h1');
    return h.length === 1 && h[0].textContent.trim() && document.querySelector('link[rel="canonical"]');
  }, {timeout:10000});
  await page.waitForLoadState('networkidle', {timeout:10000});
  const result = await page.evaluate(() => {
    const app = document.querySelector('#app');
    const canonical = document.querySelector('link[rel="canonical"]');
    if (!app || !canonical || app.querySelectorAll('main h1').length !== 1) throw new Error('Incomplete render');
    return {html: app.innerHTML, title: document.title, description: document.querySelector('meta[name="description"]')?.content, canonical: canonical.href};
  });
  if (!result.description || !result.title) throw new Error('Incomplete metadata');
  process.stdout.write(JSON.stringify(result));
} finally {
  clearTimeout(timer);
  await browser.close();
}
