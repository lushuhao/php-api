<?php
/**
 * Created by PhpStorm.
 * User: lu
 * @desc 短信处理功能
 * Date: 2017/9/2
 * Time: 19:32
 */

class SmsController extends Yaf_Controller_Abstract {
    public function indexAction() {

    }

    public function sendAction() {
        $submit = $this->getRequest()->getQuery( "submit", '1' );// submit没有传，默认0，额外验证，防止程序登录
        if ( $submit != '1' ) {
            echo json_encode( array('errno' => -4001, 'errmsg' => '请通过正确渠道提交') );
            return false;
        }

        // 获取参数
        $uid = $this->getRequest()->getPost( "uid", false );
        $templateId = $this->getRequest()->getPost( "templateId", false );

        if ( !$uid || !$templateId ) {
            echo json_encode( array("errno" => -4002, "errmsg" => "用户ID, 模板Id均不能为空") );
            return false;
        }

        // 调用Model，发送短信
        $model = new SmsModel();
        if ( $model->send( intval( $uid ), intval( $templateId ) ) ) {
            echo json_encode( array(
                "errno" => 0,
                "errmsg" => "",
            ) );
        } else {
            echo json_encode( array(
                "errno" => $model->errno,
                "errmsg" => $model->errmsg,
            ) );
        }

        return true;
    }
}