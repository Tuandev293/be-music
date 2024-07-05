<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'password' => 'min:6|required|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).*$/u',
            'email' => 'required|email|max:150|unique:users',
            'gender' => 'required|in:' . User::GENDER_MALE . ',' . User::GENDER_FEMALE . ',' . User::GENDER_OTHER,
            'name' => 'required|min:3|max:150',
            'phone' => ['nullable', 'min:10', 'max:16', 'regex:/^(0|84|\+84|\+\(84\)|\(\+84\)|\(84\))\d{3}([ .-]?)(\d{3})\2(\d{3})$/', 'unique:users'],
        ];
        return $rules;
    }


    public function messages()
    {
        $messages = [
            'password.required' => trans('validation.request.input_required', ['attribute' => trans('validation.attributes.password')]),
            'password.min' => trans('messages.request.input_min', ['attributes' => trans('validation.attributes.password'), 'min' => "6" ]),
            'password.regex' => trans('messages.request.input_regex', ['attribute' => trans('validation.attributes.password')]),
            'email.email' => trans('validation.request.input_invalid', ['attribute' => trans('validation.attributes.email')]),
            'email.required' => trans('validation.request.input_required', ['attribute' => trans('validation.attributes.email')]),
            'email.max' => trans('validation.request.input_max', ['attribute' => trans('validation.attributes.email'), 'max' => "150"]),
            'email.unique' => trans('validation.request.input_unique', ['attribute' => trans('validation.attributes.email')]),
            'gender.required' => trans('validation.request.select_required', ['attribute' => trans('validation.attributes.gender')]),
            'gender.in' => trans('validation.request.input_regex', ['attribute' => trans('validation.attributes.gender')]),
            'name.required' => trans('validation.request.input_required', ['attribute' => trans('validation.attributes.name')]),
            'name.min' => trans('messages.request.input_min', ['attributes' => trans('validation.attributes.name'), 'min' => "6" ]),
            'name.max' => trans('messages.request.input_max', ['attributes' => trans('validation.attributes.name'), 'min' => "150" ]),
            'phone.min' => trans('validation.request.input_regex', ['attribute' => trans('validation.attributes.phone')]),
            'phone.max' => trans('validation.request.input_regex', ['attribute' => trans('validation.attributes.phone')]),
            'phone.regex' => trans('validation.request.input_regex', ['attribute' => trans('validation.attributes.phone')]),
            'phone.unique' => trans('validation.request.input_unique', ['attribute' => trans('validation.attributes.phone')]),
        ];
        return $messages;
    }
}
