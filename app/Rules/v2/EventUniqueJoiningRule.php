<?php

namespace App\Rules\v2;

use Illuminate\Contracts\Validation\Rule;

class EventUniqueJoiningRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $user;
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
        if ($this->user->events()->where('_id', $value)->count()) {
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
        return 'You are already participated in this event.';
    }
}
