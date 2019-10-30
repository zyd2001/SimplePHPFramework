<?php

namespace Framework;

use Framework\Exceptions\RouterException;

/**
 * class Request
 * wrapper of php native request
 */
class Request
{
    private $type;
    private $rawPost;
    private $path;
    private $post;
    private $get;
    private static $current;

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
        $this->type = strtoupper($_SERVER["REQUEST_METHOD"]);
        $this->post = $_POST;
        $this->rawPost = file_get_contents("php://input");
        $this->get = $_GET;
    }

    /**
     * return the current Request or make a new Request
     *
     * @return Request
     */
    public static function catch() : Request
    {
        if (!isset(self::$current))
            self::$current = new Request();
        return self::$current;
    }

    /**
     * return the current Request
     *
     * @return Request
     */
    public static function req() : Request
    {
        return self::$current;
    }
    
    public function __get($name)
    {
        if (isset($this->$name))
            return $this->$name;
    }

    public function __isset($name)
    {
        return isset($this->$name);
    }

    /**
     * get request header defined by $name
     *
     * @param string $name
     * @return string|null
     */
    public function header(string $name)
    {
        $name = 'HTTP_' . str_replace('-', '_', strtoupper($name));
        if (array_key_exists($name, $_SERVER))
            return $_SERVER[$name];
        else
            return null;
    }

    /**
     * return the get argument with name $name
     *
     * @param string $name
     * @return mixed
     */
    public function get(string $name)
    {
        if (array_key_exists($name, $this->get))
            return $this->get[$name];
        else
            return null;
    }

    /**
     * return the post argument with name $name
     *
     * @param string $name
     * @return mixed
     */
    public function post(string $name)
    {
        if (array_key_exists($name, $this->post))
            return $this->post[$name];
        else
            return null;
    }

    /**
     * return decoded json data
     *
     * @return mixed
     */
    public function json()
    {
        return json_decode($this->rawPost);
    }
}