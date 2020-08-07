<?php

namespace App\Http\Requests\User;

use App\Http\Requests\APIResponseTrait;
use App\Http\Requests\AuthUserAccesibilityTrait;
use App\Rules\EmailLoginRule;
use Illuminate\Foundation\Http\FormRequest;

class SyncAnAccountRequest extends FormRequest
{
    use AuthUserAccesibilityTrait, APIResponseTrait;

    public $user; 
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return ($this->user = $this->ownableUser())? true: false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'=> ['required_if:sync_to,string', 'email'],
            'username'=> ['required_if:sync_to,string'],
            'password'=> ['required_if:sync_to,string'],
            'sync_to'=> ['required', 'in:email']
        ];
    }
}
