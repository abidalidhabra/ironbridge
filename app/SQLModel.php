<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SQLModel extends Model
{
	protected $fillable = [
        'name', 'email', 'email_verified_at', 'password', 'remember_token'
    ];
    
    protected $connection = 'mysql';
}
