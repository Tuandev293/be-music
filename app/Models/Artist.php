<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Artist extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
        
    const ARTIST_OUTSIDE_SYSTEM = 1;
    const DELETE_SONG_ALBUM = 1;

    public function songs()
    {
        return $this->hasMany(Song::class, 'artist_id');
    }
    public function albums()
    {
        return $this->hasMany(Album::class, 'artist_id');
    }
}
