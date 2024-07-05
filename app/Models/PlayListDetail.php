<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayListDetail extends Model
{
    use HasFactory;
    protected $table = 'play_list_details';
    
    protected $fillable = [
        'playlist_id',
        'song_id',
    ];
    
    public function song()
    {
        return $this->belongsTo(Song::class, 'song_id');
    }
}
