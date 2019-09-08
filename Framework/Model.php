<?php

namespace Framework;

use Framework\Exceptions\DatabaseException;
use Medoo\Medoo;

class Model
{
    private $data = [];
    protected static $table;
    private static $db = null;

    private static function setupDB()
    {
        if ($_ENV["DB_TYPE"] === "sqlite")
            self::$db = new Medoo([
                'database_type' => $_ENV["DB_TYPE"],
                'database_file' => $_ENV["DB_PATH"],
            ]);
        else
            self::$db = new Medoo([
                'database_type' => $_ENV["DB_TYPE"],
                'database_name' => $_ENV["DB_NAME"],
                'server' => $_ENV["DB_SERVER"],
                'username' => $_ENV["DB_USERNAME"],
                'password' => $_ENV["DB_PASSWORD"],
                'port' => $_ENV["DB_PORT"]
            ]);
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->data))
            return $this->data[$name];
        else
            return null;
    }
    
    private static function errorHandler($db)
    {
        if ($db->pdo->errorCode() !== "00000")
            throw new DatabaseException($db->pdo->errorInfo()[2], 1); 
    }

    private static function db()
    {
        if (self::$db === null)
            self::setupDB();
        return self::$db;
    }

    public static function all()
    {
        $res = self::db()->select(static::$table, "*");
        self::errorHandler(self::db());
        return $res;
    }

    public static function where(array $q)
    {
        $res = self::db()->select(static::$table, "*", $q);
        self::errorHandler(self::db());
        return $res;
    }

    public static function find($i)
    {
        $res = self::db()->select(static::$table, "*", ["id" => $i]);
        self::errorHandler(self::db());
        return $res;
    }

    public function save()
    {
        self::db()->insert(static::$table, $this->data);
        self::errorHandler(self::db());
    }
}