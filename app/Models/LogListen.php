<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogListen extends Model
{
    /**
     * @var string
     */
    protected $table = 'log_listen';
    protected $fillable = [
        'song_id',
        'ip',
        'date_listen',
    ];
}
