<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pustaha extends Model {
    use SoftDeletes;

    protected $fillable = [
        'pustaha_type',
        'author',
        'title',
        'name',
        'city',
        'country',
        'publisher',
        'editor',
        'isbn_issn',
        'volume',
        'issue',
        'url_address',
        'pages',
        'pustaha_date',
        'propose_no',
        'creator_name',
        'creator_address',
        'creator_citizenship',
        'owner_name',
        'owner_address',
        'owner_citizenship',
        'creation_type',
        'announcement_date',
        'announcement_place',
        'protection_period',
        'registration_no',
        'file_name_ori',
        'file_name',
        'file_claim_request_ori',
        'file_claim_request',
        'file_claim_accomodation_ori',
        'file_claim_accomodation',
        'file_certification_ori',
        'file_certification',
        'approved_by_1',
        'approved_by_2',
        'amount_id',
    ];

    public function pustahaItem()
    {
        return $this->hasMany(PustahaItem::class);
    }

    public function amount()
    {
        return $this->belongsTo(Amount::class);
    }

    public function approval()
    {
        return $this->hasMany(Approval::class);
    }

    public function diseminasi()
    {
        return $this->hasMany(Diseminasi::class);
    }

}
