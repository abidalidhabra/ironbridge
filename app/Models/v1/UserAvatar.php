<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class UserAvatar extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'avatar_id', 'skin_color', 'hairs_color', 'eyes_color', 'widgets', 'used_widgets'
    ];
}
