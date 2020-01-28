<?php

namespace App\Models\v2;

//use Illuminate\Database\Eloquent\Model;
use App\Collections\HuntUserCollection;
use App\Models\v2\Relic;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class HuntUser extends Eloquent
{

    /** status ->  [participated, paused, running, completed] **/
    protected $fillable = [
        'user_id',
        'hunt_id',
        'complexity',
        'hunt_mode',
        'status',
        'started_at',
        'ended_at',
        'finished_in',
        'hunt_complexity_id',
        'estimated_time',
        'relic_id',
        'collected_piece',
        // 'relic_reference_id',
    ];

    protected $dates = [
        'started_at',
        'ended_at',
    ];

    protected $attributes = [
        'status'     => 'participated',
        'started_at' => null,
        'ended_at'   => null,
        'finished_in'=> 0
    ];

    public function hunt()
    {
        return $this->belongsTo('App\Models\v1\Hunt','hunt_id');
    }

    public function hunt_user_details()
    {
        return $this->hasMany('App\Models\v2\HuntUserDetail');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\v1\User','user_id');
    }    

    public function hunt_complexities()
    {
        return $this->belongsTo('App\Models\v2\HuntComplexity','hunt_complexity_id');
    }

    public function hunt_complexity()
    {
        return $this->belongsTo('App\Models\v2\HuntComplexity','hunt_complexity_id');
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new HuntUserCollection($models);
    }

    public function relic()
    {
        return $this->belongsTo(Relic::class, 'relic_id', '_id');
    }

    // public function relic_reference()
    // {
    //     return $this->belongsTo(Relic::class, 'relic_reference_id', '_id');
    // }
}
