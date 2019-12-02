<?php

namespace App\Repositories;

use App\Models\v2\MinigameHistory;
use App\Repositories\ModelRepository;

class MinigameHistoryRepository extends ModelRepository
{

    public function __construct()
    {
        $this->model = new MinigameHistory;
    }
}