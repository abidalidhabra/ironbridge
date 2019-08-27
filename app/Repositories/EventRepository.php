<?php

namespace App\Repositories;

use App\Models\v2\Event;
use App\Repositories\Contracts\EventInterface;

class EventRepository implements EventInterface
{

    public function find($id, $columns = ['*'])
    {
        return Event::find($id, $columns);
    }
}