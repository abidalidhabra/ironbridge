<?php

namespace App\Rules\User;

use App\Models\v3\Event;
use App\Services\Event\EventUserService;
use Illuminate\Contracts\Validation\Rule;

class UpdateHomeCity implements Rule
{

    public $message;
    public $user;
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
        $event = (new EventUserService)->setUser($this->user)->running(['*'], true);
        $participation = $event->participations->first();

        // if ($event && $event->city_id == $value) {
        //     $this->message = 'No need to update! You\'r home city is same as before';
        //     return false;
        // }else if ($event && ($participation->compasses['utilized'] > 0 || $participation->compasses['remaining'] > 0)) {
        //     $this->message = 'You cannot change your home city until the running event not ends.';
        //     return false;
        // }else{
        //     return true;
        // }
        if ($event) {
            $this->message = 'You cannot change your home city until the running event not ends.';
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
