<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use App\Collections\MiniGameCollection;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use MongoDB\BSON\UTCDateTime;

class PracticeGameUser extends Eloquent
{
    protected $fillable = ['user_id', 'game_id', 'completed_at', /*'piece', 'piece_collected',*/ 'unlocked_at', 'completion_times'];

    protected $dates = [
        'completed_at',
        'unlocked_at',
    ];

    protected $attributes = [
        'completed_at' => null,
        // 'piece_collected' => false,
        'unlocked_at' => null,
        'completion_times' => 0,
    ];
    
    public function setCompletedAtAttribute($value)
    {
    	$this->attributes['completed_at'] = new UTCDateTime($value);
    }

    public function game()
    {
        return $this->belongsTo('App\Models\v1\Game','game_id');
    }

    public function markAsIncomplete()
    {

        if (!is_null($this->completed_at)) {
            // \DB::connection()->enableQueryLog();
            $this->forceFill(['completed_at' => null])->save();
            // $queries = \DB::getQueryLog();
            // dump($this->completed_at);
            // dd($queries);
        }
    }

    public function newCollection(array $models = [])
    {
        return new MiniGameCollection($models);
    }
}
