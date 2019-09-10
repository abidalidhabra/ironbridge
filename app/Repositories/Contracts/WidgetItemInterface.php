<?php

namespace App\Repositories\Contracts;

interface WidgetItemInterface {
    
    public function find($id, $fields);
}