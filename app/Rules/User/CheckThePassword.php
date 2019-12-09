<?php

namespace App\Rules\User;

use App\Repositories\User\UserRepository;
use App\Services\User\Authentication\UsernameIdentifier;
use Hash;
use Illuminate\Contracts\Validation\Rule;

class CheckThePassword implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $username;
    public function __construct($username)
    {
        $this->username = $username;
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
        $user = (new UserRepository)->getModel()->where($field, $this->username)->select('id', 'password')->first();
        if (!$user) {
            return true;
        }else{
            if (Hash::check($value, $user->password)) {
                return true;
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
        return 'Provide the valid password';
    }
}
