<?php

namespace App\Rules\MiniGame;

use Illuminate\Contracts\Validation\Rule;

class MiniGameTutorialCompletionRule implements Rule
{
    private $user;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
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
        if (collect($this->user->minigame_tutorials)->where('game_id', $value)->where('completed_at', '!=', null)->count()) {
            return false;
        }else{
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Tutorial of this minigame is already marked as complete.';
    }
}
