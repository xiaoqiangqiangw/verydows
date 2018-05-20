<?php
class wechat_controller extends general_controller
{

    private $weObj;
    public function action_index(){
        //微信公众号配置参数
        $paramwx=[
            'token'=> $GLOBALS['cfg']['token'],          //填写你设定的key
            'appid'=> $GLOBALS['cfg']['appid'],          //填写高级调用功能的app id
            'appsecret'=> $GLOBALS['cfg']['appsecret'], //填写高级调用功能的密钥
        ];
        $weObj = plugin::instance('wechat','Wechat',[$paramwx]);
        $this->weObj = $weObj;
        ob_clean();
        $weObj->valid();
        //消息类型判断
        $type = $weObj->getRev()->getRevType();
        $wechatModel = new wechat_model($weObj);
        switch($type) {
            case $weObj::MSGTYPE_TEXT:
                //文本消息类型处理
                $content = $weObj->getRevContent();
                $wechatModel->textProcess($content);
                break;
            case $weObj::MSGTYPE_EVENT:
                //事件消息类型处理
                $eventData = $weObj->getRev()->getRevEvent();
                $wechatModel->eventProcess($eventData);
                break;
            case $weObj::MSGTYPE_IMAGE:
                //图片消息类型处理
                $wechatModel->imageProcess();
                break;
            case $weObj::MSGTYPE_VOICE:
                //图片消息类型处理MSGTYPE_VOICE
                $wechatModel->voiceProcess();
                break;
            case $weObj::MSGTYPE_VIDEO:
                //图片消息类型处理MSGTYPE_VOICE
                $wechatModel->videoProcess();
                break;
            case $weObj::MSGTYPE_LOCATION:
                //图片消息类型处理MSGTYPE_VOICE
                $wechatModel->locationProcess();
                break;
            default:
                //其他类型处理
                $weObj->text('')->reply();
        }

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