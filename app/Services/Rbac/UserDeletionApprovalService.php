<?php

namespace App\Services\Rbac;

use App\Models\Role;
use App\Models\User;
use App\Models\UserDeletionRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserDeletionApprovalService
{
    public function requestDeletion(User $target, User $requestedBy, ?string $reason = null): UserDeletionRequest
    {
        if (! $requestedBy->hasRole(Role::ADMIN_PUSAT)) {
            throw ValidationException::withMessages([
                'user' => 'Hanya admin pusat yang membuat antrean persetujuan hapus user.',
            ]);
        }

        if (! in_array($target->role?->name, $requestedBy->assignableRoleNames(), true)) {
            throw ValidationException::withMessages([
                'user' => 'Admin pusat hanya dapat menghapus user dengan role admin_wilayah atau user.',
            ]);
        }

        return DB::transaction(function () use ($target, $requestedBy, $reason) {
            $existing = UserDeletionRequest::where('user_id', $target->id)
                ->where('status', UserDeletionRequest::PENDING)
                ->first();

            if ($existing) {
                return $existing;
            }

            $target->forceFill([
                'status' => User::STATUS_PENDING_DELETION,
                'deletion_requested_by' => $requestedBy->id,
                'deletion_requested_at' => now(),
            ])->save();

            return UserDeletionRequest::create([
                'user_id' => $target->id,
                'requested_by' => $requestedBy->id,
                'status' => UserDeletionRequest::PENDING,
                'requested_reason' => $reason,
                'requested_at' => now(),
            ]);
        });
    }

    public function approve(UserDeletionRequest $request, User $reviewer, ?string $note = null): UserDeletionRequest
    {
        $this->assertPendingAndReviewable($request, $reviewer);

        return DB::transaction(function () use ($request, $reviewer, $note) {
            $target = $request->user()->withTrashed()->firstOrFail();

            $target->forceFill([
                'status' => User::STATUS_DELETED,
                'deletion_requested_by' => $request->requested_by,
                'deletion_requested_at' => $request->requested_at,
            ])->save();
            $target->delete();

            $request->forceFill([
                'status' => UserDeletionRequest::APPROVED,
                'reviewed_by' => $reviewer->id,
                'review_note' => $note,
                'reviewed_at' => now(),
            ])->save();

            return $request->fresh(['user', 'requester', 'reviewer']);
        });
    }

    public function reject(UserDeletionRequest $request, User $reviewer, ?string $note = null): UserDeletionRequest
    {
        $this->assertPendingAndReviewable($request, $reviewer);

        return DB::transaction(function () use ($request, $reviewer, $note) {
            $target = $request->user()->withTrashed()->firstOrFail();

            $target->forceFill([
                'status' => User::STATUS_ACTIVE,
                'deletion_requested_by' => null,
                'deletion_requested_at' => null,
            ])->save();

            $request->forceFill([
                'status' => UserDeletionRequest::REJECTED,
                'reviewed_by' => $reviewer->id,
                'review_note' => $note,
                'reviewed_at' => now(),
            ])->save();

            return $request->fresh(['user', 'requester', 'reviewer']);
        });
    }

    private function assertPendingAndReviewable(UserDeletionRequest $request, User $reviewer): void
    {
        if (! $reviewer->hasRole(Role::SUPERADMIN)) {
            throw ValidationException::withMessages([
                'reviewer' => 'Hanya superadmin yang dapat meninjau penghapusan user.',
            ]);
        }

        if ($request->status !== UserDeletionRequest::PENDING) {
            throw ValidationException::withMessages([
                'request' => 'Permintaan ini sudah ditinjau.',
            ]);
        }
    }
}
