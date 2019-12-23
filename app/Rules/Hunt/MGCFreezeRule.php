<?php

namespace App\Rules\Hunt;

use App\Repositories\HuntStatisticRepository;
use App\Repositories\Hunt\HuntUserRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class MGCFreezeRule implements Rule
{
    private $user;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($user)
    {
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
        $data = $this->user->mgc_status->where('game_id', $value)->first();
        $huntStatisticRepository = (new HuntStatisticRepository)->first(['id', 'freeze_till']);
        if ($data && $data['completed_at']) {
            return (Carbon::parse($data['completed_at'])->diffInSeconds() < $huntStatisticRepository->freeze_till['mgc'])? false: true;
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
        return 'This minigame challenge node is under freeze mode.';
    }
}
