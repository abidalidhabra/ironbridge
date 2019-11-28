<?php

namespace App\Http\Requests\v1;

use App\Rules\Hunt\HuntParticipationRule;
use App\Rules\v2\GoldAvailabilityForRelicRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ParticipateRequest extends FormRequest
{
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
            'random'=> "required_without_all:relic_id,hunt_mode",
            'relic_id'=> [
                "string", 
                "exists:relics,_id", 
                "required_without:random", 
                // new HuntParticipationRule($this->ownableUser()), 
                // new GoldAvailabilityForRelicRule($this->ownableUser())
            ],
            'complexity'=> "required_with:random|integer|between:1,5"
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['message' => $validator->messages()->first()], 422));
    }

    public function ownableUser()
    {
        return auth()->user();
    }
}
