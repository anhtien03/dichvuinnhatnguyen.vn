#!/usr/bin/env bash
set -euo pipefail
export APP_ENV=testing DB_CONNECTION=sqlite DB_URL='' CACHE_STORE=array SESSION_DRIVER=array SEO_SNAPSHOTS_ENABLED=true SEO_AUTO_REFRESH=false
export DB_DATABASE="$(mktemp /tmp/seo-ci-XXXXXX.sqlite)"
export APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
(cd backend && php artisan migrate --force)
php scripts/seo-ci-fixtures.php
php -S 127.0.0.1:8000 -t dist scripts/seo-http-router.php >/tmp/seo-http.log 2>&1 &
http=$!
SEO_RENDER_PROXY=1 npm run preview -- --host 127.0.0.1 --port 4173 --strictPort >/tmp/seo-render.log 2>&1 &
render=$!
trap 'kill "$http" "$render" 2>/dev/null || true; rm -f "$DB_DATABASE"; cat /tmp/seo-http.log /tmp/seo-render.log' EXIT
probe() {
  local url="$1" code body
  body=$(mktemp)
  for i in $(seq 1 30); do
    code=$(curl -sS --connect-timeout 2 --max-time 5 -o "$body" -w '%{http_code}' "$url") || code=000
    echo "Readiness $url attempt=$i HTTP=$code"
    if [ "$code" = 200 ]; then rm -f "$body"; return 0; fi
    sleep 1
  done
  echo "Readiness failed: $url HTTP=$code"
  head -c 2000 "$body"; echo
  rm -f "$body"
  tail -n 60 backend/storage/logs/laravel.log 2>/dev/null || true
  return 1
}
probe http://127.0.0.1:8000/sitemap.xml
probe http://127.0.0.1:8000/api/v1/products/fixture-product
probe http://127.0.0.1:4173/
probe http://127.0.0.1:4173/api/v1/products/fixture-product
(cd backend && php artisan seo:render)
python3 - <<'PY'
from html.parser import HTMLParser
from urllib.request import urlopen
import xml.etree.ElementTree as ET
class Scan(HTMLParser):
    def __init__(self):
        super().__init__()
        self.tags=[]
    def handle_starttag(self,tag,attrs):
        self.tags.append((tag,dict(attrs)))
base='http://127.0.0.1:8000'
origin='https://dichvuinnhatnguyen.vn'
urls=ET.fromstring(urlopen(base+'/sitemap.xml',timeout=10).read()).findall('.//{http://www.sitemaps.org/schemas/sitemap/0.9}loc')
assert len(urls)>=9
for entry in urls:
    url=entry.text
    assert url.startswith(origin+'/')
    with urlopen(base+url[len(origin):],timeout=10) as r:
        assert r.status==200
        p=Scan()
        p.feed(r.read().decode('utf-8'))
    checks={
        'language':any(t=='html' and a.get('lang')=='vi' for t,a in p.tags),
        'title':sum(t=='title' for t,a in p.tags)==1,
        'description':any(t=='meta' and a.get('name')=='description' and a.get('content') for t,a in p.tags),
        'canonical':[a.get('href') for t,a in p.tags if t=='link' and a.get('rel')=='canonical']==[url],
        'H1':sum(t=='h1' for t,a in p.tags)==1,
    }
    print(url,checks)
    assert all(checks.values()),url
PY
if [ "${SEO_RAW_ONLY:-0}" != 1 ]; then
  status=0
  SEO_BASE_URL=http://127.0.0.1:8000 node tests/seo-browser-diagnostic.mjs || status=1
  node tests/seo-snapshot-faults.mjs || status=1
  exit "$status"
fi
