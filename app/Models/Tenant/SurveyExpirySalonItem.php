<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyExpirySalonItem extends Model
{
    protected $table = 'survey_expiry_salon_items';

    protected $fillable = [
        'survey_ques_id',
        'salon_id',
        'salon_items_id',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class, 'survey_ques_id');
    }

    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class, 'salon_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(SalonItem::class, 'salon_items_id');
    }
}