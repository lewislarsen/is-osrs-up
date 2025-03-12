<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusCheck extends Model
{
    protected $table = 'status_checks';
    protected $fillable = ['current_status', 'maintenance_data'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'maintenance_data' => 'array',
    ];

    /**
     * Check if this status represents a downtime
     *
     * @return bool
     */
    public function isDowntime(): bool
    {
        return strtolower($this->current_status) !== 'online';
    }
}