<?php
/**
 * Created by PhpStorm.
 * User: lu
 * Date: 2017/9/10
 * Time: 19:46
 */

class Db_Base {
    public static $errno = 0;
    public static $errmsg = "";
    private static $db = null;

    public static function getDb() {
        if ( !self::$db ) {
            self::$db = new PDO( 'mysql:host=127.0.0.1;dbname=imooc;', 'root', '342623' );
        }
        return self::$db;
    }

    public function errno(){
        return self::$errno;
    }

    public function errmsg(){
        return self::$errmsg;
    }
}