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
        $itemId = $this->getRequest()->getQuery( "itemid", "" );
        if ( !$itemId ) {
            echo json_encode( array("errno" => -6001, "errmsg" => "请传递正确的商品ID") );
            return false;
        }

        /**
         * 检查是否登录
         */
        session_start();
        if ( !isset( $_SESSION['user_token_time'] ) || !isset( $_SESSION['user_token'] ) || !isset( $_SESSION['user_id'] ) || md5( "salt".$_SESSION['user_token_time'].$_SESSION['user_id'] ) != $_SESSION['user_token'] ) {
            echo json_encode( array("errno" => -6002, "errmsg" => "请先登录后操作") );
            return false;
        }

        // 调用Model，返回订单id
        $model = new WxpayModel();
        if ( $data = $model->createbill( $itemId, $_SESSION['user_id'] ) ) {
            echo json_encode( array(
                "errno" => 0,
                "errmsg" => "",
                "data" => $data,
            ) );
        } else {
            echo json_encode( array(
                "errno" => $model->errno,
                "errmsg" => $model->errmsg,
            ) );
        }
        return true;
    }

    public function qrcodeAction() {
        $billId = $this->getRequest()->getQuery( "billId", "" );
        if ( !$billId ) {
            echo json_encode( array("errno" => -6008, "errmsg" => "请传递正确的订单ID") );
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
            echo json_encode( array(
                "errno" => $model->errno,
                "errmsg" => $model->errmsg
            ) );
        }
        return true;
    }

    public function callbackAction() {
        $model = new WxpayModel();
        $model->callback();
        echo json_encode(array(
            "errno"=>0,
            "errmsg"=>''
        ));
        return true;
    }
}