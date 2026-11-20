<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PtoRecord extends Model
{
    protected $table = 'pto_records';

    protected $fillable = [
        'employee_id',
        'pto_date',
        'pto_minutes',
        'reason',
        'status',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}