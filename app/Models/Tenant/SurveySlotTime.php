<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class SurveySlotTime extends Model
{
    protected $table = 'survey_slot_times';

    protected $fillable = [
        'slot_name',
        'slot_start_time',
        'slot_end_time',
        'status',
    ];

    public function getFormattedSlotAttribute(): string
    {
        $start = date('g:i A', strtotime($this->slot_start_time));
        $end = date('g:i A', strtotime($this->slot_end_time));
        return "{$this->slot_name} ({$start} - {$end})";
    }
}