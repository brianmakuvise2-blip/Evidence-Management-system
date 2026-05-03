<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create institution-specific admin roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']); // COB overall admin
        $rbzSystemAdmin = Role::firstOrCreate(['name' => 'rbz-system-admin', 'guard_name' => 'web']);
        $zaccSystemAdmin = Role::firstOrCreate(['name' => 'zacc-system-admin', 'guard_name' => 'web']);
        $npaSystemAdmin = Role::firstOrCreate(['name' => 'npa-system-admin', 'guard_name' => 'web']);
        $zrpSystemAdmin = Role::firstOrCreate(['name' => 'zrp-system-admin', 'guard_name' => 'web']);
        $judicialSystemAdmin = Role::firstOrCreate(['name' => 'judicial-system-admin', 'guard_name' => 'web']);
        $judicialCourtsAdmin = Role::firstOrCreate(['name' => 'judicial-courts-admin', 'guard_name' => 'web']);

        // Legacy roles (keeping for backward compatibility)
        $systemAdmin = Role::firstOrCreate(['name' => 'system-administrator', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);

        // New Module 4 roles
        $sourceOfficer = Role::firstOrCreate(['name' => 'source-officer', 'guard_name' => 'web']);
        $investigator = Role::firstOrCreate(['name' => 'investigator', 'guard_name' => 'web']);
        $financialVerifier = Role::firstOrCreate(['name' => 'financial-verifier', 'guard_name' => 'web']);
        $prosecutor = Role::firstOrCreate(['name' => 'prosecutor', 'guard_name' => 'web']);
        $judicialViewer = Role::firstOrCreate(['name' => 'judicial-viewer', 'guard_name' => 'web']);
        $supervisor = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);

        // Legacy roles
        $evidenceOfficer = Role::firstOrCreate(['name' => 'evidence-officer', 'guard_name' => 'web']);
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Create permissions
        $permissions = [
            'view-dashboard',
            'manage-users',
            'create-user',
            'edit-user',
            'delete-user',
            'archive-user',
            'manage-evidence',
            'create-evidence',
            'edit-evidence',
            'delete-evidence',
            'manage-custody',
            'request-transfer',
            'approve-transfer',
            'reject-transfer',
            'acknowledge-receipt',
            'disclose-evidence',
            'prepare-bundle',
            'view-bundle',
            'view-all-evidence',
            'view-reports',
            'manage-roles',
            'manage-permissions',
            'view-audit-logs',
            'manage-settings',
            'manage-notifications',
            'archive-evidence',
            'dispose-evidence',
            'register-seizure',
            'retrieve-evidence',
            'verify-evidence',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Sync permissions to roles (instead of giving directly to avoid duplicates)

        // Super Admin (COB Overall Admin) - Full system access including audit logs and settings
        $superAdmin->syncPermissions([
            'view-dashboard',
            'manage-users',
            'create-user',
            'edit-user',
            'delete-user',
            'archive-user',
            'manage-evidence',
            'create-evidence',
            'edit-evidence',
            'delete-evidence',
            'manage-custody',
            'request-transfer',
            'approve-transfer',
            'reject-transfer',
            'acknowledge-receipt',
            'disclose-evidence',
            'prepare-bundle',
            'view-bundle',
            'view-all-evidence',
            'view-reports',
            'manage-roles',
            'manage-permissions',
            'view-audit-logs',
            'manage-settings',
            'manage-notifications',
            'archive-evidence',
            'dispose-evidence',
            'register-seizure',
            'retrieve-evidence',
            'verify-evidence',
        ]);

        // RBZ System Admin - Limited evidence registration with audit logs
        $rbzSystemAdmin->syncPermissions([
            'view-dashboard',
            'manage-users',
            'create-user',
            'edit-user',
            'manage-evidence',
            'create-evidence',
            'edit-evidence',
            'manage-custody',
            'request-transfer',
            'view-reports',
            'view-audit-logs',
            'manage-notifications',
            'verify-evidence',
        ]);

        // ZACC System Admin - Limited evidence registration with audit logs
        $zaccSystemAdmin->syncPermissions([
            'view-dashboard',
            'manage-users',
            'create-user',
            'edit-user',
            'manage-evidence',
            'create-evidence',
            'edit-evidence',
            'manage-custody',
            'request-transfer',
            'view-reports',
            'view-audit-logs',
            'manage-notifications',
            'verify-evidence',
        ]);

        // NPA System Admin - Retrieve evidence for prosecution and trial preparation
        $npaSystemAdmin->syncPermissions([
            'view-dashboard',
            'manage-users',
            'create-user',
            'edit-user',
            'manage-evidence',
            'create-evidence',
            'edit-evidence',
            'manage-custody',
            'request-transfer',
            'disclose-evidence',
            'prepare-bundle',
            'view-bundle',
            'retrieve-evidence',
            'view-reports',
            'view-audit-logs',
            'manage-notifications',
            'verify-evidence',
        ]);

        // ZRP System Admin - Register seizure docs and exhibits
        $zrpSystemAdmin->syncPermissions([
            'view-dashboard',
            'manage-users',
            'create-user',
            'edit-user',
            'manage-evidence',
            'create-evidence',
            'edit-evidence',
            'register-seizure',
            'manage-custody',
            'request-transfer',
            'view-reports',
            'view-audit-logs',
            'manage-notifications',
            'verify-evidence',
        ]);

        // Judicial System Admin - Access approved evidence bundles and orders
        $judicialSystemAdmin->syncPermissions([
            'view-dashboard',
            'manage-users',
            'create-user',
            'edit-user',
            'view-bundle',
            'view-reports',
            'view-audit-logs',
            'manage-notifications',
            'verify-evidence',
        ]);

        // Judicial Courts Admin - Archive, retention, disposal of evidence
        $judicialCourtsAdmin->syncPermissions([
            'view-dashboard',
            'manage-users',
            'create-user',
            'edit-user',
            'manage-evidence',
            'view-bundle',
            'archive-evidence',
            'dispose-evidence',
            'view-reports',
            'view-audit-logs',
            'manage-notifications',
            'verify-evidence',
        ]);

        // Legacy System Administrator - Management Only (NOT operational approvals)
        $systemAdmin->syncPermissions([
            'view-dashboard',
            'manage-users',
            'create-user',
            'edit-user',
            'delete-user',
            'archive-user',
            'manage-roles',
            'manage-permissions',
            'manage-settings',
            'view-all-evidence',
            'view-audit-logs',
            'view-reports',
            'manage-notifications',
            'verify-evidence',
        ]);

        // Legacy Administrator - User Management
        $admin->syncPermissions([
            'view-dashboard',
            'manage-users',
            'create-user',
            'edit-user',
            'delete-user',
            'archive-user',
            'manage-custody',
            'view-all-evidence',
            'view-reports',
            'view-audit-logs',
            'manage-notifications',
            'verify-evidence',
        ]);

        // Source Officer - Create & upload evidence
        $sourceOfficer->syncPermissions([
            'view-dashboard',
            'create-evidence',
            'manage-evidence',
            'request-transfer', // Added: Source Officers can request transfers
        ]);

        // Investigator - View, manage evidence, request transfers
        $investigator->syncPermissions([
            'view-dashboard',
            'manage-evidence',
            'edit-evidence',
            'request-transfer',
            'manage-custody',
        ]);

        // Financial Verifier - View financial evidence
        $financialVerifier->syncPermissions([
            'view-dashboard',
            'manage-evidence',
            'view-audit-logs',
        ]);

        // Prosecutor - Review evidence, prepare court bundles
        $prosecutor->syncPermissions([
            'view-dashboard',
            'manage-evidence',
            'request-transfer',
            'manage-custody',
            'prepare-bundle',
            'view-bundle',
            'disclose-evidence',
            'view-audit-logs',
            'manage-notifications',
        ]);

        // Judicial Viewer - Read-only access
        $judicialViewer->syncPermissions([
            'view-dashboard',
            'view-bundle',
            'view-audit-logs',
            'manage-notifications',
        ]);

        // Supervisor - Approve/reject transfers within institution
        $supervisor->syncPermissions([
            'view-dashboard',
            'manage-evidence',
            'approve-transfer',
            'reject-transfer',
            'manage-custody',
            'view-audit-logs',
            'manage-notifications',
        ]);

        // Evidence Officer - Evidence & Custody Management (legacy)
        $evidenceOfficer->syncPermissions([
            'view-dashboard',
            'manage-evidence',
            'create-evidence',
            'edit-evidence',
            'manage-custody',
            'manage-notifications',
        ]);

        // Regular User - View Only
        $user->syncPermissions([
            'view-dashboard',
        ]);
    }
}
