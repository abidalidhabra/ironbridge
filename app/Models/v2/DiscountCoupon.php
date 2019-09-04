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
    	'can_mutitime_use',
        'total_used_coupon',
        'users_id',
        'avatar_ids'
    ];

    protected $dates = [
    	'start_at',
    	'end_at',
    ];

    protected $attributes = [
        'total_used_coupon' => 0,
        'users_id' => [],
        'avatar_ids' => [],
    ];
}
