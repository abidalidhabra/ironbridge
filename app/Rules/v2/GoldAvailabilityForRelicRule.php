<?php

namespace App\Rules\v2;

use App\Models\v2\Relic;
use Illuminate\Contracts\Validation\Rule;

class GoldAvailabilityForRelicRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $user;
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
        $relic = Relic::find($value);
        if ($relic && $this->user->gold_balance >= $relic->fees) {
            return true;
        }else{
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
        return 'You don\'t have enough gold to join this relic.';
    }
}
