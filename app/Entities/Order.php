<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'uuid',
        'user_id',
        'plan_id',
        'plan_name',
        'amount'
    ];
}
