<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $fillable = [
        'pustaha_id',
        'item',
        'approval_status',
        'approval_annotation',
        'created_by',
    ];

    public function pustaha()
    {
        return $this->belongsTo(Pustaha::class);
    }
}
