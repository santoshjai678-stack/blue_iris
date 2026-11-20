<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Salon extends Model
{
    protected $table = 'salons';

    protected $fillable = [
        'salon_name',
        'salon_email',
        'salon_phone',
        'address_1',
        'address_2',
        'city',
        'country_id',
        'state_id',
        'state',
        'zipcode',
        'salon_lat',
        'salon_long',
        'status',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function stateRel(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function timings(): HasMany
    {
        return $this->hasMany(SalonTiming::class, 'salon_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalonItem::class, 'salon_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(EmployeeSchedule::class, 'salon_id');
    }

    public function employees(): HasManyThrough
    {
        return $this->hasManyThrough(
            Employee::class,
            EmployeeSchedule::class,
            'salon_id',          // Foreign key on intermediate table (employee_schedules.salon_id)
            'id',                // Foreign key on target table (employees.id)
            'id',                // Local key on origin table (salons.id)
            'employee_user_id'   // Local key on intermediate table (employee_schedules.employee_user_id)
        );
    }
}
