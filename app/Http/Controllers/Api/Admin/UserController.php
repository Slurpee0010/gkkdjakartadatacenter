<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use App\Services\Rbac\UserDeletionApprovalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function __construct(private readonly UserDeletionApprovalService $deletionApprovalService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $actor = $request->user();

        $users = User::query()
            ->with(['role', 'wilayah'])
            ->when($actor->isAdminPusat(), function ($query) use ($actor) {
                $query->whereHas('role', fn ($roleQuery) => $roleQuery->whereIn('name', $actor->assignableRoleNames()));
            })
            ->latest()
            ->paginate((int) $request->get('per_page', 25));

        return response()->json($users);
    }

    public function store(Request $request): JsonResponse
    {
        $actor = $request->user();
        $validated = $this->validatedUserPayload($request);
        $role = Role::where('name', $validated['role'])->firstOrFail();

        $this->assertAssignable($actor, $role);
        $this->assertWilayahRequirement($role, $validated['wilayah_id'] ?? null);

        $user = User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'role_id' => $role->id,
            'wilayah_id' => $validated['wilayah_id'],
            'status' => User::STATUS_ACTIVE,
        ]);

        return response()->json([
            'message' => 'User berhasil dibuat.',
            'data' => $user->load(['role', 'wilayah']),
        ], 201);
    }

    public function update(Request $request, User $user, AuditLogger $auditLogger): JsonResponse
    {
        $actor = $request->user();
        $validated = $this->validatedUserPayload($request, $user);
        $role = Role::where('name', $validated['role'])->firstOrFail();

        $this->assertAssignable($actor, $role);
        $this->assertManageableTarget($actor, $user);
        $this->assertWilayahRequirement($role, $validated['wilayah_id'] ?? null);

        $payload = [
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'role_id' => $role->id,
            'wilayah_id' => $validated['wilayah_id'],
            'status' => $actor->isSuperadmin() && isset($validated['status'])
                ? $validated['status']
                : $user->status,
        ];

        $passwordChanged = ! empty($validated['password']);

        if ($passwordChanged) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);

        if ($passwordChanged) {
            $auditLogger->log(AuditLog::EVENT_PASSWORD_CHANGED, [
                'module' => 'users',
                'auditable_type' => $user::class,
                'auditable_id' => $user->id,
                'auditable_label' => $user->email,
                'metadata' => [
                    'target_user_id' => $user->id,
                    'target_email' => $user->email,
                    'target_role' => $user->role?->name,
                    'changed_by_self' => $actor->id === $user->id,
                    'password_value' => '[redacted]',
                ],
            ]);
        }

        return response()->json([
            'message' => 'User berhasil diperbarui.',
            'data' => $user->fresh(['role', 'wilayah']),
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $actor = $request->user();

        abort_if($actor->id === $user->id, 422, 'User tidak dapat menghapus akun sendiri.');

        if ($actor->isAdminPusat()) {
            $this->assertManageableTarget($actor, $user);

            $approvalRequest = $this->deletionApprovalService->requestDeletion(
                $user,
                $actor,
                $request->string('reason')->trim()->toString() ?: null
            );

            return response()->json([
                'message' => 'Permintaan hapus user masuk ke antrean persetujuan superadmin.',
                'data' => $approvalRequest->load(['user.role', 'requester']),
            ], 202);
        }

        $this->assertAssignable($actor, $user->role);

        $user->forceFill([
            'status' => User::STATUS_DELETED,
        ])->save();
        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus.',
        ]);
    }

    private function validatedUserPayload(Request $request, ?User $user = null): array
    {
        if ($request->has('id_role') && ! $request->has('role')) {
            $request->merge(['role' => $request->input('id_role')]);
        }

        if ($request->has('id_wilayah') && ! $request->has('wilayah_id')) {
            $request->merge(['wilayah_id' => $request->input('id_wilayah')]);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:10', 'max:255'],
            'role' => ['required', Rule::in(array_keys(config('rbac.roles')))],
            'wilayah_id' => ['required', 'integer', 'exists:wilayahs,id'],
            'status' => ['nullable', Rule::in([User::STATUS_ACTIVE, User::STATUS_PENDING_DELETION])],
        ]);
    }

    private function assertAssignable(User $actor, ?Role $role): void
    {
        if (! $role || ! in_array($role->name, $actor->assignableRoleNames(), true)) {
            throw ValidationException::withMessages([
                'role' => 'Role ini tidak dapat diberikan oleh akun Anda.',
            ]);
        }
    }

    private function assertManageableTarget(User $actor, User $target): void
    {
        if ($actor->isSuperadmin()) {
            return;
        }

        if (! in_array($target->role?->name, $actor->assignableRoleNames(), true)) {
            throw ValidationException::withMessages([
                'user' => 'Anda tidak dapat mengelola user dengan role tersebut.',
            ]);
        }
    }

    private function assertWilayahRequirement(Role $role, mixed $wilayahId): void
    {
        if (empty($wilayahId)) {
            throw ValidationException::withMessages([
                'wilayah_id' => 'Wilayah wajib diisi untuk setiap user.',
            ]);
        }
    }
}
