<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SurveyQuestion extends Model
{
    protected $table = 'survey_questions';

    protected $fillable = [
        'question',
        'salon_id',
        'designations_id',
        'task_categorys_id',
        'survey_periods_id',
        'task_type',
        'task_tag_slug',
        'control_type',
        'is_question_mandatory',
        'is_other_option_add',
        'next_renewal_date',
        'next_payable_amount',
        'grace_period_day',
        'comment',
        'status',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TaskCategory::class, 'task_categorys_id');
    }

    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class, 'salon_id');
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designations_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(SurveyPeriod::class, 'survey_periods_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(SurveyQuesOption::class, 'survey_ques_id');
    }

    public function slotTimings(): HasMany
    {
        return $this->hasMany(SurveyQuesSlotTiming::class, 'survey_questions_id');
    }

    public function slots(): BelongsToMany
    {
        return $this->belongsToMany(
            SurveySlotTime::class,
            'survey_ques_slot_timings',
            'survey_questions_id',
            'survey_slot_times_id'
        );
    }
}