<?php

namespace App\Models\v3;

use App\Models\v3\City;
use App\Models\v3\EventUser;
use DateTimeZone;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use MongoDB\BSON\UTCDateTime;

class Event extends Eloquent
{
    
	protected $fillable = [ 'name', 'city_id', 'centeric_points', 'total_radius', 'least_radius', 'total_compasses', 'weekly_max_compasses', 'deductable_radius', 'time', 'started_at', 'finished_at'];   

	// protected $dates = [
	// 	'started_at',
	// 	'finished_at'
	// ];

	public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function participations()
    {
        return $this->hasMany(EventUser::class);
    }

    public function getTimeAttribute($value)
    {
        $city = $this->city()->select('_id', 'timezone')->first();
    	return [
    		'start'=> $value['start']->toDateTime()->setTimezone(new DateTimeZone($city->timezone))->format('Y-m-d H:i:s'),
    		'end'=> $value['end']->toDateTime()->setTimezone(new DateTimeZone($city->timezone))->format('Y-m-d H:i:s')
    	];
    }

    public function getStartedAtAttribute($value)
    {
        if ($value) {
            $city = $this->city()->select('_id', 'timezone')->first();
            return $value->toDateTime()->setTimezone(new DateTimeZone($city->timezone))->format('Y-m-d H:i:s');
        }
    }

    public function scopeParticipateable($query)
    {
    	return $query->where('time.start', '<=', new UTCDateTime())->whereNull('started_at')->whereNull('ended_at')->orderBy('time.start', 'asc');
    }

    // public function scopeUpcoming($query)
    // {
    //     return $query->where('time.start', '>', new UTCDateTime())->whereNull('started_at')->whereNull('ended_at');
    // }

    public function scopeRunning($query)
    {
        return $query->where('time.start', '<=', new UTCDateTime())->where('time.end', '>', new UTCDateTime());
    }

    public function scopeFinished($query)
    {
        return $query->where('time.end', '<=', new UTCDateTime())->whereNull('finished_at');
    }
}
