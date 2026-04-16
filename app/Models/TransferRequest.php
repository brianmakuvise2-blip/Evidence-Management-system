<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TransferRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'evidence_id',
        'requested_by_user_id',
        'receiving_officer_id',
        'supervisor_approver_id',
        'acknowledged_by_user_id',
        'transfer_reason',
        'destination_institution_id',
        'urgency_level',
        'status',
        'requested_at',
        'approved_at',
        'rejected_at',
        'in_transit_at',
        'acknowledged_at',
        'completed_at',
        'approval_notes',
        'rejection_reason',
        'rejection_correction_notes',
        'acknowledgment_notes',
        'transfer_reference',
        'transfer_hash',
        'digital_signature',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'in_transit_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_ACKNOWLEDGED = 'acknowledged';
    const STATUS_COMPLETED = 'completed';

    /**
     * Get all statuses
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending Supervisor Review',
            self::STATUS_APPROVED => 'Approved - In Transit',
            self::STATUS_REJECTED => 'Rejected - Awaiting Correction',
            self::STATUS_IN_TRANSIT => 'In Transit',
            self::STATUS_ACKNOWLEDGED => 'Acknowledged by Receiver',
            self::STATUS_COMPLETED => 'Transfer Complete',
        ];
    }

    /**
     * Urgency levels
     */
    public static function getUrgencyLevels()
    {
        return [
            'low' => 'Low Priority',
            'medium' => 'Normal',
            'high' => 'High Priority',
            'critical' => 'Critical/Court Order',
        ];
    }

    /**
     * Relationships
     */
    public function evidence()
    {
        return $this->belongsTo(Evidence::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function receivingOfficer()
    {
        return $this->belongsTo(User::class, 'receiving_officer_id');
    }

    public function supervisorApprover()
    {
        return $this->belongsTo(User::class, 'supervisor_approver_id');
    }

    public function acknowledgedBy()
    {
        return $this->belongsTo(User::class, 'acknowledged_by_user_id');
    }

    public function destinationInstitution()
    {
        return $this->belongsTo(Institution::class, 'destination_institution_id');
    }

    /**
     * Generate unique transfer reference
     */
    public static function generateTransferReference(): string
    {
        do {
            $reference = 'TRF-' . strtoupper(Str::random(3)) . '-' . now()->format('YmdHis') . '-' . random_int(1000, 9999);
        } while (self::where('transfer_reference', $reference)->exists());

        return $reference;
    }

    /**
     * Generate transfer hash (for integrity)
     */
    public function generateTransferHash(): string
    {
        $data = json_encode([
            'evidence_id' => $this->evidence_id,
            'requested_by' => $this->requested_by_user_id,
            'receiving_officer' => $this->receiving_officer_id,
            'destination' => $this->destination_institution_id,
            'timestamp' => now()->toIso8601String(),
            'reference' => $this->transfer_reference,
        ]);

        return hash('sha256', $data);
    }

    /**
     * Approve transfer request
     */
    public function approve(User $supervisor, string $notes = null): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            throw new \Exception('Only pending transfers can be approved.');
        }

        $this->update([
            'status' => self::STATUS_APPROVED,
            'supervisor_approver_id' => $supervisor->id,
            'approved_at' => now(),
            'in_transit_at' => now(),
            'approval_notes' => $notes,
            'transfer_hash' => $this->generateTransferHash(),
        ]);

        // Log activity
        $supervisor->logActivity('transfer_approved', 'success', [
            'evidence_id' => $this->evidence_id,
            'transfer_reference' => $this->transfer_reference,
            'destination_institution' => $this->destinationInstitution->name,
        ]);

        return true;
    }

    /**
     * Reject transfer request
     */
    public function reject(User $supervisor, string $reason, string $correctionNotes = null): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            throw new \Exception('Only pending transfers can be rejected.');
        }

        $this->update([
            'status' => self::STATUS_REJECTED,
            'supervisor_approver_id' => $supervisor->id,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'rejection_correction_notes' => $correctionNotes,
        ]);

        // Log activity
        $supervisor->logActivity('transfer_rejected', 'warning', [
            'evidence_id' => $this->evidence_id,
            'transfer_reference' => $this->transfer_reference,
            'reason' => $reason,
        ]);

        return true;
    }

    /**
     * Acknowledge receipt of transferred evidence
     */
    public function acknowledgeReceipt(User $receivingOfficer, string $notes = null): bool
    {
        if ($this->status !== self::STATUS_IN_TRANSIT) {
            throw new \Exception('Transfer must be in transit before acknowledgment.');
        }

        if ($receivingOfficer->id !== $this->receiving_officer_id) {
            throw new \Exception('Only the designated receiving officer can acknowledge receipt.');
        }

        $this->update([
            'status' => self::STATUS_ACKNOWLEDGED,
            'acknowledged_by_user_id' => $receivingOfficer->id,
            'acknowledged_at' => now(),
            'acknowledgment_notes' => $notes,
            'completed_at' => now(),
            'digital_signature' => $this->generateDigitalSignature($receivingOfficer),
        ]);

        // Update evidence status
        $this->evidence->update([
            'status' => Evidence::STATUS_TRANSFERRED,
        ]);

        // Create chain of custody record
        ChainOfCustody::create([
            'evidence_id' => $this->evidence_id,
            'from_user_id' => $this->requestedBy->id,
            'from_institution_id' => $this->requestedBy->institution_id,
            'to_user_id' => $receivingOfficer->id,
            'to_institution_id' => $receivingOfficer->institution_id,
            'transferred_at' => $this->acknowledged_at,
            'transfer_reason' => $this->transfer_reason,
            'transfer_reference' => $this->transfer_reference,
            'supervisor_approver_id' => $this->supervisor_approver_id,
            'notes' => $notes,
        ]);

        // Log activity
        $receivingOfficer->logActivity('receipt_acknowledged', 'success', [
            'evidence_id' => $this->evidence_id,
            'transfer_reference' => $this->transfer_reference,
        ]);

        return true;
    }

    /**
     * Generate digital signature (for non-repudiation)
     */
    private function generateDigitalSignature(User $user): string
    {
        $data = json_encode([
            'transfer_id' => $this->id,
            'user_id' => $user->id,
            'timestamp' => now()->toIso8601String(),
            'email' => $user->email,
        ]);

        return hash('sha256', $data . config('app.key'));
    }

    /**
     * Get status badge CSS class
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-warning',
            self::STATUS_APPROVED => 'bg-info',
            self::STATUS_REJECTED => 'bg-danger',
            self::STATUS_IN_TRANSIT => 'bg-primary',
            self::STATUS_ACKNOWLEDGED => 'bg-success',
            self::STATUS_COMPLETED => 'bg-dark',
            default => 'bg-secondary',
        };
    }

    /**
     * Check if request is pending approval
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if request is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if request is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if transfer is complete
     */
    public function isComplete(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Scope: Get pending transfers for supervisor
     */
    public function scopePendingForSupervisor($query, User $supervisor)
    {
        return $query->where('status', self::STATUS_PENDING)
            ->where('destination_institution_id', $supervisor->institution_id)
            ->orWhere(function ($q) use ($supervisor) {
                $q->where('status', self::STATUS_PENDING)
                  ->whereHas('requestedBy', function ($userQuery) use ($supervisor) {
                      $userQuery->where('institution_id', $supervisor->institution_id);
                  });
            });
    }

    /**
     * Scope: Get pending acknowledgments for receiving officer
     */
    public function scopePendingAcknowledgmentFor($query, User $officer)
    {
        return $query->where('status', self::STATUS_IN_TRANSIT)
            ->where('receiving_officer_id', $officer->id);
    }
}
