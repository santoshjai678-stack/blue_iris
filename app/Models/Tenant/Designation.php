<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Designation extends Model
{
    protected $table = 'designations';

    protected $fillable = [
        'designation',
        'is_public',
        'status',
    ];

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(
            Employee::class,
            'employee_designation',
            'designation_id',
            'employee_user_id'
        )->wherePivot('active', 1);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class, 'designations_id');
    }
}