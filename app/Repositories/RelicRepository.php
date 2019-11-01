<?php

namespace App\Repositories;

use App\Models\v2\Relic;

class RelicRepository
{

    protected $model;

    public function __construct()
    {
        $this->model = new Relic;
    }

    public function find($id, $columns = ['*'])
    {
        return $this->model->find($id, $columns);
    }

    public function all($fields = ['*'])
    {
        return $this->model->all($fields);
    }

    public function getModel()
    {
        return $this->model;
    }
}