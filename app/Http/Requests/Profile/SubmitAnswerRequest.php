<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\APIResponseTrait;
use App\Http\Requests\AuthUserAccesibilityTrait;
use App\Rules\Profile\SubmitAnswerRule;
use Illuminate\Foundation\Http\FormRequest;

class SubmitAnswerRequest extends FormRequest
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
            'tag' => [
                "required", 
                "string", 
                "in:robo_intro,avatar_i_dont_liked,avatar_i_cant_afford,avatar_i_dont_cares", 
                new SubmitAnswerRule($this->ownableUser())
            ]
        ];
    }
}
