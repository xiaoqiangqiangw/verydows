<?php
class wechat_controller extends general_controller
{

    private $weObj;
    public function action_callback(){
        //微信公众号配置
        $paramwx=[
            'token'=> 'fca7dade95a208622a9d41b3c49d0450',          //填写你设定的key
            'appid'=> 'wx81ad79a6a47a8720',                         //填写高级调用功能的app id
            //'encodingaeskey'=>'encodingaeskey',                         //填写加密用的EncodingAESKey
            'appsecret'=> '033ac3f9d4dbabb839da09bed009bed2',     //填写高级调用功能的密钥
        ];
        $weObj = plugin::instance('wechat','Wechat',[$paramwx]);
        $this->weObj = $weObj;
//        ob_clean();
//        $weObj->valid();
        //消息类型判断
        $type = $weObj->getRev()->getRevType();
        switch($type) {
            case $weObj::MSGTYPE_TEXT:
                //文本消息类型处理
                $content = $weObj->getRevContent();
                $this->textProcess($content);
                break;
            case $weObj::MSGTYPE_EVENT:
                //事件消息类型处理
                $eventData = $weObj->getRev()->getRevEvent();
                $this->eventProcess($eventData);
                break;
            case $weObj::MSGTYPE_IMAGE:
                //图片消息类型处理
                $this->imageProcess();
                break;
            case $weObj::MSGTYPE_VOICE:
                //图片消息类型处理MSGTYPE_VOICE
                $this->voiceProcess();
                break;
            case $weObj::MSGTYPE_VIDEO:
                //图片消息类型处理MSGTYPE_VOICE
                $this->videoProcess();
                break;
            case $weObj::MSGTYPE_LOCATION:
                //图片消息类型处理MSGTYPE_VOICE
                $this->locationProcess();
                break;
            default:
                //其他类型处理
                $weObj->text('')->reply();
        }
    }

    //事件类型处理
    public function eventProcess($eventData){
        $weObj = $this->weObj;
        switch ($eventData['event']){
            //订阅事件类型处理
            case 'subscribe':
                $content = $weObj->text('订阅事件类型处理')->reply();
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
        $callBackUrl = 'http://vip.51gpc.com/index.php/WeChat/Home/dyCallback';
        $url = $weObj->getOauthRedirect($callBackUrl);

        $manuData = array (
            'button' => array (
                0 => array (
                    'name' => '校园商城',
                    'sub_button' => array (
                        0 => array (
                            'type' => 'view',
                            'name' => '首页',
                            'key' => 'menu_0_0',
                            'url'=>'http://47.100.112.119/index.php?m=mobile&c=main&a=index',
                        ),
                        1 => array (
                            'type' => 'view',
                            'name' => '我的订单',
                            'key' => 'menu_0_1',
                            'url'=>'http://47.100.112.119/index.php?m=mobile&c=order&a=list',
                        ),
                        2 => array (
                            'type' => 'view',
                            'name' => '淘物街',
                            'key' => 'menu_0_2',
                            'url'=>'http://47.100.112.119/index.php?m=mobile&c=main&a=index',
                        ),
                        3 => array (
                            'type' => 'view',
                            'name' => '商务合作',
                            'key' => 'menu_0_3',
                            'url'=>'http://47.100.112.119/index.php?m=mobile&c=feedback&a=list',
                        ),
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
                            'name' => '消息首发',
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
                    'type' => 'view',
                    'name' => '我的',
                    'key' => 'menu_2_0',
                    'url'=>'http://47.100.112.119/index.php?m=mobile&c=user&a=index',
                ),
            ),
        );

        $weObj->deleteMenu();
        return $weObj->createMenu($manuData);
    }

    public function action_url()
    {
        $user_id = $this->is_logined();
        $order_id = bigintstr(request('order_id'));
        $order_model = new order_model();
        if($order = $order_model->find(array('user_id' => $user_id, 'order_id' => $order_id, 'order_status' => 1)))
        {
            $payment_id = (int)request('payment_id');
            $payment_map = vcache::instance()->payment_method_model('indexed_list');
            if(isset($payment_map[$payment_id]))
            {
                $order_model->update(array('order_id' => $order_id), array('payment_method' => $payment_id));
                $order['payment_method'] = $payment_id;
                $plugin = plugin::instance('payment', $payment_map[$payment_id]['pcode'], array($payment_map[$payment_id]['params']));
                $plugin->device = request('device', 'pc');
                $res = array('status' => 'success', 'url' => $plugin->create_pay_url($order));
            }
            else
            {
                $res = array('status' => 'error', 'msg' => '支付方式不存在');
            }
        }
        else
        {
            $res = array('status' => 'error', 'msg' => '订单不存在');
        }
        echo json_encode($res);
    }
    
    public function action_notify()
    {
        $pcode = request('pcode', '', 'get');
        $res = 'fail';
        $payment_model = new payment_method_model();
        if($payment = $payment_model->find(array('pcode' => $pcode, 'enable' => 1), null, 'params'))
        {
            $plugin = plugin::instance('payment', $pcode, array($payment['params']));
            if($plugin->response($_POST)) $res = 'success';
        }
        echo $res;
    }
}