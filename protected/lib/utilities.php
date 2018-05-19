<?php
/*
 * 有用的工具类
 */
class utilities
{
    //定时任务
    public static function crontab($interval = 3600)
    {
        $timer = APP_DIR.DS.'protected'.DS.'resources'.DS.'timer.txt';
        if($_SERVER['REQUEST_TIME'] - (int)file_get_contents($timer) >= $interval)
        {
            $order_model = new order_model();
            $order_model->expired();
            file_put_contents($timer, $_SERVER['REQUEST_TIME']);
        }
    }
}