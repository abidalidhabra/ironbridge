<?php

namespace App\Http\Requests\v1;

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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'random'=> "required_without_all:hunt_id,hunt_mode",
            'hunt_id'=> "string|exists:hunts,_id|required_without:random",
            'complexity'=> "required|integer|between:1,5",
            'hunt_mode'=>'string|in:challenge,normal|required_with:hunt_id'
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
}
