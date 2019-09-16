<?php

namespace Framework;

use Framework\Exceptions\RouteException;
use Framework\Middlewares\CSRFVerify;

class Route
{
    public $middleware = [];
    public $handler;
    public $index;
    public $count = 0;

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
            array_push($this->middleware, $mid);
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
        unset($this->middleware[CSRFVerify::class]);
        $this->count--;
    }
}