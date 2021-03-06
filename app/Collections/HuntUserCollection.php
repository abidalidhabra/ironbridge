<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

class HuntUserCollection extends Collection
{
    /**
     * Mark all messages as read.
     *
     * @return void
     */
    public function getKMWalkedDistance()
    {
        $kmWalked = 0;
        $this->each(function ($hutUser) use (&$kmWalked) {
            $kmWalked += ($hutUser->hunt_user_details()->sum('walked') / 1000);
            return $hutUser;
        });
        return round($kmWalked, 2);
    }
}
