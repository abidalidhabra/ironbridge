<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class PlanPurchase extends Eloquent
{
    protected $fillable = ['user_id', 'plan_id', 'country_code', 'gold_price', 'price', 'transaction_id', 'compasses'];

    public function plan()
    {
    	return $this->belongsTo('App\Models\v2\Plan');
    }

    public function country()
    {
    	return $this->belongsTo('App\Models\v1\Country','country_code','code');
    }

    public function user()
    {
    	return $this->belongsTo('App\Models\v1\User','user_id');
    }
}
