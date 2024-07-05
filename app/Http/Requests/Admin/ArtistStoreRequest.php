<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use App\Rules\CheckAlbumBelongArtist;
use Illuminate\Foundation\Http\FormRequest;

class ArtistStoreRequest extends FormRequest
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
            'name' => ['required', 'min:3','max:250'],
            'gender' => 'required|in:' . User::GENDER_MALE . ',' . User::GENDER_FEMALE,
            'avatar' => empty($this->artist_id) ? 'required' : 'nullable'.'|max:10240|mimes:png,jpg,jpeg,gif,bmp,webp',
        ];
        if(!empty($this->artist_id)){
            $rules['artist_id'] = ['exists:artists,id'];
        }
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
            'name.required' => trans('validation.request.input_required', ['attribute' => 'Tên ca sĩ']),
            'name.min' => trans('messages.request.input_min', ['attribute' => 'Tên ca sĩ', 'min' => "3" ]),
            'name.max' => trans('messages.request.input_max', ['attribute' => 'Tên ca sĩ', 'min' => "250" ]),
            'artist_id.exists' => trans('validation.request.input_invalid', ['attribute' => 'Ca sĩ']),
            'avatar.required' => trans('messages.request.select_required', ['attribute' => 'Ảnh ca sĩ']),
            'avatar.mimes' => trans('messages.request.upload_mine', ['attribute' => 'Ảnh chính', 'extension' => 'png,jpg,jpeg,gif,bmp,webp']),
            'avatar.max' => trans('messages.request.upload_max', ['attribute' => 'Ảnh chính', 'extension' => '10MB']),
            'gender.in' => trans('messages.request.input_regex', ['attribute' => trans('messages.attributes.gender')]),
            'gender.required' => trans('messages.request.input_required', ['attribute' => trans('messages.attributes.gender')]),
        );
        return $messages;
    }
}
