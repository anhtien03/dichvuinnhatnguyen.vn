<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'avatar',
        'is_active',
        'custom_permissions',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'custom_permissions' => 'array',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if user is Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role?->slug === 'super_admin';
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permissionSlug): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        // Check custom overrides
        $custom = $this->custom_permissions ?? [];
        if (isset($custom[$permissionSlug])) {
            return (bool) $custom[$permissionSlug];
        }

        // Check role permissions
        if (!$this->relationLoaded('role')) {
            $this->load('role.permissions');
        }

        return $this->role?->permissions->contains('slug', $permissionSlug) ?? false;
    }

    /**
     * Get all active permission slugs for this user
     */
    public function getAllPermissionSlugs(): array
    {
        if ($this->isSuperAdmin()) {
            return Permission::pluck('slug')->toArray();
        }

        if (!$this->relationLoaded('role')) {
            $this->load('role.permissions');
        }

        $rolePerms = $this->role?->permissions->pluck('slug')->toArray() ?? [];

        // Apply custom overrides
        $custom = $this->custom_permissions ?? [];
        foreach ($custom as $slug => $allowed) {
            if ($allowed && !in_array($slug, $rolePerms)) {
                $rolePerms[] = $slug;
            } elseif (!$allowed) {
                $rolePerms = array_diff($rolePerms, [$slug]);
            }
        }

        return array_values($rolePerms);
    }
}
