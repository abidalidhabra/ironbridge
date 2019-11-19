<?php

namespace App\Rules\Hunt;

use App\Repositories\Hunt\HuntUserRepository;
use Illuminate\Contracts\Validation\Rule;

class HuntParticipationRule implements Rule
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
        $huntUser = (new HuntUserRepository)->getModel()->where(['user_id'=> $this->user->id, 'relic_id'=> $value])->count();
        return ($huntUser)? false: true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You are already participated in this hunt.';
    }
}
