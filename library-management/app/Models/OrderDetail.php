<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'type',
        'type_id',
        'item_details',
        'amount'
    ];
}
