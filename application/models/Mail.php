<?php
/**
 * Created by PhpStorm.
 * User: lu
 * @desc 邮件操作Model类
 * Date: 2017/9/2
 * Time: 16:05
 */
require __DIR__.'/../../vendor/autoload.php'; // 相当于'./../../vendor'
use Nette\Mail\Message; // 反斜杠用来目录

class MailModel {
    public $errno = 0;
    public $errmsg = "";
    private $_db = null;

    public function __construct() {
        $this->_db = new PDO( "mysql:host=127.0.0.1;dbname=imooc;", "root", "342623" );
    }

    public function send( $uid, $title, $contents ) {
        $query = $this->_db->prepare( "select `email` from `user` WHERE `id`=?" );
        $query->execute(array(intval($uid)));
        $ret = $query->fetchAll();
        if (!$ret || count($ret)!=1){
            $this->errno = -3003;
            $this->errmsg = "用户邮箱信息查找失败";
            return false;
        }
        $userEmail = $ret[0]['email'];
        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)){ // 检查是否是邮箱
            $this->errno = -3004;
            $this->errmsg = "用户邮箱信息不符合标准，邮箱地址为: ".$userEmail;
            return false;
        }

        $mail = new Message;
        $mail->setFrom('PHP API <shahramlu@163.com>') // 设置标题，邮箱和发件地址一致
            ->addTo($userEmail)
            ->setSubject($title)
            ->setHTMLBody($contents); // 支持html格式，可以插入1px的图片，htt://host/1px.gif?username=**&sendtime=**等等，服务器接受日志

        $mailer = new Nette\Mail\SmtpMailer([
            'host' => 'smtp.163.com', /* smtp服务器域名 */
          'username' => 'shahramlu@163.com',
            'password' => 'lu853517', /* smtp独立密码 */
            'secure' => 'ssl'
        ]);
        $rep = $mailer->send($mail);
        return true;
    }
}