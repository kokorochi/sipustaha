<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pustaha extends Model
{
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
    ];

    public function pustahaItem()
    {
        return $this->hasMany(PustahaItem::class);
    }
}
