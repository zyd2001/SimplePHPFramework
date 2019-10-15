<?php

namespace Framework;

use Framework\Exceptions\RouteException;
use Framework\Exceptions\RouterException;
use Framework\Middlewares\CSRFVerify;

class Route
{
    private $middleware = [];
    private $handler;
    private $index;
    private $count = 0;

    public function call($vars) : Response
    {
        $this->index = 0;
        $keys = array_keys($this->middleware);
        $func = null; // get rid of intelephense warning
        $func = function ($req) use (&$func, $vars, $keys) { // use recursion to process middleware
            if ($this->index < $this->count) 
            {
                $this->index++;
                $temp = call_user_func($keys[$this->index - 1] . "::handle", $req, $func);
                return $temp;
            } 
            else
                return call_user_func($this->handler, $req, $vars);
        };
        $res = $func(Request::req());
        if ($res instanceof Response)
            return $res;
        else
            throw new RouterException("Must return a Response", 1);
    }

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Add a middleware to the Route
     * Middleware type is any class that have static method 'handle'
     *
     * @param Middleware ...$middlewares
     * @return Route
     */
    public function middleware(...$middlewares) : Route
    {
        foreach ($middlewares as $mid) 
        {
            if (!is_subclass_of($mid, Middleware::class))
                throw new RouteException('Given middleware ' . $mid . " isn't a subclass of Framework\Middleware");
            $this->middleware[$mid] = 1;
            $this->count++;
        }
        return $this;
    }

    /**
     * get rid of CSRFVerify middleware
     *
     * @return void
     */
    public function noCSRF()
    {
        if (isset($this->middleware[CSRFVerify::class]))
        {
            unset($this->middleware[CSRFVerify::class]);
            $this->count--;
        }
    }
}