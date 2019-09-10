<?php

namespace App\Repositories;

use App\Models\v1\WidgetItem;
use App\Repositories\Contracts\WidgetItemInterface;

class WidgetItemRepository implements WidgetItemInterface
{
    
    public function find($id, $fields = ['*'])
    {
        return WidgetItem::find($id, $fields);
    }
}