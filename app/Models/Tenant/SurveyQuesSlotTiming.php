<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyQuesSlotTiming extends Model
{
    protected $table = 'survey_ques_slot_timings';

    protected $fillable = [
        'survey_questions_id',
        'survey_slot_times_id',
    ];

    public function slotTime(): BelongsTo
    {
        return $this->belongsTo(SurveySlotTime::class, 'survey_slot_times_id');
    }
}