<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $table = 'countries';

    protected $fillable = [
        'name',
        'short_name',
        'phone_code',
        'status',
    ];

    public function states(): HasMany
    {
        return $this->hasMany(State::class, 'country_id');
    }
}