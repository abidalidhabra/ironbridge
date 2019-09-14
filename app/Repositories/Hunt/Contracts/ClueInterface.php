<?php

namespace App\Repositories\Hunt\Contracts;

use App\Models\v2\HuntUserDetail;

interface ClueInterface
{
    public function action($huntUserDetailId);
}