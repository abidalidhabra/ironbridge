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
        $miniGameUserData = PracticeGameUser::where('_id', request()->practice_game_user_id)->first();
        
        if ($miniGameUserData && $miniGameUserData->user_id == auth()->user()->id && is_null($miniGameUserData->completed_at)) {
            return true;
        }

        if (!$miniGameUserData) {
            $this->message = 'Wrong practice game user id provided.';
        }

        if ($miniGameUserData->user_id != auth()->user()->id) {
            $this->message = 'You are not authorized to access this practice game user id.';
        }

        if (!is_null($miniGameUserData->completed_at)) {
            $this->message = 'This mini game is already completed.';
        }

        return false;
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
