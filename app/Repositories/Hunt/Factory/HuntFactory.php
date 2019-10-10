<?php

namespace App\Repositories\Hunt\Factory;

use App\Repositories\Hunt\ParticipationInRandomHuntRepository;
use Exception;

class HuntFactory
{
    
    public function init($request)
    {
        if ($request->filled('random')) {
            return new ParticipationInRandomHuntRepository;
        }

        throw new Exception("Invalid type provided for hunt participation.");
    }
}