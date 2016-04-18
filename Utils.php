<?php
class Utils{

    public function __construct()
    {

    }

    public function exe_curl($url,$type="get",$post_data=null,$data_type="json",$header=null){
		$ch=curl_init();
		$options = array(
			CURLOPT_URL =>$url, 
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_HEADER=>0
		);
		if($header){
			$options[CURLOPT_HTTPHEADER]=$header;
		}
		if(strtolower($type)=="post"){
			$options[CURLOPT_POST]=1;
			$options[CURLOPT_POSTFIELDS]=$post_data;
		}
		curl_setopt_array($ch, $options);
		$result=curl_exec($ch);
		curl_close($ch);
		if(strtolower($data_type)=="json"){
			return json_decode($result);
		}
		return $result;

	}

	public function curl_get($url,$data_type="json",$header=null){
		return $this->exe_curl($url,"get",null,$data_type,$header);
	}

	public function curl_post($url,$post_data,$data_type="json",$header=null){
		return $this->exe_curl($url,"post",$post_data,$data_type,$header);
	}

    //输出log文件
    public function output_log($strs, $logFile = null)
    {
        date_default_timezone_set("Asia/Shanghai");
        $output_time = Date("Y-m-d H:i:s");
        $logFile = ($logFile ? $logFile : Date("Y-m-d")) . ".log";
        $str="";
        foreach ($strs as $key => $value) {
            $str .= $key . ":" . $value . "\r\n";
        }
        error_log("输出时间：" . $output_time . "\r\n" . $str, 3, $logFile);
    }
}
?>