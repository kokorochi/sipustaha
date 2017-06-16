<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller {
    public function __construct()
    {
        $this->middleware('isauth');
    }

    public function index()
    {
        return 'home page';
    }

    public function login()
    {
        Redirect::to('akun.usu.ac.id');
    }
}
