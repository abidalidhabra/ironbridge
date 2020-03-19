<?php

namespace App\Services\User;

use App\Helpers\UserHelper;
use App\Repositories\MiniGameRepository;
use App\Repositories\RelicRepository;
use App\Services\Hunt\RelicService;
use App\Services\Traits\UserTraits;

class PostRegisterService
{
    use UserTraits;

    public function configure()
    {
        $this->addMinigameTutorialsForUser();
        $this->addPracticeGameUser();
        $this->setupFirstRelic();
        return $this;
    }

    public function configureForNewRegistration()
    {
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

    public function setupFirstRelic()
    {
        if (!$this->user->streaming_relic_id) {
            $relic = (new RelicRepository)->getModel()->active()->orderBy('number', 'asc')->select('_id', 'name', 'number', 'active', 'pieces', 'icon')->first();
            $this->user->streaming_relic_id = $relic->id;
            $this->user->save();
        }
        return $this;
    }
}