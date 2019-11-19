<?php

namespace App\Services\User;

use App\Models\v2\AgentComplementary;
use App\Repositories\PracticeGameUserRepository;
use App\Repositories\User\UserRepository;
use App\Repositories\WidgetItemRepository;

class AddRelicService
{

	private $user;
    private $userRepository;
    private $relicId;

    public function setUser($user) {
        $this->user = $user;
        $this->userRepository = new UserRepository($this->user);
        return $this;
    }    

    public function setRelicId($relicId) {
        $this->relicId = $relicId;
        return $this;
    }

    public function add() {

        // $this->user->relics()->attach($this->relicId);
        $this->userRepository->addRelic($this->relicId);
        return $this;
    }

    public function getRelic($fields = ['*'])
    {
        return $this->user->relics_info()->where('_id', $this->relicId)->first($fields);
    }
}