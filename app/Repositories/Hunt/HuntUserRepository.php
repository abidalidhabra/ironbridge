<?php

namespace App\Repositories\Hunt;

use App\Models\v2\HuntUser;

class HuntUserRepository
{
    private $model;

    public function __construct()
    {
        $this->model = new HuntUser;
    }

    public function find($id, $fields = ['*'])
    {
        return $this->model->find($id, $fields);
    }
    
    public function update(array $fields, array $cond)
    {
        return $this->model->where($cond)->update($fields);
    }

    public function whereHas($relation, \Closure $closure)
    {
        return $this->model->whereHas($relation, $closure);
    }

    public function where($column, $value)
    {
        return $this->model->where($column, $value);
    }
}