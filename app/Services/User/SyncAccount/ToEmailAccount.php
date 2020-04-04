<?php

namespace App\Services\User\SyncAccount;

use App\Models\v2\HuntUser;
use App\Repositories\User\UserRepository;

class ToEmailAccount
{
	
	public function sync($user, $targetUser)
    {

        (new UserRepository)->getModel()->where('_id', $user->id)->update([
            'first_name'=> "",
            'email'=> $targetUser->email,
            'username'=> $targetUser->username,
            'password'=> $targetUser->password,
            'last_login_as'=> 'email'
        ]);
        (new UserRepository)->getModel()->where('_id', $user->id)->unset('guest_id');


    	HuntUser::where('user_id', $targetUser->id)->get()->map(function($huntUser){
            $huntUser->hunt_user_details()->delete();
            $huntUser->delete();
        });
    	$targetUser->practice_games()->delete();
    	$targetUser->plans_purchases()->delete();
    	$targetUser->events()->delete();
    	$targetUser->user_relic_map_pieces()->delete();
    	$targetUser->chests()->delete();
    	$targetUser->answers()->delete();
    	$targetUser->assets()->delete();
    	$targetUser->reported_locations()->delete();
        $targetUser->relics_info()->delete();
        $targetUser->minigames_history()->delete();
        $targetUser->delete();
    }
}