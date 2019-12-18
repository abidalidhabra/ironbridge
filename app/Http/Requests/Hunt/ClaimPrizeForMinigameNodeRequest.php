<?php

namespace App\Http\Requests\Hunt;

use App\Http\Requests\Hunt\APIResponseTrait;
use App\Http\Requests\Hunt\AuthUserAccesibilityTrait;
use App\Rules\Hunt\MGCFreezeRule;
use Illuminate\Foundation\Http\FormRequest;

class ClaimPrizeForMinigameNodeRequest extends FormRequest
{
    use AuthUserAccesibilityTrait, APIResponseTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return ($this->ownableUser())? true: false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'game_id'=> ['required', 'exists:games,_id', new MGCFreezeRule($this->ownableUser())]
        ];
    }
}
