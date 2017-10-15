<?php
/**
 * Created by PhpStorm.
 * User: lu
 * @desc 微信支付功能封装
 * Date: 2017/9/7
 * Time: 21:56
 */

$wxpayLibPath = dirname( __FILE__ ).'/../library/ThirdParty/Wxpay/';
include_once( $wxpayLibPath.'WxPay.Api.php' );
include_once( $wxpayLibPath.'WxPay.Notify.php' );
include_once( $wxpayLibPath.'WxPay.NativePay.php' );
include_once( $wxpayLibPath.'WxPay.Data.php' );

class WxpayModel extends WxPayNotify {
    public $errno = 0;
    public $errmsg = "";
    private $_db;

    public function __construct() {
        $this->_db = new PDO( "mysql:host=127.0.0.1;dbname=imooc;", "root", "342623" );
    }

    public function createbill( $itemId, $uid ) {
        $query = $this->_db->prepare( "select * from `item` WHERE  `id`=? " );
        $query->execute( array($itemId) );
        $ret = $query->fetchAll();
        if ( !$ret || count( $ret ) != 1 ) {
            $this->errno = -6003;
            $this->errmsg = "找不到这件商品";
            return false;
        }
        $item = $ret[0];
        if ( strtotime( $item['etime'] ) <= time() ) {
            $this->errno = -6004;
            $this->errmsg = "商品已过期，不能购买";
            return false;
        }
        if ( intval( $item['stock'] ) <= 0 ) {
            $this->errno = -6005;
            $this->errmsg = "商品库存不够，不能购买";
            return false;
        }

        /**
         * 创建bill
         */

        try {
            $this->_db->beginTransaction(); // 开启一个事务

            $query = $this->_db->prepare( "insert into `bill` (`itemid`, `uid`,`price`,`status`) VALUES (?,?,?,'unpaid')" );
            $ret = $query->execute( array($itemId, $uid, intval( $item['price'] )) );
            if ( !$ret ) {
                $this->errno = -6006;
                $this->errmsg = "创建账单失败";
                // return false;
                throw new PDOException("创建账单失败");
            }
            $lastId = $this->_db->lastInsertId();

            // 库存 -1
            $query = $this->_db->prepare( "update `item` set `stock`=`stock`-1 WHERE `id` = ?" );
            $ret = $query->execute( array($itemId) );
            if ( !$ret ) {
                $this->errno = -6007;
                $this->errmsg = "更新库存失败";
                // return false;
                throw new PDOException("更新库存失败");
            }
            $this->_db->commit();
        } catch (PDOException $e){
            // $e->getMessage();
            $this->_db->rollBack();// 回滚，两条sql语句全部重新执行
        }
        return $lastId;
    }

    public function qrcode( $billId ) {
        $query = $this->_db->prepare( "select * from `bill` WHERE  `id`=? " );
        $query->execute( array($billId) );
        $ret = $query->fetchAll();
        if ( !$ret || count( $ret ) != 1 ) {
            $this->errno = -6009;
            $this->errmsg = "找不到账单信息";
            return false;
        }

        $bill = $ret[0];

        $query = $this->_db->prepare( "select * from `item` WHERE  `id`=? " );
        $query->execute( array($bill['itemid']) );
        $ret = $query->fetchAll();
        if ( !$ret || count( $ret ) != 1 ) {
            $this->errno = -6010;
            $this->errmsg = "找不到商品信息";
            return false;
        }

        $item = $ret[0];

        $input = new WxPayUnifiedOrder(); // 商户id等在config中设置
        $input->SetBody( $item['name'] );
        $input->SetAttach( $billId );  // 订单id，支付通知原路返回
        $input->SetOut_trade_no( WxPayConfig::MCHID.date( "YmdHis" ) );  // 设置商户内部订单号，MCHID商户号加当前时间
        $input->SetTotal_fee( $bill['price'] ); //订单金额
        $input->SetTime_start( date( 'YmdHis' ) ); //开始时间
        $input->SetTime_expire( date( "YmdHis", time() + 86400*3 ) ); //过期时间
        $input->SetGoods_tag( $item['name'] ); // 商品标记
        $input->SetNotify_url( "http://api.lushuhao.club/wxpay/callback" ); //异步通知回调地址
        $input->SetTrade_type( "NATIVE" ); //JSAPI--公众号支付、NATIVE--原生扫码支付、APP--app支付
        $input->SetProduct_id( $billId ); //生成二维码包含的商品id

        $notify = new NativePay();
        $result = $notify->GetPayUrl( $input ); //生成code url
        $url = $result["code_url"];
        return $url;
    }

    public function callback() {
        /**
         * 订单成功，更新账单
         * TODO 因为SK没有，没法与微信支付的服务器做Response确认，只能单方面记账,微信尝试发起三次回调,最多5次
         */
        $xmlData = file_get_contents( "php://input" ); // 标准输入流,微信返回一个xml
        if ( substr_count( $xmlData, "<result_code><![CDATA[SUCCESS]]></result_code>" ) == 1 && substr_count( $xmlData, "<return_code><![CDATA[SUCCESS]]></return_code>" ) == 1 ) {  //支付成功
            preg_match( '/<attach>(.*)\[(\d+)\](.*)<\/attach>/i', $xmlData, $match ); // 传递给微信attach是订单id
            if ( isset( $match[2] ) && is_numeric( $match[2] ) ) {
                $billId = intval( $match[2] ); // 返回第2个捕获的子组
            }
            preg_match( '/<transaction_id>(.*)\[(\d+)\](.*)<\/transaction_id>/i', $xmlData, $match );
            error_log('match2----'.$match[2]);
            if ( isset( $match[2] ) && is_numeric( $match[2] ) ) {
                $transactionId = intval( $match[2] ); // 返回第2个捕获的子组
            }
        }
        if ( isset( $billId ) && isset( $transactionId ) ) {
            $query = $this->_db->prepare( "update `bill` set `transaction`=?, `ptime`=?, `status`='paid' where `id`=?" );
            $query->execute(array($transactionId,date("Y-m-d H:i:s"), $billId));
        }
    }
}