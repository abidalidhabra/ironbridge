<?php

namespace App\Repositories;

use App\Models\v2\MGCLoot;
use App\Repositories\ModelRepository;

class MGCLootRepository extends ModelRepository
{

    protected $model;
	
    public function __construct()
    {
        $this->model = new MGCLoot;
    }
}