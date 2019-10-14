<?php

namespace App\Http\Requests\MiniGame;

use App\Rules\v2\UserTitleOfMiniGame;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MarkMiniGameAsUnfinish extends FormRequest
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
            'type' => ['required', 'in:hunt,practice,event'],
            'hunt_user_details_id' => ["exists:hunt_user_details,_id",'required_if:type,hunt'],
            'practice_game_user_id' => ['exists:practice_game_users,_id', new UserTitleOfMiniGame, 'required_if:type,practice'],
            'random_mode' => ['required', 'string', 'in:true,false'],
            'status' => ['required', 'string', 'in:exited,failed'],
            'score' => ['required', 'numeric', 'integer', 'min:0'],
            'time' => ['required', 'numeric', 'integer', 'min:0'],
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
