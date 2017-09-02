<?php
/**
 * Created by PhpStorm.
 * User: lu
 * @desc 短信操作Model类，使用sms.cn服务，帐号lushuhao33，密码342623
 * Date: 2017/9/2
 * Time: 19:39
 */

class SmsModel {
    public $errno = 0;
    public $errmsg = "";
    private $_db = null;

    public function __construct() {
        $this->_db = new PDO( "mysql:host=127.0.0.1;dbname=imooc;", "root", "342623" );
    }

    public function send( $uid,$templateId ) {
        $query = $this->_db->prepare( "select `mobile` from `user` WHERE `id`=?" );
        $query->execute( array(intval( $uid )) );
        $ret = $query->fetchAll();
        if ( !$ret || count( $ret ) != 1 ) {
            $this->errno = -4003;
            $this->errmsg = "用户手机信息查找失败";
            return false;
        }
        $userMobile = $ret[0]['mobile'];
        if ( !$userMobile || !is_numeric( $userMobile ) || strlen( $userMobile ) != 11 ) { // 检查手机号的格式
            $this->errno = -4004;
            $this->errmsg = "用户手机号信息不符合标准，手机号为: ".( !$userMobile ? '空' : $userMobile );
            return false;
        }

        /* 调用第三方服务，发送短信*/
        $smsUid = "lushuhao33";
        $smsPwd = "342623";
        $sms = new ThirdParty_Sms( $smsUid, $smsPwd ); // 目录要一致ThirdParty/Sms.php;

        $contentParam = array('code' => rand( 1000, 9999 )); // 四位随机验证码
        $result = $sms->send( $userMobile, $contentParam, $templateId );

        // 100 发送成功
        if ($result['stat']=='100'){
            return true;
        } else {
            $this->errno = -4005;
            $this->errmsg = '发送失败:'.$result['stat'].'('.$result['message'].')';
            return false;
        }

    }
}