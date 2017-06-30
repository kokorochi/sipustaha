<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller {
    public function __construct()
    {
//        $this->middleware('isauth');
    }

    public function index()
    {
        $token = json_decode($response->getBody());
        
//        curl_setopt_array($curl, array(
//            CURLOPT_RETURNTRANSFER => TRUE,
//            CURLOPT_URL => 'https://akun.usu.ac.id/auth/login/apps',
//            CURLOPT_POST => 1,
//            CURLOPT_POSTFIELDS => array("identity" => "141402072994031003", "password" => "test", "random_char" => "TVWBJBSuwyewbwgcuw23657438zs",
//                                        CURLOPT_HEADER => TRUE
//            )));



//        curl_setopt_array($curl, array(
//            CURLOPT_RETURNTRANSFER => TRUE,
//            CURLOPT_URL => 'https://akun.usu.ac.id/auth/listen',
//            CURLOPT_POST => 1,
//            CURLOPT_POSTFIELDS => array("Authorization" => "Bearer token", "password" => "test", "random_char" => "TVWBJBSuwyewbwgcuw23657438zs",
//                                        CURLOPT_HEADER => TRUE
//            )));
//        return 'home page';
    }
}
