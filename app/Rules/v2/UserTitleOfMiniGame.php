<?php

namespace App\Rules\v2;

use App\Models\v2\PracticeGameUser;
use Illuminate\Contracts\Validation\Rule;

class UserTitleOfMiniGame implements Rule
{
    public $message;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $userId = auth()->user()->id;
        $miniGameUserData = PracticeGameUser::where('_id', request()->practice_game_user_id)->first();
        if ($miniGameUserData) {
            $coolDown = ($miniGameUserData->completed_at && $miniGameUserData->completed_at->diffInHours() < 24)?true:false;

            if ($miniGameUserData && $miniGameUserData->user_id == $userId && !$coolDown) {
                return true;
            }

            if (!$miniGameUserData) {
                $this->message = 'Wrong practice game user id provided.';
            }else if ($miniGameUserData->user_id != $userId){
                $this->message = 'You are not authorized to access this practice game user id.';
            }else if ($miniGameUserData->piece_collected === true){
                $this->message = 'This mini game is already completed, try different game.';
            }else if ($coolDown){
                $this->message = 'This mini game is under the freeze mode.';
            }
            return false;
        }
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
