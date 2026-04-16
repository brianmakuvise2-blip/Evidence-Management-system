<?php

namespace Database\Seeders;

use App\Models\Evidence;
use App\Models\User;
use App\Models\Institution;
use App\Models\Department;
use Illuminate\Database\Seeder;

class EvidenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $institutions = Institution::all();
        $departments = Department::all();

        if ($users->isEmpty() || $institutions->isEmpty()) {
            $this->command->warn('No users or institutions found. Skipping evidence seeding.');
            return;
        }

        $evidenceTypes = [
            'physical' => [
                'Seized cash bundle',
                'Confiscated vehicle',
                'Recovered stolen property',
                'Drug paraphernalia',
                'Firearm evidence',
                'Digital storage device',
                'Financial documents',
                'Surveillance footage',
                'Witness statements',
                'Crime scene photographs',
            ],
            'digital' => [
                'Email communications',
                'Financial transaction logs',
                'Digital photographs',
                'Video recordings',
                'Database records',
                'Chat logs',
                'Social media evidence',
                'GPS tracking data',
                'Network logs',
                'Encrypted files',
            ],
        ];

        $classifications = ['public', 'confidential', 'restricted', 'sealed'];
        $statuses = [Evidence::STATUS_REGISTERED, Evidence::STATUS_VERIFIED, Evidence::STATUS_STORED, Evidence::STATUS_ARCHIVED];

        // Create additional evidence items
        for ($i = 0; $i < 50; $i++) {
            $type = array_rand($evidenceTypes);
            $title = $evidenceTypes[$type][array_rand($evidenceTypes[$type])];
            $collectedBy = $users->random();
            $institution = $institutions->random();
            $department = $departments->where('institution_id', $institution->id)->first() ?? $departments->random();
            $status = $statuses[array_rand($statuses)];

            $evidence = Evidence::create([
                'case_reference' => 'CASE-2026-' . str_pad($i + 2, 3, '0', STR_PAD_LEFT),
                'exhibit_number' => 'EXH-2026-' . str_pad($i + 2, 3, '0', STR_PAD_LEFT) . '-' . chr(65 + rand(0, 25)),
                'title' => $title . ' - Case ' . ($i + 1),
                'description' => 'Detailed description of ' . strtolower($title) . ' collected as evidence in criminal investigation.',
                'evidence_type' => $type,
                'collected_date' => now()->subDays(rand(1, 365)),
                'source' => ['Search warrant', 'Voluntary surrender', 'Crime scene', 'Witness tip', 'Surveillance'][rand(0, 4)],
                'location_found' => $this->generateLocation(),
                'classification_level' => $classifications[array_rand($classifications)],
                'collected_by_user_id' => $collectedBy->id,
                'institution_id' => $institution->id,
                'department_id' => $department->id,
                'file_type' => $type === 'digital' ? 'application/pdf' : 'image/jpeg',
                'file_size' => rand(1024, 10485760), // 1KB to 10MB
                'file_hash' => hash('sha256', 'evidence_file_' . $i . '_' . time()),
                'status' => $status,
                'verified_at' => in_array($status, [Evidence::STATUS_VERIFIED, Evidence::STATUS_ARCHIVED]) ? now()->subDays(rand(1, 30)) : null,
                'verified_by_user_id' => in_array($status, [Evidence::STATUS_VERIFIED, Evidence::STATUS_ARCHIVED]) ? $users->random()->id : null,
                'verification_notes' => in_array($status, [Evidence::STATUS_VERIFIED, Evidence::STATUS_ARCHIVED]) ? 'Evidence verified and authenticated' : null,
            ]);
        }

        $this->command->info('✓ Additional evidence seeded: ' . (Evidence::count() - 1) . ' evidence items created (total: ' . Evidence::count() . ')');
    }

    /**
     * Generate a random location
     */
    private function generateLocation(): string
    {
        $locations = [
            '123 Main Street, Bulawayo',
            '456 Park Avenue, Harare',
            '789 Industrial Road, Gweru',
            '321 Residential Area, Mutare',
            '654 Commercial District, Masvingo',
            '987 Rural Area, Chitungwiza',
            '147 Business Center, Kwekwe',
            '258 Shopping Complex, Kadoma',
            '369 Warehouse District, Chegutu',
            '741 Office Building, Marondera',
        ];

        return $locations[array_rand($locations)];
    }
}