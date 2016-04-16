<?php
class Utils{
	function exe_curl($url,$type="get",$post_data=null,$data_type="json"){
		$ch=curl_init();
		$options = array(
			CURLOPT_URL =>$url, 
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_HEADER=>0
		);
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
}
?>