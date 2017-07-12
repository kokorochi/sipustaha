<?php

namespace App\Http\Controllers;

use App\Simsdm;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;

class UserController extends MainController {
    public function __construct()
    {
        $this->middleware('is_auth');
    }

    public function searchUser()
    {
        $input = Input::get();
        $simsdm = new Simsdm();
        $users = $simsdm->searchEmployee($input['query'], $input['limit']);

        $results = new Collection();
        foreach ($users->data as $user)
        {
            $result = new \stdClass();
            $result->username = $user->nip;
            $result->full_name = $user->full_name;
            $result->label = 'NIP: ' . $user->nip . ', NIDN: ' . $user->nidn . ', Nama: ' . $user->full_name;
            $results->push($result);
        }
        $results = json_encode($results, JSON_PRETTY_PRINT);

        return response($results, 200)->header('Content-Type', 'application/json');
    }
}
