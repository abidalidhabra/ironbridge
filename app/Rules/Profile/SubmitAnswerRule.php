<?php

namespace App\Rules\Profile;

use App\Models\v3\UserQA;
use Illuminate\Contracts\Validation\Rule;

class SubmitAnswerRule implements Rule
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
        $status = UserQA::where(['user_id'=> $this->user->id, 'answers.'.$value=> null])->count();
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
        return 'You have already given answer to this question.';
    }
}
