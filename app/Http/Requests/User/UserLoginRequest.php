<?php

namespace App\Http\Requests\User;
use Illuminate\Foundation\Http\FormRequest;

class UserLoginRequest extends FormRequest
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
        $rules = [
            'email' => ['required', 'email','exists:users'],
            'password' => 'required|min:6',
        ];
        return $rules;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        $messages = array(
            'email.email' => trans('validation.request.input_invalid', ['attribute' => trans('validation.attributes.email')]),
            'email.exists' => trans('validation.request.input_invalid', ['attribute' => trans('validation.attributes.email')]),
            'email.required' => trans('validation.request.input_required', ['attribute' => trans('validation.attributes.email')]),
            'password.min' => trans('messages.request.input_min', ['attribute' => trans('validation.attributes.password'), 'min' => "6" ]),
            'password.required' => trans('messages.request.input_required', ['attributes' => trans('validation.attributes.password')]),
        );
        return $messages;
    }
}
