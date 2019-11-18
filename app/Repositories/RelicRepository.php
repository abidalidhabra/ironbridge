<?php

namespace App\Repositories;

use App\Models\v2\Relic;

class RelicRepository
{

    protected $model;
    protected $relicId;

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

    public function setRelicId($relicId)
    {
        $this->relicId = $relicId;
        return $this;
    }

    public function addUser($userId)
    {
        $this->model->where('_id', $this->relicId)->push('users', $userId, true);
        return $userId;
    }
}