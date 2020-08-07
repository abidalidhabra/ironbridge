<?php

namespace App\Http\Requests\User;

use App\Rules\EmailLoginRule;
use App\Rules\User\UsernameRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
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
            // 'username'=> ['required', new UsernameRule],
            // 'password'=> ['required', new CheckThePassword($this->username)],
            'type'=> 'required|in:google,facebook,apple,guest,email',
            'google_id'=> 'required_if:type,google',
            'facebook_id'=> 'required_if:type,facebook',
            'apple_id'=> 'required_if:type,apple',
            'name'=> 'required_unless:type,guest,apple',
            'email'=> 'required_unless:type,guest,apple',
            'apple_data'=> 'required_if:type,apple|json',
            'latitude'=> 'required',
            'longitude'=> 'required',
            'firebase_id'=> 'nullable',
            'device_type'=> 'required|in:ios,android',
            'device_id'=> 'required',
            'device_model'=> 'required',
            'device_os'=> 'required'
        ];
    }

    protected function getValidatorInstance() {
        $validator = parent::getValidatorInstance();

        $validator->sometimes('email', ['required', 'email', 'required_unless:type,guest,apple'], function($input){
            return $this->type == 'email';
        });

        $validator->sometimes('username', ['required'], function($input){
            return $this->type == 'email';
        });

        $validator->sometimes('password', ['required', new EmailLoginRule($this->username, $this->email)], function($input){
            return $this->type == 'email';
        });

        return $validator;
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
        throw new HttpResponseException(response()->json(['message' => $validator->messages()], 422));
    }

    // public function prepareForValidation()
    // {
    //     $inputs = $this->all();
    //     foreach ($inputs as $i => $input) {
    //         if ($i == 'email' || $i == 'username') {
    //             $inputs[$i] = strtolower($input);
    //         }
    //     }
    //     $this->replace($inputs);
    //     return $inputs;
    // }
}
