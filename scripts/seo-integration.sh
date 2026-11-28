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
for url in http://127.0.0.1:8000/sitemap.xml http://127.0.0.1:4173/; do
  for i in $(seq 1 30); do curl -fs "$url" >/dev/null && break; sleep 1; done
  curl -fs "$url" >/dev/null
done
(cd backend && php artisan seo:render)
SEO_BASE_URL=http://127.0.0.1:8000 node tests/seo-browser-diagnostic.mjs
node tests/seo-snapshot-faults.mjs
