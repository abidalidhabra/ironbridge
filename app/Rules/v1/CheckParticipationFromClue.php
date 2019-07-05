<?php

namespace App\Rules\v1;

use App\Models\v2\HuntUserDetail;
use Illuminate\Contracts\Validation\Rule;

class CheckParticipationFromClue implements Rule
{

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $userId, $status, $message;
    public function __construct($userId, $status)
    {
        $this->userId = $userId;
        $this->status = $status;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $huntUserDetail = HuntUserDetail::where('_id',$value)
                            ->whereHas('hunt_user', function($query){
                                $query->where('user_id', $this->userId);
                            })
                            ->select('_id','user_id','hunt_user_id','status', 'revealed_at')
                            ->first();

        if (!$huntUserDetail) {
            
            $this->message = 'You are not participated in the hunt, you requested.';
            return false;
        }else if($this->status == 'reveal' && $huntUserDetail->revealed_at != null){
            
            $this->message = 'You cannot reveal this clue, as it already revealed.';
            return false;
        }else if($huntUserDetail->status == $this->status){

            if($huntUserDetail->status == 'running'){
                $this->message = 'You cannot start this clue, as it already started.';
            }else if($huntUserDetail->status == 'paused'){
                $this->message = 'You cannot pause this clue, as it already paused.';
            }else if($huntUserDetail->status == 'completed'){
                $this->message = 'You cannot complete this clue, as it already completed.';
            }
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
