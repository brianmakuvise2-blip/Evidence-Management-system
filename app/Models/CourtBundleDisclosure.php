<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourtBundleDisclosure extends Model
{
    protected $table = 'court_bundle_disclosures';

    protected $fillable = [
        'court_bundle_id',
        'shared_by_user_id',
        'shared_with_user_id',
        'recipient_name',
        'notes',
    ];

    public function bundle()
    {
        return $this->belongsTo(CourtBundle::class, 'court_bundle_id');
    }

    public function sharedBy()
    {
        return $this->belongsTo(User::class, 'shared_by_user_id');
    }

    public function sharedWith()
    {
        return $this->belongsTo(User::class, 'shared_with_user_id');
    }
}
