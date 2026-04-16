<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourtBundleItem extends Model
{
    protected $table = 'court_bundle_items';

    protected $fillable = [
        'court_bundle_id',
        'evidence_id',
        'exhibit_number',
        'description',
        'page_reference',
        'item_order',
    ];

    public function bundle()
    {
        return $this->belongsTo(CourtBundle::class, 'court_bundle_id');
    }

    public function evidence()
    {
        return $this->belongsTo(Evidence::class, 'evidence_id');
    }
}
