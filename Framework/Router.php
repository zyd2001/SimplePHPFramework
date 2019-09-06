<?php

namespace Framework;

use Framework\Exceptions\BaseException;
use Framework\Exceptions\RouterException;
use Framework\Middlewares\CSRFVerify;

class Router
{
    private static $routes = [];

    public static function addRoute(string $uri, callable $func, string $type = "get")
    {
        $route = new Route($func);
        $type = strtolower($type);
        if (!array_key_exists($uri, self::$routes))
        {
            self::$routes[$uri] = [$type => $route];
        }
        else
        {
            self::$routes[$uri][$type] = $route;
        }
        if ($type !== "get")
            $route->middleware(CSRFVerify::class);
        return $route;
    }

    public static function dispatch(Request $r)
    {
        try 
        {
            ob_start(); // prevent output before Request::send
            if (array_key_exists($r->path, self::$routes))
            {
                $route = self::$routes[$r->path];
                $type = $r->type;
                if (array_key_exists($type, $route))
                {
                    return $r->response($route[$type]);
                }
                else
                    throw new RouterException("Unsupported Request Method " . $type, 1);
            }
            else
                throw new RouterException($r->path . " Not Found", 0);     
        } 
        catch (BaseException $e) 
        {
            if (!$_ENV["DEBUG"])
                return $e->response();
            else
                throw $e;
        }
    }
}
