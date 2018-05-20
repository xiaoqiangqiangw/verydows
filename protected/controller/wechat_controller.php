<?php
class wechat_controller extends general_controller
{


    public function action_callback(){
        $this->setUserinfo();
        jump($_SESSION['REDIRECT']);
    }

    private function setUserinfo(){
        //根据code 获取 access_token
        $paramwx=[
            'token'=> $GLOBALS['cfg']['token'],          //填写你设定的key
            'appid'=> $GLOBALS['cfg']['appid'],          //填写高级调用功能的app id
            'appsecret'=> $GLOBALS['cfg']['appsecret'], //填写高级调用功能的密钥
        ];
        $weObj = plugin::instance('wechat','Wechat',[$paramwx]);
        $access_token = $weObj->getOauthAccessToken();
        //获取用户信息
        $user = $weObj->getOauthUserinfo($access_token['access_token'],$access_token['openid']);
        $filename = $_SERVER['DOCUMENT_ROOT']."/userinfotest.php";
        logfile($filename,$user,'用户信息');
        //TBD 获取用户数据 处理
    }
}