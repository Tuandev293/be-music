<?php

namespace App\Http\Requests\Admin;

use App\Rules\CheckAlbumBelongArtist;
use Illuminate\Foundation\Http\FormRequest;

class SongStoreRequest extends FormRequest
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
        $isUpdate = empty($this->song_id) ? 'required' : 'nullable';
        $rules = [
            'title' => ['required', 'min:3','max:250'],
            'category_id' => ['required', 'exists:song_category,id'],
            'artist_id' => ['required', 'exists:artists,id'],
            'album_id' => ['nullable', new CheckAlbumBelongArtist($this->input('artist_id'))],
            'song' => $isUpdate.'|max:15000|mimes:mp3,wav,ogg',
            'thumbnail_image' => $isUpdate.'|max:10240|mimes:png,jpg,jpeg,gif,bmp,webp',
            'original_image' => $isUpdate.'|max:10240|mimes:png,jpg,jpeg,gif,bmp,webp'
        ];
        if(!empty($this->song_id)){
            $rules['song_id'] = ['exists:songs,id'];
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
            'category_id.exists' => trans('validation.request.input_invalid', ['attribute' => 'Thể loại nhạc']),
            'song_id.exists' => trans('validation.request.input_invalid', ['attribute' => 'Bài hát']),
            'artist_id.required' => trans('validation.request.input_required', ['attribute' => 'Id ca sĩ']),
            'category_id.required' => trans('validation.request.input_required', ['attribute' => 'Thể loại nhạc']),
            'song.required' => trans('messages.request.select_required', ['attribute' => 'Bài hát']),
            'song.mimes' => trans('messages.request.upload_mine', ['attribute' => 'Bài hát', 'extension' => 'mp3']),
            'song.max' => trans('messages.request.upload_max', ['attribute' => 'Bài hát', 'extension' => '15MB']),
            'thumbnail_image.required' => trans('messages.request.select_required', ['attribute' => 'Ảnh bài hát']),
            'thumbnail_image.mimes' => trans('messages.request.upload_mine', ['attribute' => 'Ảnh bài hát', 'extension' => 'png,jpg,jpeg,gif,bmp,webp']),
            'thumbnail_image.max' => trans('messages.request.upload_max', ['attribute' => 'Ảnh bài hát', 'extension' => '10MB']),
            'original_image.required' => trans('messages.request.select_required', ['attribute' => 'Ảnh chính']),
            'original_image.mimes' => trans('messages.request.upload_mine', ['attribute' => 'Ảnh chính', 'extension' => 'png,jpg,jpeg,gif,bmp,webp']),
            'original_image.max' => trans('messages.request.upload_max', ['attribute' => 'Ảnh chính', 'extension' => '10MB']),
        );
        return $messages;
    }
}
