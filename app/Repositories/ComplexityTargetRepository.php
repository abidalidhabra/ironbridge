<?php

namespace App\Repositories;

use App\Models\v1\ComplexityTarget;

class ComplexityTargetRepository extends ModelRepository
{

    protected $model;
    public function __construct(){
        $this->model = new ComplexityTarget;
    }

}