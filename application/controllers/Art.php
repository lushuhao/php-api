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
            echo json_encode( array("errno" => -2000, "errmsg" => "需要管理员权限才可以操作") );
            return false;
        }

        $submit = $this->getRequest()->getQuery( "submit", '1' );// submit没有传，默认0，额外验证，防止程序登录
        if ( $submit != '1' ) {
            echo json_encode( array('errno' => -1001, 'errmsg' => '请通过正确渠道提交') );
            return false;
        }

        // 获取参数
        $title = $this->getRequest()->getPost( "title", false );
        $contents = $this->getRequest()->getPost( "contents", false );
        $author = $this->getRequest()->getPost( "author", false );
        $cate = $this->getRequest()->getPost( "cate", false );

        if ( !$title || !$contents || !$author || !$cate ) {
            echo json_encode( array("errno" => -2002, "errmsg" => "标题、内容、作者、分类信息为空，不能为空") );
            return false;
        }

        // 调用Model，做登录验证
        $model = new ArtModel();
        if ( $lastId = $model->add( trim( $title ), trim( $contents ), trim( $author ), trim( $cate ), $artId ) ) {
            echo json_encode( array(
                "errno" => 0,
                "errmsg" => "",
                "data" => array("lastId" => $lastId),
            ) );
        } else {
            echo json_encode( array(
                "errno" => $model->errno,
                "errmsg" => $model->errmsg,
            ) );
        }

        return true;
    }

    public function editAction() {
        if ( !$this->_isAdmin() ) {
            echo json_encode( array("errno" => -2000, "errmsg" => "需要管理员权限才可以操作") );
            return false;
        }

        $artId = $this->getRequest()->getQuery( "artId", "0" );
        if ( is_numeric( $artId ) && $artId ) {
            return $this->addAction( $artId );
        } else {
            echo json_encode( array("errno" => -2003, "errmsg" => "缺少必要的文章ID参数") );
        }
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