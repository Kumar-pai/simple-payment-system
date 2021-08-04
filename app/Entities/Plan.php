<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = "plans";

    protected $fillable = [
        'name',
        'amount',
        'valid_date'
    ];
}
