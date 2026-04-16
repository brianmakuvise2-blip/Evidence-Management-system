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

        // Create roles (skip if they already exist)
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
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Sync permissions to roles (instead of giving directly to avoid duplicates)

        // System Administrator - Management Only (NOT operational approvals)
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
        ]);

        // Administrator - User Management
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
        ]);

        // Judicial Viewer - Read-only access
        $judicialViewer->syncPermissions([
            'view-dashboard',
            'view-bundle',
            'view-audit-logs',
        ]);

        // Supervisor - Approve/reject transfers within institution
        $supervisor->syncPermissions([
            'view-dashboard',
            'manage-evidence',
            'approve-transfer',
            'reject-transfer',
            'manage-custody',
            'view-audit-logs',
        ]);

        // Evidence Officer - Evidence & Custody Management (legacy)
        $evidenceOfficer->syncPermissions([
            'view-dashboard',
            'manage-evidence',
            'create-evidence',
            'edit-evidence',
            'manage-custody',
        ]);

        // Regular User - View Only
        $user->syncPermissions([
            'view-dashboard',
        ]);
    }
}
