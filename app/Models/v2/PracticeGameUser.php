<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use App\Collections\MiniGameCollection;
use App\Models\v2\MinigameHistory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use MongoDB\BSON\UTCDateTime;

class PracticeGameUser extends Eloquent
{
    protected $fillable = ['user_id', 'game_id', 'completed_at', 'unlocked_at', 'completion_times', 'favourite', 'last_play'];

    protected $dates = [
        'completed_at',
        'unlocked_at',
    ];

    protected $attributes = [
        'completed_at' => null,
        'unlocked_at' => null,
        'completion_times' => 0,
        'last_play' => [
            'stage'=> 0,
            'score'=> 0
        ],
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
            $this->forceFill(['completed_at' => null])->save();
        }
    }

    public function newCollection(array $models = [])
    {
        return new MiniGameCollection($models);
    }

    public function histories()
    {
        return $this->hasMany(MinigameHistory::class)->where(['user_id'=> $this->user_id, 'from'=> 'practice']);
    }

    public function highestScore()
    {
        return $this->histories()->where(['action'=> 'completed'])->orderBy('score', 'desc')->select('_id', 'practice_game_user_id', 'score')->limit(1);
    }

    public function practice_games_targets()
    {
        return $this->hasOne('App\Models\v1\PracticeGameTarget', 'game_id', 'game_id');
    }
}
