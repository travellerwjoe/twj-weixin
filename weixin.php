<?php 
error_reporting(E_ALL & ~E_NOTICE);
error_reporting (E_ALL & ~E_NOTICE);
function WxValidate(){
	$signature=$_GET['signature'];
	$timestamp=$_GET['timestamp'];
	$nonce=$_GET['nonce'];
	$echostr=$_GET['echostr'];
	$strs=array(
		'signature'=>$signature,
		'timestamp'=>$timestamp,
		'nonce'=>$nonce,
		'echostr'=>$echostr
	);
	output_log($strs);
}

//输出log文件
function output_log($strs,$logFile=null){
	$output_time=Date("Y-m-d H:i:s");
	$logFile=($logFile?$logFile:Date("Y-m-d")).".log";
	foreach ($strs as $key => $value) {
		$str.=$key.":".$value."\r\n";
	}
	error_log("输出时间：".$output_time."\r\n".$str,3,$logFile);
}

WxValidate();

?>