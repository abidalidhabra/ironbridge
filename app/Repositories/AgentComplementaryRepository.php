<?php

namespace App\Repositories;

use App\Models\v2\AgentComplementary;
use App\Repositories\ModelRepository;

class AgentComplementaryRepository extends ModelRepository
{

    protected $model;
	
    public function __construct()
    {
        $this->model = new AgentComplementary;
    }
}