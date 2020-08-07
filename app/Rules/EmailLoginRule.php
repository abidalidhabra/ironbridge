<?php

namespace App\Rules;

use App\Repositories\User\UserRepository;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class EmailLoginRule implements Rule
{

    public $username;
    public $email;
    public $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($username, $email)
    {
        $this->username = $username;
        $this->email = $email;
        $this->message = 'You have provided wrong password.';
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
        $user = (new UserRepository)->getModel()
                ->where(['email'=> $this->email, 'username'=> $this->username])
                ->select('id', 'password')
                ->first();

        if (!$user) {
            $user = (new UserRepository)->getModel()
                ->orWhere(['email'=> $this->email, 'username'=> $this->username])
                ->select('id', 'password')
                ->first();
            if ($user) {
                // $this->message = 'Wrong password has provided.';
                return false;
            }else{
                return true;
            }
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
        return $this->message;
    }
}
