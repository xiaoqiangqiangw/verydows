<?php
class wechat_model extends Model
{
    public $table_name = 'user';
    public $weObj;

    public function __construct($weObj,$table_name = null)
    {
        parent::__construct($table_name);
        $this->weObj = $weObj;
    }

    //事件类型处理
    public function eventProcess($eventData){
        $weObj = $this->weObj;
        switch ($eventData['event']){
            //订阅事件类型处理
            case 'subscribe':
                $content = $weObj->text('亲爱的，欢迎你关注 指尖购！新人有优惠商品哦，快去<a href="http://47.100.112.119/index.php?m=mobile&c=main&a=index">商城</a>里找找吧！')->reply();
                break;
            //取消关注事件类型处理
            case 'unsubscribe':
                break;
            //上报地理位置事件类型处理
            case 'LOCATION':
                break;
            //点击菜单推送信息事件类型处理
            case 'CLICK':
                $this->clickMenu($eventData['key']);
                break;
            //点击菜单跳转链接事件类型处理
            case 'VIEW':
                $this->viewMenu($eventData['key']);
                break;
            default:
                $content = $weObj->text('666')->reply();
        }
    }

    //文本消息类型处理
    public function textProcess($content){
        //$weObj = new \Wechat($this->options);
        $weObj = $this->weObj;
        if($content == '创建菜单'){
            $filename = $_SERVER['DOCUMENT_ROOT']."/mbcs.php";
            $remark = '创建菜单';
//            $this->logfile($filename,$weObj->getRevContent(),$remark);
            $result = $this->createManu();
            if($result){
                $weObj->text("创建成功，请重新关注，查看效果！")->reply();
            }else{
                $weObj->text('创建失败，错误码：'.$weObj->errCode.',错误信息：'.$weObj->errMsg.'!')->reply();
            }
        }elseif($content == '测试'){
            $weObj->text(" 文本消息类型处理测试")->reply();
        }elseif($content == '模型测试'){
        }
    }

    //图片消息类型处理
    public function imageProcess(){
        //$weObj = new \Wechat($this->options);
        $weObj = $this->weObj;
        $weObj->text(" 图片消息类型处理")->reply();
    }

    //位置消息类型处理
    public function locationProcess(){
        //$weObj = new \Wechat($this->options);
        $weObj = $this->weObj;
        $weObj->text(" 位置消息类型处理")->reply();
    }

    //链接消息类型处理
    public function linkProcess(){
        //$weObj = new \Wechat($this->options);
        $weObj = $this->weObj;
        $weObj->text("链接消息类型处理 ")->reply();
    }

    //音乐消息类型处理
    public function musicProcess(){
        //$weObj = new \Wechat($this->options);
        $weObj = $this->weObj;
        $weObj->text(" 音乐消息类型处理")->reply();
    }

    //图文消息类型处理
    public function newsProcess(){
        //$weObj = new \Wechat($this->options);
        $weObj = $this->weObj;
        $weObj->text("图文消息类型处理 ")->reply();
    }

    //音频消息类型处理
    public function voiceProcess(){
        //$weObj = new \Wechat($this->options);
        $weObj = $this->weObj;
        $weObj->text("音频消息类型处理 ")->reply();
    }

    //视频消息类型处理
    public function videoProcess(){
        //$weObj = new \Wechat($this->options);
        $weObj = $this->weObj;
        $weObj->text(" 视频消息类型处理")->reply();
    }

    //点击菜单推送信息事件类型处理
    private function clickMenu($eventKey){
        //T.B.D 根据 <EventKey><![CDATA[EVENTKEY]]></EventKey> 做业务处理
    }

    //点击菜单推送信息事件类型处理
    private function viewMenu($eventData){

        $weObj = $this->weObj;
        switch ($eventData){
            //跳转到后台首页
            case 'http://vip.51gpc.com/index.php/WeChat/home/index':
                $openid = $weObj->getRevFrom();
                //$filename = $_SERVER['DOCUMENT_ROOT']."/test.php";
                session('dy_openid',$openid,300);
                //$remark = 'openid';
                //$this->logfile($filename,$openid,$remark);
                //$remark = 'SESSION'.session_id();
                //$this->logfile($filename,$_SESSION,$remark);
                break;
            default:
                //$filename = $_SERVER['DOCUMENT_ROOT']."/test.php";
                //$remark = '未知信息事件类型';
                //$this->logfile($filename,$_SESSION,$remark);
                break;
        }
    }

