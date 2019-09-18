<?php

namespace Framework;

use FastRoute\BadRouteException;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Framework\Exceptions\BaseException;
use Framework\Exceptions\RouterException;
use Framework\Middlewares\CSRFVerify;

use function FastRoute\cachedDispatcher;

class Router
{
    private static $routes = [];

    public static function dispatch(Request $req)
    {
        try 
        {
            $routes = self::$routes;
            $dispatcher = cachedDispatcher(function (RouteCollector $r) use ($routes) {
                try {
                    foreach ($routes as $route) 
                        $r->addRoute($route[0], $route[1], $route[2]);
                } catch (BadRouteException $e) {
                    throw new RouterException("BadRouteException: ". $e->getMessage(), 2);
                }
            }, ['cacheFile' => '../Cache/route.cache', 'cacheDisabled' => $_ENV["DEBUG"]]);

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
                    $handler = $routeInfo[1];
                    $vars = $routeInfo[2];

                    ob_start(); // prevent output before Request::send
                    return self::call($handler, $vars);
                    break;
                }
            }
        } catch (BaseException $e) {
            if (!$_ENV["DEBUG"])
                return $e->response();
            else
                throw $e;
        }
    }

    private static function call($handler, $vars) : Response
    {
        $handler->index = 0;
        $func = null; // get rid of intelephense warning
        $func = function ($req) use (&$func, $handler, $vars) { // use recursion to process middleware
            if ($handler->index < $handler->count) 
            {
                $handler->index++;
                $temp = call_user_func($handler->middleware[$handler->index - 1] . "::handle", $req, $func);
                return $temp;
            } 
            else
                return call_user_func($handler->handler, $req, $vars);
        };
        $res = $func(Request::req());
        if ($res instanceof Response)
            return $res;
        else
            throw new RouterException("Must return a Response", 1);
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
