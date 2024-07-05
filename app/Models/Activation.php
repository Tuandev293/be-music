<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Activation extends Model
{
    /**
     * @var string
     */
    protected $table = 'activations';

    public $timestamps = true;

    protected $guarded = [];
    
    const COMPLETED_FALSE = 0;
    const COMPLETED_TRUE = 1;

}
