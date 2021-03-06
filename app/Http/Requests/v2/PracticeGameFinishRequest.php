<?php

namespace App\Http\Requests\v2;

use App\Rules\v2\UserTitleOfMiniGame;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PracticeGameFinishRequest extends FormRequest
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
            'practice_game_user_id' => ['required', 'exists:practice_game_users,_id', new UserTitleOfMiniGame],
            'random_mode' => ['required', 'string', 'in:true,false'],
            'score' => ['required_with:time', 'numeric', 'integer', 'min:1'],
            'time' => ['required_with:score', 'numeric', 'integer', 'min:1'],
            'type' => ['required', 'in:completed,finished'],
            'increase_counter' => ['required', 'in:true,false'],
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
