<?php

namespace App\Repositories\Hunt;

use App\Models\v2\HuntUserDetail;

class HuntUserDetailRepository
{

    public function find($id, $fields = ['*'])
    {
        return HuntUserDetail::find($id, $fields);
    }
    
    public function update(array $fields, array $cond, boolean $onObject)
    {
        return HuntUserDetail::where($cond)->update($fields);
    }

    public function calculateTheTimer(HuntUserDetail $huntUserDetail, $action)
    {
        $startdate  = $huntUserDetail->started_at;
        $finishedIn = $huntUserDetail->finished_in + now()->diffInSeconds($startdate);
        $huntUserDetail->finished_in = $finishedIn;
        $huntUserDetail->started_at = null;
        $huntUserDetail->ended_at = null;
        $huntUserDetail->status = $action;
        $huntUserDetail->save();
        return $huntUserDetail;
    }
}