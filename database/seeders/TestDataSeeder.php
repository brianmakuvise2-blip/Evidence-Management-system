<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Institution;
use App\Models\Department;
use App\Models\Evidence;
use App\Models\ChainOfCustody;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TestDataSeeder extends Seeder
{
    /**
     * Seed the application's database with test institutions and users.
     */
    public function run(): void
    {
        // Create the 6 institutions
        $institutions = [
            [
                'name' => 'City of Bulawayo',
                'code' => 'COB',
                'type' => 'Municipality',
                'region' => 'Bulawayo',
                'contact_email' => 'info@bulawayo.gov.zw',
                'contact_phone' => '+263-9-60000',
                'is_active' => true,
            ],
            [
                'name' => 'Zimbabwe Republic Police',
                'code' => 'ZRP',
                'type' => 'Law Enforcement',
                'region' => 'National',
                'contact_email' => 'info@zrp.gov.zw',
                'contact_phone' => '+263-9-60100',
                'is_active' => true,
            ],
            [
                'name' => 'Reserve Bank of Zimbabwe',
                'code' => 'RBZ',
                'type' => 'Financial',
                'region' => 'National',
                'contact_email' => 'info@rbz.org.zw',
                'contact_phone' => '+263-9-70000',
                'is_active' => true,
            ],
            [
                'name' => 'National Prosecuting Authority',
                'code' => 'NPA',
                'type' => 'Prosecution',
                'region' => 'National',
                'contact_email' => 'info@npa.gov.zw',
                'contact_phone' => '+263-9-80000',
                'is_active' => true,
            ],
            [
                'name' => 'Zimbabwe Anti-Corruption Commission',
                'code' => 'ZACC',
                'type' => 'Investigative',
                'region' => 'National',
                'contact_email' => 'info@zacc.gov.zw',
                'contact_phone' => '+263-9-90000',
                'is_active' => true,
            ],
            [
                'name' => 'Judiciary',
                'code' => 'JUD',
                'type' => 'Judicial',
                'region' => 'National',
                'contact_email' => 'info@judiciary.gov.zw',
                'contact_phone' => '+263-9-50000',
                'is_active' => true,
            ],
        ];

        $createdInstitutions = [];
        foreach ($institutions as $institutionData) {
            $institution = Institution::firstOrCreate(
                ['code' => $institutionData['code']],
                $institutionData
            );
            $createdInstitutions[$institution->code] = $institution;
            
            // Create departments for each institution
            $this->createDepartments($institution);
        }

        // Create users for each institution and role
        $this->createUsers($createdInstitutions);
    }

    /**
     * Create departments for an institution
     */
    private function createDepartments(Institution $institution): void
    {
        $departments = match($institution->code) {
            'COB' => ['Municipal Services', 'Finance', 'Evidence Handling'],
            'ZRP' => ['Investigations', 'Forensics', 'Evidence Management', 'Operations'],
            'RBZ' => ['Investigations Unit', 'Asset Recovery', 'Record Keeping'],
            'NPA' => ['Major Crimes', 'Economic Offences', 'Evidence Bureau'],
            'ZACC' => ['Investigations', 'Case Management', 'Evidence Custody'],
            'JUD' => ['Court Registry', 'Evidence Repository', 'Case Management'],
            default => ['Default Department'],
        };

        foreach ($departments as $deptName) {
            Department::firstOrCreate([
                'institution_id' => $institution->id,
                'name' => $deptName,
            ], [
                'code' => strtoupper(substr(str_replace(' ', '', $deptName), 0, 5)),
                'is_active' => true,
            ]);
        }
    }

    /**
     * Create test users for each institution and role
     */
    private function createUsers($institutions): void
    {
        $password = Hash::make('TestPass123@');
        $defaultPassword = 'TestPass123@'; // For reference

        // Get all departments
        $departments = Department::all();

        // Define roles to create if they don't exist
        $this->ensureRolesExist();

        // System Administrator
        $sysAdmin = User::firstOrCreate([
            'email' => 'sysadmin@evidence-system.local',
        ], [
            'name' => 'System Administrator',
            'password' => $password,
            'employee_id' => 'SA001',
            'badge_number' => 'SA-001',
            'institution_id' => $institutions['COB']->id,
            'department_id' => $departments->first()->id,
            'job_title' => 'System Administrator',
            'phone_work' => '+263-9-60001',
            'data_access_scope' => 'all',
            'account_status' => 'active',
            'mfa_enabled' => false,
            'last_password_change_at' => now(),
            'password_expires_at' => now()->addDays(90),
            'failed_login_attempts' => 0,
            'lockout_expires_at' => now()->addMinutes(15),
            'last_login_at' => now(),
            'last_login_ip' => '+127.0.0.1',
            'login_history' => [
                [
                    'ip' => '127.0.0.1',
                    'user_agent' => 'Seeder',
                    'timestamp' => now()->toDateTimeString(),
                ],
            ],
        ]);
        $sysAdmin->assignRole('system-administrator');

        // City of Bulawayo - Source Officer
        $sourceOfficer = User::firstOrCreate([
            'email' => 'source@bulawayo.local',
        ], [
            'name' => 'Source Officer - COB',
            'password' => $password,
            'employee_id' => 'COB001',
            'badge_number' => 'COB-SO-001',
            'institution_id' => $institutions['COB']->id,
            'department_id' => $departments->where('institution_id', $institutions['COB']->id)->first()->id,
            'job_title' => 'Source Officer',
            'phone_work' => '+263-9-60100',
            'data_access_scope' => 'personal',
            'account_status' => 'active',
            'last_password_change_at' => now(),
            'password_expires_at' => now()->addDays(90),
        ]);
        $sourceOfficer->assignRole('source-officer');

        // ZRP - Investigator
        $investigator = User::create([
            'name' => 'Investigator - ZRP',
            'email' => 'investigator@zrp.local',
            'password' => $password,
            'employee_id' => 'ZRP001',
            'badge_number' => 'ZRP-INV-001',
            'institution_id' => $institutions['ZRP']->id,
            'department_id' => $departments->where('institution_id', $institutions['ZRP']->id)->first()->id,
            'job_title' => 'Criminal Investigator',
            'phone_work' => '+263-9-60200',
            'data_access_scope' => 'department',
            'account_status' => 'active',
            'last_password_change_at' => now(),
            'password_expires_at' => now()->addDays(90),
        ]);
        $investigator->assignRole('investigator');

        // RBZ - Financial Verifier
        $financialVerifier = User::create([
            'name' => 'Financial Verifier - RBZ',
            'email' => 'verifier@rbz.local',
            'password' => $password,
            'employee_id' => 'RBZ001',
            'badge_number' => 'RBZ-FV-001',
            'institution_id' => $institutions['RBZ']->id,
            'department_id' => $departments->where('institution_id', $institutions['RBZ']->id)->first()->id,
            'job_title' => 'Financial Verifier',
            'phone_work' => '+263-9-70100',
            'data_access_scope' => 'department',
            'account_status' => 'active',
            'last_password_change_at' => now(),
            'password_expires_at' => now()->addDays(90),
        ]);
        $financialVerifier->assignRole('financial-verifier');

        // NPA - Prosecutor
        $prosecutor = User::create([
            'name' => 'Prosecutor - NPA',
            'email' => 'prosecutor@npa.local',
            'password' => $password,
            'employee_id' => 'NPA001',
            'badge_number' => 'NPA-PROS-001',
            'institution_id' => $institutions['NPA']->id,
            'department_id' => $departments->where('institution_id', $institutions['NPA']->id)->first()->id,
            'job_title' => 'State Prosecutor',
            'phone_work' => '+263-9-80100',
            'data_access_scope' => 'department',
            'account_status' => 'active',
            'last_password_change_at' => now(),
            'password_expires_at' => now()->addDays(90),
        ]);
        $prosecutor->assignRole('prosecutor');

        // ZACC - Investigator
        $zaccInvestigator = User::create([
            'name' => 'Investigator - ZACC',
            'email' => 'investigator@zacc.local',
            'password' => $password,
            'employee_id' => 'ZACC001',
            'badge_number' => 'ZACC-INV-001',
            'institution_id' => $institutions['ZACC']->id,
            'department_id' => $departments->where('institution_id', $institutions['ZACC']->id)->first()->id,
            'job_title' => 'Anti-Corruption Investigator',
            'phone_work' => '+263-9-90100',
            'data_access_scope' => 'department',
            'account_status' => 'active',
            'last_password_change_at' => now(),
            'password_expires_at' => now()->addDays(90),
        ]);
        $zaccInvestigator->assignRole('investigator');

        // Judiciary - Judicial Viewer
        $judicialViewer = User::create([
            'name' => 'Judicial Viewer - Judiciary',
            'email' => 'viewer@judiciary.local',
            'password' => $password,
            'employee_id' => 'JUD001',
            'badge_number' => 'JUD-VIEW-001',
            'institution_id' => $institutions['JUD']->id,
            'department_id' => $departments->where('institution_id', $institutions['JUD']->id)->first()->id,
            'job_title' => 'Court Officer',
            'phone_work' => '+263-9-50100',
            'data_access_scope' => 'all',
            'account_status' => 'active',
            'last_password_change_at' => now(),
            'password_expires_at' => now()->addDays(90),
        ]);
        $judicialViewer->assignRole('judicial-viewer');

        // Supervisors (one per institution)
        $supervisors = [
            [
                'name' => 'Supervisor - COB',
                'email' => 'supervisor@bulawayo.local',
                'employee_id' => 'COB-SUP-001',
                'badge_number' => 'COB-SUP-001',
                'institution_id' => $institutions['COB']->id,
                'phone_work' => '+263-9-60300',
            ],
            [
                'name' => 'Supervisor - ZRP',
                'email' => 'supervisor@zrp.local',
                'employee_id' => 'ZRP-SUP-001',
                'badge_number' => 'ZRP-SUP-001',
                'institution_id' => $institutions['ZRP']->id,
                'phone_work' => '+263-9-60400',
            ],
            [
                'name' => 'Supervisor - RBZ',
                'email' => 'supervisor@rbz.local',
                'employee_id' => 'RBZ-SUP-001',
                'badge_number' => 'RBZ-SUP-001',
                'institution_id' => $institutions['RBZ']->id,
                'phone_work' => '+263-9-70200',
            ],
            [
                'name' => 'Supervisor - NPA',
                'email' => 'supervisor@npa.local',
                'employee_id' => 'NPA-SUP-001',
                'badge_number' => 'NPA-SUP-001',
                'institution_id' => $institutions['NPA']->id,
                'phone_work' => '+263-9-80200',
            ],
            [
                'name' => 'Supervisor - ZACC',
                'email' => 'supervisor@zacc.local',
                'employee_id' => 'ZACC-SUP-001',
                'badge_number' => 'ZACC-SUP-001',
                'institution_id' => $institutions['ZACC']->id,
                'phone_work' => '+263-9-90200',
            ],
            [
                'name' => 'Supervisor - JUD',
                'email' => 'supervisor@judiciary.local',
                'employee_id' => 'JUD-SUP-001',
                'badge_number' => 'JUD-SUP-001',
                'institution_id' => $institutions['JUD']->id,
                'phone_work' => '+263-9-50200',
            ],
        ];

        foreach ($supervisors as $supervisorData) {
            $supervisor = User::create(array_merge($supervisorData, [
                'password' => $password,
                'department_id' => $departments->where('institution_id', $supervisorData['institution_id'])->first()->id,
                'job_title' => 'Supervisor',
                'data_access_scope' => 'department',
                'account_status' => 'active',
                'last_password_change_at' => now(),
                'password_expires_at' => now()->addDays(90),
            ]));
            $supervisor->assignRole('supervisor');
        }

        // Create sample evidence and chain of custody for testing
        $this->createSampleEvidence($sourceOfficer, $institutions, $departments);

        $this->command->info('✓ Test data seeded successfully!');
        $this->command->info('');
        $this->command->info('Test Credentials:');
        $this->command->info('================');
        $this->command->info('Password: ' . $defaultPassword);
        $this->command->info('');
        $this->command->info('Users:');
        $this->command->info('  System Admin: sysadmin@evidence-system.local');
        $this->command->info('  Source Officer: source@bulawayo.local');
        $this->command->info('  Investigator (ZRP): investigator@zrp.local');
        $this->command->info('  Financial Verifier: verifier@rbz.local');
        $this->command->info('  Prosecutor: prosecutor@npa.local');
        $this->command->info('  ZACC Investigator: investigator@zacc.local');
        $this->command->info('  Judicial Viewer: viewer@judiciary.local');
        $this->command->info('  Supervisors across all 6 institutions');
    }

    /**
     * Create sample evidence and custody records
     */
    private function createSampleEvidence(User $sourceOfficer, $institutions, $departments): void
    {
        // Create a sample evidence item
        $evidence = Evidence::create([
            'case_reference' => 'CASE-2026-001',
            'exhibit_number' => 'EXH-2026-001-A',
            'title' => 'Seized Cash Bundle',
            'description' => 'Bundle of ZWL currency recovered from suspected illegal operation',
            'evidence_type' => 'physical',
            'collected_date' => now()->subDays(5),
            'source' => 'Search Warrant - Case 2026-001',
            'location_found' => '123 Main Street, Bulawayo',
            'classification_level' => 'restricted',
            'collected_by_user_id' => $sourceOfficer->id,
            'institution_id' => $sourceOfficer->institution_id,
            'department_id' => $sourceOfficer->department_id,
            'file_type' => 'application/pdf',
            'file_size' => 5242880, // 5MB
            'file_hash' => hash('sha256', 'sample_evidence_file_123'),
            'status' => Evidence::STATUS_VERIFIED,
            'verified_at' => now()->subDays(3),
            'verified_by_user_id' => $sourceOfficer->id,
            'verification_notes' => 'Verified and catalogued - ready for transfer',
        ]);

        $this->command->info('✓ Sample evidence created: ' . $evidence->exhibit_number);
    }

    /**
     * Ensure all required roles exist
     */
    private function ensureRolesExist(): void
    {
        $roles = [
            'system-administrator',
            'administrator',
            'source-officer',
            'investigator',
            'financial-verifier',
            'prosecutor',
            'judicial-viewer',
            'supervisor',
            'evidence-officer',
            'user',
        ];

        foreach ($roles as $roleName) {
            if (!Role::where('name', $roleName)->exists()) {
                Role::create([
                    'name' => $roleName,
                    'guard_name' => 'web',
                ]);
            }
        }
    }
}
