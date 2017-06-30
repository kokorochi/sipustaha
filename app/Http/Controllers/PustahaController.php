<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use parinpan\fanjwt\libs\JWTAuth;

class PustahaController extends MainController {
    public function __construct()
    {
        $this->middleware('is_auth')->except('index');
    }

    public function index()
    {
        if (env('APP_ENV') == 'local')
        {
            $user = new User();
            $user->username = env('LOGIN_USERNAME');
            Auth::login($user);

            return view('pustaha.pustaha-list');
        }
        $login = JWTAuth::communicate('https://akun.usu.ac.id/auth/listen', @$_COOKIE['ssotok'], function ($credential)
        {
            $loggedIn = $credential->logged_in;
            if ($loggedIn)
            {
                //kalau udah login
            } else
            {
                setcookie('ssotok', null, -1, '/');

                return false;
            }
        }
        );
        if (! $login->logged_in)
        {
            $login_link = JWTAuth::makeLink([
                'baseUrl'  => 'https://akun.usu.ac.id/auth/login',
                'callback' => url('/') . '/callback.php',
                'redir'    => url('/'),
            ]);

            return view('landing-page', compact('login_link'));
        } else
        {
            $user = new User();
            $user->username = $login->payload->identity;
            Auth::login($user);

            return view('pustaha.pustaha-list');
        }
    }
}
