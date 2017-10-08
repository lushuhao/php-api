<?php
/**
 * Created by PhpStorm.
 * User: lu
 * Date: 2017/9/10
 * Time: 19:50
 */

class Db_User extends Db_Base {
    public function find( $uname ) {
        $query = self::getDb()->prepare( 'select `pwd`,`id` from `user` WHERE `name`= ?' );
        $query->execute( array($uname) );
        $ret = $query->fetchAll();
        if ( !$ret || count( $ret ) != 1 ) {
            list(self::$errno, self::$errmsg) = Err_Map::get(1003);
            return false;
        }
        return $ret[0];
    }

    public function checkExists( $uname ) {
        $query = self::getDb()->prepare( 'select count(*) as c from `user` WHERE `name`= ?' );
        $query->execute( array($uname) );
        $count = $query->fetchAll();
        if ( $count[0]['c'] > 0 ) {
            list(self::$errno, self::$errmsg) = Err_Map::get(1005);
            return false;
        }
        return true;
    }

    public function addUser( $uname, $password, $datetime ) {
        print_r($uname, $password);
        $query = self::getDb()->prepare( 'insert into `user` (`name`, `pwd`,`reg_time`) VALUES (?, ?, ?)' );
        $ret = $query->execute( array($uname, $password, $datetime) );
        if ( !$ret ) {
            self::$errno = -1007;
            self::$errmsg = '注册失败，写入数据失败';
            return false;
        }
        return true;
    }
}