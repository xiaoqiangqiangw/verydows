<?php
class wechat_controller extends general_controller
{


    public function action_callback(){
        $this->setUserinfo();
        jump(urlsafe_b64decode($_GET['u']));
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
        $weUser = $weObj->getOauthUserinfo($access_token['access_token'],$access_token['openid']);
        if(empty($weUser)) jump(url('mobile/main', 'index'));
        $filename = $_SERVER['DOCUMENT_ROOT']."/userinfotest.php";
        logfile($filename,$weUser,'用户信息');
//        var_dump($weUser);die;
        //TBD 获取用户数据 处理
        $client_ip = get_ip();
        $user_model = new user_model();
        if($user = $user_model->find(array('openid' => $weUser['openid'])))
        {
//            var_dump($weUser);die;
            if(request('stay')) $user_model->stay_login($user['user_id'], $user['password'], $client_ip);
            $user_model->set_logined_info($client_ip, $user['user_id'], $user['username'], $user['avatar']);

        }else{
//            print_r($weUser);die;
//            var_dump($weUser);die;
            $usernum = $user_model->find_count()+1;
            $username = 'zjg'.date('md').$usernum;
            $data = array
            (
                'username' => $username,
                'email' => '',
                'password' => '',
                'unionid' => $weUser['unionid'],
                'openid' => $weUser['openid'],
                'avatar' => $weUser['headimgurl'],
                'sex' => $weUser['sex'],
                'nickname' => $weUser['nickname'],
            );
            if(!$user_model->register($data)) {
                $filename = $_SERVER['DOCUMENT_ROOT']."/userRegister.php";
                logfile($filename,$weUser,'微信注册失败');
            }else{

            }
        }
    }

}