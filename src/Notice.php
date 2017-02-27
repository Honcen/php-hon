<?php
/**
 * Created by PhpStorm.
 * User: Hon
 * Date: 2017/1/4
 * Time: 17:03
 */

namespace Hon;

use Illuminate\Support\Facades\Request;

class Notice
{
    protected static $to_user_uri = '/service/send-text/to-user';
    protected static $to_group_uri = '/service/send-text/to-all';
    public static function toUser($in_data=[],$out_data=[],$e)
    {
        $message = $e->getMessage();
        $fl = " File ".$e->getFile().' in line '.$e->getLine();
        $params = [
            'content' => self::makeContent($message,$fl,$in_data,$out_data),
            'to_user' => config('dmds.notice.user_id'),
            'agent_id' => 0
        ];
       return self::send(self::$to_user_uri,$params);
    }

    public static function toAll($in_data=[],$out_data=[],$e)
    {
        $message = $e->getMessage();
        $fl = " File ".$e->getFile().' in line '.$e->getLine();
        $params = [
            'content' => self::makeContent($message,$fl,$in_data,$out_data),
            'agent_id' => config('dmds.notice.agent_id')
        ];
        return self::send(self::$to_group_uri,$params);
    }

    protected static function makeContent($message,$fl,$in_data=[],$out_data=[])
    {
        $content = 'DMDS接口服务错误:';
        $content .= "\n 服务器环境:".env('SERVER_ENV','dev');
        $content .= "\n 请求时间:".date("Y-m-d H:i:s");
        $content .= "\n 请求信息: Ip:'".Request::getClientIp()."',Url:'".Request::url()."',Query:'".notice_json(Request::query())."'";
        $content .= "\n 提示消息:".$message;
        $content .= "\n 错误文件及位置:".$fl;
        $content .= "\n 数据内容:".notice_json($in_data);
        $content .= "\n 返回结果:".notice_json($out_data);
        return $content;
    }
    protected static function send($uri,$param)
    {
        $host = config('dmds.notice.host');
        $url = $host.$uri;
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ci, CURLOPT_TIMEOUT, 30);//设置超时
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);//要求结果为字符串且输出到屏幕上
        curl_setopt($ci, CURLOPT_ENCODING, "");
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);//设置为 true ，说明进行SSL证书认证
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ci, CURLOPT_POST, TRUE);
        curl_setopt($ci, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);
        curl_exec($ci);
        curl_close($ci);
    }

}