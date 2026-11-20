<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyPeriod extends Model
{
    protected $table = 'survey_periods';

    protected $fillable = [
        'period',
        'status',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class, 'survey_periods_id');
    }
}