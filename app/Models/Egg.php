<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Egg extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'crates', 'date_laid','sold_quantity','sold_date','sale_price',
    ];

    protected $dates = ['date_laid', 'sold_date', 'deleted_at'];
}