<?php

namespace App\Models;

use App\Models\CourtBundleItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission;

class Evidence extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'case_reference',
        'exhibit_number',
        'title',
        'description',
        'evidence_type',
        'collected_date',
        'source',
        'location_found',
        'classification_level',
        'collected_by_user_id',
        'verified_at',
        'verified_by_user_id',
        'verification_notes',
        'transferred_at',
        'transferred_by_user_id',
        'disclosed_at',
        'disclosed_by_user_id',
        'disposed_at',
        'disposed_by_user_id',
        'disposal_reason',
        'status',
        'file_path',
        'file_type',
        'file_size',
        'file_hash',
        'metadata',
        'institution_id',
        'department_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'collected_date' => 'datetime',
        'verified_at' => 'datetime',
        'transferred_at' => 'datetime',
        'disclosed_at' => 'datetime',
        'disposed_at' => 'datetime',
        'metadata' => 'array',
        'file_size' => 'integer',
    ];

    /**
     * Constants for evidence status
     */
    const STATUS_REGISTERED = 'registered';
    const STATUS_VERIFIED = 'verified';
    const STATUS_STORED = 'stored';
    const STATUS_TRANSFERRED = 'transferred';
    const STATUS_DISCLOSED = 'disclosed';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_DISPOSED = 'disposed';
    const STATUS_REJECTED = 'rejected';

    /**
     * Available status options
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_REGISTERED => 'Registered (Pending Verification)',
            self::STATUS_VERIFIED => 'Verified',
            self::STATUS_STORED => 'Stored',
            self::STATUS_TRANSFERRED => 'Transferred',
            self::STATUS_DISCLOSED => 'Disclosed',
            self::STATUS_ARCHIVED => 'Archived',
            self::STATUS_DISPOSED => 'Disposed',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    /**
     * Evidence types
     */
    public static function getEvidenceTypes()
    {
        return [
            'physical' => 'Physical Evidence',
            'digital' => 'Digital Evidence',
            'document' => 'Document',
            'biological' => 'Biological',
            'forensic' => 'Forensic',
            'trace' => 'Trace Evidence',
            'multimedia' => 'Multimedia',
            'other' => 'Other',
        ];
    }

    /**
     * Relationships
     */

    /**
     * Get the institution that the evidence belongs to
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Get the department that collected the evidence
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the user who collected the evidence
     */
    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collected_by_user_id');
    }

    /**
     * Get the user who verified the evidence
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    /**
     * Get all chain of custody records for this evidence
     */
    public function chainOfCustodyRecords()
    {
        return $this->hasMany(ChainOfCustody::class);
    }

    /**
     * Alias for chainOfCustodyRecords for convenience
     */
    public function chainOfCustody()
    {
        return $this->chainOfCustodyRecords();
    }

    /**
     * Get all transfer requests for this evidence
     */
    public function transferRequests()
    {
        return $this->hasMany(TransferRequest::class);
    }

    /**
     * Get all court bundle items for this evidence
     */
    public function bundleItems()
    {
        return $this->hasMany(CourtBundleItem::class);
    }

    /**
     * Get all activity logs for this evidence
     */
    public function activityLogs()
    {
        return $this->morphMany(UserActivityLog::class, 'loggable');
    }

    /**
     * Scopes
     */

    /**
     * Filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Filter by evidence type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('evidence_type', $type);
    }

    /**
     * Filter by institution
     */
    public function scopeByInstitution($query, $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    /**
     * Filter by department
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Filter by case reference
     */
    public function scopeByCaseReference($query, $reference)
    {
        return $query->where('case_reference', 'like', "%{$reference}%");
    }

    /**
     * Filter by exhibit number
     */
    public function scopeByExhibitNumber($query, $exhibitNumber)
    {
        return $query->where('exhibit_number', 'like', "%{$exhibitNumber}%");
    }

    /**
     * Filter by file hash
     */
    public function scopeByFileHash($query, $fileHash)
    {
        return $query->where('file_hash', 'like', "%{$fileHash}%");
    }

    /**
     * Filter by classification level
     */
    public function scopeByClassificationLevel($query, $classificationLevel)
    {
        return $query->where('classification_level', $classificationLevel);
    }

    /**
     * Filter by officer name
     */
    public function scopeByOfficerName($query, $officerName)
    {
        return $query->where(function ($subquery) use ($officerName) {
            $subquery->whereHas('collectedBy', function ($q) use ($officerName) {
                $q->where('name', 'like', "%{$officerName}%");
            })->orWhereHas('verifiedBy', function ($q) use ($officerName) {
                $q->where('name', 'like', "%{$officerName}%");
            });
        });
    }

    /**
     * Filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('collected_date', [$startDate, $endDate]);
    }

    /**
     * Determine whether a user can view evidence details in the system.
     */
    public function canBeViewedBy(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether a user can download the evidence file.
     */
    public function canBeDownloadedBy(User $user): bool
    {
        if (! $this->canBeViewedBy($user)) {
            return false;
        }

        if ($user->hasAnyRole(['administrator', 'system-administrator'])) {
            return true;
        }

        if (! Permission::where('name', 'download-evidence')->where('guard_name', 'web')->exists()) {
            return false;
        }

        return $user->hasPermissionTo('download-evidence');
    }

    /**
     * Determine whether a user can access the evidence file
     */
    public function canBeAccessedBy(User $user): bool
    {
        return $this->canBeDownloadedBy($user);
    }

    /**
     * Get status badge CSS class
     */
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            self::STATUS_REGISTERED => 'bg-warning',
            self::STATUS_VERIFIED => 'bg-success',
            self::STATUS_STORED => 'bg-info',
            self::STATUS_TRANSFERRED => 'bg-primary',
            self::STATUS_DISCLOSED => 'bg-secondary',
            self::STATUS_ARCHIVED => 'bg-dark',
            self::STATUS_DISPOSED => 'bg-danger',
            self::STATUS_REJECTED => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Get the status display text
     */
    public function getStatusDisplay()
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Get the evidence type display text
     */
    public function getEvidenceTypeDisplay()
    {
        return self::getEvidenceTypes()[$this->evidence_type] ?? $this->evidence_type;
    }

    /**
     * Generate a public URL for the evidence file
     */
    public function getFileUrl()
    {
        if ($this->file_path) {
            return route('evidence.download', $this);
        }
        return null;
    }

    /**
     * Check if evidence has been verified
     */
    public function isVerified()
    {
        return $this->status === self::STATUS_VERIFIED && $this->verified_at !== null;
    }

    /**
     * Mark evidence as verified
     */
    public function markAsVerified($verifiedBy, $notes = null)
    {
        $this->update([
            'status' => self::STATUS_VERIFIED,
            'verified_at' => now(),
            'verified_by_user_id' => $verifiedBy,
            'verification_notes' => $notes,
        ]);

        return $this;
    }

    /**
     * Reject evidence verification
     */
    public function reject($verifiedBy, $notes = null)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'verified_by_user_id' => $verifiedBy,
            'verification_notes' => $notes,
        ]);

        return $this;
    }

    /**
     * Get the user who transferred the evidence
     */
    public function transferredBy()
    {
        return $this->belongsTo(User::class, 'transferred_by_user_id');
    }

    /**
     * Get the user who disclosed the evidence
     */
    public function disclosedBy()
    {
        return $this->belongsTo(User::class, 'disclosed_by_user_id');
    }

    /**
     * Get the user who disposed of the evidence
     */
    public function disposedBy()
    {
        return $this->belongsTo(User::class, 'disposed_by_user_id');
    }

    /**
     * Transfer evidence to another location/person
     */
    public function transfer($transferredBy, $details = null)
    {
        $this->update([
            'status' => self::STATUS_TRANSFERRED,
            'transferred_at' => now(),
            'transferred_by_user_id' => $transferredBy,
            'metadata' => array_merge($this->metadata ?? [], ['transfer_details' => $details]),
        ]);

        return $this;
    }

    /**
     * Mark evidence as stored
     */
    public function markAsStored($storedAt = null)
    {
        $this->update([
            'status' => self::STATUS_STORED,
            'metadata' => array_merge($this->metadata ?? [], ['stored_at' => $storedAt ?? now()]),
        ]);

        return $this;
    }

    /**
     * Disclose evidence (make available for legal proceedings)
     */
    public function disclose($disclosedBy, $reason = null)
    {
        if ($this->status !== self::STATUS_VERIFIED && $this->status !== self::STATUS_STORED) {
            throw new \Exception('Only verified or stored evidence can be disclosed.');
        }

        $this->update([
            'status' => self::STATUS_DISCLOSED,
            'disclosed_at' => now(),
            'disclosed_by_user_id' => $disclosedBy,
            'metadata' => array_merge($this->metadata ?? [], ['disclosure_reason' => $reason]),
        ]);

        return $this;
    }

    /**
     * Dispose of evidence
     */
    public function dispose($disposedBy, $reason = null)
    {
        $this->update([
            'status' => self::STATUS_DISPOSED,
            'disposed_at' => now(),
            'disposed_by_user_id' => $disposedBy,
            'disposal_reason' => $reason,
        ]);

        return $this;
    }
}
