<?php

namespace App\Services\Rbac;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class DataScope
{
    public function scopedWilayahId(?User $user): ?int
    {
        return $user?->hasRole(Role::ADMIN_WILAYAH) ? (int) $user->wilayah_id : null;
    }

    public function scopedWilayahIdForRequest(Request $request, string $inputKey = 'wilayah_id'): ?int
    {
        $forcedWilayahId = $request->attributes->get('scope.wilayah_id');

        if ($forcedWilayahId !== null) {
            return (int) $forcedWilayahId;
        }

        return $this->scopedWilayahId($request->user()) ?: ($request->filled($inputKey) ? (int) $request->input($inputKey) : null);
    }

    public function applyToQuery(EloquentBuilder|QueryBuilder $query, ?User $user, string $column): EloquentBuilder|QueryBuilder
    {
        $wilayahId = $this->scopedWilayahId($user);

        if ($wilayahId !== null) {
            $query->where($column, $wilayahId);
        }

        return $query;
    }

    public function applyToRequestQuery(EloquentBuilder|QueryBuilder $query, Request $request, string $column): EloquentBuilder|QueryBuilder
    {
        $wilayahId = $request->attributes->get('scope.wilayah_id') ?? $this->scopedWilayahId($request->user());

        if ($wilayahId !== null) {
            $query->where($column, (int) $wilayahId);
        }

        return $query;
    }

    public function injectRegionIntoRequest(Request $request, string $field = 'wilayah_id'): void
    {
        $wilayahId = $request->attributes->get('scope.wilayah_id') ?? $this->scopedWilayahId($request->user());

        if ($wilayahId !== null) {
            $request->merge([$field => (int) $wilayahId]);
        }
    }

    public function coercePayload(array $payload, ?User $user, string $field = 'wilayah_id'): array
    {
        $wilayahId = $this->scopedWilayahId($user);

        if ($wilayahId !== null) {
            $payload[$field] = $wilayahId;
        }

        return $payload;
    }

    public function assertWritableRegion(?User $user, int|string|null $wilayahId): void
    {
        $scopedWilayahId = $this->scopedWilayahId($user);

        if ($scopedWilayahId !== null && (int) $wilayahId !== $scopedWilayahId) {
            throw ValidationException::withMessages([
                'wilayah_id' => 'Wilayah tidak sesuai dengan wilayah akun yang sedang login.',
            ]);
        }
    }

    public function wilayahOptionsFor(?User $user): Collection
    {
        if ($this->scopedWilayahId($user) !== null && $user->wilayah) {
            return collect([$user->wilayah]);
        }

        return \App\Models\Wilayah::orderBy('nama_wilayah')->get();
    }
}
