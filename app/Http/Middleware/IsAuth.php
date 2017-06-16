<?php

namespace App\Http\Middleware;

use Closure;
use parinpan\fanjwt\libs\JWTAuth;

class IsAuth {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
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
            return 'test';
        } else
        {
            return $next($request);
        }
    }
}
