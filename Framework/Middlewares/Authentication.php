<?php

namespace Framework\Middlewares;

use Framework\Request;

class Authentication
{
    public static function handle(Request $req, callable $next)
    {
        if ($req->path !== '/register')
            if (session('logged_in') !== true)
            {
                session('before_login', $req->path);
                return redirect('/login');
            }
            else
                return $next($req);
        else
            if (session('logged_in') === true)
                return redirect('/');
            else
                return $next($req);
    }
}
