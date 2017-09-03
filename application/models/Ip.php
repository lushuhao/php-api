<?php
/**
 * Created by PhpStorm.
 * User: lu
 * @desc IP地址归属地查询功能
 * Date: 2017/9/3
 * Time: 14:20
 */

class IpModel {
    public $errno = 0;
    public $errmsg = "";

    public function get( $ip ) {
        $rep = ThirdParty_Ip::find($ip);  // 调用ipip.net 封装的SDK
        return $rep;
    }
}