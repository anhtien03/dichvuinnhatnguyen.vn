import test from 'node:test'
import assert from 'node:assert/strict'
import { api, apiRequest, readSeoBootstrap, resetSeoBootstrapCacheForTest } from '../src/services/api.js'

const storage = () => {
  const map = new Map()
  return { getItem: k => map.get(k) ?? null, setItem: (k,v) => map.set(k,String(v)), removeItem: k => map.delete(k), key: i => [...map.keys()][i], get length() { return map.size } }
}
globalThis.localStorage = storage()
globalThis.sessionStorage = storage()
globalThis.window = { location: { pathname: '/' } }
const ok = () => new Response(JSON.stringify({ success: true, data: [] }))
test('public GET retries transient failure', async () => {
  let calls = 0
  globalThis.fetch = async () => ++calls === 1 ? new Response('{}', {status:503}) : ok()
  await apiRequest('/products')
  assert.equal(calls,2)
})
test('404 and writes are never retried', async () => {
  for (const [path, options, status] of [['/products/missing',{},404],['/quotes',{method:'POST',body:{}},503]]) {
    let calls = 0
    globalThis.fetch = async () => { calls++; return new Response('{}',{status}) }
    await assert.rejects(apiRequest(path,options), e => e.status === status)
    assert.equal(calls,1)
  }
})
test('long Retry-After is returned without retry', async () => {
  let calls = 0
  globalThis.fetch = async () => { calls++; return new Response('{}',{status:429,headers:{'Retry-After':'60'}}) }
  await assert.rejects(apiRequest('/products'), e => e.status === 429 && e.retryAfter === '60')
  assert.equal(calls,1)
})
test('equivalent public queries share one request', async () => {
  let calls = 0
  globalThis.fetch = async () => { calls++; return ok() }
  await Promise.all([api.getProducts({search:'fixture',compact:1}),api.getProducts({compact:1,search:'fixture'})])
  assert.equal(calls,1)
})
test('authenticated reads bypass public cache', async () => {
  localStorage.setItem('admin_token','fixture-token')
  let calls = 0
  globalThis.fetch = async (_,options) => { calls++; assert.equal(options.cache,'no-store'); return ok() }
  try {
    await api.getProducts({search:'fixture',compact:1})
    await api.getProducts({search:'fixture',compact:1})
    assert.equal(calls,2)
  } finally { localStorage.removeItem('admin_token') }
})

test('ERP payment and cashbook forward stable retry keys without automatic write retry', async () => {
  for (const submit of [
    (data,key) => api.addErpOrderPayment(42,data,key),
    (data,key) => api.createErpCashbook(data,key)
  ]) {
    const calls = []
    globalThis.fetch = async (url,options) => {
      calls.push({url,key:options.headers['Idempotency-Key'],body:JSON.parse(options.body)})
      if (calls.length === 1) throw new TypeError('network failure')
      return ok()
    }
    const payload = {amount:100}
    await assert.rejects(submit(payload,'fixture-key-1'), TypeError)
    assert.equal(calls.length,1)
    await submit(payload,'fixture-key-1')
    await submit(payload,'fixture-key-2')
    assert.equal(calls.length,3)
    assert.deepEqual(calls.map(c=>c.key),['fixture-key-1','fixture-key-1','fixture-key-2'])
    assert.deepEqual(calls[0],calls[1])
    assert.deepEqual(calls[2].body,payload)
  }
})

test('SEO bootstrap serves valid public route without duplicate fetch and falls back when expired, mismatched, or admin', async () => {
  const prevDoc = globalThis.document
  const setBootstrap = (payload) => {
    resetSeoBootstrapCacheForTest()
    globalThis.document = {
      getElementById: (id) => id === 'public-seo-bootstrap' ? { textContent: JSON.stringify(payload) } : null
    }
  }
  try {
    let calls = 0
    globalThis.fetch = async () => { calls++; return ok() }
    globalThis.window = { location: { pathname: '/tin-tuc/bai-viet-mau' } }
    setBootstrap({
      route: '/tin-tuc/bai-viet-mau',
      generatedAt: Date.now(),
      ttlMs: 60000,
      responses: {
        '/posts/bai-viet-mau': { success: true, data: { slug: 'bai-viet-mau', title: 'Bài viết mẫu' } },
        '/settings': { success: true, data: { site_name: 'In Ấn Nhật Nguyên' } }
      }
    })
    const post = await api.getPostBySlug('bai-viet-mau')
    const settings = await api.getSettings()
    assert.equal(post.data.slug, 'bai-viet-mau')
    assert.equal(settings.data.site_name, 'In Ấn Nhật Nguyên')
    assert.equal(calls, 0)

    // Product detail always fetches live pricing even if bootstrap is present
    await api.getProductBySlug('in-namecard')
    assert.equal(calls, 1)

    // Route mismatch must fall back to fetch
    globalThis.window = { location: { pathname: '/tin-tuc/khac' } }
    await api.getPostBySlug('khac')
    assert.equal(calls, 2)

    // Expired TTL must fall back to fetch
    globalThis.window = { location: { pathname: '/tin-tuc/bai-viet-mau' } }
    setBootstrap({
      route: '/tin-tuc/bai-viet-mau',
      generatedAt: Date.now() - 120000,
      ttlMs: 60000,
      responses: { '/posts/bai-viet-mau': { success: true, data: { slug: 'bai-viet-mau' } } }
    })
    assert.equal(readSeoBootstrap('/posts/bai-viet-mau'), null)
    await api.getPostBySlug('bai-viet-mau')
    assert.equal(calls, 3)

    // Admin token must bypass bootstrap
    localStorage.setItem('admin_token', 'fixture-token')
    setBootstrap({
      route: '/tin-tuc/bai-viet-mau',
      generatedAt: Date.now(),
      ttlMs: 60000,
      responses: { '/posts/bai-viet-mau': { success: true, data: { slug: 'bai-viet-mau' } } }
    })
    assert.equal(readSeoBootstrap('/posts/bai-viet-mau'), null)
    await api.getPostBySlug('bai-viet-mau')
    assert.equal(calls, 4)
  } finally {
    localStorage.removeItem('admin_token')
    resetSeoBootstrapCacheForTest()
    globalThis.document = prevDoc
    globalThis.window = { location: { pathname: '/' } }
  }
})
