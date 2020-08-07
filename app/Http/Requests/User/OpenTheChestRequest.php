<?php

namespace App\Http\Requests\User;

use App\Http\Requests\APIResponseTrait;
use App\Http\Requests\AuthUserAccesibilityTrait;
use App\Rules\User\OpenTheChestRule;
use Illuminate\Foundation\Http\FormRequest;

class OpenTheChestRequest extends FormRequest
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
            'skip'=> ['nullable', 'string', 'in:true,false', new OpenTheChestRule($this->user)]
        ];
    }
}
