<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SongFavorite extends Model
{
    use HasFactory;
    protected $table = 'song_favorites';

    protected $fillable = ['user_id', 'song_id'];
}
