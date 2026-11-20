<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeHireRecord extends Model
{
    protected $table = 'employee_hire_records';

    protected $fillable = [
        'employee_id',
        'hire_date',
        'termination_date',
        'reason',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'termination_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}