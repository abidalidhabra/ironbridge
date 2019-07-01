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
    ];

    public function widget(){
    	return $this->belongsTo(WidgetItem::class);
    }
}
