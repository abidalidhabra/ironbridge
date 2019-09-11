<?php

namespace App\Rules\v2;

use App\Models\v1\User;
use App\Repositories\Contracts\WidgetItemInterface;
use Illuminate\Contracts\Validation\Rule;

class UnlockWidgetItemRule implements Rule
{
    private $user;
    private $message;
    private $widgetItemInterface;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($user, WidgetItemInterface $widgetItemInterface)
    {
        $this->user = $user;
        $this->widgetItemInterface = $widgetItemInterface;
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
        $alreadyInAccount = User::where('_id', $this->user->id)
                            ->where('widgets.id', $value)
                            ->select('_id', 'widgets')
                            ->first();

        if ($alreadyInAccount) {
            $this->message = 'This widget is already purchased by you.';
            return false;
        }else {
            
            $widgetItem = $this->widgetItemInterface->find($value);

            if($widgetItem->gold_price > 0 && $this->user->gold_balance < $widgetItem->gold_price){
                $this->message = 'You do not have the required gold to purchase this item.';
                return false;
            }
        }
        return true;
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
