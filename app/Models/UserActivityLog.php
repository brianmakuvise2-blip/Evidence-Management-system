<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    protected $table = 'user_activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'ip_address',
        'user_agent',
        'details',
        'status',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failure');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    protected static function booted()
    {
        static::updating(function ($log) {
            throw new \Exception('Audit logs are immutable and cannot be modified.');
        });

        static::deleting(function ($log) {
            throw new \Exception('Audit logs are immutable and cannot be deleted.');
        });
    }

    public function delete()
    {
        throw new \Exception('Audit logs are immutable and cannot be deleted.');
    }

    public function forceDelete()
    {
        throw new \Exception('Audit logs are immutable and cannot be deleted.');
    }
}