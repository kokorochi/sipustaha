<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlowStatus extends Model
{
    protected $fillable = [
        'pustaha_id',
        'item',
        'status_code',
        'description',
        'created_by',
    ];

    public function pustaha()
    {
        return $this->belongsTo(Pustaha::class);
    }

    public function statusCode()
    {
        return $this->belongsTo(StatusCode::class, 'status_code', 'code');
    }
}
