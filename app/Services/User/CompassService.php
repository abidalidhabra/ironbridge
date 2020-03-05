<?php

namespace App\Services\User;

// use App\Services\AssetsLogService;
use App\Services\Event\EventUserService;
use App\Services\Traits\UserTraits;
use Exception;

class CompassService
{
    use UserTraits;

    // protected $userRadius;
    public $userCompasses;
    public $added;
    public $event;
    public $eventUser;

    public function init()
    {
        $event = (new EventUserService)->setUser($this->user)->running(['*'], true);
        $this->event = $event;
        $this->eventUser = $event->participations->first();
    }

    public function add(int $size)
    {
        $this->init();

        $this->setUserCompasses(
            $this->eventUser->compasses
        );
        
        $this->userCompasses = [
            'remaining'=> $this->userCompasses['remaining'] + $size,
            'utilized'=> $this->userCompasses['utilized']
        ];
        
        $this->added = $size;
        $this->save();
        return $this;
    }

    public function deduct()
    {
        $this->init();

        $this->setUserCompasses(
            $this->eventUser->compasses
        );

        if ($this->userCompasses['remaining'] <= 0) {
            throw new Exception("You dont have enough compasses to reduce the circle");
        }
        
        $this->userCompasses = [
            'remaining'=> $this->userCompasses['remaining'] - 1,
            'utilized'=> $this->userCompasses['utilized'] + 1
        ];

        $this->eventUser->radius = $this->eventUser->radius - $this->event->deductable_radius;
        $this->save();

        return $this;
    }

    public function save()
    {
        $this->eventUser->compasses = $this->userCompasses;
        $this->eventUser->save();
        return $this;
    }

    public function setUserCompasses($userCompasses)
    {
        $this->userCompasses = $userCompasses;
        return $this;
    }

    public function response()
    {
        if ($this->added) {
            $this->userCompasses['added'] = $this->added;
        }
        return $this->userCompasses;
    }
}