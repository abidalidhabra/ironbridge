<?php

namespace App\Repositories\Game;

use App\Models\v2\UserRelicMapPiece;
use App\Repositories\ModelRepository;

class UserRelicMapPieceRepository extends ModelRepository
{

    protected $model;
    public function __construct(){
        $this->model = new UserRelicMapPiece;
    }
}