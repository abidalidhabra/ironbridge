<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Model;

class UserWidget extends Model
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'widget_item_id',
    	'selected',
    ];
}
