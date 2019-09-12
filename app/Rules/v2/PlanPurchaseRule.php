<?php

namespace App\Rules\v2;

use App\Repositories\PlanRepository;
use Illuminate\Contracts\Validation\Rule;

class PlanPurchaseRule implements Rule
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
        $plan = (new PlanRepository)->findPlanById($value);
        if ($plan->skeleton_keys && $this->user->available_skeleton_keys >= $this->user->skeletons_bucket) {
            $this->message = "Sorry, you have exceeded your skeleton inventory space.";
            return false;
        }else if ($plan->gold_price && $plan->gold_price > $this->user->gold_balance) {
            $this->message = "You do not have enough gold to buy this plan.";
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
