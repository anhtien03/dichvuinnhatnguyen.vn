import { chromium } from 'playwright';

const origin = new URL(process.env.RENDER_ORIGIN || 'http://127.0.0.1:4173');
const path = process.argv[2] || '/';
const staticPaths = new Set(['/','/san-pham','/tin-tuc','/gioi-thieu','/lien-he','/yeu-cau-bao-gia']);
if (!staticPaths.has(path) && !/^\/(san-pham|danh-muc|tin-tuc)\/[\p{L}\p{N}_%-]+$/u.test(path)) throw new Error('Invalid public render path');
if (!['127.0.0.1','localhost','[::1]'].includes(origin.hostname) || origin.username || origin.password) throw new Error('Renderer requires isolated loopback origin');
const browser = await chromium.launch({headless:true,timeout:10000});
const timer = setTimeout(() => { console.error('Render deadline exceeded'); process.exit(1); }, 30000);
try {
  const page = await browser.newPage({locale:'vi-VN',timezoneId:'Asia/Ho_Chi_Minh',serviceWorkers:'block'});
  const errors = [], pending = new Set(), responses = new Map(), categoryReads = [];
  const isApi = url => new URL(url).pathname.startsWith('/api/');
  page.on('pageerror', e => errors.push(e.message));
  page.on('request', r => {if(isApi(r.url())) pending.add(r);});
  page.on('requestfinished', r => pending.delete(r));
  page.on('requestfailed', r => {pending.delete(r);if(isApi(r.url())) errors.push('API request failed');});
  page.on('response', r => {if(isApi(r.url())) {responses.set(new URL(r.url()).pathname,r.status());if(!r.ok()) errors.push('API HTTP '+r.status()+' '+new URL(r.url()).pathname); if(r.ok() && new URL(r.url()).pathname==='/api/v1/categories') categoryReads.push(r.json());}});
  await page.route('**/*', route => {
    const req=route.request(), url=new URL(req.url());
    if(url.origin!==origin.origin || !['GET','HEAD'].includes(req.method())) return route.abort();
    if(isApi(req.url()) && (!/^\/api\/v1\/(products|categories|posts|settings|banners)(\/[^/]+)?$/.test(url.pathname) || url.searchParams.has('all'))) return route.abort();
    return route.continue();
  });
  const response=await page.goto(new URL(path,origin).href,{waitUntil:'domcontentloaded',timeout:10000});
  if(!response?.ok()) throw new Error('Public page HTTP failure');
  await page.waitForFunction(() => {
    const h=document.querySelectorAll('main h1');
    return h.length===1 && h[0].textContent.trim() && document.querySelector('link[rel="canonical"]');
  },null,{timeout:10000});
  await page.waitForLoadState('networkidle',{timeout:10000});
  const detail=path.match(/^\/(san-pham|danh-muc|tin-tuc)\/(.+)$/);
  if(detail){
    const endpoint={ 'san-pham':'products','danh-muc':'categories','tin-tuc':'posts' }[detail[1]];
    const expected='/api/v1/'+endpoint+'/'+detail[2];
    if(detail[1]==='danh-muc'){
      const slug=decodeURIComponent(detail[2]);
      const lists=await Promise.all(categoryReads);
      const category=lists.flatMap(r=>Array.isArray(r.data)?r.data:[]).find(c=>c.slug===slug && c.is_active!==false && c.is_active!==0);
      if(!category || responses.get('/api/v1/products')!==200) throw new Error('Missing category or product list data: '+slug);
      const rendered=await page.evaluate(()=>({h1:document.querySelector('main h1')?.textContent.trim(),schemas:[...document.querySelectorAll('script[type="application/ld+json"]')].map(s=>JSON.parse(s.textContent))}));
      if(rendered.h1!==category.name || !rendered.schemas.some(s=>s['@type']==='CollectionPage' && s.url==='https://dichvuinnhatnguyen.vn/danh-muc/'+slug)) throw new Error('Category content or CollectionPage schema mismatch: '+slug);
    }else if(![...responses].some(([p,status])=>decodeURIComponent(p)===decodeURIComponent(expected)&&status===200)) throw new Error('Missing successful detail API response: '+expected);
  }
  if(errors.length||pending.size) throw new Error('Incomplete public data: '+errors.join('; '));
  const result=await page.evaluate(()=>{
    const app=document.querySelector('#app');
    const canonical=document.querySelector('link[rel="canonical"]');
    if(!app||!canonical||app.querySelectorAll('main h1').length!==1) throw new Error('Incomplete render');
    const clone=app.cloneNode(true);
    clone.querySelectorAll('script,iframe,object,embed').forEach(e=>e.remove());
    clone.querySelectorAll('*').forEach(e=>{
      for(const a of [...e.attributes]) if(/^on/i.test(a.name)) e.removeAttribute(a.name);
      if(e.matches('.reveal-on-scroll,.reveal-left,.reveal-right')) e.classList.add('is-revealed');
    });
    return {html:clone.innerHTML,title:document.title,description:document.querySelector('meta[name="description"]')?.content,canonical:canonical.href,
      metadata:[...document.querySelectorAll('meta[property^="og:"],meta[name^="twitter:"],meta[name="robots"]')].map(e=>({name:e.getAttribute('name'),property:e.getAttribute('property'),content:e.content})),
      schemas:[...document.querySelectorAll('script[type="application/ld+json"]')].map(e=>JSON.parse(e.textContent))};
  });
  if(!result.description||!result.title||!result.canonical.startsWith('https://dichvuinnhatnguyen.vn/')) throw new Error('Incomplete metadata');
  process.stdout.write(JSON.stringify(result));
} finally {
  clearTimeout(timer);
  await browser.close();
}
