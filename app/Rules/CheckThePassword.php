<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\v1\User;
use Hash;

class CheckThePassword implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $username;
    public function __construct($username = "")
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
        $user = User::where('username',$this->username)->select('password')->first();

        if (!$user) {
            return true;
        }

        $password = $user->password;
        if (Hash::check($value, $password)) {
            return true;
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
        return 'Provide the valid password';
    }
}
