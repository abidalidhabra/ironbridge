<?php

namespace App\Repositories;

use App\Models\v2\Season;

class SeasonRepository
{

    protected $model;

    public function __construct()
    {
        $this->model = new Season;
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function all($fields = ['*'])
    {
        return $this->model->all($fields);
    }

    public function getModel()
    {
        return $this->model;
    }

    public function with($ralation = null, \Closure $callback = null)
    {
        if ($callback) {
            return $this->model->with($ralation, $callback);
        }else{
            return $this->model->with($ralation);
        }
    }
}