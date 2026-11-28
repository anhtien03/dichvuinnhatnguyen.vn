import { chromium } from 'playwright';
import { writeFile } from 'node:fs/promises';
const browser = await chromium.launch();
const results = [];
const base=process.env.UI_BASE_URL||'http://127.0.0.1:4173';
const output=process.env.UI_OUTPUT||'ui-baseline';
await import('node:fs/promises').then(fs=>fs.mkdir(output,{recursive:true}));
try {
  for (const [width,height] of [[1920,1080],[1366,768],[768,1024],[390,844],[360,800]]) {
    const page = await browser.newPage({viewport:{width,height}, reducedMotion:'reduce',locale:'vi-VN',timezoneId:'Asia/Ho_Chi_Minh'});
    page.setDefaultTimeout(10000);
    const category={id:1,slug:'fixture-category',name:'Danh mục kiểm thử',is_active:true};
    const product={id:1,slug:'fixture-product',name:'Ấn phẩm kiểm thử',category_id:1,category,is_active:true,base_price:1000,min_quantity:100,unit:'cái',image:'/logo.png',description:'Mô tả kiểm thử',specs:{quantities:[{count:100,label:'100',pricePerUnit:1000}],sizes:[],materials:[],finishings:[]}};
    const post={id:1,slug:'fixture-post',title:'Bài viết kiểm thử',excerpt:'Mô tả bài viết',content:'<p>Nội dung kiểm thử</p>',is_published:true,published_at:'2026-01-01T00:00:00Z'};
    await page.route('**/api/**', route => {
      const path=new URL(route.request().url()).pathname;
      const data=path.endsWith('/products/fixture-product')?product:path.endsWith('/categories/fixture-category')?category:path.endsWith('/posts/fixture-post')?post:path.endsWith('/products')?[product]:path.endsWith('/categories')?[category]:path.endsWith('/posts')?[post]:path.endsWith('/settings')?{}:[];
      return route.fulfill({json:{data}});
    });
    for (const [name,path] of [['home','/'],['catalog','/san-pham'],['category','/danh-muc/fixture-category'],['product','/san-pham/fixture-product'],['post','/tin-tuc/fixture-post'],['news','/tin-tuc'],['quote','/yeu-cau-bao-gia'],['about','/gioi-thieu'],['contact','/lien-he'],['login','/he-thong-admin/login']]) {
      const errors=[];
      const handler=e=>errors.push(e.message);
      page.on('pageerror',handler);
      console.log(`[capture] ${output} ${name}-${width}`);
      try {
      await page.goto(base+path,{waitUntil:'domcontentloaded',timeout:15000});
      await page.locator('#app > *').first().waitFor({state:'attached'});
      if(name==='product') await page.locator('#calculator-section').waitFor({timeout:10000});
      if(name==='post') await page.getByRole('heading',{name:post.title,exact:true}).waitFor({timeout:10000});
      // Trigger lazy images and reveal observers with bounded work.
      await page.evaluate(async () => {
        const sleep=ms=>new Promise(resolve=>setTimeout(resolve,ms));
        const height=document.documentElement.scrollHeight;
        for(let i=0;i<=20;i++){
          window.scrollTo({top:height*i/20,behavior:'instant'});
          await sleep(40);
        }
        window.scrollTo({top:0,behavior:'instant'});
        const images=[...document.images];
        images.forEach(i=>{if(i.loading==='lazy')i.loading='eager'});
        await Promise.race([
          Promise.all([document.fonts.ready,...images.map(i=>i.decode().catch(()=>{}))]),
          sleep(4000)
        ]);
      });
      await page.waitForTimeout(500);
      await page.screenshot({path:output+'/'+name+'-'+width+'.png',fullPage:true,animations:'disabled',timeout:15000});
      const metrics=await page.evaluate(()=>({width:document.documentElement.clientWidth,scrollWidth:document.documentElement.scrollWidth,app:!!document.querySelector('#app')?.children.length,h1:[...document.querySelectorAll('main h1')].map(e=>e.textContent.trim()),canonical:document.querySelector('link[rel=canonical]')?.href,robots:document.querySelector('meta[name=robots]')?.content,title:document.title}));
      if(['home','catalog','category','product','post'].includes(name)&&metrics.h1.length!==1) errors.push('Expected one main H1: '+JSON.stringify(metrics.h1));
      if(name!=='login'&&metrics.canonical!=='https://dichvuinnhatnguyen.vn'+path) errors.push('Canonical mismatch: '+metrics.canonical);
      if(name==='login'&&!metrics.robots?.includes('noindex')) errors.push('Admin must be noindex');
      results.push({name,width,height,...metrics,errors});
      console.log('[captured]',name,width);
      } catch(error) {
        results.push({name,width,height,app:false,errors:[...errors,String(error)]});
        console.error('[capture failed]',name,width,String(error));
      } finally {
        page.off('pageerror',handler);
        await writeFile(output+'/results.json',JSON.stringify(results,null,2));
      }
    }
    await page.close();
  }
} finally {
  await browser.close();
  await writeFile(output+'/results.json',JSON.stringify(results,null,2));
}
if(results.some(r=>!r.app||r.scrollWidth>r.width+1||r.errors.length)) process.exitCode=1;
