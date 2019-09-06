<?php

namespace Framework\Middlewares;

use Framework\Exceptions\CSRFException;
use Framework\Request;

class CSRFVerify
{
    public static function handle(Request $req, callable $next)
    {
        if ($req->post('csrf_token') === session('csrf_token'))
            return $next($req);
        else
            throw new CSRFException("Invalid Request (no correct CSRF Token)", 1);
    }
}
