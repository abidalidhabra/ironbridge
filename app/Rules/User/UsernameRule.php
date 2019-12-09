<?php

namespace App\Rules\User;

use App\Repositories\User\UserRepository;
use App\Services\User\Authentication\UsernameIdentifier;
use Illuminate\Contracts\Validation\Rule;

class UsernameRule implements Rule
{
    protected $message;

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
        $field = (new UsernameIdentifier)->setUsername($value)->init()->getUsernameField();
        if(!(new UserRepository)->getModel()->where($field, $value)->first(['id', $field])){
            $this->message = 'User with this '.$field.' does not exists.';
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
