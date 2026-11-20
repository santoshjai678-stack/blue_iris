<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskCategory extends Model
{
    protected $table = 'task_categorys';

    protected $fillable = [
        'task_category_name',
        'order_by_id',
        'status',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class, 'task_categorys_id');
    }
}