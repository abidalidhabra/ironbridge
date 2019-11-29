<?php

namespace App\Repositories;

use App\Models\v2\Relic;
use App\Repositories\ModelRepository;

class RelicRepository extends ModelRepository
{

    protected $model;
    protected $relicId;

    public function __construct()
    {
        $this->model = new Relic;
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