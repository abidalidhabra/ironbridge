<?php

namespace App\Rules\v2;

use Illuminate\Contracts\Validation\Rule;

class EventParticipationRule implements Rule
{
    private $eventUserMiniGameUniqueId;
    private $user;
    private $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($eventUserMiniGameUniqueId, $user)
    {
        $this->eventUserMiniGameUniqueId = $eventUserMiniGameUniqueId;
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
        
        $event = $this->user->events()
                    ->whereHas('minigames', function($query) use ($value){ 
                        $query->where('_id', $value)->where('mini_games._id', $this->eventUserMiniGameUniqueId); 
                    })
                    ->with(['minigames'=> function($query) use ($value){ 
                        $query->where('_id', $value)->select('_id', 'from', 'to','events_user_id'); 
                    }])
                    ->first();

        if (!$event) {

            $this->message = 'You are not authorized to make action in this event.';
            return false;
        }else if($event->minigames->count() && $event->minigames[0]['status']){

            $this->message = 'Warning! Round is already closed.';
            return false;
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
