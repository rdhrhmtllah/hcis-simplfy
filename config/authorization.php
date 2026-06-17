<?php

return [
    'aliases' => [
        'pages' => [
            'superadminmanajemenperanpage' => 'superAdminManajemenPeranPage',
            'systemauthmanagerpage' => 'systemAuthManagerPage',
            'system-manager' => 'systemAuthManagerPage',
            'role-manager' => 'superAdminManajemenPeranPage',
        ],
        'resources' => [
            'system-manager' => 'systemAuthManagerPage',
            'role-manager' => 'superAdminManajemenPeranPage',
        ],
        'actions' => [
            'view' => 'VIEW',
            'create' => 'CREATE',
            'edit' => 'EDIT',
            'update' => 'UPDATE',
            'delete' => 'DELETE',
            'assign' => 'ASSIGN',
            'delegate' => 'DELEGATE',
            'approve' => 'APPROVE',
        ],
    ],

    'resource_access' => [
        'default_context' => 'admin',
        'default_page' => 'systemAuthManagerPage',
        'default_tab_codes' => ['dashboard', 'users', 'master', 'governance', 'distribution', 'matrix', 'monitoring'],
    ],

    'cache' => [
        'cross_request_enabled' => true,
        'ttl_seconds' => 60,
    ],

    'audit' => [
        'enabled' => true,
        'db_enabled' => false,
        'channel' => 'stack',
    ],
];
