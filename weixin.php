<?php
require_once('Utils.php');
require_once('config.php');

class WeiXin
{


    public function __construct()
    {
        global $wx;
        $utils = new Utils();
        $this->wx = $wx;
        $this->utils = $utils;
        $this->access_token = $this->get_access_token();
    }

    public function WxValidate()
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
        output_log($strs);
    }

    //输出log文件
    public function output_log($strs, $logFile = null)
    {
        date_default_timezone_set("Asia/Shanghai");
        $output_time = Date("Y-m-d H:i:s");
        $logFile = ($logFile ? $logFile : Date("Y-m-d")) . ".log";
        foreach ($strs as $key => $value) {
            $str .= $key . ":" . $value . "\r\n";
        }
        error_log("输出时间：" . $output_time . "\r\n" . $str, 3, $logFile);
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
}

$weixin = new WeiXin();

$body = json_encode(array(
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
));

$weixin->create_menu($body);
?>
