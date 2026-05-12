<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use App\Services\Audit\AuditLogger;
use App\Services\Rbac\UserDeletionApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function __construct(private readonly UserDeletionApprovalService $deletionApprovalService)
    {
    }

    public function index(Request $request): View
    {
        $actor = $request->user();
        $search = trim((string) $request->query('q'));

        $users = User::query()
            ->with(['role', 'wilayah'])
            ->when($actor->isAdminPusat(), function ($query) use ($actor) {
                $query->whereHas('role', fn ($roleQuery) => $roleQuery->whereIn('name', $actor->assignableRoleNames()));
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('uuid', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('users.index', compact('users', 'search'));
    }

    public function create(Request $request): View
    {
        return view('users.create', [
            'roles' => $this->assignableRolesFor($request->user()),
            'wilayahs' => Wilayah::orderBy('nama_wilayah')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $actor = $request->user();
        $validated = $this->validatedPayload($request);
        $role = Role::where('name', $validated['id_role'])->firstOrFail();

        $this->assertAssignable($actor, $role);

        User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'role_id' => $role->id,
            'wilayah_id' => $validated['id_wilayah'],
            'status' => User::STATUS_ACTIVE,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(Request $request, User $user): View
    {
        $this->assertManageableTarget($request->user(), $user);

        return view('users.edit', [
            'user' => $user->load(['role', 'wilayah']),
            'roles' => $this->assignableRolesFor($request->user()),
            'wilayahs' => Wilayah::orderBy('nama_wilayah')->get(),
        ]);
    }

    public function update(Request $request, User $user, AuditLogger $auditLogger): RedirectResponse
    {
        $actor = $request->user();
        $this->assertManageableTarget($actor, $user);

        $validated = $this->validatedPayload($request, $user);
        $role = Role::where('name', $validated['id_role'])->firstOrFail();

        $this->assertAssignable($actor, $role);

        $payload = [
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'role_id' => $role->id,
            'wilayah_id' => $validated['id_wilayah'],
            'status' => $actor->isSuperadmin()
                ? ($validated['status'] ?? $user->status)
                : $user->status,
        ];

        $passwordChanged = filled($validated['password'] ?? null);

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
                    'target_role' => $user->fresh('role')->role?->name,
                    'changed_by_self' => $actor->id === $user->id,
                    'password_value' => '[redacted]',
                ],
            ]);
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();

        abort_if($actor->id === $user->id, 422, 'User tidak dapat menghapus akun sendiri.');

        if ($actor->isAdminPusat()) {
            $this->assertManageableTarget($actor, $user);
            $this->deletionApprovalService->requestDeletion(
                $user,
                $actor,
                $request->string('reason')->trim()->toString() ?: null
            );

            return redirect()
                ->route('users.index')
                ->with('success', 'Permintaan hapus user masuk ke antrean persetujuan superadmin.');
        }

        $this->assertManageableTarget($actor, $user);

        $user->forceFill(['status' => User::STATUS_DELETED])->save();
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    private function validatedPayload(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:10', 'max:255', 'confirmed'],
            'id_role' => ['required', Rule::in(array_keys(config('rbac.roles')))],
            'id_wilayah' => ['required', 'integer', 'exists:wilayahs,id'],
            'status' => ['nullable', Rule::in([User::STATUS_ACTIVE, User::STATUS_PENDING_DELETION])],
        ]);
    }

    private function assignableRolesFor(User $actor)
    {
        return Role::whereIn('name', $actor->assignableRoleNames())
            ->orderBy('label')
            ->get();
    }

    private function assertAssignable(User $actor, Role $role): void
    {
        if (! in_array($role->name, $actor->assignableRoleNames(), true)) {
            throw ValidationException::withMessages([
                'id_role' => 'Role ini tidak dapat diberikan oleh akun Anda.',
            ]);
        }
    }

    private function assertManageableTarget(User $actor, User $target): void
    {
        if (! $target->role || ! in_array($target->role->name, $actor->assignableRoleNames(), true)) {
            throw ValidationException::withMessages([
                'user' => 'Anda tidak dapat mengelola user dengan role tersebut.',
            ]);
        }
    }
}
