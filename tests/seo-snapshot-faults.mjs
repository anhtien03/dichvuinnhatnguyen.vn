import assert from 'node:assert/strict';
import {readFile,writeFile,rename} from 'node:fs/promises';
import {createHash} from 'node:crypto';
const base='http://127.0.0.1:8000';
const route='/san-pham/fixture-product';
const file='backend/storage/app/private/seo-snapshots/'+createHash('sha256').update(route).digest('hex')+'.json';
const get = async (path, headers = {}) => {
 const res = await fetch(base + path, { headers });
 const body = await res.text();
 return { status: res.status, body };
};
assert.equal((await get('/san-pham/hidden-fixture')).status,404);
assert.equal((await get('/api/v1/products/hidden-fixture?all=1')).status,404);
const normal=await get(route);assert.equal(normal.status,200);
const bot=await get(route,{'User-Agent':'Googlebot'});assert.equal(bot.status,200);
assert.equal(normal.body,bot.body);
const original=await readFile(file);
try {
 await writeFile(file,'{broken');
 assert.equal((await get(route)).status,503);
 const stale=JSON.parse(original);stale.version='invalid';
 await writeFile(file,JSON.stringify(stale));
 assert.equal((await get(route)).status,503);
} finally {await writeFile(file,original);}
await rename(file,file+'.test-backup');
try {assert.equal((await get(route)).status,503);}
finally {await rename(file+'.test-backup',file);}
assert.equal((await get(route)).status,200);
assert.match(await readFile('public/robots.txt','utf8'),/Disallow:\s*\/api\//);
console.log('PASS snapshot corrupt/stale/missing/recovery, visibility, user-agent parity and robots');
