<?php

namespace App\Services\User\SyncAccount;

use App\Models\v2\HuntUser;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use stdClass;

class ToEmailAccount
{

    protected $user;
    protected $targetUser;
    protected $request;
    protected $newRegistration;

    public function __construct($user, $request)
    {
        $this->user = $user;
        $this->request = $request;

        $this->getTargetUser();

        $this->validate();
    }

    public function getTargetUser()
    {
        $this->targetUser = (new UserRepository)->getModel()->where(['email'=> $this->request->email, 'username'=> $this->request->username])->first();
    }

    public function validate()
    {
        if ($this->user->last_login_as != 'guest') {
            throw ValidationException::withMessages(['password'=> "You are not signed in as guest."]);
        }

        if ($this->targetUser) {
            $this->newRegistration = false;
            if (!Hash::check($this->request->password, $this->targetUser->password)) {
                throw ValidationException::withMessages(['password'=> "You have provided wrong password."]);
            }
        }else{
            $this->newRegistration = true;
            $this->targetUser = new stdClass;
            $this->targetUser->email = $this->request->email;
            $this->targetUser->username = $this->request->username;
            $this->targetUser->password = $this->request->password;
        }
    }

    public function trashTargetUserData()
    {
        HuntUser::where('user_id', $this->targetUser->id)->get()->map(function($huntUser){
            $huntUser->hunt_user_details()->delete();
            $huntUser->delete();
        });
        $this->targetUser->practice_games()->delete();
        $this->targetUser->plans_purchases()->delete();
        $this->targetUser->events()->delete();
        $this->targetUser->user_relic_map_pieces()->delete();
        $this->targetUser->chests()->delete();
        $this->targetUser->answers()->delete();
        $this->targetUser->assets()->delete();
        $this->targetUser->reported_locations()->delete();
        $this->targetUser->relics_info()->delete();
        $this->targetUser->minigames_history()->delete();
        $this->targetUser->delete();
    }
	
	public function sync()
    {

        $this->user->first_name = "";
        $this->user->email = $this->targetUser->email;
        $this->user->username = $this->targetUser->username;
        $this->user->password = $this->targetUser->password;
        $this->user->last_login_as = 'email';
        $this->user->save();

        (new UserRepository)->getModel()->where('_id', $this->user->id)->unset('guest_id');

        if ($this->newRegistration == false) {
    	   $this->trashTargetUserData();
        }

        return $this->user->makeHidden(['reffered_by','updated_at','created_at', 'widgets', 'skeleton_keys', 'avatar', 'tutorials', 'additional', 'device_info', 'hat_selected', 'guest_id']);
    }
}