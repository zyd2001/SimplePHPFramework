<?php

namespace App\Middlewares;

use Framework\Middleware;
use Framework\Request;
use Framework\Response;

use function Framework\redirect;
use function Framework\session;

class Authentication extends Middleware
{
    public static function handle(Request $req, callable $next) : Response
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
