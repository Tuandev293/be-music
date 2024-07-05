<?php

namespace App\Rules;

use App\Models\Album;
use App\Models\Artist;
use Illuminate\Contracts\Validation\Rule;

class CheckAlbumBelongArtist implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $artist_id;

    public function __construct($artist_id)
    {
        $this->artist_id = $artist_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if($this->artist_id == Artist::ARTIST_OUTSIDE_SYSTEM && !empty($value)) return false;
        $album = Album::where('id', $value)
                        ->where('artist_id', $this->artist_id)
                        ->first();
        return !empty($album);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('messages.request.input_in', ['attribute' => "Id album"]);
    }
}
