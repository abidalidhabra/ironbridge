<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Auth;
use App\Models\v1\User;
use Hash;

class IsPasswordValid implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $user_id;
    public function __construct($user_id = "")
    {
        $this->user_id = $user_id;
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
        if ($this->user_id == "") {
            $password = Auth::user()->password;

        }else{
            $user = User::find($this->user_id);
            if ($user->isEmpty()) {
                return false;
            }
            $password = $user->password;
        }

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
        return 'Please provide valid current password first !';
    }
}
