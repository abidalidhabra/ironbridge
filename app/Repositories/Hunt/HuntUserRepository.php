<?php

namespace App\Repositories\Hunt;

use App\Models\v2\HuntUser;

class HuntUserRepository
{

    public function find($id, $fields = ['*'])
    {
        return HuntUser::find($id, $fields);
    }
    
    public function update(array $fields, array $cond, bool $onObject)
    {
        return HuntUser::where($cond)->update($fields);
    }
}