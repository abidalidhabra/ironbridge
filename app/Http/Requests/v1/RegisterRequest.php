<?php

namespace App\Http\Requests\v1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{

    public $validator;

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
            'first_name' => "required|string|max:20",
            'last_name'  => "required|string|max:20",
            'email'      => "required|string|email|unique:users,email",
            'password'   => "required|string|min:6",
            'username'   => "required|string|max:10||unique:users,username",
            'dob'        => "required|date_format:Y-m-d H:i:s",
            'longitude' => ['required','regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'], 
            'latitude'  => ['required','regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'device_type'  => "nullable|string",
            'firebase_id'  => "nullable|string",
            'reffered_by'  => "nullable|string|exists:users,reffered_id",
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
        return $this->validator = $validator;
    }

}
