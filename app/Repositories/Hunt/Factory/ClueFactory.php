<?php

namespace App\Repositories\Hunt\Factory;

use App\Repositories\Hunt\CompleteTheClueRepository;
use App\Repositories\Hunt\HuntUserDetailRepository;
use App\Repositories\Hunt\PauseTheClueRepository;
use App\Repositories\Hunt\RevealTheClueRepository;
use App\Repositories\Hunt\StartTheClueRepository;
use Exception;

class ClueFactory
{
    
    public function initializeAction($status)
    {
        if ($status == 'reveal') {
            return new RevealTheClueRepository;
        }else if($status == 'running') {
            return new StartTheClueRepository;
        }else if($status == 'paused') {
            return new PauseTheClueRepository;
        }else if($status == 'completed') {
            return new CompleteTheClueRepository;
        }
        throw new Exception("Invalid type provided.");
    }
}