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
    private $_db = null;

    public function __construct() {
        $this->_db =  new PDO('mysql:host=127.0.0.1;dbname=imooc;','root', '342623');
    }

    public function login($uname, $pwd){
        $query = $this->_db->prepare('select `pwd`,`id` from `user` WHERE `name`= ?');
        $query->execute(array($uname));
        $ret = $query->fetchAll();
        if (!$ret || count($ret)!=1) {
            $this->errno = -1003;
            $this->errmsg = '用户查找失败';
            return false;
        }
        $userInfo = $ret[0];
        if (Common_Password::pwdEncode($pwd) != $userInfo['pwd']){ // 校验密码
            $this->errno = -1004;
            $this->errmsg = '密码错误';
            return false;
        }
        return intval($userInfo[1]); // 返回int类型
    }

    public function register($uname, $pwd) {
        $query = $this->_db->prepare('select count(*) as c from `user` WHERE `name`= ? '); // 备要执行的SQL语句并返回一个 PDOStatement 对象
        $query->execute(array($uname)); // 执行一条预处理语句, 把$uname 赋值到 ?
        $count = $query->fetchAll(); // 返回一个包含结果集中所有行的数组
        if ($count[0]['c'] > 0) {
            $this->errno = -1005;
            $this->errmsg = '用户名已存在';
            return false;
        }

        if (strlen($pwd) < 8) {
            $this->errno = -1006;
            $this->errmsg = '密码太短，请设置至少8位的密码';
            return false;
        } else {
            $password = Common_Password::pwdEncode($pwd); // 生成32位的MD5加密字符串
        }
        $query = $this->_db->prepare('insert into `user` (`name`, `pwd`,`reg_time`) VALUES (?, ?, ?)');
        $ret = $query->execute(array($uname, $password, date('Y-m-d H:i:s')));
        if (!$ret){
            $this->errno = -1007;
            $this->errmsg = '注册失败，写入数据失败';
            return false;
        }

        return true;
    }
}