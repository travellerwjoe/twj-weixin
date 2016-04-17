<?php
/**
 * Created by PhpStorm.
 * User: trajoe_wu
 * Date: 16/4/18
 * Time: 01:47
 */
require_once('Weixin.php');

class WxMessage
{
    public function __construct()
    {
        $this->weixin = new WeiXin();
        if ($GLOBALS['HTTP_RAW_POST_DATA']) {
            $this->do_message();
        }
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
        $serviceData = $contentArr[1];

        $service = new Service($serviceName, $serviceData);
        $replyContent = $service->get_reply_content();
        $replyMsgType = $service->get_reply_msgType();

        //判断要回复消息的类型,默认回复文本消息
        switch ($replyMsgType) {
            case 'text':
                $this->reply_message_text($replyContent);
                break;
            default:
                $this->reply_message_text($replyMsgType);
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
                $replyMessage = "";
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

}