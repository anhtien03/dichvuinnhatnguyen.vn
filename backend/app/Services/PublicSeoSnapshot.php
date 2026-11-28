<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class PublicSeoSnapshot
{
    public const STATE_FRESH = 'FRESH';
    public const STATE_STALE = 'STALE';
    public const STATE_REVALIDATING = 'REVALIDATING';
    public const STATE_INVALID = 'INVALID';
    public const STATE_MISSING = 'MISSING';

    private static function baseDir(): string
    {
        return storage_path('app/private/seo-snapshots');
    }

    private static function manifestPath(): string
    {
        return self::baseDir().'/manifest.json';
    }

    private static function lockPath(): string
    {
        return self::baseDir().'/manifest.lock';
    }

    public static function path(string $route): string
    {
        return self::baseDir().'/'.hash('sha256', $route).'.json';
    }

    public static function lkgPath(string $route): string
    {
        return self::baseDir().'/'.hash('sha256', $route).'.lkg.json';
    }

    public static function currentBuildHash(): string
    {
        $indexFile = base_path('../dist/index.html');
        if (!is_file($indexFile)) {
            return 'missing-build';
        }

        return (string) hash_file('sha256', $indexFile);
    }

    /**
     * Read manifest without scanning database tables (O(1) file read).
     */
    public static function readManifest(): array
    {
        $file = self::manifestPath();
        $buildFile = base_path('../dist/index.html');
        $buildMtime = is_file($buildFile) ? (int) filemtime($buildFile) : 0;

        $default = [
            'global_rev' => 1,
            'build_mtime' => $buildMtime,
            'build_hash' => self::currentBuildHash(),
            'routes' => [],
        ];

        if (!is_file($file)) {
            return $default;
        }

        $decoded = json_decode((string) file_get_contents($file), true);
        if (!is_array($decoded)) {
            return $default;
        }

        $decoded['global_rev'] = (int) ($decoded['global_rev'] ?? 1);
        $decoded['routes'] = is_array($decoded['routes'] ?? null) ? $decoded['routes'] : [];

        if (($decoded['build_mtime'] ?? 0) !== $buildMtime) {
            $actualHash = self::currentBuildHash();
            if (($decoded['build_hash'] ?? '') !== $actualHash) {
                $decoded['build_hash'] = $actualHash;
                $decoded['build_mtime'] = $buildMtime;
                $decoded['global_rev']++;
            }
        }

        return $decoded;
    }

    /**
     * Atomically mutate manifest under exclusive file lock.
     */
    private static function mutateManifest(callable $mutator): array
    {
        $dir = self::baseDir();
        File::ensureDirectoryExists($dir, 0700);
        $lock = fopen(self::lockPath(), 'c+');
        if (!$lock || !flock($lock, LOCK_EX)) {
            if ($lock) {
                fclose($lock);
            }
            throw new \RuntimeException('Unable to lock SEO snapshot manifest');
        }

        try {
            $manifest = self::readManifest();
            $updated = $mutator($manifest);
            File::replace(self::manifestPath(), json_encode($updated, JSON_THROW_ON_ERROR));

            return $updated;
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    /**
     * Return O(1) global/build version token without full-table DB scans.
     */
    public static function version(): string
    {
        $manifest = self::readManifest();

        return ($manifest['build_hash'] ?? 'missing-build').':'.($manifest['global_rev'] ?? 1);
    }

    public static function routeRevision(string $route, ?array $manifest = null): string
    {
        $manifest ??= self::readManifest();
        $buildHash = (string) ($manifest['build_hash'] ?? 'missing-build');
        $routeEntry = $manifest['routes'][$route] ?? null;
        $routeRev = is_array($routeEntry) ? (int) ($routeEntry['revision'] ?? 1) : 1;

        return $buildHash.':'.$routeRev;
    }

    /**
     * Invalidate only specific affected routes instead of wiping the entire site.
     */
    public static function invalidateRoutes(array $routes, bool $critical = false, string $reason = 'update'): void
    {
        $normalized = array_values(array_unique(array_filter(array_map(
            static fn (string $r): string => '/'.ltrim($r, '/'),
            $routes
        ))));

        // Normalize root "/"
        $normalized = array_map(static fn (string $r): string => $r === '/' ? '/' : rtrim($r, '/'), $normalized);
        $normalized = array_values(array_unique($normalized));

        if ($normalized === []) {
            return;
        }

        $now = time();
        self::mutateManifest(static function (array $manifest) use ($normalized, $critical, $reason, $now): array {
            foreach ($normalized as $route) {
                $existing = $manifest['routes'][$route] ?? ['revision' => 1];
                $prevCritical = (bool) ($existing['critical'] ?? false);
                $manifest['routes'][$route] = [
                    'revision' => ((int) ($existing['revision'] ?? 1)) + 1,
                    'critical' => $prevCritical || $critical,
                    'invalidated_at' => $now,
                    'reason' => $reason,
                ];
            }

            return $manifest;
        });
    }

    public static function invalidateAll(bool $critical = false, string $reason = 'global'): void
    {
        $now = time();
        self::mutateManifest(static function (array $manifest) use ($critical, $reason, $now): array {
            $manifest['global_rev'] = ((int) ($manifest['global_rev'] ?? 1)) + 1;
            foreach ($manifest['routes'] as $route => $entry) {
                $manifest['routes'][$route] = [
                    'revision' => ((int) ($entry['revision'] ?? 1)) + 1,
                    'critical' => ((bool) ($entry['critical'] ?? false)) || $critical,
                    'invalidated_at' => $now,
                    'reason' => $reason,
                ];
            }

            return $manifest;
        });
    }

    /**
     * Determine exact affected public routes for a mutated/deleted model.
     */
    public static function invalidateForModel(Model $model, string $event = 'saved'): void
    {
        $routes = [];
        $critical = $event === 'deleted';
        $reason = class_basename($model).':'.$event;

        if ($model instanceof Product) {
            if ($model->wasRecentlyCreated || $model->wasChanged(['base_price', 'specs', 'is_active', 'slug', 'category_id'])) {
                $critical = true;
            }
            if (!empty($model->slug)) {
                $routes[] = '/san-pham/'.rawurlencode((string) $model->slug);
            }
            $oldSlug = $model->getOriginal('slug');
            if (!empty($oldSlug) && $oldSlug !== $model->slug) {
                $routes[] = '/san-pham/'.rawurlencode((string) $oldSlug);
            }
            $routes[] = '/san-pham';
            $routes[] = '/';

            $catIds = array_values(array_unique(array_filter([
                $model->category_id,
                $model->getOriginal('category_id'),
            ])));
            if ($catIds !== []) {
                foreach (Category::whereIn('id', $catIds)->pluck('slug') as $catSlug) {
                    $routes[] = '/danh-muc/'.rawurlencode((string) $catSlug);
                }
            }
        } elseif ($model instanceof Category) {
            if ($model->wasRecentlyCreated || $model->wasChanged(['is_active', 'slug', 'parent_id'])) {
                $critical = true;
            }
            if (!empty($model->slug)) {
                $routes[] = '/danh-muc/'.rawurlencode((string) $model->slug);
            }
            $oldSlug = $model->getOriginal('slug');
            if (!empty($oldSlug) && $oldSlug !== $model->slug) {
                $routes[] = '/danh-muc/'.rawurlencode((string) $oldSlug);
            }
            $routes[] = '/san-pham';
            $routes[] = '/';
            foreach (Product::where('category_id', $model->id)->where('is_active', true)->pluck('slug') as $prodSlug) {
                $routes[] = '/san-pham/'.rawurlencode((string) $prodSlug);
            }
        } elseif ($model instanceof Post) {
            if ($model->wasRecentlyCreated || $model->wasChanged(['is_published', 'slug'])) {
                $critical = true;
            }
            if (!empty($model->slug)) {
                $routes[] = '/tin-tuc/'.rawurlencode((string) $model->slug);
            }
            $oldSlug = $model->getOriginal('slug');
            if (!empty($oldSlug) && $oldSlug !== $model->slug) {
                $routes[] = '/tin-tuc/'.rawurlencode((string) $oldSlug);
            }
            $routes[] = '/tin-tuc';
            $routes[] = '/';
        } elseif ($model instanceof Banner) {
            $routes[] = '/';
        } elseif ($model instanceof SiteSetting) {
            $routes = ['/', '/gioi-thieu', '/lien-he', '/yeu-cau-bao-gia'];
        }

        if ($routes !== []) {
            self::invalidateRoutes($routes, $critical, $reason);
        }
    }

    /**
     * Resolve lifecycle state for a public route with zero DB scans.
     *
     * @return array{state: string, page: ?array, reason: string, generated_at: ?int}
     */
    public static function resolve(string $route): array
    {
        $file = self::path($route);
        if (!is_file($file)) {
            return ['state' => self::STATE_MISSING, 'page' => null, 'reason' => 'snapshot_missing', 'generated_at' => null];
        }

        $raw = file_get_contents($file);
        if ($raw === false) {
            return ['state' => self::STATE_INVALID, 'page' => null, 'reason' => 'snapshot_unreadable', 'generated_at' => null];
        }

        $snapshot = json_decode($raw, true);
        if (!is_array($snapshot) || !self::isValidSnapshotPayload($snapshot, $route)) {
            return ['state' => self::STATE_INVALID, 'page' => null, 'reason' => 'snapshot_corrupt_or_invalid_schema', 'generated_at' => null];
        }

        $manifest = self::readManifest();
        $expectedVersion = self::routeRevision($route, $manifest);
        $expectedBuild = (string) ($manifest['build_hash'] ?? '');
        $snapshotVersion = (string) ($snapshot['version'] ?? '');
        $snapshotBuild = (string) ($snapshot['build_hash'] ?? explode(':', $snapshotVersion)[0]);
        $generatedAt = (int) ($snapshot['generated_at'] ?? 0);

        if ($snapshotBuild !== $expectedBuild) {
            return ['state' => self::STATE_INVALID, 'page' => null, 'reason' => 'frontend_build_changed', 'generated_at' => $generatedAt];
        }

        $routeEntry = $manifest['routes'][$route] ?? null;
        $isCritical = is_array($routeEntry) && (bool) ($routeEntry['critical'] ?? false);
        $invalidatedAt = is_array($routeEntry) ? ($routeEntry['invalidated_at'] ?? null) : null;
        $staleTtl = (int) config('seo.stale_ttl_seconds', 3600);
        $freshTtl = (int) config('seo.fresh_ttl_seconds', 86400);
        $now = time();

        if ($snapshotVersion !== $expectedVersion) {
            // Non-critical content change within stale TTL can be served as STALE.
            $withinStaleWindow = $invalidatedAt !== null && ($now - (int) $invalidatedAt) <= $staleTtl;
            if (!$isCritical && $withinStaleWindow) {
                return ['state' => self::STATE_STALE, 'page' => $snapshot['page'], 'reason' => 'non_critical_stale', 'generated_at' => $generatedAt];
            }

            return ['state' => self::STATE_INVALID, 'page' => null, 'reason' => 'version_mismatch', 'generated_at' => $generatedAt];
        }

        if ($generatedAt > 0 && ($now - $generatedAt) > $freshTtl) {
            return ['state' => self::STATE_STALE, 'page' => $snapshot['page'], 'reason' => 'ttl_expired', 'generated_at' => $generatedAt];
        }

        return ['state' => self::STATE_FRESH, 'page' => $snapshot['page'], 'reason' => 'fresh', 'generated_at' => $generatedAt];
    }

    public static function read(string $route): ?array
    {
        $resolved = self::resolve($route);

        return in_array($resolved['state'], [self::STATE_FRESH, self::STATE_STALE], true)
            ? $resolved['page']
            : null;
    }

    public static function write(string $route, string $expectedVersion, array $page): void
    {
        $currentVersion = self::routeRevision($route);
        if ($expectedVersion !== $currentVersion) {
            throw new \RuntimeException('Public data changed during render');
        }

        if (!self::isValidPage($page)) {
            throw new \RuntimeException('Invalid rendered page payload');
        }

        $manifest = self::readManifest();
        $buildHash = (string) ($manifest['build_hash'] ?? self::currentBuildHash());
        $checksum = self::computeChecksum($page);
        $generatedAt = time();

        $record = [
            'route' => $route,
            'version' => $currentVersion,
            'build_hash' => $buildHash,
            'checksum' => $checksum,
            'generated_at' => $generatedAt,
            'page' => $page,
        ];

        $encoded = json_encode($record, JSON_THROW_ON_ERROR);
        $file = self::path($route);
        File::ensureDirectoryExists(dirname($file), 0700);
        File::replace($file, $encoded);
        File::replace(self::lkgPath($route), $encoded);

        self::mutateManifest(static function (array $m) use ($route, $currentVersion, $generatedAt): array {
            $rev = (int) (explode(':', $currentVersion)[1] ?? 1);
            $m['routes'][$route] = [
                'revision' => $rev,
                'critical' => false,
                'invalidated_at' => null,
                'rendered_at' => $generatedAt,
                'reason' => 'rendered',
            ];

            return $m;
        });
    }

    private static function computeChecksum(array $page): string
    {
        return hash('sha256', json_encode($page, JSON_THROW_ON_ERROR));
    }

    private static function isValidPage(mixed $page): bool
    {
        if (!is_array($page)) {
            return false;
        }
        if (empty($page['html']) || empty($page['title']) || empty($page['description']) || empty($page['canonical'])) {
            return false;
        }
        $canonical = (string) $page['canonical'];
        if (!str_starts_with($canonical, 'https://dichvuinnhatnguyen.vn/')) {
            return false;
        }
        $html = (string) $page['html'];
        if (preg_match('/<\s*script\b|javascript:/i', $html)) {
            return false;
        }

        return true;
    }

    private static function isValidSnapshotPayload(array $snapshot, string $expectedRoute): bool
    {
        if (($snapshot['route'] ?? '') !== $expectedRoute) {
            return false;
        }
        if (!self::isValidPage($snapshot['page'] ?? null)) {
            return false;
        }
        if (!empty($snapshot['checksum'])) {
            $expected = self::computeChecksum($snapshot['page']);
            if (!hash_equals((string) $snapshot['checksum'], $expected)) {
                return false;
            }
        }

        return true;
    }
}
