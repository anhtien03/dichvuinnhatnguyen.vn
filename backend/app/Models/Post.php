<?php

namespace App\Models;

use App\Services\SecuritySanitizerService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'author_name',
        'title',
        'slug',
        'excerpt',
        'content',
        'thumbnail',
        'banner_image',
        'category',
        'is_published',
        'published_at',
        'meta_title',
        'meta_description',
    ];

    protected $appends = [
        'detected_external_links',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Inspect and return list of external links found in post content
     */
    public function getDetectedExternalLinksAttribute(): array
    {
        return SecuritySanitizerService::extractExternalLinks($this->content);
    }
}
