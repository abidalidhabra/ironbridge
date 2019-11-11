<?php

namespace App\Repositories;

use App\Models\v2\PracticeGameUser;
use App\Repositories\ModelRepository;

class PracticeGameUserRepository extends ModelRepository
{
    protected $user;
	function __construct()
    {
        $this->model = new PracticeGameUser;
    }
}