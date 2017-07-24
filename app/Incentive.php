<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incentive extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'description',
        'created_by',
        'updated_by',
    ];
}
