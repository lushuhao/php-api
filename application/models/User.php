<?php
/**
 * @name UserModel
 * @desc 用户操作Model类
 * Created by PhpStorm.
 * User: lu
 * Date: 2017/8/20
 * Time: 18:04
 */
class UserModel {
    public $errno = 0;
    public $errmsg = '';

    public function __construct() {
    }

    public function register($uname, $pwd) {
        return true;
    }
}