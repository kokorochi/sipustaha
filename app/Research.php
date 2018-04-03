<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Research
{
    protected $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client();
    }

    public function searchResearchTitle($input)
    {
        $response = $this->client->get('https://simpel.usu.ac.id/researches/search?title=' . $input);
        $json = json_decode($response->getBody());

        return $json;
    }

    public function searchResearchTitleOwn($input, $identifier)
    {
        $response = $this->client->get('https://simpel.usu.ac.id/researches/search?title=' . $input .'&identifier='.$identifier);
        $json = json_decode($response->getBody());

        return $json;
    }

    public function getResearchById($input)
    {
        $response = $this->client->get('https://simpel.usu.ac.id/researches/search?id=' . $input);
        $json = json_decode($response->getBody());

        return $json->data[0];
    }

    public function getResearches($input){
        $response = $this->client->get('https://simpel.usu.ac.id/researches/search?identifier='.$input);
        $json = json_decode($response->getBody());

        return $json->data;
    }
}
