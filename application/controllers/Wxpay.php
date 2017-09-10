<?php
/**
 * Created by PhpStorm.
 * User: lu
 * @desc 微信支付功能封装
 * Date: 2017/9/7
 * Time: 21:34
 */

$qrcodeLibPath = dirname( __FILE__ ).'/../library/ThirdParty/Qrcode/';
include_once( $qrcodeLibPath.'Qrcode.php' );

class WxpayController extends Yaf_Controller_Abstract {
    public function indexAction() {

    }

    public function createbillAction() {
        $itemId = Common_Request::getRequest( 'itemid', '' );
        if ( !$itemId ) {
            echo Common_Request::response( -6001, '请传递正确的商品ID' );
            return false;
        }

        /**
         * 检查是否登录
         */
        session_start();
        if ( !isset( $_SESSION['user_token_time'] ) || !isset( $_SESSION['user_token'] ) || !isset( $_SESSION['user_id'] ) || md5( "salt".$_SESSION['user_token_time'].$_SESSION['user_id'] ) != $_SESSION['user_token'] ) {
            echo Common_Request::response( -6002, '请先登录后操作' );
            return false;
        }

        // 调用Model，返回订单id
        $model = new WxpayModel();
        if ( $data = $model->createbill( $itemId, $_SESSION['user_id'] ) ) {
            echo Common_Request::response( 0, '', $data );
        } else {
            echo Common_Request::response( $model->errno, $model->errmsg );
        }
        return true;
    }

    public function qrcodeAction() {
        $billId = Common_Request::getRequest( 'billId', '' );
        if ( !$billId ) {
            echo Common_Request::response( -6008, "请传递正确的订单ID" );
            return false;
        }

        // 调用Model
        $model = new WxpayModel();
        if ( $data = $model->qrcode( $billId ) ) {
            /**
             * 输出二维码
             */
            QRcode::png( $data );
        } else {
            echo Common_Request::response( $model->errno, $model->errmsg );
        }
        return true;
    }

    public function callbackAction() {
        $model = new WxpayModel();
        $model->callback();
        echo Common_Request::response( 0, "" );
        return true;
    }
}