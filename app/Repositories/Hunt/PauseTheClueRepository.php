<?php

namespace App\Repositories\Hunt;

use App\Models\v2\HuntUserDetail;
use App\Repositories\Hunt\Contracts\ClueInterface;
use App\Repositories\Hunt\HuntUserDetailRepository;
use App\Repositories\Hunt\HuntUserRepository;

class PauseTheClueRepository implements ClueInterface
{
    
    public function action($huntUserDetailId)
    {

        // get the hunt user detail
        $huntUserDetail = (new HuntUserDetailRepository)->find($huntUserDetailId);

        // update the hunt user
        (new HuntUserRepository)->update(['status'=> 'paused'], ['_id'=> $huntUserDetail->hunt_user_id], false);

        //reset the timer
        (new HuntUserDetailRepository)->calculateTheTimer($huntUserDetail, 'paused');

        //send the response
        return ['huntUserDetail'=> $huntUserDetail];
    }
}