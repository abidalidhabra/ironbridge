<?php

namespace App\Rules\MiniGame;

use Illuminate\Contracts\Validation\Rule;

class MarkMiniGameAsFavouriteRule implements Rule
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
        if (!$this->user->practice_games()->where('_id', $value)->count()) {
            $this->message = 'You are not authorized to do this action.';
            return false;
        }else {
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
