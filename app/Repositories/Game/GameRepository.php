<?php

namespace App\Repositories\Game;

use App\Models\v1\Game;

class GameRepository
{

    protected $model;
    public function __construct(){
        $this->model = new Game;
    }

    public function all($fields = ['*'])
    {
        return $this->model->all($fields);
    }

    public function with($ralation = null, \Closure $callback = null)
    {
        return $this->model->with($ralation, $callback);
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