<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalonTiming extends Model
{
    protected $table = 'salon_timings';

    protected $fillable = [
        'salon_id',
        'day',
        'start_time',
        'end_time',
        'is_closed',
    ];

    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class, 'salon_id');
    }
}