    //构建菜单数据
    private function createManu()
    {
        //$weObj = new \Wechat($this->options);
        $weObj = $this->weObj;
//        $callBackUrl = 'http://vip.51gpc.com/index.php/WeChat/Home/dyCallback';
        $url_index = $weObj->getOauthRedirect('http://47.100.112.119/index.php?c=wechat&a=index');
        $url_category = $weObj->getOauthRedirect('http://47.100.112.119/index.php?c=wechat&a=category');
        $url_taowu = $weObj->getOauthRedirect('http://47.100.112.119/index.php?c=wechat&a=taowu');
        $url_userindex = $weObj->getOauthRedirect('http://47.100.112.119/index.php?c=wechat&a=userindex');
        $url_order = $weObj->getOauthRedirect('http://47.100.112.119/index.php?c=wechat&a=order');
        $url_help = $weObj->getOauthRedirect('http://47.100.112.119/index.php?c=wechat&a=help');
        $url_feedback = $weObj->getOauthRedirect('http://47.100.112.119/index.php?c=wechat&a=feedback');

        $manuData = array (
            'button' => array (
                0 => array (
                    'name' => '逛商城',
                    'sub_button' => array (
                        0 => array (
                            'type' => 'view',
                            'name' => '首页',
                            'key' => 'menu_0_0',
                            'url'=>$url_index,
                        ),
                        1 => array (
                            'type' => 'view',
                            'name' => '商品分类',
                            'key' => 'menu_0_1',
                            'url'=>$url_category,
                        ),
                        2 => array (
                            'type' => 'view',
                            'name' => '淘物街',
                            'key' => 'menu_0_2',
                            'url'=>$url_taowu,
                        ),
//                        3 => array (
//                            'type' => 'view',
//                            'name' => '商务合作',
//                            'key' => 'menu_0_3',
//                            'url'=>'http://47.100.112.119/index.php?m=mobile&c=feedback&a=list',
//                        ),
                    ),
                ),
                1 => array (
                    'name' => '最近动态',
                    'sub_button' => array (
                        0 => array (
                            'type' => 'view',
                            'name' => '新品上架',
                            'key' => 'menu_1_0',
                            'url'=>'http://47.100.112.119/index.php?m=mobile&c=main&a=index',
                        ),
                        1 => array (
                            'type' => 'view',
                            'name' => '今日优惠',
                            'key' => 'menu_1_1',
                            'url'=>'http://47.100.112.119/index.php?m=mobile&c=main&a=index',
                        ),
                        2 => array (
                            'type' => 'view',
                            'name' => '最新资讯',
                            'key' => 'menu_1_2',
                            'url'=>'http://47.100.112.119/index.php?m=mobile&c=article&a=index',
                        ),
                        3 => array (
                            'type' => 'view',
                            'name' => '关于我们',
                            'key' => 'menu_1_2',
                            'url'=>'http://47.100.112.119/index.php?m=mobile&c=main&a=index',
                        ),
                    ),
                ),
                2 => array (
                    'name' => '我的',
                    'sub_button' => array (
                        0 => array (
                            'type' => 'view',
                            'name' => '个人中心',
                            'key' => 'menu_1_0',
                            'url'=>$url_userindex,
                        ),
                        1 => array (
                            'type' => 'view',
                            'name' => '我的订单',
                            'key' => 'menu_1_1',
                            'url'=>$url_order,
                        ),
                        2 => array (
                            'type' => 'view',
                            'name' => '帮助中心',
                            'key' => 'menu_1_2',
                            'url'=>$url_help,
                        ),
                        3 => array (
                            'type' => 'view',
                            'name' => '我要咨询',
                            'key' => 'menu_1_2',
                            'url'=>'http://47.100.112.119/index.php?m=mobile&c=feedback&a=list',
                        ),
                    ),
                ),
            ),
        );

        $weObj->deleteMenu();
        return $weObj->createMenu($manuData);
    }
}