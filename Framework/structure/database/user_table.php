<?php

use Framework\Migration;

require_once "../vendor/autoload.php";

class user_table extends Migration
{
    protected static $name = 'user';

    public static function up()
    {
        $table = self::table();
        $table->int('id')->increment();  // when use sqlite, do not use increment()
        $table->text('username');
        $table->text('email')->unique();
        $table->text('password_hash')->unique();
        $table->primary('id');
        $table->create();
    }
}