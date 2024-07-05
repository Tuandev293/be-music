<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserUpdateRequest extends FormRequest
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
        $today = Carbon::now()->addDay()->format('Y-m-d');
        $id = Auth::guard('api')->user()->id;
        $ruleAvatar = is_file($this->avatar)  ? 'nullable|file|max:10240|mimes:png,jpg,jpeg,gif,bmp,webp' : '';
        $ruleCoverImage = is_file($this->cover_image)  ? 'nullable|file|max:10240|mimes:png,jpg,jpeg,gif,bmp,webp' : '';
        $rules = [
            'birthday' => 'nullable|date_format:Y-m-d|before:' . $today,
            'cover_image' => $ruleCoverImage,
            'avatar' => $ruleAvatar,
            'gender' => 'required|in:' . User::GENDER_MALE . ',' . User::GENDER_FEMALE . ',' . User::GENDER_OTHER,
            'name' => 'required|min:3|max:150',
            'phone' => ['nullable', 'min:10', 'max:16', 'regex:/^(0|84|\+84|\+\(84\)|\(\+84\)|\(84\))\d{3}([ .-]?)(\d{3})\2(\d{3})$/', 'unique:users,id,'.$id],
        ];
        return $rules;
    }


    public function messages()
    {
        $messages = [
            'gender.required' => trans('validation.request.select_required', ['attribute' => trans('validation.attributes.gender')]),
            'gender.in' => trans('validation.request.input_regex', ['attribute' => trans('validation.attributes.gender')]),
            'name.required' => trans('validation.request.input_required', ['attribute' => trans('validation.attributes.name')]),
            'name.min' => trans('messages.request.input_min', ['attributes' => trans('validation.attributes.name'), 'min' => "6" ]),
            'name.max' => trans('messages.request.input_max', ['attributes' => trans('validation.attributes.name'), 'min' => "150" ]),
            'phone.min' => trans('validation.request.input_regex', ['attribute' => trans('validation.attributes.phone')]),
            'phone.max' => trans('validation.request.input_regex', ['attribute' => trans('validation.attributes.phone')]),
            'phone.regex' => trans('validation.request.input_regex', ['attribute' => trans('validation.attributes.phone')]),
            'phone.unique' => trans('validation.request.input_unique', ['attribute' => trans('validation.attributes.phone')]),
            'cover_image.required' => trans('messages.request.select_required', ['attribute' => 'Ảnh bìa']),
            'cover_image.mimes' => trans('messages.request.upload_mine', ['attribute' => 'Ảnh bìa', 'extension' => 'png,jpg,jpeg,gif,bmp,webp']),
            'cover_image.max' => trans('messages.request.upload_max', ['attribute' => 'Ảnh bìa', 'extension' => '10MB']),
            'avatar.required' => trans('messages.request.select_required', ['attribute' => 'Ảnh đại diện']),
            'avatar.mimes' => trans('messages.request.upload_mine', ['attribute' => 'Ảnh đại diện', 'extension' => 'png,jpg,jpeg,gif,bmp,webp']),
            'avatar.max' => trans('messages.request.upload_max', ['attribute' => 'Ảnh đại diện', 'extension' => '10MB']),
        ];
        return $messages;
    }
}
