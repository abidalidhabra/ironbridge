<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use App\Models\v1\WidgetItem;

class Avatar extends Eloquent
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'gender', 'skin_colors', 'hairs_colors', 'eyes_colors'
    ];
	    
    public function widget_item()
    {
    	return $this->hasMany(WidgetItem::class,'avatar_id');
    }
}
