<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        $roles = collect(config('rbac.roles'))->mapWithKeys(function (string $label, string $name) {
            return [
                $name => Role::updateOrCreate(
                    ['name' => $name],
                    [
                        'label' => $label,
                        'description' => $this->roleDescription($name),
                    ]
                ),
            ];
        });

        $permissions = collect();
        $permissions->put('*.*', Permission::updateOrCreate(
            ['module' => '*', 'action' => '*'],
            ['name' => '*.*', 'label' => 'Akses penuh semua modul']
        ));

        foreach (config('rbac.modules') as $module) {
            foreach (['create', 'read', 'update', 'delete'] as $action) {
                $permissions->put("{$module}.{$action}", Permission::updateOrCreate(
                    ['module' => $module, 'action' => $action],
                    [
                        'name' => "{$module}.{$action}",
                        'label' => "{$action} {$module}",
                    ]
                ));
            }
        }

        foreach (['approve', 'reject'] as $action) {
            $permissions->put("user_deletion_requests.{$action}", Permission::updateOrCreate(
                ['module' => 'user_deletion_requests', 'action' => $action],
                [
                    'name' => "user_deletion_requests.{$action}",
                    'label' => "{$action} user deletion requests",
                ]
            ));
        }

        $permissions->put('notifications.send', Permission::updateOrCreate(
            ['module' => 'notifications', 'action' => 'send'],
            [
                'name' => 'notifications.send',
                'label' => 'Kirim notifikasi',
            ]
        ));

        $roles[Role::SUPERADMIN]->permissions()->sync([
            $permissions['*.*']->id,
        ]);

        $adminPusatPermissions = $this->permissionIds($permissions, [
            'master_buku_pa.create',
            'master_buku_pa.read',
            'blesscomn.create',
            'blesscomn.read',
            'blesscomn.update',
            'blesscomn.delete',
            'pa.create',
            'pa.read',
            'pa.update',
            'pa.delete',
            'kehadiran_ibadah.create',
            'kehadiran_ibadah.read',
            'kehadiran_ibadah.update',
            'kehadiran_ibadah.delete',
            'users.create',
            'users.read',
            'users.update',
            'users.delete',
            'notifications.read',
            'notifications.send',
        ]);

        $roles[Role::ADMIN_PUSAT]->permissions()->sync($adminPusatPermissions);

        $adminWilayahPermissions = $this->permissionIds($permissions, [
            'master_buku_pa.create',
            'master_buku_pa.read',
            'blesscomn.create',
            'blesscomn.read',
            'blesscomn.update',
            'blesscomn.delete',
            'pa.create',
            'pa.read',
            'pa.update',
            'pa.delete',
            'kehadiran_ibadah.create',
            'kehadiran_ibadah.read',
            'kehadiran_ibadah.update',
            'kehadiran_ibadah.delete',
            'notifications.read',
        ]);

        $roles[Role::ADMIN_WILAYAH]->permissions()->sync($adminWilayahPermissions);
        $roles[Role::USER]->permissions()->sync([]);
    }

    private function permissionIds($permissions, array $names): array
    {
        return collect($names)
            ->map(fn (string $name) => $permissions[$name]->id)
            ->all();
    }

    private function roleDescription(string $roleName): string
    {
        return match ($roleName) {
            Role::SUPERADMIN => 'Root access untuk seluruh modul, user, role, notifikasi, dan approval.',
            Role::ADMIN_PUSAT => 'CRUD Blesscomn, PA, Kehadiran Ibadah, dan manajemen user terbatas.',
            Role::ADMIN_WILAYAH => 'CRUD Blesscomn, PA, Kehadiran Ibadah terbatas wilayah akun.',
            Role::USER => 'Role target notifikasi dan endpoint publik tanpa login.',
            default => '',
        };
    }
}
