<?php
include(VIEW_DIR.DS.'function'.DS.'mobile_layout.php');
include(VIEW_DIR.DS.'function'.DS.'reviser.php');

class general_controller extends Controller
{
    public function init()
    {
        //定义全局参数
        $this->common = array
        (
            'baseurl' => $GLOBALS['cfg']['http_host'],
            'theme' => $GLOBALS['cfg']['http_host'] . '/public/theme/mobile/' . $GLOBALS['cfg']['enabled_theme'],
        );
        //处理定时任务
        utilities::crontab();
        //判断是否是微信进入
        if(is_weixin()){
            $user_id = $this->is_logined(false);
            if(!$user_id){
                //微信进入且非登录状态进入授权页
                $paramwx=[
                    'token'=> $GLOBALS['cfg']['token'],          //填写你设定的key
                    'appid'=> $GLOBALS['cfg']['appid'],          //填写高级调用功能的app id
                    'appsecret'=> $GLOBALS['cfg']['appsecret'], //填写高级调用功能的密钥
                ];
                $weObj = plugin::instance('wechat','Wechat',[$paramwx]);
                $redirectUrl = urlsafe_b64encode(substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'&nsukey')));
//                var_dump($redirectUrl);
                $callBackUrl = $weObj->getOauthRedirect('http://47.100.112.119/index.php?c=wechat&a=callback&u='.$redirectUrl);
//                var_dump($callBackUrl);die;
                jump($callBackUrl);
            }
        }
    }

    //渲染输出移动端模板页面
    protected function compiler($tpl)
    {
        $this->display('mobile'.DS.$GLOBALS['cfg']['enabled_theme'].DS.$tpl);
    }

    //验证用户是否登录并返回id
    protected function is_logined($jump = TRUE)
    {
        if (empty($_SESSION['USER']['USER_ID']))
        {
            if($cookie = request('USER_STAYED', null, 'cookie'))
            {
                $user_model = new user_model();
                if($user_model->check_stayed($cookie, get_ip()))
                {
                    $_SESSION['REDIRECT'] = $_SERVER['REQUEST_URI'];
                    if($jump) jump($_SERVER['REQUEST_URI']);
                }
            }
            if($jump) jump(url('mobile/user', 'login'));
            return FALSE;
        }
        return $_SESSION['USER']['USER_ID'];
    }

    protected function prompt($type = null, $text = null, $redirect = null, $time = 3)
    {
        if(empty($type)) $type = 'default';
        if(empty($redirect)) $redirect = 'javascript:history.back()';
        $this->rs = array('type' => $type, 'text' => $text, 'redirect' => $redirect, 'time' => $time);
        $this->compiler('prompt.html');
        exit;
    }

}
