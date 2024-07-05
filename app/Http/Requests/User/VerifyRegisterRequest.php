<?php

namespace App\Http\Requests\User;

use App\Rules\CheckCodeRegister;
use Illuminate\Foundation\Http\FormRequest;

class VerifyRegisterRequest extends FormRequest
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
            'code' => ['required','max:6','min:6', new CheckCodeRegister($this->input('user_id'))],
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
            'code.required' => trans('messages.request.input_required', ['attribute' => "Mã xác thực"]),
            'code.max' => trans('messages.request.input_max', ['attribute' => "Mã xác thực", 'max' => "6" ]),
            'code.min' => trans('messages.request.input_min', ['attribute' => "Mã xác thực", 'min' => "6" ]),
        );
        return $messages;
    }
}

