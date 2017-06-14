<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use parinpan\fanjwt\libs\JWTAuth;

class CallbackController extends Controller {
    public function callback()
    {
        return JWTAuth::recv([
            'ssotok'  => @$_GET['ssotok'],
            'secured' => true
        ]);
    }
}
