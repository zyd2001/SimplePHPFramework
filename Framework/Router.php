<?php

namespace Framework;

use FastRoute\BadRouteException;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Framework\Exceptions\BaseException;
use Framework\Exceptions\RouterException;
use Framework\Middlewares\CSRFVerify;

use function FastRoute\cachedDispatcher;
use function Opis\Closure\serialize;
use function Opis\Closure\unserialize;

class Router
{
    private static $routes = [];

    public static function dispatch(Request $req)
    {
        try 
        {
            $routes = self::$routes;
            $cacheEnabled = (!env('DEBUG', true)) && env('FASTROUTE_CACHE', true);
            $dispatcher = cachedDispatcher(function (RouteCollector $r) use ($routes, $cacheEnabled) {
            try {
                    foreach ($routes as $route)
                    { 
                        if ($cacheEnabled)
                            $r->addRoute($route[0], $route[1], serialize($route[2]));
                        else
                            $r->addRoute($route[0], $route[1], $route[2]);
                    }
                } catch (BadRouteException $e) {
                    throw new RouterException("BadRouteException: ". $e->getMessage(), 2);
                }
            }, ['cacheFile' => '../storage/cache/route/route.cache', 'cacheDisabled' => !$cacheEnabled]);

            $routeInfo = $dispatcher->dispatch($req->type, $req->path);
            switch ($routeInfo[0]) 
            {
                case Dispatcher::NOT_FOUND:
                    throw new RouterException($req->path . " Not Found", 0);
                    break;
                case Dispatcher::METHOD_NOT_ALLOWED:
                    throw new RouterException("Unsupported Request Method " . $req->type, 1);
                    break;
                case Dispatcher::FOUND: 
                {
                    if ($cacheEnabled)
                        $handler = unserialize($routeInfo[1]);
                    else
                        $handler = $routeInfo[1];
                 
                    $vars = $routeInfo[2];

                    ob_start(); // prevent output before Request::send
                    return $handler->call($vars);
                    break;
                }
            }
        } catch (BaseException $e) {
            if (!env('DEBUG', true))
                return $e->response();
            else
                throw $e;
        }
    }

    /**
     * add a new Route
     *
     * @param string $uri
     * @param callable $func the callback function
     * @param string $type
     * @return Route the new Route created
     */
    public static function addRoute(string $uri, callable $func, string $type = "GET") : Route
    {
        $route = new Route($func);
        $type = strtoupper($type);
        if ($type !== "GET")
            $route->middleware(CSRFVerify::class);
        array_push(self::$routes, [$type, $uri, $route]);
        return $route;
    }

    /**
     * add a GET route
     *
     * @param string $uri
     * @param callable $func
     * @return Route
     */
    public static function get(string $uri, callable $func) : Route
    {
        return self::addRoute($uri, $func, "GET");
    }

     /**
     * add a POST route
     *
     * @param string $uri
     * @param callable $func
     * @return Route
     */
    public static function post(string $uri, callable $func) : Route
    {
        return self::addRoute($uri, $func, "POST");
    }

     /**
     * add a PUT route
     *
     * @param string $uri
     * @param callable $func
     * @return Route
     */
    public static function put(string $uri, callable $func) : Route
    {
        return self::addRoute($uri, $func, "PUT");
    }

     /**
     * add a PATCH route
     *
     * @param string $uri
     * @param callable $func
     * @return Route
     */
    public static function patch(string $uri, callable $func) : Route
    {
        return self::addRoute($uri, $func, "PATCH");
    }

     /**
     * add a DELETE route
     *
     * @param string $uri
     * @param callable $func
     * @return Route
     */
    public static function delete(string $uri, callable $func) : Route
    {
        return self::addRoute($uri, $func, "DELETE");
    }

     /**
     * add a HEAD route
     *
     * @param string $uri
     * @param callable $func
     * @return Route
     */
    public static function head(string $uri, callable $func) : Route
    {
        return self::addRoute($uri, $func, "HEAD");
    }
}
