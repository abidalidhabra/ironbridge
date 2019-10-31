<?php

namespace App\Rules\Profile;

use App\Repositories\User\UserRepository;
use Illuminate\Contracts\Validation\Rule;

class MarkTutorialAsCompleteRule implements Rule
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
        $status = (new UserRepository($this->user))->getModel()->where(['_id'=> $this->user->id, 'tutorials.'.$value=> null])->first();
        if ($status) {
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
        return 'This module might already marked as complete.';
    }
}
