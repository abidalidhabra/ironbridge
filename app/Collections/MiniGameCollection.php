<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

class MiniGameCollection extends Collection
{
    /**
     * Mark all messages as read.
     *
     * @return void
     */
    public function markAsIncomplete()
    {
        $this->each(function ($userMiniGame) {
            $userMiniGame->markAsIncomplete();
        });
    }
}
