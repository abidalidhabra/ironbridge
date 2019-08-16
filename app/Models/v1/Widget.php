<?php

namespace App\Models\v1;

use App\Models\v1\WidgetItem;
// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Widget extends Eloquent
{
    protected $fillable = [
        'name',
    ];

    public function items(){
    	return $this->hasMany(WidgetItem::class);
    }
}
