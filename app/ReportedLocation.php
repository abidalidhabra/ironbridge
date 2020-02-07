<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Model;

class ReportedLocation extends Model
{
    protected $fillable = ['locationName', 'user_id', 'reasons', 'reasonDetails', 'languageCode', 'sent', 'requestId'];

    protected $attributes = [
    	'sent'=> false,
    ];

    public function scopeNotSended($query)
    {
    	return $query->where('sent', false);
    }
}
