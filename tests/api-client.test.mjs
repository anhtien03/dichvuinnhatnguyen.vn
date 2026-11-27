import test from 'node:test'
import assert from 'node:assert/strict'
import { api, apiRequest } from '../src/services/api.js'

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
