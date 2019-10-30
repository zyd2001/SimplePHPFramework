<?php

namespace Framework\Middlewares;

use Framework\Exceptions\CSRFException;
use Framework\Middleware;
use Framework\Request;
use Framework\Response;

use function Framework\session;

class CSRFVerify extends Middleware
{
    public static function handle(Request $req, callable $next) : Response
    {
        if ($req->post('csrf_token') === session('csrf_token'))
            return $next($req);
        if ($req->header('x-csrf-token') === session('csrf_token'))
            return $next($req);
        throw new CSRFException("Invalid Request (no correct CSRF Token)", 1);
    }
}
