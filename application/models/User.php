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
    private $_dao = null;

    public function __construct() {
        $this->_dao = new Db_User();
    }

    public function login( $uname, $pwd ) {
        $userInfo = $this->_dao->find( $uname );
        if ( !$userInfo ) {
            $this->errno = $this->_dao->errno();
            $this->errmsg = $this->_dao->errmsg();
            return false;
        }
        if ( Common_Password::pwdEncode( $pwd ) != $userInfo['pwd'] ) { // 校验密码
            $this->errno = -1004;
            $this->errmsg = '密码错误';
            return false;
        }
        return intval( $userInfo[1] ); // 返回int类型
    }

    public function register( $uname, $pwd ) {
        if ( !$this->_dao->checkExists( $uname ) ) {
            $this->errno = $this->_dao->errno();
            $this->errmsg = $this->_dao->errmsg();
            return false;
        }

        if ( strlen( $pwd ) < 8 ) {
            $this->errno = -1006;
            $this->errmsg = '密码太短，请设置至少8位的密码';
            return false;
        } else {
            $password = Common_Password::pwdEncode( $pwd ); // 生成32位的MD5加密字符串
        }
        print_r($uname, $password);
        if ( !$this->_dao->addUser( $uname, $password, date( 'Y-m-d H:i:s' ) ) ) {
            $this->errno = $this->_dao->errno();
            $this->errmsg = $this->_dao->errmsg();
            return false;
        }

        return true;
    }
}