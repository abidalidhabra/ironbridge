<?php

namespace App\Models\v3;

use App\Models\v3\Event;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class EventUser extends Eloquent
{
    protected $fillable = ['event_id', 'user_id', 'status'];

    public function scopeRunning($query)
    {
    	return $query->where('status', 'running');
    }

    public function event()
    {
    	return $this->belongsTo(Event::class);
    }
}
