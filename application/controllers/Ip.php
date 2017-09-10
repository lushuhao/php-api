<?php
/**
 * Created by PhpStorm.
 * User: lu
 * @desc IP地址归属地查询功能
 * Date: 2017/9/3
 * Time: 14:10
 */

class IpController extends Yaf_Controller_Abstract{
    public function indexAction(  ) {

    }

    public function getAction(  ) {
        $ip = Common_Request::getRequest( 'ip', '' );
        if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP)) {
            echo Common_Request::response( -5001, "请传递正确的IP地址" );
            return false;
        }

        // 调用Model，查询IP归属地
        $model = new IpModel();
        if ($data=$model->get(trim($ip))){
            echo Common_Request::response( 0, "", $data );
        } else {
            echo Common_Request::response( $model->errno, $model->errmsg );
        }
        return true;
    }
}