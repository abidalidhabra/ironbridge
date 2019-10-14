<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;

class MinigamesHistory extends Model
{
    protected $fillable = ['game_id', 'time', 'score', 'action', 'from', 'random_mode', 'complexity'];
}
