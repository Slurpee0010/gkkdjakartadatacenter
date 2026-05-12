<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Role::with('permissions')
                ->orderBy('name')
                ->get(),
            'available_permissions' => Permission::orderBy('module')
                ->orderBy('action')
                ->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', 'alpha_dash:ascii', 'unique:roles,name'],
            'label' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permission_names' => ['nullable', 'array'],
            'permission_names.*' => ['required', 'string', 'exists:permissions,name'],
        ]);

        $role = Role::create([
            'name' => strtolower($validated['name']),
            'label' => trim($validated['label']),
            'description' => $validated['description'] ?? null,
        ]);

        $this->syncPermissions($role, $validated['permission_names'] ?? []);

        return response()->json([
            'message' => 'Role berhasil dibuat.',
            'data' => $role->fresh('permissions'),
        ], 201);
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permission_names' => ['nullable', 'array'],
            'permission_names.*' => ['required', 'string', 'exists:permissions,name'],
        ]);

        $role->update([
            'label' => trim($validated['label']),
            'description' => $validated['description'] ?? null,
        ]);

        $this->syncPermissions($role, $validated['permission_names'] ?? []);

        return response()->json([
            'message' => 'Role berhasil diperbarui.',
            'data' => $role->fresh('permissions'),
        ]);
    }

    public function destroy(Role $role): JsonResponse
    {
        if (array_key_exists($role->name, config('rbac.roles'))) {
            throw ValidationException::withMessages([
                'role' => 'Role inti sistem tidak dapat dihapus.',
            ]);
        }

        if ($role->users()->exists()) {
            throw ValidationException::withMessages([
                'role' => 'Role tidak dapat dihapus karena masih dipakai user.',
            ]);
        }

        $role->permissions()->detach();
        $role->delete();

        return response()->json([
            'message' => 'Role berhasil dihapus.',
        ]);
    }

    private function syncPermissions(Role $role, array $permissionNames): void
    {
        if ($role->name === Role::SUPERADMIN) {
            $permissionIds = Permission::where('name', '*.*')->pluck('id');
            $role->permissions()->sync($permissionIds);

            return;
        }

        if (array_intersect($permissionNames, ['*.*'])) {
            throw ValidationException::withMessages([
                'permission_names' => 'Wildcard hanya boleh dimiliki superadmin.',
            ]);
        }

        if ($role->name === Role::ADMIN_PUSAT) {
            $forbidden = collect($permissionNames)
                ->contains(fn (string $name) => str_starts_with($name, 'master_data.'));

            if ($forbidden) {
                throw ValidationException::withMessages([
                    'permission_names' => 'Admin pusat tidak boleh memiliki permission Master Data.',
                ]);
            }
        }

        if ($role->name === Role::ADMIN_WILAYAH) {
            $allowedModules = ['blesscomn', 'pa', 'kehadiran_ibadah'];
            $forbidden = collect($permissionNames)
                ->map(fn (string $name) => explode('.', $name)[0])
                ->diff($allowedModules)
                ->isNotEmpty();

            if ($forbidden) {
                throw ValidationException::withMessages([
                    'permission_names' => 'Admin wilayah hanya boleh memiliki permission Blesscomn, PA, dan Kehadiran Ibadah.',
                ]);
            }
        }

        if ($role->name === Role::USER && $permissionNames !== []) {
            throw ValidationException::withMessages([
                'permission_names' => 'Role user publik tidak boleh memiliki permission backend.',
            ]);
        }

        $permissionIds = Permission::whereIn('name', $permissionNames)->pluck('id');
        $role->permissions()->sync($permissionIds);
    }
}
