<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class DiscountCoupon extends Eloquent
{
    protected $fillable = [
    	'discount_code',
    	'discount_types',
    	'discount',
    	'number_of_uses',
    	'start_at',
    	'end_at',
    	'description',
    	'can_mutitime_use'
    ];

    protected $dates = [
    	'start_at',
    	'end_at',
    ];
}
