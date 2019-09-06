<?php

namespace App\Rules\MiniGame;

use Illuminate\Contracts\Validation\Rule;

class UnlockMiniGameRule implements Rule
{
    private $user;
    private $message;

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
        if ($this->user->practice_games()->where('game_id', $value)->whereNotNull('unlocked_at')->count()) {
            $this->message = 'This minigame is already marked as unlock.';
            return false;
        }else if($this->user->available_skeleton_keys <= 0) {
            $this->message = 'You don\'t have enough skeleton keys to unlock minigame.';
            return false;
        }else if($this->user->available_skeleton_keys > 0) {
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
        return $this->message;
    }
}
