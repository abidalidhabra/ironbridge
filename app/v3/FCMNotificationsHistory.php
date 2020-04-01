<?php

namespace App\v3;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class FCMNotificationsHistory extends Eloquent
{
    
    protected $table = 'fcm_notifications_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'message',
        'target',
        'target_audience',
        'cities',
        'countries',
        'send_at', 
        'status'
    ];

    protected $dates = [
        'send_at'
    ];

    protected $attributes = [
        'status'=> 'pending'
    ];

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
