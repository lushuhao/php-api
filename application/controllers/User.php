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

    public function loginAction() {
    }
	public function registerAction() {
		// 获取参数
		$uname = $this->getRequest()->getPost("uname", false); //getRequst()是父类，返回实例，getPost(第二个参数可选是在未找到的情况下返回)
        $pwd = $this->getRequest()->getPost('pwd', false);
		if (!$uname || !$pwd) {
		    echo json_encode(array('errno'=>-1002, 'errmsg'=>'用户名与密码必须传递'));
            return false;
        }

        // 调用Model,做登录验证
        $model = new UserModel();
		if ($model->register(trim($uname), trim($pwd))) {
		    echo json_encode(array(
		        'errno'=>0,
                'errmsg'=>'',
                'data'=>array('name'=>$uname)
            ));
        } else {
		    echo json_encode(array(
		        'errno'=>$model->errno,
                'errmsg'=>$model->errmsg,
            ));
        }
        return true;
	}
}
