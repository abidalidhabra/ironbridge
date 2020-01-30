<?php

namespace App\Http\Requests\User;

use App\Http\Requests\APIResponseTrait;
use App\Http\Requests\AuthUserAccesibilityTrait;
use Illuminate\Foundation\Http\FormRequest;

class ChangeTheChestMGRequest extends FormRequest
{
    use AuthUserAccesibilityTrait, APIResponseTrait;

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
            'minigames_ids'=> 'required|array'
        ];
    }
}
