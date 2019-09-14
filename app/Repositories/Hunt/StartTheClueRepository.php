<?php

namespace App\Repositories\Hunt;

use App\Models\v2\HuntUserDetail;
use App\Repositories\Hunt\Contracts\ClueInterface;
use App\Repositories\Hunt\HuntUserRepository;

class StartTheClueRepository implements ClueInterface
{
    
    public function action($huntUserDetailId)
    {
        // get the hunt user detail
        $huntUserDetail = (new HuntUserDetailRepository)->find($huntUserDetailId);

        // update the hunt user
        (new HuntUserRepository)->update(['status'=> 'running'], ['_id'=> $huntUserDetail->hunt_user_id], false);

        // start the clue
        $huntUserDetail->started_at = now();
        $huntUserDetail->status = 'running';
        $huntUserDetail->save();
       
        //send the response
        return ['huntUserDetail'=> $huntUserDetail];
    }
}