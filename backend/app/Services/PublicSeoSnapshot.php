<?php

namespace App\Services;

use App\Models\{Product, Category, Post, Banner, SiteSetting};
use Illuminate\Support\Facades\File;

class PublicSeoSnapshot
{
    public static function version(): string
    {
        $hash = hash_init('sha256');
        hash_update($hash, hash_file('sha256', base_path('../dist/index.html')));
        foreach ([Product::where('is_active', true), Category::where('is_active', true),
            Post::where('is_published', true), Banner::where('is_active', true), SiteSetting::query()] as $query) {
            foreach ($query->orderBy($query->getModel()->getKeyName())->cursor() as $row) {
                hash_update($hash, json_encode($row->getAttributes(), JSON_THROW_ON_ERROR));
                hash_update($hash, "\n");
            }
        }
        return hash_final($hash);
    }

    private static function path(string $route): string
    {
        return storage_path('app/private/seo-snapshots/'.hash('sha256', $route).'.json');
    }

    public static function read(string $route): ?array
    {
        $file = self::path($route);
        if (!is_file($file)) return null;
        $snapshot = json_decode(file_get_contents($file), true);
        if (!is_array($snapshot) || ($snapshot['version'] ?? '') !== self::version()
            || ($snapshot['route'] ?? '') !== $route || empty($snapshot['page']['html'])) return null;
        return $snapshot['page'];
    }

    public static function write(string $route, string $version, array $page): void
    {
        if ($version !== self::version()) throw new \RuntimeException('Public data changed during render');
        $file = self::path($route);
        File::ensureDirectoryExists(dirname($file), 0700);
        File::replace($file, json_encode(compact('route', 'version', 'page'), JSON_THROW_ON_ERROR));
    }
}
