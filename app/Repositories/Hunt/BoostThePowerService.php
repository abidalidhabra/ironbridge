<?php

namespace App\Repositories\Hunt;

class BoostThePowerService implements ClueInterface
{

    public function action($request)
    {
        // get the hunt user detail
        $huntUserDetail = (new HuntUserDetailRepository)->find($request->hunt_user_details_id);

        // update the hunt user
        (new HuntUserRepository)->update(['status'=> 'running'], ['_id'=> $huntUserDetail->hunt_user_id]);

        // if ($request->filled('walked')) {
        //     $huntUserDetail->walked = (float)$request->walked;
        // }
        // start the clue
        $huntUserDetail->location = ['type'=> 'Point', 'coordinates'=> [$request->longitude, $request->latitude]];
        $huntUserDetail->walked = (float)$request->walked;
        $huntUserDetail->started_at = now();
        $huntUserDetail->status = 'running';
        $huntUserDetail->save();

        //send the response
        return ['huntUserDetail'=> $huntUserDetail];
    }
}