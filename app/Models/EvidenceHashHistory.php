<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvidenceHashHistory extends Model
{
    protected $table = 'evidence_hash_history';

    protected $fillable = [
        'evidence_id',
        'hash_type',
        'content_hash',
        'metadata_hash',
        'change_type',
        'previous_state',
        'changed_fields',
        'user_id',
        'user_ip',
        'user_agent',
        'is_verified',
        'verified_at',
        'verified_by_user_id',
        'tampering_detected',
        'tampering_notes',
        'action',
        'notes',
    ];

    protected $casts = [
        'previous_state' => 'array',
        'changed_fields' => 'array',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'tampering_detected' => 'boolean',
    ];

    /**
     * Change type constants
     */
    const CHANGE_TYPE_CREATED = 'created';
    const CHANGE_TYPE_UPDATED = 'updated';
    const CHANGE_TYPE_FILE_REPLACED = 'file_replaced';
    const CHANGE_TYPE_VERIFIED = 'verified';
    const CHANGE_TYPE_ACCESSED = 'accessed';
    const CHANGE_TYPE_VERIFICATION_FAILED = 'verification_failed';

    /**
     * Get the evidence this hash history belongs to
     */
    public function evidence(): BelongsTo
    {
        return $this->belongsTo(Evidence::class);
    }

    /**
     * Get the user who made the change
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who verified this hash entry
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    /**
     * Get available change types
     */
    public static function getChangeTypes(): array
    {
        return [
            self::CHANGE_TYPE_CREATED => 'Evidence Created',
            self::CHANGE_TYPE_UPDATED => 'Evidence Updated',
            self::CHANGE_TYPE_FILE_REPLACED => 'File Replaced',
            self::CHANGE_TYPE_VERIFIED => 'Evidence Verified',
            self::CHANGE_TYPE_ACCESSED => 'Evidence Accessed',
            self::CHANGE_TYPE_VERIFICATION_FAILED => 'Verification Failed',
        ];
    }

    /**
     * Check if this hash entry indicates tampering
     */
    public function detectTampering(): bool
    {
        if (!$this->evidence || !$this->evidence->file_path) {
            return false;
        }

        // For file-based evidence, verify the current file matches the stored hash
        $currentFilePath = storage_path('app/evidence/' . $this->evidence->file_path);
        
        if (!file_exists($currentFilePath)) {
            $this->tampering_detected = true;
            $this->tampering_notes = 'File not found at expected location';
            $this->save();
            return true;
        }

        $currentHash = hash_file($this->hash_type, $currentFilePath);
        
        if ($currentHash !== $this->content_hash) {
            $this->tampering_detected = true;
            $this->tampering_notes = "Hash mismatch: expected {$this->content_hash}, got {$currentHash}";
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Verify this hash entry
     */
    public function verify(User $verifier): bool
    {
        $this->is_verified = true;
        $this->verified_at = now();
        $this->verified_by_user_id = $verifier->id;
        
        return $this->save();
    }

    /**
     * Get the latest hash entry for an evidence item
     */
    public static function getLatestForEvidence(int $evidenceId): ?self
    {
        return self::where('evidence_id', $evidenceId)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Get all hash entries for an evidence item
     */
    public static function getHistoryForEvidence(int $evidenceId): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('evidence_id', $evidenceId)
            ->with(['user', 'verifiedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check if evidence has been tampered with
     */
    public static function checkEvidenceIntegrity(int $evidenceId): array
    {
        $history = self::getHistoryForEvidence($evidenceId);
        $evidence = Evidence::find($evidenceId);
        
        $results = [
            'evidence_id' => $evidenceId,
            'total_entries' => $history->count(),
            'tampered_entries' => 0,
            'verified_entries' => 0,
            'unverified_entries' => 0,
            'integrity_status' => 'unknown',
            'details' => [],
        ];

        foreach ($history as $entry) {
            if ($entry->tampering_detected) {
                $results['tampered_entries']++;
                $results['details'][] = [
                    'id' => $entry->id,
                    'change_type' => $entry->change_type,
                    'created_at' => $entry->created_at,
                    'tampering_notes' => $entry->tampering_notes,
                ];
            }
            
            if ($entry->is_verified) {
                $results['verified_entries']++;
            } else {
                $results['unverified_entries']++;
            }
        }

        // Determine overall integrity status
        if ($results['tampered_entries'] > 0) {
            $results['integrity_status'] = 'compromised';
        } elseif ($results['verified_entries'] > 0 && $results['unverified_entries'] === 0) {
            $results['integrity_status'] = 'verified';
        } elseif ($results['total_entries'] > 0) {
            $results['integrity_status'] = 'unverified';
        }

        return $results;
    }
}
