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
        $this->utils = new Utils();
    }

    function get_weather($cityname)
    {
        $url = "http://apis.baidu.com/apistore/weatherservice/cityname?cityname=".$cityname;
        $result = $this->utils->curl_get($url,"json", $this->header);
        if($result->errNum!=0){
            return $result->errMsg;
        }
        return $result->retData;
    }

    function get_turing($info){
        $key="879a6cb3afb84dbf4fc84a1df2ab7319";
        $url="http://apis.baidu.com/turing/turing/turing?key=".$key."&info=".$info;
        $result=$this->utils->curl_get($url,"json",$this->header);
        return $result;
    }
}
