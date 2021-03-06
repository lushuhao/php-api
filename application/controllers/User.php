<?php

/**
 * @name UserController
 * @author
 * @desc 用户控制器
 */
class UserController extends Yaf_Controller_Abstract {

    public function indexAction() {
        return $this->loginAction();
    }

    /**
     * 用户登录
     * @return bool
     */
    public function loginAction() {
//        $submit = $this->getRequest()->getQuery("submit", '0');// submit没有传，默认0，额外验证，防止程序登录
        $submit = Common_Request::getRequest( 'submit', '0' );
        if ( $submit != '1' ) {
//            echo json_encode(array('errno'=>-1001, 'errmsg'=>'请通过正确渠道提交'));
            echo Common_Request::response( Err_Map::get( 1001 ) );
            return false;
        }

        // 获取参数
        $uname = Common_Request::postRequest( 'uname', false );
        $pwd = Common_Request::postRequest( 'pwd', false );
        if ( !$uname || !$pwd ) {
            echo Common_Request::response( Err_Map::get( 1002 ) );
            return false;
        }

        try {
            // 调用Model，做登录验证
            $model = new UserModel();
            $uid = $model->login( trim( $uname ), trim( $pwd ) );
        } catch ( Exception $e ) {
            echo json_encode( Err_Map::get( 1000 ) );
            return false;
        }
        if ( $uid ) {
            // 种Session

            session_start(); // 初始化session数据，在客户端生成cookie，服务端生成存放session数据的临时文件
            $_SESSION['user_token'] = md5( "salt".$_SERVER['REQUEST_TIME'].$uid ); // 服务器端的cookie，运行期间可以使用
            $_SESSION['user_token_time'] = $_SERVER['REQUEST_TIME']; // 请求开始时的时间戳
            $_SESSION['user_id'] = $uid;

            echo Common_Request::response( 0, '', array('name' => $uname) );
        } else {
            echo Common_Request::response( $model->errno, $model->errmsg );
        }
        return true;
    }

    /**
     * 用户注册
     * @return bool
     */
    public function registerAction() {
        // 获取参数
        $uname = $this->getRequest()->getPost( "uname", false ); //getRequst()是父类，返回实例，getPost(第二个参数可选是在未找到的情况下返回)
        $pwd = $this->getRequest()->getPost( 'pwd', false );
        if ( !$uname || !$pwd ) {
            echo json_encode( array(Err_Map::get( 1002 )) );
            return false;
        }

        // 调用Model,做登录验证
        $model = new UserModel();
        if ( $model->register( trim( $uname ), trim( $pwd ) ) ) {
            echo Common_Request::response( 0, '', array('name' => $uname) );
        } else {
            echo Common_Request::response( $model->errno, $model->errmsg );
        }
        return true;
    }
}
