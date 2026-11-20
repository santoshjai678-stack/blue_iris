<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAttendance extends Model
{
    protected $table = 'employee_attendances';

    protected $fillable = [
        'employee_id',
        'salon_id',
        'attendance_date',
        'punch_in_time',
        'punch_out_time',
        'total_working_hours',
        'status',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class, 'salon_id');
    }
}