<?php

namespace App\Models\Tenant;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalonSurveyAnswer extends Model
{
    protected $table = 'salon_survey_answers';

    protected $fillable = [
        'ans_unique_id',
        'user_id',
        'salon_id',
        'survey_questions_id',
        'survey_periods_id',
        'slot_times_id',
        'survey_ques_options_id',
        'control_type_id',
        'ans_text',
        'answer_location',
        'ans_date',
    ];

    protected $casts = [
        'ans_date' => 'date',
    ];

    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class, 'salon_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class, 'survey_questions_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(SurveyPeriod::class, 'survey_periods_id');
    }

    public function slotTime(): BelongsTo
    {
        return $this->belongsTo(SurveySlotTime::class, 'slot_times_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(SurveyQuesOption::class, 'survey_ques_options_id');
    }
}