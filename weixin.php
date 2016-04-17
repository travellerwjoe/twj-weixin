<?php
require_once('Utils.php');
require_once('Api.php');
require_once('Service.php');
require_once('config.php');

date_default_timezone_set("Asia/Shanghai");

class WeiXin
{


    public function __construct()
    {
        global $wx;
        $utils = new Utils();
        $this->wx = $wx;
        $this->utils = $utils;
        $this->access_token = $this->get_access_token();
        $this->main();

    }

    //首次与微信服务器通信验证
    public function validate()
    {
        $signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $echostr = $_GET['echostr'];
        $token = $this->wx['token'];
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode("", $tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            echo $echostr;
        }

        $strs = array(
            'signature' => $signature,
            'timestamp' => $timestamp,
            'nonce' => $nonce,
            'echostr' => $echostr,
            'tmpStr' => $tmpStr,
            'tmpArr' => implode("", $tmpArr)
        );
        $this->utils->output_log($strs);
    }
    

    public function get_access_token()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->wx['appId'] . "&secret=" . $this->wx['appSecret'];
        $result = $this->utils->exe_curl($url);
        return $result->access_token;
    }

    //获取微信服务器ip地址
    public function get_ip_list()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=" . $this->access_token;
        $result = $this->utils->exe_curl($url);
        return $result->ip_list;
    }

    //创建菜单
    public function create_menu($body)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . $this->access_token;
        $post_data = array(
            'body' => $body
        );
        $result = $this->utils->exe_curl($url, "post", $post_data);
        return $result;
    }

    //获取用户消息
    public function get_message()
    {
        $message = $GLOBALS['HTTP_RAW_POST_DATA'];
        $messageObj = simplexml_load_string($message, "SimpleXMLElement", LIBXML_NOCDATA);
        return $messageObj;
    }

    //处理消息
    public function do_message()
    {
        $messageObj = $this->get_message();
        $this->fromUserName = $messageObj->FromUserName;
        $this->toUserName = $messageObj->ToUserName;
        $msgType = $messageObj->MsgType;
        $createTime = $messageObj->CreateTime;

        switch ($msgType) {
            case "text":
                $content = trim($messageObj->Content);
                $this->do_message_text($content);
                break;
            default:
                break;
        }
    }

    //处理文本消息
    public function do_message_text($content)
    {
        $contentArr = explode(" ", $content);
        $serviceName = $contentArr[0];
        $api = new Api();
        $service = new Service();

        switch ($serviceName) {
            case "天气":
            case "天气预报":
                $cityname = $contentArr[1];
                $result = $api->get_weather($cityname);
                $replyContent = $service->do_weather($result);
                $this->reply_message_text($replyContent);
                break;
        }
    }

    //回复消息
    public function reply_message($msgType, $content)
    {
        switch ($msgType) {
            case "text":
                $replyMessage = $this->generate_wxXMLMessage_text($content);
                break;
            default:
                break;
        }

        echo $replyMessage;
    }

    //回复文本消息
    public function reply_message_text($content)
    {
        $this->reply_message("text", $content);
    }

    //生成xml格式消息
    public function generate_wxXMLMessage($msgType, $content)
    {

        $xmlMessage = "<xml>\n";
        $xmlMessage .= "<ToUserName>" . $this->toUserName . "</ToUserName>\n";
        $xmlMessage .= "<FromUserName>" . $this->fromUserName . "</FromUserName>\n";
        $xmlMessage .= "<CreateTime>" . time() . "</CreateTime>\n";
        $xmlMessage .= "<MsgType>" . $msgType . "</MsgType>\n";

        switch ($msgType) {
            case "text":
                $xmlMessage .= "<Content>" . $content . "</Content>\n";
                break;
            default:
                break;
        }
        $xmlMessage .= "</xml>";

        return $xmlMessage;
    }

    //生成xml格式文本消息
    public function generate_wxXMLMessage_text($content)
    {
        return $this->generate_wxXMLMessage("text", $content);
    }

    //主函数
    public function main()
    {

        if ($GLOBALS['HTTP_RAW_POST_DATA']) {
            $this->do_message();
        }
    }
}

/*$body = json_encode(array(
    'button' => array(
        array(
            'type' => 'click',
            'name' => '今日歌曲',
            'key' => 'TODY_MUSIC'
        ),
        array(
            'name' => '菜单',
            'sub_button' => array(
                array(
                    'type' => 'view',
                    'name' => '搜索',
                    'url' => 'http://www.baidu.com'
                ),
                array(
                    'type' => 'click',
                    'name' => '赞一个',
                    'key' => 'GOOD'
                )
            )
        )
    )
));*/


$weixin = new WeiXin();


//$weixin->main()
?>
