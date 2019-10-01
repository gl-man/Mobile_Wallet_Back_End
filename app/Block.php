<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $fillable = [
        'height', 'blocks'
    ];
}
