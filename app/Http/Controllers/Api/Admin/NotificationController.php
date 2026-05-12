<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class NotificationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $actor = $request->user();
        $allowedRoles = config('rbac.notification_target_roles.' . ($actor->role?->name ?? ''), []);

        if ($allowedRoles === []) {
            throw ValidationException::withMessages([
                'target_roles' => 'Akun ini tidak dapat mengirim notifikasi.',
            ]);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:2000'],
            'target_roles' => ['nullable', 'array'],
            'target_roles.*' => ['required', Rule::in(array_keys(config('rbac.roles')))],
            'target_wilayah_id' => ['nullable', 'integer', 'exists:wilayahs,id'],
            'metadata' => ['nullable', 'array'],
        ]);

        $targetRoles = $validated['target_roles'] ?? $allowedRoles;
        $targetRoles = array_values(array_intersect($targetRoles, $allowedRoles));
        $targetRoles = array_values(array_diff($targetRoles, [Role::USER]));

        if ($targetRoles === []) {
            throw ValidationException::withMessages([
                'target_roles' => 'Target role tidak diizinkan untuk akun ini.',
            ]);
        }

        $notification = AppNotification::create([
            'sender_id' => $actor->id,
            'target_roles' => $targetRoles,
            'target_wilayah_id' => $validated['target_wilayah_id'] ?? null,
            'title' => strip_tags($validated['title']),
            'message' => strip_tags($validated['message']),
            'metadata' => $validated['metadata'] ?? null,
            'sent_at' => now(),
        ]);

        $roleIds = Role::whereIn('name', $targetRoles)->pluck('id');
        $recipientCount = User::whereIn('role_id', $roleIds)
            ->when(isset($validated['target_wilayah_id']), fn ($query) => $query->where('wilayah_id', $validated['target_wilayah_id']))
            ->where('status', User::STATUS_ACTIVE)
            ->count();

        return response()->json([
            'message' => 'Notifikasi masuk ke outbox.',
            'recipient_count' => $recipientCount,
            'data' => $notification,
        ], 201);
    }
}
