<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class WidgetItem extends Eloquent
{
    protected $fillable = [
        'widget_name',
        'item_name',
        'gold_price',
        'avatar_id',
        'widget_category',
        'items',
        'free',
        'default',
        'similar_outfit',
    ];

    public function widget(){
    	return $this->belongsTo(WidgetItem::class);
    }

    public function avatar()
    {
        return $this->belongsTo('App\Models\v1\Avatar');
    }

    public function scopeHavingGender($query, $gender)
    {
        return $query->whereHas('avatar', function($query) use ($gender){
            return $query->where('gender', $gender); 
        });
    }
}
