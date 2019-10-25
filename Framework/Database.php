<?php

namespace Framework;

use Framework\Exceptions\DatabaseException;
use Medoo\Medoo;

class Database
{
    private $medoo;

    private static function errorHandler($db)
    {
        $err = $db->error() ?? ["00000"]; // prevent medoo return null error
        if ($err[0] !== "00000") // for pdostatement error
            throw new DatabaseException($err[2], 1);
        if ($db->pdo->errorCode() !== "00000") // for pdo error
            throw new DatabaseException($db->pdo->errorInfo()[2], 1);
    }

    public function __construct(Medoo $d)
    {
        $this->medoo = $d;
    }

    public function raw()
    {
        return $this->medoo;
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this->medoo, $name))
        {
            $res = call_user_func_array([$this->medoo, $name], $arguments);
            self::errorHandler($this->medoo);
            return $res;
        }
        else
            throw new DatabaseException("Function " . $name . " doesn't exist");
    }
}