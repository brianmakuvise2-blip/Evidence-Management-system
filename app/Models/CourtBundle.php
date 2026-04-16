<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourtBundle extends Model
{
    protected $table = 'court_bundles';

    protected $fillable = [
        'title',
        'case_reference',
        'description',
        'prepared_by_user_id',
        'approved_by_user_id',
        'approved_at',
        'status',
        'version',
        'previous_version_id',
        'metadata',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'metadata' => 'array',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_SUPERSEDED = 'superseded';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_SUPERSEDED => 'Superseded',
        ];
    }

    public function items()
    {
        return $this->hasMany(CourtBundleItem::class)->orderBy('item_order');
    }

    public function disclosures()
    {
        return $this->hasMany(CourtBundleDisclosure::class);
    }

    public function preparedBy()
    {
        return $this->belongsTo(User::class, 'prepared_by_user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function previousVersion()
    {
        return $this->belongsTo(self::class, 'previous_version_id');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeCurrent($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isVisibleTo(User $user): bool
    {
        if ($user->hasAnyRole(['system-administrator', 'administrator', 'prosecutor'])) {
            return true;
        }

        if ($user->hasRole('judicial-viewer')) {
            return $this->isApproved();
        }

        return false;
    }
}
