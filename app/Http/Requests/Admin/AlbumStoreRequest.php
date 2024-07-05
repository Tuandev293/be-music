<?php

namespace App\Http\Requests\Admin;

use App\Models\Artist;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AlbumStoreRequest extends FormRequest
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
            'title' => ['required', 'min:3','max:250'],
            'artist_id' => ['required', 'exists:artists,id',
            function ($attribute, $value, $fail) {
                if ($value == Artist::ARTIST_OUTSIDE_SYSTEM) {
                    $fail(trans('validation.request.input_invalid', ['attribute' => 'Id ca sĩ']));
                }
            }],
            'cover_image' => empty($this->album_id) ? 'required' : 'nullable'.'|max:10240|mimes:png,jpg,jpeg,gif,bmp,webp',
            'release_date' => 'required|date_format:"d-m-Y',
        ];
        if(!empty($this->album_id)){
            $rules['album_id'] = ['exists:albums,id'];
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
            'title.required' => trans('validation.request.input_required', ['attribute' => 'Tên bài hát']),
            'title.min' => trans('messages.request.input_min', ['attribute' => 'Tên bài hát', 'min' => "3" ]),
            'title.max' => trans('messages.request.input_max', ['attribute' => 'Tên bài hát', 'min' => "250" ]),
            'artist_id.exists' => trans('validation.request.input_invalid', ['attribute' => 'Id ca sĩ']),
            'artist_id.required' => trans('validation.request.input_required', ['attribute' => 'Id ca sĩ']),
            'album_id.required' => trans('validation.request.input_required', ['attribute' => 'Id album']),
            'cover_image.required' => trans('messages.request.select_required', ['attribute' => 'Ảnh album']),
            'cover_image.mimes' => trans('messages.request.upload_mine', ['attribute' => 'Ảnh album', 'extension' => 'png,jpg,jpeg,gif,bmp,webp']),
            'cover_image.max' => trans('messages.request.upload_max', ['attribute' => 'Ảnh album', 'extension' => '10MB']),
            'release_date.date_format' => trans('messages.request.input_date_format', ['attribute' => 'Ngày phát hành', 'date_format' => 'ngày- tháng- năm']),
            'release_date.required' => trans('messages.request.select_required', ['attribute' => 'Ngày phát hành']),
            'release_date.after' => trans('messages.request.input_after', ['attribute' => 'Ngày phát hành', 'value' => 'hôm nay']),
        );
        return $messages;
    }
}
