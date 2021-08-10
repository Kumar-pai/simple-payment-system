<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table= 'transactions';

    protected $fillable = [
        'order_id',
        'ord_number',
        'transaction_number',
        'transaction_amount',
        'payment_date',
        'payment_processing_fee',
        'message',
    ];
}
