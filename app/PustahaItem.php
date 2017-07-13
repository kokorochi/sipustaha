<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PustahaItem extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'item_username',
        'item_name',
        'item_affiliation',
    ];

    public function pustaha()
    {
        return $this->belongsTo(Pustaha::class);
    }
}
