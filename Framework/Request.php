<?php

namespace Framework;

use Framework\Exceptions\RouterException;

class Request
{
    private $type;
    private $rawPost;
    private $path;
    private $post;
    private $get;
    private static $current = null;

    private function __construct()
    {
        $uri = strtolower($_SERVER["REQUEST_URI"]);
        $pos = strpos($uri, "?");
        if (!$pos)
            $this->path = $uri;
        else
            $this->path = substr($uri, 0, $pos);
        if ($this->path !== '/')
            $this->path = rtrim($this->path, "/");
        $this->type = strtolower($_SERVER["REQUEST_METHOD"]);
        $this->post = $_POST;
        $this->rawPost = file_get_contents("php://input");
        $this->get = $_GET;
        static::$current = $this;
    }

    public static function catch()
    {
        if (static::$current === null)
            return new Request();
        else
            return static::$current;
    }

    public static function req()
    {
        return static::$current;
    }
    
    public function __get($name)
    {
        if (isset($this->$name))
            return $this->$name;
    }

    public function get($name)
    {
        return $this->get[$name];
    }

    public function post($name)
    {
        return $this->post[$name];
    }

    public function response(Route $route) : Response
    {
        $route->index = 0;
        $func = null; // get rid of intelephense warning
        $func = function ($req) use(&$func, $route) { // use recursion to process middleware
            if ($route->index < $route->count)
            {
                $route->index++;
                $temp = call_user_func($route->middleware[$route->index - 1]."::handle", $req, $func);
                return $temp;
            }
            else
                return call_user_func($route->handler, $req);
        };
        $res = $func($this);
        if (is_a($res, Response::class))
            return $res;
        else
            throw new RouterException("Must return a Response", 1);
    }
}