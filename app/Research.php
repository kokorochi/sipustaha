<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Research extends Model
{
    protected $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client();
    }

    public function searchResearch($input)
    {
        $response = file_get_contents('https://simpel.usu.ac.id/researches/search?title=' . $input);
        $json = json_decode($response, true);

        return $json;
    }
}
