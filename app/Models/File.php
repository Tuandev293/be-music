<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{    const EXTENSION_IMAGE = 1;
    const EXTENSION_VIDEO = 2;
    const EXTENSION_RADIO = 3;
    const EXTENSION_FILE_OTHER = 4;
}
