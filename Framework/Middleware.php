<?php

namespace Framework;

/**
 * class Middleware
 * Every Middleware should extend this class
 */
class Middleware
{
    public static function handle(Request $req, callable $next) : Response
    {
        return $next($req);
    }
}