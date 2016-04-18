<?php

/**
 * Created by PhpStorm.
 * User: trajoe_wu
 * Date: 16/4/17
 * Time: 23:22
 */
require_once('Api.php');

class Service
{
    public function __construct($serviceName, $serviceData)
    {
        $this->serviceName = $serviceName;
        $this->serviceData = $serviceData;
        $this->api = new Api();
        switch ($this->serviceName) {
            case '天气':
            case '天气预报':
                $cityName = $this->serviceData;
                $weatherObj = $this->api->get_weather($cityName);
                $this->content = $this->do_weather($weatherObj);
                //设定要回复的消息类型
                $this->replyMsgType = "text";
                break;
            case 'turing':
                $info = $serviceData;
                $turingObj = $this->api->get_turing($info);
                $this->content = $this->do_turing($turingObj);
                $this->replyMsgType = "text";
                break;
            default:
                $this->content = '暂无此服务';
                break;
        }

    }

    //处理天气服务
    public function do_weather($weatherObj)
    {
        $city = $weatherObj->city;
        $date = $weatherObj->date;
        $time = $weatherObj->time;
        $postCode = $weatherObj->postCode;
        $altitude = $weatherObj->altitude;
        $weather = $weatherObj->weather;
        $temp = $weatherObj->temp;
        $l_temp = $weatherObj->l_temp;
        $h_temp = $weatherObj->h_temp;
        $WD = $weatherObj->WD;
        $WS = $weatherObj->WS;
        $sunrise = $weatherObj->sunrise;
        $sunset = $weatherObj->sunset;

        $body = "城市:" . $city . "\n";
        $body .= "日期:" . $date . "\n";
        $body .= "发布时间:" . $time . "\n";
        $body .= "邮政编码:" . $postCode . "\n";
        $body .= "海拔:" . $altitude . "\n";
        $body .= "天气:" . $weather . "\n";
        $body .= "气温:" . $temp . "℃\n";
        $body .= "最低气温:" . $l_temp . "℃\n";
        $body .= "最高气温:" . $h_temp . "℃\n";
        $body .= "风向:" . $WD . "\n";
        $body .= "风速:" . $WS . "\n";
        $body .= "日出时间:" . $sunrise . "\n";
        $body .= "日落时间:" . $sunset;

        $body = htmlspecialchars($body);

        return $body;
    }

    public function do_turing($turingObj){
        $text=$turingObj->text;
        $body=htmlspecialchars($text);

        return $body;
    }

    //获取微信回复内容
    public function get_reply_content()
    {
        return $this->content;
    }

    //获取要回复消息的类型
    public function get_reply_msgType()
    {
        return $this->replyMsgType;
    }
}
