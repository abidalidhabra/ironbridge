<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use MongoDB\BSON\ObjectID;

class AdminPasswordSetLink extends Eloquent
{
	protected $fillable = ['_id','admin_id','token','used_on'];

	public function admin()
    {
        return $this->belongsTo('App\Models\v1\Admin');
    }

}
