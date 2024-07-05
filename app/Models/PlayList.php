<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayList extends Model
{
    use HasFactory;
    protected $table = 'playlists';
    const STATUS_ERROR = 1;
    protected $fillable = [
        'title',
        'user_id',
    ];

    public function playlistDetails()
    {
        return $this->hasMany(PlaylistDetail::class, 'playlist_id');
    }
}
