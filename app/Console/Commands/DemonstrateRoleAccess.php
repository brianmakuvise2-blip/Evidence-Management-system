<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Institution;

class DemonstrateRoleAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:role-access {--user-id= : Specific user ID to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Demonstrate role-based access control for different user roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔐 EVIDENCE MANAGEMENT SYSTEM - ROLE ACCESS DEMONSTRATION');
        $this->line('================================================================');
        $this->newLine();

        // Get all institutions
        $institutions = Institution::all();

        // Define test users for each role
        $testUsers = [
            'super-admin' => [
                'name' => 'City of Bulawayo Super Admin',
                'institution' => 'City of Bulawayo',
                'expected_access' => [
                    '✅ Full system access',
                    '✅ Audit logs & settings management',
                    '✅ Create/manage all institution admins',
                    '✅ All evidence operations',
                    '✅ User management across all institutions',
                    '✅ Notifications management'
                ]
            ],
            'rbz-system-admin' => [
                'name' => 'RBZ System Admin',
                'institution' => 'Reserve Bank of Zimbabwe',
                'expected_access' => [
                    '✅ Limited evidence registration (RBZ only)',
                    '✅ User management (RBZ only)',
                    '✅ View audit logs',
                    '✅ Manage notifications',
                    '❌ Cannot access settings',
                    '❌ Cannot manage other institutions'
                ]
            ],
            'zacc-system-admin' => [
                'name' => 'ZACC System Admin',
                'institution' => 'Zimbabwe Anti-Corruption Commission',
                'expected_access' => [
                    '✅ Limited evidence registration (ZACC only)',
                    '✅ User management (ZACC only)',
                    '✅ View audit logs',
                    '✅ Manage notifications',
                    '❌ Cannot access settings',
                    '❌ Cannot manage other institutions'
                ]
            ],
            'npa-system-admin' => [
                'name' => 'NPA System Admin',
                'institution' => 'National Prosecuting Authority',
                'expected_access' => [
                    '✅ Evidence registration (NPA only)',
                    '✅ Retrieve evidence for prosecution/trial',
                    '✅ Prepare court bundles',
                    '✅ Disclose evidence',
                    '✅ User management (NPA only)',
                    '✅ View audit logs',
                    '✅ Manage notifications',
                    '❌ Cannot access settings'
                ]
            ],
            'zrp-system-admin' => [
                'name' => 'ZRP System Admin',
                'institution' => 'Zimbabwe Republic Police',
                'expected_access' => [
                    '✅ Evidence registration (ZRP only)',
                    '✅ Register seizure documents/exhibits',
                    '✅ User management (ZRP only)',
                    '✅ View audit logs',
                    '✅ Manage notifications',
                    '❌ Cannot access settings'
                ]
            ],
            'judicial-system-admin' => [
                'name' => 'Judicial System Admin',
                'institution' => 'Judiciary',
                'expected_access' => [
                    '✅ Access approved evidence bundles',
                    '✅ Access court orders',
                    '✅ User management (Judiciary only)',
                    '✅ View audit logs',
                    '✅ Manage notifications',
                    '❌ Cannot register evidence',
                    '❌ Cannot access settings'
                ]
            ],
            'judicial-courts-admin' => [
                'name' => 'Judicial Courts Admin',
                'institution' => 'Judiciary (Courts)',
                'expected_access' => [
                    '✅ Archive evidence',
                    '✅ Retention management',
                    '✅ Disposal of evidence',
                    '✅ View evidence bundles',
                    '✅ User management (Judiciary only)',
                    '✅ View audit logs',
                    '✅ Manage notifications',
                    '❌ Cannot register evidence',
                    '❌ Cannot access settings'
                ]
            ]
        ];

        // Display role permissions
        $this->displayRolePermissions();

        $this->newLine();
        $this->info('🎭 TESTING USER ACCESS BY ROLE');
        $this->line('=====================================');

