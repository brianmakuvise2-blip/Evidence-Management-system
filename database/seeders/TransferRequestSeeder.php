<?php

namespace Database\Seeders;

use App\Models\TransferRequest;
use App\Models\Evidence;
use App\Models\User;
use App\Models\Institution;
use Illuminate\Database\Seeder;

class TransferRequestSeeder extends Seeder
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
            $this->command->warn('No evidence or users found. Skipping transfer request seeding.');
            return;
        }

        $statuses = [
            TransferRequest::STATUS_PENDING,
            TransferRequest::STATUS_APPROVED,
            TransferRequest::STATUS_REJECTED,
            TransferRequest::STATUS_IN_TRANSIT,
            TransferRequest::STATUS_ACKNOWLEDGED,
            TransferRequest::STATUS_COMPLETED,
        ];

        $urgencyLevels = ['low', 'medium', 'high', 'critical'];
        $reasons = [
            'Court proceedings',
            'Investigation support',
            'Evidence analysis',
            'Secure storage',
            'Inter-agency cooperation',
            'Trial preparation',
        ];

        // Create transfer requests for evidence items
        foreach ($evidence as $item) {
            $requestCount = rand(1, 3);

            for ($i = 0; $i < $requestCount; $i++) {
                $requestedBy = $users->random();
                $receivingOfficer = $users->random();
                $destinationInstitution = $institutions->random();
                // Ensure approver is not the evidence collector AND not the requester
                $approverPool = $users->where('id', '!=', $item->collected_by_user_id)
                                    ->where('id', '!=', $requestedBy->id);
                $supervisorApprover = $approverPool->isNotEmpty() ? $approverPool->random() : $users->where('id', '!=', $item->collected_by_user_id)->first();
                $status = $statuses[array_rand($statuses)];

                $transferRequest = TransferRequest::create([
                    'evidence_id' => $item->id,
                    'requested_by_user_id' => $requestedBy->id,
                    'receiving_officer_id' => $receivingOfficer->id,
                    'destination_institution_id' => $destinationInstitution->id,
                    'transfer_reason' => $reasons[array_rand($reasons)],
                    'urgency_level' => $urgencyLevels[array_rand($urgencyLevels)],
                    'status' => $status,
                    'requested_at' => now()->subDays(rand(1, 30)),
                    'transfer_reference' => 'TR-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                    'transfer_hash' => hash('sha256', 'transfer_request_' . $item->id . '_' . $i),
                ]);

                // Add status-specific data
                switch ($status) {
                    case TransferRequest::STATUS_APPROVED:
                        $transferRequest->update([
                            'approved_at' => $transferRequest->requested_at->addHours(rand(1, 24)),
                            'supervisor_approver_id' => $supervisorApprover->id,
                            'approval_notes' => 'Transfer approved for ' . $transferRequest->transfer_reason,
                        ]);
                        break;

                    case TransferRequest::STATUS_REJECTED:
                        $transferRequest->update([
                            'rejected_at' => $transferRequest->requested_at->addHours(rand(1, 24)),
                            'supervisor_approver_id' => $supervisorApprover->id,
                            'rejection_reason' => 'Insufficient justification provided',
                            'rejection_correction_notes' => 'Please provide more detailed reasoning for the transfer request.',
                        ]);
                        break;

                    case TransferRequest::STATUS_IN_TRANSIT:
                        $approvedAt = $transferRequest->requested_at->addHours(rand(1, 24));
                        $transferRequest->update([
                            'approved_at' => $approvedAt,
                            'in_transit_at' => $approvedAt->addHours(rand(1, 48)),
                            'supervisor_approver_id' => $supervisorApprover->id,
                            'approval_notes' => 'Transfer approved and now in transit',
                        ]);
                        break;

                    case TransferRequest::STATUS_ACKNOWLEDGED:
                        $approvedAt = $transferRequest->requested_at->addHours(rand(1, 24));
                        $inTransitAt = $approvedAt->addHours(rand(1, 48));
                        $transferRequest->update([
                            'approved_at' => $approvedAt,
                            'in_transit_at' => $inTransitAt,
                            'acknowledged_at' => $inTransitAt->addHours(rand(1, 24)),
                            'acknowledged_by_user_id' => $receivingOfficer->id,
                            'supervisor_approver_id' => $supervisorApprover->id,
                            'acknowledgment_notes' => 'Evidence received in good condition',
                        ]);
                        break;

                    case TransferRequest::STATUS_COMPLETED:
                        $approvedAt = $transferRequest->requested_at->addHours(rand(1, 24));
                        $inTransitAt = $approvedAt->addHours(rand(1, 48));
                        $acknowledgedAt = $inTransitAt->addHours(rand(1, 24));
                        $completedAt = $acknowledgedAt->addHours(rand(1, 24));
                        $transferRequest->update([
                            'approved_at' => $approvedAt,
                            'in_transit_at' => $inTransitAt,
                            'acknowledged_at' => $acknowledgedAt,
                            'completed_at' => $completedAt,
                            'acknowledged_by_user_id' => $receivingOfficer->id,
                            'supervisor_approver_id' => $supervisorApprover->id,
                            'acknowledgment_notes' => 'Transfer completed successfully',
                        ]);
                        break;
                }
            }
        }

        $this->command->info('✓ Transfer requests seeded: ' . TransferRequest::count() . ' requests created');
    }
}