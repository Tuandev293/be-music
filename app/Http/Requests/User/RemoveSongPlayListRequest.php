<?php

namespace App\Http\Requests\User;

use App\Models\PlayList;
use App\Models\PlayListDetail;
use Illuminate\Foundation\Http\FormRequest;

class RemoveSongPlayListRequest extends FormRequest
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
            'playlist_id' => 'required|exists:playlists,id',
            'song_id' => ['required', function ($attribute, $value, $fail) {
                $checkExit = PlayListDetail::where('song_id', $value)->where('playlist_id', $this->playlist_id)->first();
                if(empty($checkExit)){
                    $fail(trans('validation.request.input_invalid'),);
                }
            }],
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
            'playlist_id.required' => trans('validation.request.input_required'),
            'playlist_id.exists' => trans('validation.request.input_invalid'),
        );
        return $messages;
    }
}
