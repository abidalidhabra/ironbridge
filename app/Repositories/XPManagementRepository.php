<?php

namespace App\Repositories;

use App\Repositories\ModelRepository;

class XPManagementRepository extends ModelRepository
{
    
    protected $model;
	public function __construct()
    {
        $this->model = new XpManagement;
    }
}