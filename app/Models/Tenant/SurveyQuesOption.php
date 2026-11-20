<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyQuesOption extends Model
{
    protected $table = 'survey_ques_options';

    protected $fillable = [
        'survey_ques_id',
        'option',
        'status',
        'created_by',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class, 'survey_ques_id');
    }
}