<?php

namespace App\Rules\User;

use App\Repositories\HuntStatisticRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class AddTheChestRule implements Rule
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
        $chest = $this->user->chests()->where('place_id', $value)->latest()->first();
        $huntStatistic = (new HuntStatisticRepository)->first(['_id', 'freeze_till']);
        if ($chest) {
            $freezeTime = $chest->created_at->addSeconds($huntStatistic->freeze_till['chest']);
            if ($chest->place_id == $value && ($freezeTime->gte(now()))) {
                return false;
            }else{
                return true;
            }
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
        return 'This chest is in cool down. Try again later..';
    }
}
