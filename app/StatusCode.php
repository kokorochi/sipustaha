<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusCode extends Model
{
    protected $fillable = [
        'code',
        'code_description',
    ];
}
