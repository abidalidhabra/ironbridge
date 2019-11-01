<?php

namespace App\Repositories\Hunt\Factory;

use App\Repositories\Hunt\ParticipationInRandomHuntRepository;
use App\Repositories\Hunt\ParticipationInSeasonalHuntRepository;
use Exception;

class HuntFactory
{
    
    public function init($request)
    {
        if ($request->filled('random')) {
            return new ParticipationInRandomHuntRepository;
        }else{
            return new ParticipationInSeasonalHuntRepository;
        }

        throw new Exception("Invalid type provided for hunt participation.");
    }
}