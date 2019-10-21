<?php

namespace App\Repositories\Hunt;

use App\Models\v2\HuntUserDetail;
use App\Repositories\Hunt\Contracts\ClueInterface;
use App\Repositories\Hunt\HuntUserRepository;

class StartTheClueRepository implements ClueInterface
{

    public function action($request)
    {
        // get the hunt user detail
        $huntUserDetail = (new HuntUserDetailRepository)->find($request->hunt_user_details_id);

        // update the hunt user
        (new HuntUserRepository)->update(['status'=> 'running'], ['_id'=> $huntUserDetail->hunt_user_id]);

        // if ($request->filled('km_walked')) {
        //     $huntUserDetail->km_walked = (float)$request->km_walked;
        // }
        // start the clue
        $huntUserDetail->location = ['type'=> 'Point', 'coordinates'=> [$request->longitude, $request->latitude]];
        $huntUserDetail->km_walked = (float)$request->km_walked;
        $huntUserDetail->started_at = now();
        $huntUserDetail->status = 'running';
        $huntUserDetail->save();

        //send the response
        return ['huntUserDetail'=> $huntUserDetail];
    }
}