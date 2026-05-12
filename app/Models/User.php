<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_PENDING_DELETION = 'pending_deletion';
    public const STATUS_DELETED = 'deleted';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'id_role',
        'id_wilayah',
        'role_id',
        'wilayah_id',
        'status',
        'deletion_requested_by',
        'deletion_requested_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'id_role',
        'id_wilayah',
    ];

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (blank($user->uuid) && Schema::hasColumn('users', 'uuid')) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'deletion_requested_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function deletionRequester(): BelongsTo
    {
        return $this->belongsTo(self::class, 'deletion_requested_by')->withTrashed();
    }

    public function deletionRequests(): HasMany
    {
        return $this->hasMany(UserDeletionRequest::class);
    }

    public function requestedUserDeletions(): HasMany
    {
        return $this->hasMany(UserDeletionRequest::class, 'requested_by');
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role?->name === $roleName;
    }

    public function isSuperadmin(): bool
    {
        return $this->hasRole(Role::SUPERADMIN);
    }

    public function isAdminPusat(): bool
    {
        return $this->hasRole(Role::ADMIN_PUSAT);
    }

    public function isAdminWilayah(): bool
    {
        return $this->hasRole(Role::ADMIN_WILAYAH);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && ! $this->trashed();
    }

    public function hasPermissionTo(string $module, string $action): bool
    {
        if (! $this->relationLoaded('role')) {
            $this->load('role.permissions');
        } elseif ($this->role && ! $this->role->relationLoaded('permissions')) {
            $this->role->load('permissions');
        }

        return $this->role?->can($module, $action) ?? false;
    }

    public function assignableRoleNames(): array
    {
        return config('rbac.assignable_roles.' . ($this->role?->name ?? ''), []);
    }

    protected function idRole(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->role?->name ?? $this->role_id,
            set: function (mixed $value): array {
                if (blank($value)) {
                    return ['role_id' => null];
                }

                if (is_numeric($value)) {
                    return ['role_id' => (int) $value];
                }

                return ['role_id' => Role::where('name', (string) $value)->value('id')];
            },
        );
    }

    protected function idWilayah(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->wilayah_id,
            set: fn (mixed $value): array => ['wilayah_id' => $value ?: null],
        );
    }
}
