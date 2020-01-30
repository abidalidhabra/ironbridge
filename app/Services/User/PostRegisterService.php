<?php

namespace App\Services\User;

use App\Helpers\UserHelper;
use App\Repositories\MiniGameRepository;
use App\Services\Hunt\RelicService;
use App\Services\Traits\UserTraits;

class PostRegisterService
{
    use UserTraits;

    public function configure()
    {
        $this->addMinigameTutorialsForUser();
        $this->addPracticeGameUser();
        $this->giveMapPiece();
        return $this;
    }

    public function addMinigameTutorialsForUser()
    {
        UserHelper::minigameTutorials($this->user);
        return $this;
    }

    public function addPracticeGameUser()
    {
        (new MiniGameRepository($this->user))->createIfnotExist();
        return $this;
    }

    public function giveMapPiece()
    {
        (new RelicService)->setUser($this->user)->addMapPiece();
        return $this;
    }
}