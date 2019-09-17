<?php

namespace Framework;

use Framework\Exceptions\SessionException;

/**
 * class Session
 * wrapper of php native session
 */
class Session
{
    private static $current;
    private $data = [];
    private $flash = [];

    private function __construct() 
    {
        $_SESSION['session'] = $this;
    }

    /**
     * return the current Session instance or create one
     *
     * @return Session
     */
    public static function s()
    {
        if (!isset(self::$current))
        {
            session_start();
            self::$current = $_SESSION['session'] ?? new Session();
        }
        return self::$current;
    }

    /**
     * set session value at $i
     *
     * @param string $i
     * @param mixed $value
     * @return void
     */
    public function set(string $i, $value)
    {
        $this->data[$i] = $value;
    }

    /**
     * delete a session value at $i
     *
     * @param string $i
     * @return void
     */
    public function delete(string $i)
    {
        unset($this->data[$i]);
        unset($this->flash[$i]);
    }

    /**
     * get seesion value at $i
     * if value is flashed, it will be deleted from session
     *
     * @param string $i
     * @return mixed
     */
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

    /**
     * flash a session value at $i (cannot flash a value with key already exists in session)
     * or return the flash array and delete the array
     *
     * @param string $i
     * @param mixed $value
     * @return void|array
     */
    public function flash(string $i = null, $value = null)
    {
        if ($i === null)
        {
            $ret = $this->flash;
            $this->flash = [];
            return $ret;
        }
        if (array_key_exists($i, $this->data)) // prevent duplicated key in flash and data
            throw new SessionException('Flash a value with key exists in session');
        $this->flash[$i] = $value;
    }
}
