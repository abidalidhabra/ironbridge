<?php

namespace App\Rules\User;

use App\Models\v3\Event;
use Illuminate\Contracts\Validation\Rule;

class UpdateHomeCity implements Rule
{

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
        $event = Event::running()
                ->whereHas('participations', function($query){
                    $query->where('user_id', auth()->user()->id);
                })
                ->select('id', 'name')
                ->first();
        if ($event) {
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
        return 'You cannot change your home city until the running event not ends.';
    }
}
