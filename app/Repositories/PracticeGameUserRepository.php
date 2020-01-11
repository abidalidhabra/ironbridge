<?php

namespace App\Repositories;

use App\Models\v2\PracticeGameUser;
use App\Repositories\ModelRepository;
use Illuminate\Support\Collection;

class PracticeGameUserRepository extends ModelRepository
{
    protected $user;
    protected $model;
	public function __construct()
    {
        $this->model = new PracticeGameUser;
    }

    public function toModel(array $value)
    {
        return new PracticeGameUser($value);
    }

    public function unlockTheGame(Collection $practiceGameUsers)
    {
        $practiceGameUsers->map(function($practiceGameUser) {
        	$practiceGameUser->unlocked_at = now();
        	$practiceGameUser->save();
            return $practiceGameUser;
        });
        return $practiceGameUsers;
    }
}