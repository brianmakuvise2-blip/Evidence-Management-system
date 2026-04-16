<?php

namespace Database\Seeders;

use App\Models\ChainOfCustody;
use App\Models\Evidence;
use App\Models\User;
use App\Models\Institution;
use Illuminate\Database\Seeder;

class ChainOfCustodySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $evidence = Evidence::all();
        $users = User::all();
        $institutions = Institution::all();

        if ($evidence->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No evidence or users found. Skipping chain of custody seeding.');
            return;
        }

        // Create chain of custody records for existing evidence
        foreach ($evidence as $item) {
            // Initial collection custody record
            ChainOfCustody::create([
                'evidence_id' => $item->id,
                'from_user_id' => $item->collected_by_user_id,
                'from_institution_id' => $item->institution_id,
                'to_user_id' => $item->collected_by_user_id,
                'to_institution_id' => $item->institution_id,
                'transferred_at' => $item->collected_date,
                'received_at' => $item->collected_date,
                'location' => 'Evidence Collection Site',
                'purpose' => 'Initial collection and custody',
                'transfer_reason' => 'Evidence collection',
                'transfer_reference' => 'COLL-' . $item->id . '-' . now()->format('Ymd'),
                'condition_notes' => 'Evidence collected in good condition',
                'received_notes' => 'Evidence received and catalogued',
                'notes' => 'Initial custody established',
                'signature_from' => 'Digital signature: ' . $item->collected_by_user_id,
                'signature_to' => 'Digital signature: ' . $item->collected_by_user_id,
                'file_hash_at_transfer' => $item->file_hash,
            ]);

            // Create additional custody transfers (2-3 per evidence item)
            $transferCount = rand(1, 3);
            $currentUser = $item->collected_by_user_id;
            $currentInstitution = $item->institution_id;

            for ($i = 0; $i < $transferCount; $i++) {
                $toUser = $users->random();
                $toInstitution = $institutions->random();

                ChainOfCustody::create([
                    'evidence_id' => $item->id,
                    'from_user_id' => $currentUser,
                    'from_institution_id' => $currentInstitution,
                    'to_user_id' => $toUser->id,
                    'to_institution_id' => $toInstitution->id,
                    'transferred_at' => now()->subDays(rand(1, 30)),
                    'received_at' => now()->subDays(rand(0, 29)),
                    'location' => 'Secure Transfer Facility',
                    'purpose' => 'Evidence transfer for ' . ['investigation', 'court proceedings', 'storage', 'analysis'][rand(0, 3)],
                    'transfer_reason' => 'Case progression - ' . ['Investigation', 'Trial Preparation', 'Evidence Review', 'Secure Storage'][rand(0, 3)],
                    'transfer_reference' => 'TRANS-' . $item->id . '-' . ($i + 1) . '-' . now()->format('Ymd'),
                    'supervisor_approver_id' => $users->where('id', '!=', $currentUser)->random()->id,
                    'condition_notes' => 'Evidence in ' . ['excellent', 'good', 'fair'][rand(0, 2)] . ' condition',
                    'received_notes' => 'Evidence verified and accepted',
                    'notes' => 'Transfer completed successfully',
                    'signature_from' => 'Digital signature: ' . $currentUser,
                    'signature_to' => 'Digital signature: ' . $toUser->id,
                    'file_hash_at_transfer' => hash('sha256', 'transfer_' . $item->id . '_' . $i),
                ]);

                $currentUser = $toUser->id;
                $currentInstitution = $toInstitution->id;
            }
        }

        $this->command->info('✓ Chain of custody records seeded: ' . ChainOfCustody::count() . ' records created');
    }
}