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

$pushLibPath = dirname( __FILE__ ).'/../library/ThirdParty/Getui/';
require_once( $pushLibPath.'IGt.Push.php' );
require_once( $pushLibPath.'igetui/IGt.AppMessage.php' );
require_once( $pushLibPath.'igetui/IGt.APNPayload.php' );
require_once( $pushLibPath.'igetui/template/IGt.BaseTemplate.php' );
require_once( $pushLibPath.'IGt.Batch.php' );
require_once( $pushLibPath.'igetui/utils/AppConditions.php' );

define( 'APPKEY', 'xLTtz6cnim7tttxHSqCiX4' ); // 在个推开发者中心应用配置
define( 'APPID', 'AQBSKwdgm76C6aYiamSfX7' );
define( 'MASTERSECRET', 'VvE5fiJqw4A7xFavaByXi3' );
define( 'HOST', 'http://sdk.open.api.igexin.com/apiex.htm' ); // host是默认

class PushModel {
    public $errno = 0;
    public $errmsg = "";

    public function __construct() {

    }

    public function single( $cid, $msg = "测试内容" ) {
        $igt = new IGeTui( HOST, APPKEY, MASTERSECRET );

        $template = $this->_IGtTransmissionTemplateDemo( $msg );

        $message = new IGtSingleMessage(); // 初始化单个推送消息

        $message->set_isOffline( true ); // 是否离线
        $message->set_offlineExpireTime( 3600*12*1000 ); // 离线时间（ms）
        $message->set_data( $template ); // 设置推送消息类型
        $message->set_pushNetWorkType(0); // 设置是否根据wifi推送消息，1为wifi推送，0为不限制
        //接收方
        $target = new IGtTarget();
        $target->set_appId(APPID);
        $target->set_clientId($cid); // 设备id
        //$target->set_alias(Alias);

        try{
            $rep = $igt->pushMessageToSingle($message,$target);
        }catch (RequestException $e){ // 捕捉异常
            $requestId = $e->getRequestId();
            $rep = $igt->pushMessageToSingle($message, $target, $requestId);
            $this->errno = -7003;
            $this->errmsg = $rep['result'];
            return false;
        }
        return true;
    }

    public function toAll( $msg ) {
        $igt = new IGeTui( HOST, APPKEY, MASTERSECRET );

        $template = $this->_IGtTransmissionTemplateDemo( $msg );

        $message = new IGtAppMessage(); // 初始化单个推送消息

        $message->set_isOffline( true ); // 是否离线
        // $message->set_offlineExpireTime( 3600*12*1000 ); // 离线时间（ms）
        $message->set_data( $template ); // 设置推送消息类型

        //接收方
        $appIdList = array(APPID);
        $phoneTypeList = array('ANDROID'); //推送范围安卓
        //$provinceList=array('浙江','北京','上海');
        //$age=array("0000","0010");

        $cdt = new AppConditions();
        $cdt->addCondition2(AppConditions::PHONE_TYPE, $phoneTypeList);
        //$cdt->addCondition2(AppConditions::REGION,$provinceList);
        //$cdt->addCondition2(AppConditions::TAG,$tagList);
        //$cdt->addCondition2("age",$age);

        $message->set_appIdList($appIdList);
        $message->conditions = $cdt;

        $igt->pushMessageToApp($message); // 给所有安装app推送
        return true;
    }

    private function _IGtTransmissionTemplateDemo( $msg ) {
        $template = new IGtTransmissionTemplate(); // 初始化个推信息模板
        $template->set_appId( APPID ); // 应用appid
        $template->set_appkey( APPKEY ); // 应用appkey
        $template->set_transmissionType( 1 ); // 消息类型是否透明传输
        $template->set_transmissionContent( $msg ); // 透传消息内容打包封装

        // APN高级推送，苹果设备相关设置
        $apn = new IGtAPNPayload();
        $alertmsg = new DictionaryAlertMsg();
        $alertmsg->body = "body";
        $alertmsg->actionLocKey = "ActionLockey";
        $alertmsg->locKey = "LocKey";
        $alertmsg->locArgs = array("locargs");
        $alertmsg->launchImage = "lauchimage";
        //IOS8.2 支持
        $alertmsg->title = "Title";
        $alertmsg->titleLocKey = "TitleLocKey";
        $alertmsg->titleLocArgs = array("TitleLocArg");

        $apn->alertMsg = $alertmsg;
        $apn->badge = 7;
        $apn->sound = "";
        $apn->add_customMsg( "payload", "payload" );
        $apn->contentAvailable = 1;
        $apn->category = "ACTIONABLE";
        $template->set_apnInfo( $apn );

        return $template;  // 返回模板对象
    }
}