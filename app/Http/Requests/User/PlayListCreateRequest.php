<?php

namespace App\Http\Requests\User;
use Illuminate\Foundation\Http\FormRequest;

class PlayListCreateRequest extends FormRequest
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
            'song_id' => ['required', 'exists:songs,id'],
            'title' => 'required|min:6|max:255',
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
            'song_id.required' => trans('validation.request.input_required'),
            'song_id.exists' => trans('validation.request.input_invalid'),
            'title.required' => trans('validation.request.input_required'),
            'title.min' => trans('messages.request.input_min'),
            'title.max' => trans('messages.request.input_max'),
        );
        return $messages;
    }
}
