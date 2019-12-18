<?php

namespace App\Repositories;

use App\Models\v2\Loot;
use App\Repositories\ModelRepository;

class LootRepository extends ModelRepository
{

    protected $model;
	
    public function __construct()
    {
        $this->model = new Loot;
    }
}