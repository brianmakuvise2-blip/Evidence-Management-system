<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChainOfCustody extends Model
{
    use SoftDeletes;

    protected $table = 'chain_of_custody';

    protected $fillable = [
        'evidence_id',
        'from_user_id',
        'from_institution_id',
        'to_user_id',
        'to_institution_id',
        'transferred_at',
        'received_at',
        'location',
        'purpose',
        'transfer_reason',
        'transfer_reference',
        'supervisor_approver_id',
        'condition_notes',
        'received_notes',
        'notes',
        'signature_from',
        'signature_to',
        'digital_signature',
        'file_hash_at_transfer',
        'metadata',
    ];

    protected $casts = [
        'transferred_at' => 'datetime',
        'received_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Relationships
     */

    public function evidence()
    {
        return $this->belongsTo(Evidence::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function fromInstitution()
    {
        return $this->belongsTo(Institution::class, 'from_institution_id');
    }

    public function toInstitution()
    {
        return $this->belongsTo(Institution::class, 'to_institution_id');
    }

    public function supervisorApprover()
    {
        return $this->belongsTo(User::class, 'supervisor_approver_id');
    }

    public function activityLogs()
    {
        return $this->morphMany(UserActivityLog::class, 'loggable');
    }

    /**
     * Get formatted transfer timeline
     */
    public function getFormattedTransferInfo(): array
    {
        return [
            'from_user' => $this->fromUser?->name,
            'from_institution' => $this->fromInstitution?->name,
            'to_user' => $this->toUser?->name,
            'to_institution' => $this->toInstitution?->name,
            'transferred_at' => $this->transferred_at?->format('Y-m-d H:i:s'),
            'reason' => $this->transfer_reason,
            'reference' => $this->transfer_reference,
        ];
    }

    /**
     * Mark as received
     */
    public function markAsReceived(User $receivedBy, string $notes = null)
    {
        return $this->update([
            'to_user_id' => $receivedBy->id,
            'received_at' => now(),
            'received_notes' => $notes,
        ]);
    }

    /**
     * Scope: Get full custody history for evidence
     */
    public function scopeForEvidence($query, $evidenceId)
    {
        return $query->where('evidence_id', $evidenceId)
            ->orderBy('transferred_at', 'asc');
    }

    /**
     * Scope: Get custody history by institution
     */
    public function scopeByInstitution($query, $institutionId)
    {
        return $query->where('from_institution_id', $institutionId)
            ->orWhere('to_institution_id', $institutionId);
    }
}
