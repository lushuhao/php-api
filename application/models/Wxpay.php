<?php
/**
 * Created by PhpStorm.
 * User: lu
 * @desc 微信支付功能封装
 * Date: 2017/9/7
 * Time: 21:56
 */

class WxpayModel {
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
        if ( !ret || count( $ret ) != 1 ) {
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
        $query = $this->_db->prepare( "insert into `bill` (`itemid`, `uid`,`price`,`status`) VALUES (?,?,?,'unpaid')" );
        $ret = $query->execute( array($itemId, $uid, intval( $item['price'] )) );
        if ( !$ret ) {
            $this->errno = -6006;
            $this->errmsg = "创建账单失败";
            return false;
        }
        $lastId = $this->_db->lastInsertId();

        // 库存 -1
        $query = $this->_db->prepare( "update `item` set `stock`=`stock`-1 WHERE `id` = ?" );
        $ret = $query->execute( array($itemId) );
        if ( !$ret ) {
            $this->errno = -6007;
            $this->errmsg = "更新库存失败";
            return false;
        }
        return $lastId;    }
}