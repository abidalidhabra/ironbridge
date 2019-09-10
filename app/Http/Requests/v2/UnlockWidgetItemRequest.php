<?php

namespace App\Http\Requests\v2;

use App\Repositories\WidgetItemRepository;
use App\Rules\v2\UnlockWidgetItemRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UnlockWidgetItemRequest extends FormRequest
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
            'widget_item_id'   => ["required", "string", "exists:widget_items,_id", new UnlockWidgetItemRule($this->ownableUser(), new WidgetItemRepository)],
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
