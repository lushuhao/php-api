<?php
/**
 * Created by PhpStorm.
 * User: lu
 * @desc 推送服务接口
 * Date: 2017/9/2
 * Time: 23:06
 */

class PushController extends Yaf_Controller_Abstract {

    public function singleAction() {
        if ( !Admin_Object::isAdmin() ) {
            echo json_encode( array("errno" => -7001, "errmsg" => "需要管理员权限才可以操作") );
            return false;
        }

        $cid = $this->getRequest()->getQuery( "cid", "" );
        $msg = $this->getRequest()->getQuery( "msg", "" );
        if ( !$cid || !$msg ) {
            echo json_encode( array("errno" => -7002, "errmsg" => "请输入推送用户的设备ID与要推送的内容") );
            return false;
        }

        // 调用Model，推送
        $model = new PushModel();
        if ( $model->single( $cid, $msg ) ) {
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

    public function toallAction() {
        if ( !Admin_Object::isAdmin() ) {
            echo json_encode( array("errno" => -7001, "errmsg" => "需要管理员权限才可以操作") );
            return false;
        }

        $msg = $this->getRequest()->getQuery( "msg", "" );
        if ( !$msg ) {
            echo json_encode( array("errno" => -7002, "errmsg" => "请输入推送的内容") );
            return false;
        }

        // 调用Model，推送
        $model = new PushModel();
        if ( $model->toAll( $msg ) ) {
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