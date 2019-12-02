<?php

namespace App\Repositories\Hunt;

use App\Models\v2\HuntUserDetail;

class HuntUserDetailRepository
{

    public function find($id, $fields = ['*'])
    {
        return HuntUserDetail::find($id, $fields);
    }
    
    public function update(array $fields, array $cond)
    {
        return HuntUserDetail::where($cond)->update($fields);
    }

    public function calculateTheTimer(HuntUserDetail $huntUserDetail, $action, $dataToUpdate = [])
    {
        $startdate  = $huntUserDetail->started_at;
        $finishedIn = $huntUserDetail->finished_in + now()->diffInSeconds($startdate);
        $huntUserDetail->finished_in = $finishedIn;
        $huntUserDetail->started_at = null;
        $huntUserDetail->ended_at = null;
        $huntUserDetail->status = $action;
        if (count($dataToUpdate) > 0) {
            foreach ($dataToUpdate as $key => $value) {
                $huntUserDetail->$key = is_numeric($value)? (int)$value: $value;
            }
        }
        $huntUserDetail->save();
        return $huntUserDetail;
    }

    public function push(array $cond, String $column, array $dataToBePush)
    {
        return HuntUserDetail::where($cond)->push($column, $dataToBePush);
    }
}