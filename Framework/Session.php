<?php

namespace Framework;

class Session
{
    private static $current = null;
    private $data = [];
    private $flash = [];

    private function __construct() 
    {
        $_SESSION['session'] = $this;
    }

    public static function s()
    {
        if (self::$current === null)
        {
            session_start();
            self::$current = $_SESSION['session'] ?? new Session();
        }
        return self::$current;
    }

    public function set(string $i, $value)
    {
        $this->data[$i] = $value;
    }

    public function delete(string $i)
    {
        unset($this->data[$i]);
    }

    public function get(string $i)
    {
        if (array_key_exists($i, $this->data))
        {
            return $this->data[$i];
        }
        else if (array_key_exists($i, $this->flash))
        {
            $temp = $this->flash[$i];
            unset($this->flash[$i]);
            return $temp;
        }
        else
            return null;
    }

    public function flash(string $i = null, $value = null)
    {
        if ($i === null)
        {
            $ret = $this->flash;
            $this->flash = [];
            return $ret;
        }
        $this->flash[$i] = $value;
    }
}
