<?php

namespace App\Rules\v2;

use Illuminate\Contracts\Validation\Rule;

class EventParticipationOwnershipRule implements Rule
{
    private $user;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        //
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
        // $ownable = $this->user()->events()->whereHas('minigames', function($query){
        //                 return $query->where('_id', $value);
        //             })->count();
        
        // if ($ownable) {
        //     return true;
        // }else{
        //     return false;
        // }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Sorry! you are not authorize to do anything in this event.';
    }
}
