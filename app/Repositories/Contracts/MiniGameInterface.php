<?php

namespace App\Repositories\Contracts;

interface MiniGameInterface {
    
    public function unlockAMiniGame(string $gameId);
}