<?php

namespace App\Rules\User;

use App\Repositories\HuntStatisticRepository;
use Illuminate\Contracts\Validation\Rule;

class OpenTheChestRule implements Rule
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
        $skeletonKeysToSkip = (new HuntStatisticRepository)->first(['_id', 'chest'])->chest['skeleton_keys_to_skip'];
        if(filter_var($value, FILTER_VALIDATE_BOOLEAN) === true && $this->user->available_skeleton_keys < $skeletonKeysToSkip){
            $this->message = 'You don\'t have enough skeleton keys to skip the chest.';
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
        return $this->message;
    }
}
