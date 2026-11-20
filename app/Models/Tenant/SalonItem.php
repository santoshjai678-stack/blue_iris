<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class SalonItem extends Model
{
    protected $table = 'salon_items';

    protected $fillable = [
        'salon_id',
        'item_name',
        'item_quantity',
        'expiry_date',
        'status',
    ];
}