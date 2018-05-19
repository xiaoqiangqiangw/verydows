<?php
class user_controller extends general_controller
{
    //用户个人中心页面
    public function action_index()
    {
        //验证是否登录
        $user_id = $this->is_logined();
        //获取基本信息
        $user_model = new user_model();
        $this->user = $user_model->find(array('user_id' => $user_id));
        //获取账户信息
        $account_model = new user_account_model();
        $this->account = $account_model->get_user_account($user_id);
        $this->compiler('user_index.html');
    }

    //用户登录页面
    public function action_login()
    {
        //获取ip地址
        $client_ip = get_ip();
        //根据cookie判断用户登录状态 已登录跳转去个人中心
        if($cookie = request('USER_STAYED', null, 'cookie'))
        {
            $user_model = new user_model();
            if($user_model->check_stayed($cookie, $client_ip)) jump(url('mobile/user', 'index'));
        }
        //实例化请求异常模型 根据错误次数判断是否显示图片验证码
        $error_model = new request_error_model();
        $this->captcha = $error_model->check($client_ip, $GLOBALS['cfg']['captcha_user_login']);
        //实例化授权模型 获取授权方式及授权地址
        $oauth_model = new oauth_model();
        $this->oauth_list = $oauth_model->get_enable_list('mobile');
        //渲染登录页面
        $this->compiler('login.html');
    }

    //用户注册页面
    public function action_register()
    {
        $this->compiler('register.html');
    }


    public function action_footprint()
    {
        $this->compiler('user_footprint.html');
    }
    
    public function action_profile()
    {
        $user_id = $this->is_logined();
        $condition = array('user_id' => $user_id);
        $user_model = new user_model();
        $this->user = $user_model->find($condition);
        $profile_model = new user_profile_model();
        $this->profile = $profile_model->find($condition);
        $this->compiler('user_profile.html');
    }
    
    public function action_info()
    {
        $user_id = $this->is_logined();
        $condition = array('user_id' => $user_id);
        $this->field = request('field');
        switch($this->field)
        {
            case 'avatar':
            
                $user_model = new user_model();
                $user = $user_model->find($condition);
                $this->avatar = $user['avatar'];
                $this->title = '更换头像';
                
            break;
            
            case 'email':
                
                $user_model = new user_model();
                $user = $user_model->find($condition);
                $this->email = $user['email'];
                $this->title = '更换邮箱';
            
            break;
            
            case 'mobile':
            
                $user_model = new user_model();
                $user = $user_model->find($condition);
                $this->mobile = $user['mobile'];
                $this->title = '更换手机';
            
            break;
            
            case 'nickname':
            
                $profile_model = new user_profile_model();
                $profile = $profile_model->find($condition);
                $this->nickname = $profile['nickname'];
                $this->title = '更换昵称';
                
            break;
            
            case 'gender':
            
                $profile_model = new user_profile_model();
                $profile = $profile_model->find($condition);
                $this->gender = $profile['gender'];
                $this->title = '更换性别';
                
            break;
            
            case 'qq':
            
                $profile_model = new user_profile_model();
                $profile = $profile_model->find($condition);
                $this->qq = $profile['qq'];
                $this->title = '更换QQ';
                
            break;
            
            case 'birthdate':
                
                include(VIEW_DIR.DS.'function'.DS.'html_date_options.php');
                $profile_model = new user_profile_model();
                $this->birthdate = $profile_model->find($condition, null, 'birth_year, birth_month, birth_day');
                $this->title = '更换生日';
                
            break;
            
            case 'signature':
            
                $profile_model = new user_profile_model();
                $profile = $profile_model->find($condition);
                $this->signature = $profile['signature'];
                $this->title = '更换个性签名';
                
            break;
            
            default: jump(url('mobile/main', '404'));
        }
        $this->compiler('user_info.html');
    }

    //用户登出方法
    public function action_logout()
    {   
        $user_model = new user_model();
        $user_model->logout();
        jump(url('mobile/user', 'login'));
    }
}
