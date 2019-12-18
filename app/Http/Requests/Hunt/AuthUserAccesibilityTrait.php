<?php

namespace App\Http\Requests\Hunt;


trait AuthUserAccesibilityTrait
{

    public function ownableUser()
    {
        return auth()->user();
    }
}
