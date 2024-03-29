<?php

namespace Framework;

use Framework\Exceptions\ModelException;

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

    public function __isset($name)
    {
        if (array_key_exists($name, $this->dataChanged))
            return true;
        else if (array_key_exists($name, $this->data))
            return true;
        else
            return false;
    }
    
    private static function query($table, array $q)
    {
        $res = self::db()->select($table, "*", $q);
        return array_map(function ($v) {
            return static::transform($v);
        }, $res);
    }
    
    protected static function transform($data) // transform retrieved data to Model object
    {
        $model = new static();
        $model->inDatabase = true;
        $model->data = $data;
        return $model;
    }

    /**
     * return the medoo database instance
     *
     * @return \Medoo\Medoo
     */
    public static function raw() : \Medoo\Medoo
    {
        return self::db()->raw();
    }

    /**
     * return the database instance
     *
     * @return \Framework\Database
     */
    public static function db() : \Framework\Database
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
        return self::query(static::$table, []);
    }

    /**
     * return data in the table match the query
     * 
     * @param array $q the query array
     * @return array
     */
    public static function where(array $q) : array
    {
        return self::query(static::$table, $q);
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
        if (count($res) < 1)
            return null;
        else
            return static::transform($res[0]);
    }

    /**
     * Return $class that this model has
     *
     * @param Model $class
     * @param string $foreignKey foreign key in the table
     * @param string $table optional pivot table
     * @param string $otherKey the foreign key for $class in pivot table
     * @return array
     */
    public function has($class, string $foreignKey, $table = null, $otherKey = null) : array
    {
        if (is_subclass_of(!$class, Model::class))
            throw new ModelException("Must provide a Model", 1);
        $name = static::$primary;
        if ($table != null)
        {
            if ($otherKey == null)
                throw new ModelException("Must provide column name");
            $list = self::db()->select($table, $otherKey, [$foreignKey => $this->$name]);
            return array_map(function ($v) use($class) {
                return $class::find($v);
            }, $list);
        }
        return $class::where([$foreignKey => $this->$name]);
    }

    /**
     * return $class that this model belongs to
     *
     * @param Model $class
     * @param string $foreignKey foreign key in the table
     * @param string $table optional pivot table
     * @param string $otherKey the foreign key for $class in pivot table
     * @return array
     */
    public function belongsto($class, $foreignKey, $table = null, $otherKey = null) : array
    {
        if (is_subclass_of(!$class, Model::class))
            throw new ModelException("Must provide a Model", 1);
        $name = static::$primary;
        if ($table != null)
        {
            if ($otherKey == null)
                throw new ModelException("Must provide column name");
            $list = self::db()->select($table, $foreignKey, [$otherKey => $this->$name]);
            return array_map(function ($v) use($class) {
                return $class::find($v);
            }, $list);
        }
        return $class::where([$class::$primary => $this->$foreignKey]);
    }

    public function data()
    {
        return array_merge($this->data, $this->dataChanged);
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
            foreach ($this->dataChanged as $key => $value) 
                $this->data[$key] = $value;
            $this->dataChanged = [];
        }
        else
        {
            self::db()->insert(static::$table, $this->data);
            $id = self::db()->id();
            if (array_key_exists(static::$primary, $this->data))
                $id = $this->data[static::$primary]; // for non auto-increment primary key, it must be provided
            $data = self::db()->select(static::$table, "*", [static::$primary => $id])[0]; // update model
            $this->inDatabase = true;
            $this->data = $data;
            $this->dataChanged = [];
        }
        
    }

    /**
     * delete record
     *
     * @return void
     */
    public function delete()
    {
        self::db()->delete(static::$table, [static::$primary => $this->data[static::$primary]]);
    }

    /**
     * create multiple Models
     *
     * @param array $arr
     * @param boolean $return whether the function return created Models
     * @return array|void
     */
    public static function create(array $arr, bool $return = true)
    {
        $res = [];
        if ($return)
        {
            foreach ($arr as $data) 
            {
                self::db()->insert(static::$table, $data);
                $id = self::db()->id();
                if (array_key_exists(static::$primary, $data))
                    $id = $data[static::$primary]; // for non auto-increment primary key, it must be provided
                array_push($res, static::find($id));
            }
            return $res;
        }
        else
            self::db()->insert(static::$table, $arr);
    }
}