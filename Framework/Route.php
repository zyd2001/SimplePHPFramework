<?php

namespace Framework;

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

    public function middleware(...$middlewares)
    {
        foreach ($middlewares as $mid) {
            array_push($this->middleware, $mid);
            $this->count++;
        }
        return $this;
    }

    public function noCSRF()
    {
        unset($this->middleware[CSRFVerify::class]);
        $this->count--;
    }
}