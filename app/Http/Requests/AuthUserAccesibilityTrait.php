<?php

namespace App\Http\Requests;


trait AuthUserAccesibilityTrait
{

    public function ownableUser()
    {
        return auth()->user();
    }
}
