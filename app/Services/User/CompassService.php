<?php

namespace App\Services\User;

// use App\Services\AssetsLogService;
use App\Services\Traits\UserTraits;

class CompassService
{
    use UserTraits;

    protected $userCompasses;
    protected $added;

    public function add(int $size)
    {
        
        // (new AssetsLogService('compass', 'loot'))->setUser($this->user)->compasses($size)->save();

        $this->setUserCompasses(
            $this->user->compasses
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
        if ($this->added) {
            $this->userCompasses['added'] = $this->added;
        }
        return $this->userCompasses;
    }
}