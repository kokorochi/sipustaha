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

    public function statusCode()
    {
        return $this->belongsTo(StatusCode::class, 'approval_status', 'code');
    }

    public function incentive()
    {
        return $this->belongsTo(Incentive::class, 'incentive_id', 'id');
     }
}
