<?php
/**
 * Created by PhpStorm.
 * User: trajoe_wu
 * Date: 16/4/17
 * Time: 16:19
 */
require_once('Utils.php');
require_once('config.php');

class Api
{

    function __construct()
    {
        global $api;
        $this->BD_apikey = $api['BD_apikey'];
        $this->header=array(
            "apikey:" . $this->BD_apikey
        );
        $this->utlis = new Utils();
    }

    function get_weather($cityname)
    {
        $url = "http://apis.baidu.com/apistore/weatherservice/cityname?cityname=".$cityname;
        $result = $this->utlis->exe_curl($url, "get", null, "json", $this->header);
        if($result->errNum!=0){
            return $result->errMsg;
        }
        return $result->retData;
    }
}

$api = new Api();
print_r($api->get_weather("成都"));