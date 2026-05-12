<?php

return [
    'roles' => [
        'superadmin' => 'Superadmin',
        'admin_pusat' => 'Admin Pusat',
        'admin_wilayah' => 'Admin Wilayah',
        'user' => 'User',
    ],

    'modules' => [
        'master_data',
        'master_buku_pa',
        'blesscomn',
        'pa',
        'kehadiran_ibadah',
        'users',
        'roles',
        'notifications',
        'user_deletion_requests',
        'audit_logs',
    ],

    'actions' => [
        'create',
        'read',
        'update',
        'delete',
        'approve',
        'reject',
        'send',
        '*',
    ],

    'assignable_roles' => [
        'superadmin' => ['admin_pusat', 'admin_wilayah', 'user'],
        'admin_pusat' => ['admin_wilayah', 'user'],
        'admin_wilayah' => [],
        'user' => [],
    ],

    'notification_target_roles' => [
        'superadmin' => ['superadmin', 'admin_pusat', 'admin_wilayah'],
        'admin_pusat' => ['superadmin', 'admin_pusat', 'admin_wilayah'],
        'admin_wilayah' => [],
        'user' => [],
    ],

    'regional_scope_columns' => [
        'pa' => 'wilayah_id',
        'blesscomn' => 'id_wilayah',
        'kehadiran_ibadah' => 'id_wilayah',
    ],
];
