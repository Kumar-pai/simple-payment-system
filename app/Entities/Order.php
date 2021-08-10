<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'uuid',
        'payment_status',
        'payment_vendor',
        'payment_datetime',
        'user_id',
        'plan_id',
        'plan_name',
        'amount'
    ];
}
