<?php

namespace App\Repositories\Hunt;

use App\Models\v2\HuntUserDetail;
use App\Repositories\Hunt\Contracts\ClueInterface;
use App\Repositories\Hunt\HuntUserDetailRepository;
use App\Repositories\Hunt\HuntUserRepository;
use MongoDB\BSON\UTCDateTime;

class RevealTheClueRepository implements ClueInterface
{
    
    public function action($huntUserDetailId)
    {
        
        // get the hunt user detail
        $huntUserDetail = (new HuntUserDetailRepository)->find($huntUserDetailId);

        // mark the hunt_user as running and start the hunt_user if its first clue
        $huntUserDetails = $huntUserDetail->hunt_user->hunt_user_details()->get();
        $dataToBeUpdate['status'] = 'running';
        if ($huntUserDetails->count() == $huntUserDetails->where('revealed_at', null)->count() && $huntUserDetails->where('status', 'completed')->count() == 0) {
            $dataToBeUpdate['started_at'] = new UTCDateTime(now());
        }
        (new HuntUserRepository)->update($dataToBeUpdate, ['_id'=> $huntUserDetail->hunt_user_id], false);

        // reveal the clue
        $huntUserDetail->revealed_at = now();
        if (!$huntUserDetail->started_at) {
            $huntUserDetail->started_at = now();
        }
        $huntUserDetail->status = 'running';
        $huntUserDetail->save();
        
        //send the response
        return ['huntUserDetail'=> $huntUserDetail];
    }
}