import test from 'node:test'
import assert from 'node:assert/strict'
import { createServer } from 'vite'

test('existing public estimate formula regression', async () => {
  const server = await createServer({ configFile: false, server: { middlewareMode: true }, appType: 'custom' })
  try {
    const { mockApi } = await server.ssrLoadModule('/src/services/mockApi.js')
    const cases = [
      [{basePrice:500, selectedQuantity:{count:1000,pricePerUnit:220}},220000],
      [{basePrice:500, selectedQuantity:{count:1000,pricePerUnit:220},selectedSize:{extra:20},selectedMaterial:{extra:30},selectedFinishing:{extra:10}},280000],
      [{basePrice:500,selectedQuantity:{count:10}},5000],
      [{basePrice:500,selectedQuantity:{count:10,pricePerUnit:0}},0],
      [{basePrice:100,selectedQuantity:{count:10},selectedSize:{extra:-200}},0],
      [{basePrice:'500',selectedQuantity:{count:'100',pricePerUnit:'400'},selectedMaterial:{extra:'25'}},42500]
    ]
    for (const [input, expected] of cases) {
      assert.equal(mockApi.calculateEstimate(input).total,expected,JSON.stringify(input))
    }
  } finally { await server.close() }
})
