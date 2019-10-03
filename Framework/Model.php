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
    private $dataChanged = [];
    private $inDatabase = false;
    protected static $table;
    protected static $primary = 'id';
    private static $db;

    public function __set($name, $value)
    {
        if ($this->inDatabase)
            $this->dataChanged[$name] = $value;
        else
            $this->data[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->dataChanged))
            return $this->dataChanged[$name];
        else if (array_key_exists($name, $this->data))
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
    
    protected static function transform($data) // transform retrieved data to Model object
    {
        $model = new static();
        $model->inDatabase = true;
        $keys = array_keys($data);
        foreach ($keys as $key) {
            $model->data[$key] = $data[$key];
        }
        return $model;
    }

    /**
     * return the medoo database instance
     *
     * @return \Medoo\Medoo
     */
    public static function db() : \Medoo\Medoo
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
        $length = count($res);
        for ($i=0; $i < $length; $i++) 
            $res[$i] = static::transform($res[$i]);
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
        $length = count($res);
        for ($i=0; $i < $length; $i++) 
            $res[$i] = static::transform($res[$i]);
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
        $res = self::db()->select(static::$table, "*", [static::$primary => $i]);
        self::errorHandler(self::db());
        if (count($res) < 1)
            return null;
        else
            return static::transform($res);

    }

    /**
     * save the new record or update existed record
     *
     * @return void
     */
    public function save()
    {
        if ($this->inDatabase)
        {
            $where = [static::$primary => $this->data[static::$primary]]; // use old data
            self::db()->update(static::$table, $this->dataChanged, $where);
        }
        else
            self::db()->insert(static::$table, $this->data);
        self::errorHandler(self::db());
    }
}