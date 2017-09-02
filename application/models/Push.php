<?php
/**
 * Created by PhpStorm.
 * User: lu
 * @desc 推送服务接口
 * Date: 2017/9/2
 * Time: 23:14
 */

/**
 * 引入个推的lib
 */

$pushLibPath = dirname( __FILE__ ).'/../library/ThirdParty/Getui';
require_once( $pushLibPath.'IGt.Push.php' );
require_once( $pushLibPath.'igetui/IGt.AppMessage.php' );
require_once( $pushLibPath.'igetui/IGt.APNPayload.php' );
require_once( $pushLibPath.'igetui/template/IGt.BaseTemplate.php' );
require_once( $pushLibPath.'IGt.Batch.php' );
require_once( $pushLibPath.'igetui/utils/AppConditions.php' );

define('APPKEY','xLTtz6cnim7tttxHSqCiX4'); // 在个推开发者中心应用配置
define('APPID','AQBSKwdgm76C6aYiamSfX7');
define('MASTERSECRET','VvE5fiJqw4A7xFavaByXi3');
define('HOST','http://sdk.open.api.igexin.com/apiex.htm'); // host是默认

class PushModel {
    public $errno = 0;
    public $errmsg = "";

    public function __construct() {

    }

    public function single( $cid, $msg = "测试内容" ) {

    }

    public function toAll( $msg ) {

    }
}