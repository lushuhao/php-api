<?php
/**
 * Created by PhpStorm.
 * User: lu
 * Date: 2017/9/10
 * Time: 13:19
 */

class Common_Password{

    const  Salt = "PHP-API";
    static public function pwdEncode( $pwd ) {
        return md5(self::Salt.$pwd);
    }
}