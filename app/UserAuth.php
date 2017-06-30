<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAuth extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'username', 'auth_type', 'created_by', 'updated_by'
    ];
}
