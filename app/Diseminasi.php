<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Diseminasi extends Model{
    use SoftDeletes;

    protected $fillable = [
        'pustaha_id',
        'file_dissemination_ori',
        'file_dissemination',
        'file_iptek_ori',
        'file_iptek',
        'file_presentation_ori',
        'file_presentation',
        'file_poster_ori',
        'file_poster',
    ];

    public function pustaha()
    {
        return $this->belongsTo(Pustaha::class);
    }
}
