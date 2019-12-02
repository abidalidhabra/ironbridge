<?php

namespace App\Http\Requests\v1;

use App\Rules\v1\CheckParticipationFromClue;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ActionOnClueRequest extends FormRequest
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
        $status = $this->status;
        return [
            'status' => ["required", "in:reveal,running,paused,completed"],
            'hunt_user_details_id' => ["required", "exists:hunt_user_details,_id", new CheckParticipationFromClue(auth()->user()->id, $status)],
            'latitude' => ["numeric", "required_if:status,running"],
            'longitude' => ["numeric", "required_if:status,running"],
            'walked' => ["numeric", "required_if:status,running"],
            'score' => ["numeric", "required_if:status,completed"],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'hunt_user_details_id.required' => 'Hunt\'s USER DETAIL ID is must be needed.',
            'hunt_user_details_id.exists'  => 'You have provided wrong hunt\'s USER DETAIL ID.',
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
