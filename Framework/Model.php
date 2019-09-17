<?php

namespace Framework;

use Framework\Exceptions\DatabaseException;

/**
 * class Model
 * every model should extend this class
 */
class Model
{
    private $data = [];
    protected static $table;
    protected static $primary = 'id';
    private static $db;

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
        $err = $db->error();
        if ($err[0] !== "00000") // for pdostatement error
            throw new DatabaseException($err[2], 1);
        if ($db->pdo->errorCode() !== "00000") // for pdo error
            throw new DatabaseException($db->pdo->errorInfo()[2], 1);
    }

    private static function db()
    {
        if (!isset(self::$db))
            Initializer::setupDB(self::$db);
        return self::$db;
    }

    /**
     * return all data in the table
     * 
     * @return array
     */
    public static function all() : array
    {
        $res = self::db()->select(static::$table, "*");
        self::errorHandler(self::db());
        return $res;
    }

    /**
     * return data in the table match the query
     * 
     * @param array $q the query array
     * @return array
     */
    public static function where(array $q) : array
    {
        $res = self::db()->select(static::$table, "*", $q);
        self::errorHandler(self::db());
        return $res;
    }

    /**
     * find data in the table match the primary key
     * 
     * @param mixed $i the primary key
     * @return mixed
     */
    public static function find($i)
    {
        $res = self::db()->select(static::$table, "*", [static::$primary => $i])[0];
        self::errorHandler(self::db());
        return $res;
    }

    /**
     * save the new record
     *
     * @return void
     */
    public function save()
    {
        self::db()->insert(static::$table, $this->data);
        self::errorHandler(self::db());
    }
}