import { watchEffect } from 'vue'

/**
 * Update Document Head Meta Tags for SEO
 * @param {Object} options
 * @param {string} options.title
 * @param {string} options.description
 * @param {string} [options.keywords]
 * @param {string} [options.image]
 * @param {string} [options.url]
 * @param {string} [options.type]
 * @param {Object} [options.schema] - JSON-LD Schema.org object
 */
const CANONICAL_ORIGIN = 'https://dichvuinnhatnguyen.vn'

export function normalizeCanonicalUrl(rawUrl) {
  try {
    const parsed = new URL(rawUrl || window.location.pathname, CANONICAL_ORIGIN)
    let path = parsed.pathname || '/'
    if (path.length > 1 && path.endsWith('/')) {
      path = path.slice(0, -1)
    }
    return `${CANONICAL_ORIGIN}${path}`
  } catch {
    return `${CANONICAL_ORIGIN}/`
  }
}

export function setCanonicalLink(rawUrl) {
  const cleanUrl = normalizeCanonicalUrl(rawUrl)
  let canonicalLink = document.querySelector('link[rel="canonical"]')
  if (!canonicalLink) {
    canonicalLink = document.createElement('link')
    canonicalLink.setAttribute('rel', 'canonical')
    document.head.appendChild(canonicalLink)
  }
  canonicalLink.setAttribute('href', cleanUrl)
  return cleanUrl
}

export function setMetaTags({
  title = 'Xưởng In Nhật Nguyên - In Nhanh, Giá Gốc Trực Tiếp TPHCM',
  description = 'Xưởng in Offset & Kỹ thuật số Nhật Nguyên chuyên in hộp giấy, túi giấy, tem nhãn decal, catalogue, namecard giá gốc tận xưởng.',
  keywords = 'in hộp giấy, in túi giấy, xưởng in offset tphcm, in decal tem nhãn, in catalogue',
  image = 'https://dichvuinnhatnguyen.vn/og-banner.jpg',
  url = window.location.pathname,
  type = 'website',
  robots = 'index, follow',
  schema = null
} = {}) {
  const canonicalUrl = setCanonicalLink(url)
  // 1. Update Title
  document.title = title.includes('Nhật Nguyên') ? title : `${title} | In Ấn Nhật Nguyên`

  // Helper to set or create meta tag
  const setMeta = (attr, key, content) => {
    if (!content) return
    let el = document.querySelector(`meta[${attr}="${key}"]`)
    if (!el) {
      el = document.createElement('meta')
      el.setAttribute(attr, key)
      document.head.appendChild(el)
    }
    el.setAttribute('content', content)
  }

  // 2. Standard Meta Tags
  setMeta('name', 'description', description)
  setMeta('name', 'keywords', keywords)
  setMeta('name', 'robots', robots)

  // 3. Open Graph (Facebook / Zalo / LinkedIn)
  setMeta('property', 'og:title', title)
  setMeta('property', 'og:description', description)
  setMeta('property', 'og:type', type)
  setMeta('property', 'og:image', image)
  setMeta('property', 'og:url', canonicalUrl)
  setMeta('property', 'og:site_name', 'Xưởng In Ấn Nhật Nguyên')

  // 4. Twitter Card
  setMeta('name', 'twitter:card', 'summary_large_image')
  setMeta('name', 'twitter:title', title)
  setMeta('name', 'twitter:description', description)
  setMeta('name', 'twitter:image', image)

  // 5. Canonical Link (đã chuẩn hóa ở trên via setCanonicalLink)

  // 6. Schema.org JSON-LD Structured Data
  if (schema) {
    let script = document.querySelector('script[data-schema="dynamic"]')
    if (!script) {
      script = document.createElement('script')
      script.setAttribute('type', 'application/ld+json')
      script.setAttribute('data-schema', 'dynamic')
      document.head.appendChild(script)
    }
    script.textContent = JSON.stringify(schema)
  }
}

/**
 * Generate Slug from Vietnamese string
 */
export function slugify(str = '') {
  if (!str) return ''
  return str
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[đĐ]/g, 'd')
    .replace(/[^a-z0-9\s-]/g, '')
    .trim()
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
}

