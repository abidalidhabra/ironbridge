<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class MongoModel extends Eloquent
{

	protected $fillable = [
        'name', 'email', 'email_verified_at', 'password', 'remember_token'
    ];

    protected $connection = 'mongodb';
}
