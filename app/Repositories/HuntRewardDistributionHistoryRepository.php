<?php

namespace App\Repositories;

use App\Models\v2\HuntRewardDistributionHistory;

class HuntRewardDistributionHistoryRepository
{

    protected $model;

    public function __construct()
    {
        $this->model = new HuntRewardDistributionHistory();
    }

    /**
     *
     * @param array
     * @return array
     *
     */
    public function create($data)
    {
        return $this->model->create($data);
    }

    public function getModel()
    {
        return $this->model;
    }
}