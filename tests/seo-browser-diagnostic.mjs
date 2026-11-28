import { chromium } from 'playwright';
import { writeFile } from 'node:fs/promises';
const base='http://127.0.0.1:4173', origin='https://dichvuinnhatnguyen.vn';
const browser=await chromium.launch(), results=[];
const check=(id,name,ok,detail='')=>{results.push({id,name,ok:!!ok,detail});console.log(ok?'PASS':'FAIL',id,name,detail)};
const category={id:1,slug:'fixture-category',name:'Danh mục kiểm thử',is_active:true};
const product={id:1,slug:'fixture-product',name:'Ấn phẩm kiểm thử',category_id:1,category,is_active:true,base_price:1000,min_quantity:100,unit:'cái',image:'/logo.png',description:'Mô tả kiểm thử',specs:{quantities:[{count:100,label:'100',pricePerUnit:1000},{count:200,label:'200',pricePerUnit:900}],sizes:[{value:'a',label:'Khổ A',extra:0},{value:'b',label:'Khổ B',extra:200}],materials:[],finishings:[]}};
const post={id:1,slug:'fixture-post',title:'Bài viết kiểm thử',excerpt:'Mô tả bài viết',content:'<p>Nội dung kiểm thử</p>',is_published:true};
const paths=['/','/san-pham','/danh-muc/fixture-category','/san-pham/fixture-product','/tin-tuc','/tin-tuc/fixture-post','/gioi-thieu','/lien-he','/yeu-cau-bao-gia'];
const fixture=async(page,mode='normal')=>page.route('**/api/**',async route=>{
 const p=new URL(route.request().url()).pathname;
 if(mode==='slow') await new Promise(r=>setTimeout(r,1500));
 if(mode==='offline') return route.abort();
 if(mode==='500') return route.fulfill({status:500,json:{message:'fixture failure'}});
 const data=p.endsWith('/products/fixture-product')?product:p.endsWith('/categories/fixture-category')?category:p.endsWith('/posts/fixture-post')?post:p.endsWith('/products')?[product]:p.endsWith('/categories')?[category]:p.endsWith('/posts')?[post]:p.endsWith('/settings')?{}:[];
 return route.fulfill({json:{data}});
});
const inspect=page=>page.evaluate(()=>({title:document.title,desc:document.querySelector('meta[name=description]')?.content,canonical:document.querySelector('link[rel=canonical]')?.href,h1:[...document.querySelectorAll('main h1')].map(e=>e.textContent.trim()),og:document.querySelector('meta[property="og:title"]')?.content,schemas:[...document.querySelectorAll('script[type="application/ld+json"]')].map(e=>e.textContent),links:[...document.querySelectorAll('a[href]')].map(e=>e.getAttribute('href')),body:document.body.innerText}));
try{
 const raw=await browser.newPage({javaScriptEnabled:false});
 const page=await browser.newPage();await fixture(page);
 await page.addInitScript(()=>{
 window.__seoPerf={lcp:0,cls:0,eventMax:0};
 for(const type of ['largest-contentful-paint','layout-shift','event']){
  try{new PerformanceObserver(list=>{for(const e of list.getEntries()){
   if(type==='largest-contentful-paint')window.__seoPerf.lcp=e.startTime;
   if(type==='layout-shift'&&!e.hadRecentInput)window.__seoPerf.cls+=e.value;
   if(type==='event'&&e.interactionId)window.__seoPerf.eventMax=Math.max(window.__seoPerf.eventMax,e.duration);
  }}).observe({type,buffered:true,...(type==='event'?{durationThreshold:16}:{})})}catch{}
 }
 });
 const titles=new Set();
 for(const path of paths){
  try{
   const response=await raw.goto(base+path);const a=await inspect(raw);
   check('T01',path,response.status()===200&&a.h1.length===1&&a.body.trim().length>40,a.h1);
   check('T02','raw '+path,!!a.title&&!!a.desc&&a.canonical===origin+path);
   await page.goto(base+path);await page.waitForTimeout(900);const b=await inspect(page);
   const perf=await page.evaluate(()=>({...window.__seoPerf,load:performance.getEntriesByType('navigation')[0]?.loadEventEnd}));
   check('T10','lab '+path,perf.lcp>0&&perf.lcp<=2500&&perf.cls<=0.1,perf);
   check('T02','client '+path,!!b.title&&!!b.desc&&b.canonical===origin+path&&!titles.has(b.title));titles.add(b.title);
   check('T12',path,a.h1.length===1&&JSON.stringify(a.h1)===JSON.stringify(b.h1)&&a.title===b.title&&a.canonical===b.canonical);
   if(path.includes('fixture-product')||path.includes('fixture-post')){
    let valid=false;try{valid=b.schemas.some(s=>{const j=JSON.parse(s);return j['@type']===(path.includes('product')?'Product':'Article')})}catch{}
    check('T06',path,valid&&!!b.og);
   }
   const invalid=b.links.filter(x=>x.startsWith('/')&&!x.startsWith('//')&&!paths.includes(x.split(/[?#]/)[0])&&!/^\/(san-pham|danh-muc|tin-tuc)\/[^/]+$/.test(x.split(/[?#]/)[0])&&!x.startsWith('/he-thong-admin'));
   check('T07','internal route shape '+path,invalid.length===0,invalid);
  }catch(e){check('T01/T02/T06/T07/T12',path,false,e.message)}
 }
 for(const path of ['/missing-seo-fixture','/san-pham/missing-seo-fixture','/danh-muc/missing-seo-fixture','/tin-tuc/missing-seo-fixture']){
  const r=await raw.goto(base+path);check('T03',path,r.status()===404,r.status());
 }
 await page.goto(base+'/san-pham/fixture-product');
 try{
  const calc=page.locator('#calculator-section');await calc.waitFor();
  await calc.getByRole('button',{name:/Khổ B/}).click();
  await calc.getByRole('button',{name:/^100\s/}).click();
  const expected=new Intl.NumberFormat('vi-VN',{style:'currency',currency:'VND'}).format(120000);
  check('T09','selected quantity and size total',(await calc.innerText()).replace(/\s/g,'').includes(expected.replace(/\s/g,'')),expected);
  await calc.getByRole('button',{name:'Gửi yêu cầu báo giá',exact:true}).click();
  await page.waitForURL('**/yeu-cau-bao-gia**');check('T09','quote navigation',true);
 }catch(e){check('T09','calculator interaction',false,e.message)}
 for(const mode of ['slow','offline','500']){
  const p=await browser.newPage();const errors=[];p.on('pageerror',e=>errors.push(e.message));await fixture(p,mode);
  try{await p.goto(base+'/san-pham/fixture-product');await p.waitForTimeout(5000);const a=await inspect(p);check('T11',mode,errors.length===0&&(mode==='slow'?a.body.includes(product.name):a.body.includes('Ấn phẩm đang được cập nhật')),errors)}catch(e){check('T11',mode,false,e.message)}finally{await p.close()}
 }
}finally{await browser.close();await writeFile('seo-browser-results.json',JSON.stringify(results,null,2))}
if(results.some(r=>!r.ok))process.exitCode=1;
