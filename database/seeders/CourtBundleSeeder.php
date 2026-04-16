<?php

namespace Database\Seeders;

use App\Models\CourtBundle;
use App\Models\CourtBundleItem;
use App\Models\CourtBundleDisclosure;
use App\Models\Evidence;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourtBundleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $evidence = Evidence::all();
        $users = User::all();

        if ($evidence->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No evidence or users found. Skipping court bundle seeding.');
            return;
        }

        $caseReferences = [
            'CRIM-2026-001', 'CRIM-2026-002', 'CRIM-2026-003',
            'CIVIL-2026-001', 'CIVIL-2026-002',
            'CORR-2026-001', 'CORR-2026-002',
        ];

        $bundleTitles = [
            'Evidence Bundle - Criminal Case',
            'Court Documentation Package',
            'Investigation Evidence Collection',
            'Trial Preparation Bundle',
            'Appeal Documentation',
            'Corruption Case Evidence',
        ];

        // Create court bundles
        for ($i = 0; $i < 10; $i++) {
            $preparedBy = $users->random();
            $approvedBy = $users->random();
            $status = rand(0, 1) ? CourtBundle::STATUS_APPROVED : CourtBundle::STATUS_DRAFT;

            $bundle = CourtBundle::create([
                'title' => $bundleTitles[array_rand($bundleTitles)] . ' - ' . ($i + 1),
                'case_reference' => $caseReferences[array_rand($caseReferences)],
                'description' => 'Comprehensive evidence bundle for ' . ['criminal proceedings', 'civil litigation', 'corruption investigation', 'administrative review'][rand(0, 3)],
                'prepared_by_user_id' => $preparedBy->id,
                'approved_by_user_id' => $approvedBy->id,
                'approved_at' => $status === CourtBundle::STATUS_APPROVED ? now()->subDays(rand(1, 30)) : null,
                'status' => $status,
                'version' => 1,
                'metadata' => [
                    'court' => ['High Court', 'Magistrate Court', 'Supreme Court'][rand(0, 2)],
                    'judge' => 'Justice ' . ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones'][rand(0, 4)],
                    'urgency' => ['normal', 'urgent', 'critical'][rand(0, 2)],
                ],
            ]);

            // Add evidence items to the bundle
            $evidenceCount = rand(3, 8);
            $selectedEvidence = $evidence->random(min($evidenceCount, $evidence->count()));

            foreach ($selectedEvidence as $index => $evidenceItem) {
                CourtBundleItem::create([
                    'court_bundle_id' => $bundle->id,
                    'evidence_id' => $evidenceItem->id,
                    'item_order' => $index + 1,
                    'exhibit_number' => 'EXH-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'description' => 'Evidence item: ' . $evidenceItem->title,
                    'page_reference' => ($index + 1) * 10,
                ]);
            }

            // Add disclosures for approved bundles
            if ($status === CourtBundle::STATUS_APPROVED) {
                $disclosureCount = rand(1, 3);
                for ($j = 0; $j < $disclosureCount; $j++) {
                    CourtBundleDisclosure::create([
                        'court_bundle_id' => $bundle->id,
                        'shared_by_user_id' => $preparedBy->id,
                        'shared_with_user_id' => $users->random()->id,
                        'recipient_name' => 'Recipient ' . ($j + 1),
                        'notes' => 'Bundle disclosed for ' . ['court proceedings', 'legal review', 'investigation support'][rand(0, 2)],
                    ]);
                }
            }
        }

        $this->command->info('✓ Court bundles seeded: ' . CourtBundle::count() . ' bundles created');
        $this->command->info('✓ Court bundle items seeded: ' . CourtBundleItem::count() . ' items created');
        $this->command->info('✓ Court bundle disclosures seeded: ' . CourtBundleDisclosure::count() . ' disclosures created');
    }
}