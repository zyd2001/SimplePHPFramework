<?php

namespace Framework;

use Framework\Exceptions\DatabaseException;
use Symfony\Component\Dotenv\Dotenv;

/**
 * class Migration
 * every migration table should extend this class
 */
class Migration
{
    protected static $name;
    private static $db;

    public static function db()
    {
        if (!isset(self::$db))
        {
            $dotenv = new Dotenv(false);
            $dotenv->load("../.env");
            Initializer::setupDB(self::$db);
        }
        return self::$db;
    }

    public static function drop()
    {
        self::db()->drop(static::$name);
    }

    public static function table()
    {
        $table = new Table(static::$name);
        return $table;
    }

    public static function up() {}

    public static function down() 
    {
        static::drop(static::$name);
    }
}

class Table
{
    private $name;
    private $cols = [];
    private $primary;
    private $foreign = [];

    private function add($col) : Col
    {
        array_push($this->cols, $col);
        return $col;
    }

    public function int($name)
    {
        return $this->add(new Col('INTEGER', $name));
    }
    public function real($name)
    {
        return $this->add(new Col('REAL', $name));
    }
    public function date($name)
    {
        return $this->add(new Col('DATE', $name));
    }
    public function dateTime($name)
    {
        return $this->add(new Col('DATETIME', $name));
    }
    public function text($name)
    {
        return $this->add(new Col('TEXT', $name));
    }
    public function bool($name)
    {
        return $this->add(new Col('BOOLEAN', $name));
    }
    public function json($name)
    {
        return $this->add(new Col('JSON', $name));
    }

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function newRow($type, $name)
    {
        return $this->add(new Col($type, $name));
    }

    public function primary(string $cols)
    {
        $this->primary = $cols;
    }

    public function foreign(string $key, string $table, $fkey)
    {
        $f = new foreign($table, $fkey);
        $this->foreign[$key] = $f;
        return $f;
    }

    public function create()
    {
        $cols = [];
        foreach ($this->cols as $col)
        {
            $cols[$col->name] = $col->toArray();
            if ($col->foreign)
                $this->foreign[$col->name] = $col->foreign;
        }
        array_push($cols, "PRIMARY KEY ($this->primary)");
        foreach ($this->foreign as $key => $f)
            array_push($cols, "FOREIGN KEY ($key) " . $f->get());
        Migration::db()->create($this->name, $cols);
    }
}

class Col
{
    public $name;
    private $type;
    private $default = null;
    private $nullable = false;
    private $auto = false;
    private $unique = false;
    public $foreign = null;

    public function __construct($type, $name)
    {
        $this->type = $type;
        $this->name = $name;
    }

    public function nullable()
    {
        $this->nullable = true;
        return $this;
    }
    public function unique()
    {
        $this->unique = true;
        return $this;
    }
    public function default($val)
    {
        $this->default = $val;
        return $this;
    }
    public function increment()
    {
        $this->auto = true;
        return $this;
    }
    public function foreign(string $table, string $key)
    {
        $this->foreign = new foreign($table, $key);
        return $this->foreign;
    }
    
    public function toArray()
    {
        $arr = [];
        array_push($arr, $this->type);
        if ($this->default !== null)
            array_push($arr, 'DEFAULT ' . var_export($this->default, true));
        if (!$this->nullable)
            array_push($arr, 'NOT NULL');
        if ($this->auto)
            array_push($arr, 'AUTOINCREMENT');
        if ($this->unique)
            array_push($arr, 'UNIQUE');
        return $arr;
    }
}

class foreign
{
    private $table;
    private $key;
    private $del = '';
    private $update = '';

    public function __construct(string $table, string $key)
    {
        $this->table = $table;
        $this->key = $key;
    }

    public function get()
    {
        $t = $this->table;
        $key = $this->key;
        $option = '';
        if (strlen($this->del) != 0)
            $option .= ' ON DELETE ' . $this->del;
        if (strlen($this->update) != 0)
            $option .= ' ON UPDATE ' . $this->update;
        return "REFERENCES $t($key) $option";
    }

    function onUpdate(string $s)
    {
        $this->update = $s;
    }

    function onDelete(string $s)
    {
        $this->del = $s;
    }
}