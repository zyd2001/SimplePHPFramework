<?php

namespace App\Middlewares;

use Framework\Request;

class Authentication
{
    public static function handle(Request $req, callable $next)
    {
        if ($req->path !== '/signup' && $req->path !== '/signin')
            if (session('signed_in') !== true)
            {
                session('before_signin', $req->path);
                return redirect('/signin');
            }
            else
                return $next($req);
        else
            if (session('signed_in') === true)
                return redirect('/');
            else
                return $next($req);
    }
}
