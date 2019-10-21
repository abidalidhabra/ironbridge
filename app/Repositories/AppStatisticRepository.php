<?php

namespace App\Repositories;

use App\Models\v2\AppStatistic;

class AppStatisticRepository
{

    protected $model;

    public function __construct()
    {
        $this->model = new AppStatistic();
    }

    /**
     *
     * @param array
     * @return array
     *
     */
    public function where($field, $value)
    {
        return $this->model->where($field, $value);
    }

    public function getModel()
    {
        return $this->model;
    }
}