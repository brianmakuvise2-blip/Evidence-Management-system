<?php
/**
 * EVIDENCE MANAGEMENT SYSTEM - ROLE ACCESS DEMONSTRATION
 * Static demonstration of role-based access control
 */

echo "🔐 EVIDENCE MANAGEMENT SYSTEM - ROLE ACCESS DEMONSTRATION\n";
echo "================================================================\n\n";

// Define the roles and their permissions (as configured in RolePermissionSeeder)
$roles = [
    'super-admin' => [
        'name' => 'City of Bulawayo Super Admin',
        'institution' => 'City of Bulawayo (COB)',
        'permissions' => [
            'view-dashboard', 'manage-users', 'create-user', 'edit-user', 'delete-user', 'archive-user',
            'manage-evidence', 'create-evidence', 'edit-evidence', 'delete-evidence', 'manage-custody',
            'request-transfer', 'approve-transfer', 'reject-transfer', 'acknowledge-receipt',
            'disclose-evidence', 'prepare-bundle', 'view-bundle', 'view-all-evidence', 'view-reports',
            'manage-roles', 'manage-permissions', 'view-audit-logs', 'manage-settings',
            'manage-notifications', 'archive-evidence', 'dispose-evidence', 'register-seizure', 'retrieve-evidence'
        ],
        'description' => 'Overall system administrator with full access to all features'
    ],
    'rbz-system-admin' => [
        'name' => 'RBZ System Admin',
        'institution' => 'Reserve Bank of Zimbabwe',
        'permissions' => [
            'view-dashboard', 'manage-users', 'create-user', 'edit-user',
            'manage-evidence', 'create-evidence', 'edit-evidence', 'manage-custody',
            'request-transfer', 'view-reports', 'view-audit-logs', 'manage-notifications'
        ],
        'description' => 'Limited evidence registration within RBZ institution'
    ],
    'zacc-system-admin' => [
        'name' => 'ZACC System Admin',
        'institution' => 'Zimbabwe Anti-Corruption Commission',
        'permissions' => [
            'view-dashboard', 'manage-users', 'create-user', 'edit-user',
            'manage-evidence', 'create-evidence', 'edit-evidence', 'manage-custody',
            'request-transfer', 'view-reports', 'view-audit-logs', 'manage-notifications'
        ],
        'description' => 'Limited evidence registration within ZACC institution'
    ],
    'npa-system-admin' => [
        'name' => 'NPA System Admin',
        'institution' => 'National Prosecuting Authority',
        'permissions' => [
            'view-dashboard', 'manage-users', 'create-user', 'edit-user',
            'manage-evidence', 'create-evidence', 'edit-evidence', 'manage-custody',
            'request-transfer', 'disclose-evidence', 'prepare-bundle', 'view-bundle',
            'retrieve-evidence', 'view-reports', 'view-audit-logs', 'manage-notifications'
        ],
        'description' => 'Retrieve evidence for prosecution and trial preparation'
    ],
    'zrp-system-admin' => [
        'name' => 'ZRP System Admin',
        'institution' => 'Zimbabwe Republic Police',
        'permissions' => [
            'view-dashboard', 'manage-users', 'create-user', 'edit-user',
            'manage-evidence', 'create-evidence', 'edit-evidence', 'register-seizure',
            'manage-custody', 'request-transfer', 'view-reports', 'view-audit-logs', 'manage-notifications'
        ],
        'description' => 'Register seizure documents and exhibits'
    ],
    'judicial-system-admin' => [
        'name' => 'Judicial System Admin',
        'institution' => 'Judiciary',
        'permissions' => [
            'view-dashboard', 'manage-users', 'create-user', 'edit-user',
            'view-bundle', 'view-reports', 'view-audit-logs', 'manage-notifications'
        ],
        'description' => 'Access approved evidence bundles and orders'
    ],
    'judicial-courts-admin' => [
        'name' => 'Judicial Courts Admin',
        'institution' => 'Judiciary (Courts)',
        'permissions' => [
            'view-dashboard', 'manage-users', 'create-user', 'edit-user',
            'manage-evidence', 'view-bundle', 'archive-evidence', 'dispose-evidence',
            'view-reports', 'view-audit-logs', 'manage-notifications'
        ],
        'description' => 'Archive, retention, and disposal of evidence'
    ]
];

// Define route access matrix
$routeAccess = [
    'evidence.create' => [
        'roles' => ['source-officer', 'administrator', 'system-administrator', 'super-admin', 'rbz-system-admin', 'zacc-system-admin', 'npa-system-admin', 'zrp-system-admin'],
        'description' => 'Create new evidence records'
    ],
    'evidence.edit' => [
        'roles' => ['evidence-officer', 'administrator', 'system-administrator', 'super-admin', 'rbz-system-admin', 'zacc-system-admin', 'npa-system-admin', 'zrp-system-admin'],
        'description' => 'Edit existing evidence'
    ],
    'evidence.verify' => [
        'roles' => ['administrator', 'system-administrator', 'super-admin'],
        'description' => 'Verify evidence submissions'
    ],
    'evidence.destroy' => [
        'roles' => ['system-administrator', 'super-admin'],
        'description' => 'Delete evidence (admin only)'
    ],
    'audit-logs.index' => [
        'roles' => ['administrator', 'system-administrator', 'super-admin', 'rbz-system-admin', 'zacc-system-admin', 'npa-system-admin', 'judicial-system-admin', 'judicial-courts-admin'],
        'permissions' => ['view-audit-logs'],
        'description' => 'View system audit logs'
    ],
    'settings.index' => [
        'roles' => ['super-admin'],
        'permissions' => ['manage-settings'],
        'description' => 'Access system settings (COB Super Admin only)'
    ],
    'admin.users.index' => [
        'roles' => ['administrator', 'system-administrator', 'super-admin', 'rbz-system-admin', 'zacc-system-admin', 'npa-system-admin', 'zrp-system-admin', 'judicial-system-admin', 'judicial-courts-admin'],
        'description' => 'User management interface'
    ],
    'transfers.create' => [
        'permissions' => ['request-transfer'],
        'description' => 'Request evidence transfers'
    ],
    'bundles.index' => [
        'roles' => ['prosecutor', 'judicial-viewer', 'npa-system-admin', 'judicial-system-admin', 'judicial-courts-admin'],
        'permissions' => ['view-bundle'],
        'description' => 'Access court bundles'
    ]
];

