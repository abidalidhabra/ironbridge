<?php

namespace App\Services\User;

use App\Services\Traits\UserTraits;

class CompassService
{
    use UserTraits;

    protected $userCompasses;

    public function add(int $size)
    {
        $this->setUserCompasses(
            $this->user->compasses
        );
        $this->userCompasses = [
            'remaining'=> $this->userCompasses['remaining'] + $size,
            'utilized'=> $this->userCompasses['utilized']
        ];
        $this->save();
        return $this;
    }

    public function deduct()
    {
        $this->setUserCompasses(
            $this->user->compasses
        );
        $this->userCompasses = [
            'remaining'=> $this->userCompasses['remaining'],
            'utilized'=> $this->userCompasses['utilized'] - 1
        ];
        $this->save();
        return $this;
    }

    public function save()
    {
        $this->user->compasses = $this->userCompasses;
        $this->user->save();
        return $this;
    }

    public function setUserCompasses($userCompasses)
    {
        $this->userCompasses = $userCompasses;
        return $this;
    }

    public function response()
    {
        return $this->userCompasses;
    }
}