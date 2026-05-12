<?php

namespace App\Services\Audit;

use App\Models\AnakBimbingan;
use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\KehadiranIbadah;
use App\Models\LaporanBlesscomn;
use App\Models\LaporanPa;
use App\Models\MasterBlesscomn;
use App\Models\MasterBukuPa;
use App\Models\Pelayanan;
use App\Models\Pembimbing;
use App\Models\PengurusBlesscomn;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserDeletionRequest;
use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * Passwords and tokens are never persisted to logs.
     */
    private const SENSITIVE_KEYS = [
        'password',
        'password_value',
        'current_password',
        'new_password',
        'new_password_confirmation',
        'password_confirmation',
        'remember_token',
        'token',
        'api_token',
    ];

    public function log(string $event, array $payload = []): AuditLog
    {
        /** @var User|null $actor */
        $actor = $payload['actor'] ?? Auth::user();
        $request = $payload['request'] ?? request();
        $metadata = $payload['metadata'] ?? null;

        if ($request instanceof Request && $request->hasSession() && $request->session()->has('impersonator_id')) {
            $metadata = is_array($metadata) ? $metadata : ['value' => $metadata];
            $metadata['impersonator_id'] = $request->session()->get('impersonator_id');
            $metadata['impersonator_email'] = $request->session()->get('impersonator_email');
        }

        return AuditLog::create([
            'actor_id' => $actor?->id,
            'actor_name' => $actor?->name,
            'actor_email' => $actor?->email,
            'actor_role' => $actor?->role?->name,
            'event' => $event,
            'module' => $payload['module'] ?? null,
            'auditable_type' => $payload['auditable_type'] ?? null,
            'auditable_id' => isset($payload['auditable_id']) ? (string) $payload['auditable_id'] : null,
            'auditable_label' => $payload['auditable_label'] ?? null,
            'ip_address' => $request instanceof Request ? $request->ip() : null,
            'user_agent' => $request instanceof Request ? substr((string) $request->userAgent(), 0, 1000) : null,
            'old_values' => $this->sanitize($payload['old_values'] ?? null),
            'new_values' => $this->sanitize($payload['new_values'] ?? null),
            'metadata' => $this->sanitize($metadata),
            'created_at' => now(),
        ]);
    }

    public function logModel(Model $model, string $event, ?array $oldValues = null, ?array $newValues = null): AuditLog
    {
        return $this->log($event, [
            'module' => $this->moduleForModel($model),
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'auditable_label' => $this->labelForModel($model),
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    public function moduleForModel(Model|string $model): string
    {
        $class = is_string($model) ? $model : $model::class;

        return match ($class) {
            Pelayanan::class, Wilayah::class, MasterBukuPa::class => 'master_data',
            Pembimbing::class, AnakBimbingan::class, LaporanPa::class => 'pa',
            PengurusBlesscomn::class, MasterBlesscomn::class, LaporanBlesscomn::class => 'blesscomn',
            KehadiranIbadah::class => 'kehadiran_ibadah',
            User::class => 'users',
            Role::class, Permission::class => 'roles',
            UserDeletionRequest::class => 'user_deletion_requests',
            AppNotification::class => 'notifications',
            default => 'system',
        };
    }

    public function labelForModel(Model $model): ?string
    {
        foreach ([
            'name',
            'email',
            'label',
            'nama_wilayah',
            'nama_pelayanan',
            'nama_pembimbing',
            'nama_anak',
            'nama_buku',
            'nama_blesscomn',
            'nama_ibadah',
            'title',
        ] as $field) {
            if (filled($model->{$field} ?? null)) {
                return (string) $model->{$field};
            }
        }

        return $model::class . '#' . $model->getKey();
    }

    public function sanitize(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Model) {
            $value = $value->toArray();
        }

        if (! is_array($value)) {
            return $value;
        }

        $sanitized = [];

        foreach ($value as $key => $item) {
            if (in_array(strtolower((string) $key), self::SENSITIVE_KEYS, true)) {
                $sanitized[$key] = '[redacted]';
                continue;
            }

            $sanitized[$key] = is_array($item) ? $this->sanitize($item) : $item;
        }

        return $sanitized;
    }
}
