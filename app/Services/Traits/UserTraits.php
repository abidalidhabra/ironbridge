<?php

namespace App\Services\Traits;

trait UserTraits
{

	protected $user;

    /**
     * @param mixed $user
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }    

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }
}
