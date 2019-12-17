<?php

namespace App\Repositories;

use App\Models\v2\HuntStatistic;
use App\Repositories\ModelRepository;

class HuntStatisticRepository extends ModelRepository
{

    protected $model;
	
    public function __construct()
    {
        $this->model = new HuntStatistic;
    }
}