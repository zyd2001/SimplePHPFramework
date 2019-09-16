<?php

namespace Framework;

class Middleware
{
    public static function handle(Request $req, callable $next) : Response
    {
        return $next($req);
    }
}