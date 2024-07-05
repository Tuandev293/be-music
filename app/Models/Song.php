<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Song extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::created(function ($song) {
            $song->slug = $song->createSlug($song->title);
            $song->save();
        });
    }
    /** 
     * Write code on Method
     *
     * @return string
     */
    private function createSlug($title)
    {
        if (static::whereSlug($slug = Str::slug($title))->exists()) {
            $max = static::whereTitle($title)->latest('id')->skip(1)->value('slug');
            if (is_numeric($max[-1])) {
                return preg_replace_callback('/(\d+)$/', function ($mathces) {
                    return $mathces[1] + 1;
                }, $max);
            }

            return "{$slug}-soundtrack-2";
        }

        return $slug;
    }
    public function logListen()
    {
        return $this->hasMany(LogListen::class, 'song_id');
    }
}
