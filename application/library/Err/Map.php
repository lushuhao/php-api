<?php
/**
 * Created by PhpStorm.
 * User: lu
 * Date: 2017/10/8
 * Time: 15:50
 */

const ERRMAP = array(
    1000=> 'Exception error',
    1001 => '请通过正确渠道提交',
    1002 => '用户名与密码必须传递',
    1003 => '用户查找失败',
    1004 => '密码错误',
    1005 => '用户名已存在',
    1006 => '密码太短，请设置至少8位的密码',
    /**
     * 2*** 3*** ...
     */
);

class Err_Map {
    public static function get( $code ) {
        $errmsg = ERRMAP[$code] ? ERRMAP[$code] : 'undefined this error number.';
        return array( 0 - $code , $errmsg);
    }
}