echo "🎭 TESTING USER ACCESS BY ROLE\n";
echo "=====================================\n\n";

foreach ($roles as $roleKey => $roleData) {
    echo "🔍 Testing Role: {$roleKey}\n";
    echo "👤 User Type: {$roleData['name']}\n";
    echo "🏢 Institution: {$roleData['institution']}\n";
    echo "📝 Description: {$roleData['description']}\n";
    echo str_repeat('─', 60) . "\n";

    echo "📋 Granted Permissions:\n";
    foreach ($roleData['permissions'] as $permission) {
        echo "   ✅ {$permission}\n";
    }

    echo "\n🚪 Route Access Check:\n";
    foreach ($routeAccess as $route => $access) {
        $hasRoleAccess = in_array($roleKey, $access['roles'] ?? []);
        $hasPermissionAccess = true;

        if (isset($access['permissions'])) {
            foreach ($access['permissions'] as $requiredPerm) {
                if (!in_array($requiredPerm, $roleData['permissions'])) {
                    $hasPermissionAccess = false;
                    break;
                }
            }
        }

        $canAccess = $hasRoleAccess && $hasPermissionAccess ? '✅' : '❌';
        echo "   {$canAccess} {$route} - {$access['description']}\n";
    }

    echo "\n🎯 Key Capabilities:\n";
    switch ($roleKey) {
        case 'super-admin':
            echo "   • Full system administration\n";
            echo "   • Manage all institutions and users\n";
            echo "   • Access audit logs and system settings\n";
            echo "   • Override all restrictions\n";
            break;
        case 'rbz-system-admin':
        case 'zacc-system-admin':
            echo "   • Register evidence within institution\n";
            echo "   • Manage users within institution\n";
            echo "   • View audit logs\n";
            echo "   • Cannot access system settings\n";
            break;
        case 'npa-system-admin':
            echo "   • Register evidence within institution\n";
            echo "   • Retrieve evidence for prosecution\n";
            echo "   • Prepare court bundles\n";
            echo "   • Disclose evidence to courts\n";
            break;
        case 'zrp-system-admin':
            echo "   • Register evidence and seizures\n";
            echo "   • Document exhibits and seizure docs\n";
            echo "   • Manage custody chain\n";
            break;
        case 'judicial-system-admin':
            echo "   • Access approved evidence bundles\n";
            echo "   • View court orders\n";
            echo "   • Read-only evidence access\n";
            break;
        case 'judicial-courts-admin':
            echo "   • Archive evidence\n";
            echo "   • Manage retention schedules\n";
            echo "   • Dispose of evidence\n";
            break;
    }

    echo "\n" . str_repeat('═', 60) . "\n\n";
}

echo "📊 MODULE ACCESS SUMMARY\n";
echo "========================\n";
echo "| Module | Super Admin | RBZ | ZACC | NPA | ZRP | Judicial | Courts |\n";
echo "|--------|-------------|-----|------|-----|-----|----------|--------|\n";
echo "| Audit Logs & Settings | ✅ Full     | ❌  | ❌   | ❌  | ❌  | ❌       | ❌     |\n";
echo "| Audit Logs Only      | ✅          | ✅  | ✅   | ✅  | ✅  | ✅       | ✅     |\n";
echo "| Notifications        | ✅          | ✅  | ✅   | ✅  | ✅  | ✅       | ✅     |\n";
echo "| Evidence Registration| ✅          | ✅  | ✅   | ✅  | ✅  | ❌       | ❌     |\n";
echo "| Evidence Retrieval   | ✅          | ❌  | ❌   | ✅  | ❌  | ❌       | ❌     |\n";
echo "| Seizure Registration | ✅          | ❌  | ❌   | ❌  | ✅  | ❌       | ❌     |\n";
echo "| Bundle Access        | ✅          | ❌  | ❌   | ✅  | ❌  | ✅       | ✅     |\n";
echo "| Archive/Disposal     | ✅          | ❌  | ❌   | ❌  | ❌  | ❌       | ✅     |\n";
echo "\n";

echo "✅ DEMONSTRATION COMPLETE\n";
echo "Note: To apply these roles to the database, run:\n";
echo "php artisan db:seed --class=RolePermissionSeeder\n";
echo "\n";
echo "Then assign roles to users:\n";
echo "\$user->assignRole('super-admin'); // For COB\n";
echo "\$user->assignRole('rbz-system-admin'); // For RBZ\n";
// etc.