<?php

namespace App\Repositories;

use App\Models\v1\WidgetItem;
use App\Repositories\Contracts\WidgetItemInterface;
use App\Repositories\ModelRepository;

class WidgetItemRepository extends ModelRepository implements WidgetItemInterface
{
    
    protected $model;
	public function __construct()
    {
        $this->model = new WidgetItem;
    }
}