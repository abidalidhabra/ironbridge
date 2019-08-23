<?php

namespace App\Http\Requests\v2;

use App\Rules\v2\EventMinigameRule;
use App\Rules\v2\EventParticipationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MarkTheEventMGAsCompleteRequest extends FormRequest
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
            'events_minigame_id' => ['required', 'exists:events_minigames,_id', new EventParticipationRule($this->minigame_unique_id, $this->ownableUser())],
            'minigame_unique_id' => ['required'],
            'completion_score' => ['nullable', 'numeric', 'integer', 'min:1'],
            'completion_time' => ['nullable', 'numeric', 'integer', 'min:1'],
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

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required_without' => 'You have to either provide completion_score or completion_time',
        ];
    }

    public function ownableUser()
    {
        return auth()->user();
    }
}
