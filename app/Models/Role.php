<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    public const SUPERADMIN = 'superadmin';
    public const ADMIN_PUSAT = 'admin_pusat';
    public const ADMIN_WILAYAH = 'admin_wilayah';
    public const USER = 'user';

    protected $fillable = [
        'name',
        'label',
        'description',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function can(string $module, string $action): bool
    {
        if ($this->name === self::SUPERADMIN) {
            return true;
        }

        return $this->permissions->contains(function (Permission $permission) use ($module, $action) {
            $moduleMatches = $permission->module === '*' || $permission->module === $module;
            $actionMatches = $permission->action === '*' || $permission->action === $action;

            return $moduleMatches && $actionMatches;
        });
    }
}
