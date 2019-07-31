<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class PlanPurchase extends Eloquent
{
    protected $fillable = ['user_id', 'plan_id', 'country_code', 'gold_value', 'keys_amount', 'price', 'transaction_id'];

    public function plan()
    {
    	return $this->belongsTo('App\Models\v2\Plan');
    }
}
