<?php

namespace App\Repositories;

class ModelRepository
{
    protected $model;

    public function findOrFail($id, $columns = ['*'])
    {
        return  $this->model->findOrFail($id, $columns);
    }

    public function find($id, $columns = ['*'])
    {
        return  $this->model->find($id, $columns);
    }

    public function all($fields = ['*'])
    {
        return $this->model->all($fields);
    }

    public function with($ralation = null, \Closure $callback = null)
    {
        if ($callback) {
            return $this->model->with($ralation, $callback);
        }else{
            return $this->model->with($ralation);
        }
    }

    public function whereHas($relation, \Closure $callback = null)
    {
        return $this->model->whereHas($relation, $callback);
    }

    public function getModel()
    {
        return $this->model;
    }
}