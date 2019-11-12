<?php

namespace App\Repositories;

use App\Models\v2\PracticeGameUser;
use App\Repositories\ModelRepository;

class PracticeGameUserRepository extends ModelRepository
{
    protected $user;
    protected $model;
	public function __construct()
    {
        $this->model = new PracticeGameUser;
    }

    public function unlockTheGame(PracticeGameUser $practiceGameUser)
    {
    	$practiceGameUser->unlocked_at = now();
    	return $practiceGameUser->save();
    }
}