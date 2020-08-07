<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Model;

class ReportedLocation extends Model
{
    protected $fillable = ['locationName', 'user_id', 'reasons', 'reasonDetails', 'languageCode', 'sent', 'requestId'];

    protected $dates = [
    	'sent_at',
    ];

    public function scopeNotSended($query)
    {
    	return $query->whereNull('sent_at');
    }    

    public function scopeSent($query)
    {
    	return $query->whereNotNull('sent_at');
    }
}