        foreach ($testUsers as $roleName => $userData) {
            $this->newLine();
            $this->warn("🔍 Testing Role: {$roleName}");
            $this->line("👤 User: {$userData['name']}");
            $this->line("🏢 Institution: {$userData['institution']}");
            $this->line('─'.str_repeat('─', 50));

            // Get role and permissions
            $role = Role::where('name', $roleName)->first();
            if (!$role) {
                $this->error("❌ Role '{$roleName}' not found in database!");
                continue;
            }

            $permissions = $role->permissions->pluck('name')->toArray();

            $this->line("📋 Permissions granted:");
            foreach ($permissions as $permission) {
                $this->line("   • {$permission}");
            }

            $this->newLine();
            $this->line("🎯 Expected Access:");
            foreach ($userData['expected_access'] as $access) {
                $this->line("   {$access}");
            }

            $this->newLine();
            $this->line("🚪 Route Access Check:");
            $this->checkRouteAccess($roleName, $permissions);

            $this->line('─'.str_repeat('─', 60));
        }

        $this->newLine();
        $this->info('✅ DEMONSTRATION COMPLETE');
        $this->comment('Note: Actual access would be tested through the web interface.');
        $this->comment('Each role is restricted to their institution\'s data and capabilities.');
    }

    private function displayRolePermissions()
    {
        $this->info('📊 ROLE PERMISSIONS MATRIX');
        $this->line('===========================');

        $roles = Role::all();
        $permissions = Permission::all();

        // Create a table
        $table = [];
        $header = ['Permission'];
        foreach ($roles as $role) {
            $header[] = $role->name;
        }
        $table[] = $header;

        foreach ($permissions as $permission) {
            $row = [$permission->name];
            foreach ($roles as $role) {
                $hasPermission = $role->hasPermissionTo($permission->name) ? '✅' : '❌';
                $row[] = $hasPermission;
            }
            $table[] = $row;
        }

        $this->table($header, array_slice($table, 1));
    }

    private function checkRouteAccess($roleName, $permissions)
    {
        // Simulate route access checks based on middleware
        $routeChecks = [
            'evidence.create' => [
                'required_roles' => ['source-officer', 'administrator', 'system-administrator', 'super-admin', 'rbz-system-admin', 'zacc-system-admin', 'npa-system-admin', 'zrp-system-admin'],
                'description' => 'Create new evidence'
            ],
            'evidence.edit' => [
                'required_roles' => ['evidence-officer', 'administrator', 'system-administrator', 'super-admin', 'rbz-system-admin', 'zacc-system-admin', 'npa-system-admin', 'zrp-system-admin'],
                'description' => 'Edit evidence'
            ],
            'evidence.verify' => [
                'required_roles' => ['administrator', 'system-administrator', 'super-admin'],
                'description' => 'Verify evidence'
            ],
            'evidence.destroy' => [
                'required_roles' => ['system-administrator', 'super-admin'],
                'description' => 'Delete evidence'
            ],
            'audit-logs.index' => [
                'required_roles' => ['administrator', 'system-administrator', 'super-admin', 'rbz-system-admin', 'zacc-system-admin', 'npa-system-admin', 'judicial-system-admin', 'judicial-courts-admin'],
                'required_permissions' => ['view-audit-logs'],
                'description' => 'View audit logs'
            ],
            'settings.index' => [
                'required_roles' => ['super-admin'],
                'required_permissions' => ['manage-settings'],
                'description' => 'Access system settings'
            ],
            'admin.users.index' => [
                'required_roles' => ['administrator', 'system-administrator', 'super-admin', 'rbz-system-admin', 'zacc-system-admin', 'npa-system-admin', 'zrp-system-admin', 'judicial-system-admin', 'judicial-courts-admin'],
                'description' => 'User management'
            ]
        ];

        foreach ($routeChecks as $route => $check) {
            $hasRoleAccess = in_array($roleName, $check['required_roles']);
            $hasPermissionAccess = !isset($check['required_permissions']) ||
                                 collect($check['required_permissions'])->every(fn($perm) => in_array($perm, $permissions));

            $access = $hasRoleAccess && $hasPermissionAccess ? '✅' : '❌';
            $this->line("   {$access} {$route} - {$check['description']}");
        }
    }
}