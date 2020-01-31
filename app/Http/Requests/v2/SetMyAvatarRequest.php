<?php

namespace App\Http\Requests\v2;

use App\Http\Requests\APIResponseTrait;
use App\Http\Requests\AuthUserAccesibilityTrait;
use Illuminate\Foundation\Http\FormRequest;

class SetMyAvatarRequest extends FormRequest
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
            'avatar_id'   => "required|string|exists:avatars,_id",
            'eyes_color'  => "required|string",
            'hairs_color' => "required|string",
            'skin_color'  => "required|string",
            'widgets'     => "required|array",
            'widgets.*' => "required|string|exists:widget_items,_id",
            'hat_selected' => "required|string|in:true,false",
        ];
    }
}
