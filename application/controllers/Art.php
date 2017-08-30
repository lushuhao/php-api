<?php
/**
 * Created by PhpStorm.
 * User: lu
 * @name ArtController
 * @desc 文章控制器
 * Date: 2017/8/30
 * Time: 20:31
 */

class ArtController extends Yaf_Controller_Abstract {

    public function indexAction() {
        return $this->listAction();
    }

    public function addAction( $artId = 0 ) {
        if ( !$this->_isAdmin() ) {
            echo json_encode( array( "errno" => -2000, "errmsg" => "需要管理员权限才可以操作" ) );
            return false;
        }

        $submit = $this->getRequest()->getQuery( "submit", '0' );// submit没有传，默认0，额外验证，防止程序登录
        if ( $submit != '1' ) {
            echo json_encode( array( 'errno' => -1001, 'errmsg' => '请通过正确渠道提交' ) );
            return false;
        }

        return true;
    }

    public function editAction() {
        return true;
    }

    public function delAction() {
        return true;
    }

    public function statusAction() {
        return true;
    }

    public function getAction() {
        return true;
    }

    public function listAction() {
        return true;
    }

    private function _isAdmin() {
        return true;
    }

}