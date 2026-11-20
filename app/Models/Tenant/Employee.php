<?php

namespace App\Models\Tenant;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    protected $table = 'employees';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone_no',
        'dob',
        'joining_date',
        'rejoin_date',
        'address',
        'city',
        'state',
        'zipcode',
        'status',
    ];

    protected $casts = [
        'dob' => 'date',
        'joining_date' => 'date',
        'rejoin_date' => 'date',
        'status' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function designations(): BelongsToMany
    {
        return $this->belongsToMany(
            Designation::class,
            'employee_designation',
            'employee_user_id',
            'designation_id'
        )->wherePivot('active', 1)->withTimestamps();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(EmployeeSchedule::class, 'employee_user_id', 'id');
    }

    public function activeSchedule(): HasOne
    {
        return $this->hasOne(EmployeeSchedule::class, 'employee_user_id', 'id')
            ->where('is_active', 1)
            ->latestOfMany();
    }

    public function hireRecords(): HasMany
    {
        return $this->hasMany(EmployeeHireRecord::class, 'employee_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getStatusLabelAttribute(): string
    {
        return match ((int)$this->status) {
            1 => 'Active',
            0 => 'Inactive',
            2 => 'Terminated',
            default => 'Unknown',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ((int)$this->status) {
            1 => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
            0 => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
            2 => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
            default => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
        };
    }
}