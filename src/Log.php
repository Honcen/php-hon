<?php
/**
 * Created by PhpStorm.
 * User: Hon
 * Date: 2017/1/4
 * Time: 13:04
 */

namespace Hon;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Request;

class Log
{
    protected static $level = ['debug','info','notice','warning','error','critical','alert','emergency'];

    /**
     * @description 日志记录方法，
     *              可以自定义日8种志类型[DEBUG,INFO,NOTICE,WARNING,ERROR,CRITICAL,ALERT,EMERGENCY]，默认类型为DEBUG。
     *              日志文件统一放在custom_log目录下
     * @param $message
     * @param array $in_data
     * @param array $out_data
     * @param string $level
     * @return bool|string
     * @author Hon
     * @datetime  2015-11-12 11:00
     */
    public static function log($level="debug",$message,$in_data=[],$out_data=[],$fl='')
    {
        try{
            $req = "\n       请求信息: Ip:'".Request::getClientIp()."',Url:'".Request::url()."',Query:'".json_encode(Request::query())."'";
            $msg = "\n       提示消息:".$message;
            $loc = "\n 错误文件及位置:".$fl;
            $cnt = "\n       数据内容:".json_encode($in_data);
            $res = "\n       返回结果:".json_encode($out_data);
            $Log = new Logger('Dmds'); // 通过"个性化名称"实例化日志
            //验证日志级别是否正确
            if (!in_array(strtolower($level),static::$level)) {
                $level = 'error';
            }

            //验证日志目录是否存在，不存在则新建之
            $log_dir = storage_path()."/logs/";

            if(!file_exists($log_dir)){
                mkdir($log_dir,0777,true);
            }

            $Log->pushHandler(new StreamHandler($log_dir.date("Y-m-d")."_".strtolower($level).'.log', Logger::toMonologLevel($level)));

            return $Log->log($level, $req.$msg.$loc.$cnt.$res);

        } catch (\Exception $e) {
            return $e->getMessage();
            exit;
        }
    }

    public static  function errLog($in_data=[],$out_data=[],\Exception $e)
    {
        $fl = "File ".$e->getFile().' in line '.$e->getLine();
        return self::log('error',$e->getMessage(),$in_data,$out_data,$fl);
    }

    public static function debugLog($message, $in_data=[], $out_data  = [])
    {
        return self::log('debug',$message,$in_data,$out_data,self::callPos());
    }

    public static function infoLog($message, $in_data=[], $out_data  = [])
    {
        return self::log('info',$message,$in_data,$out_data,self::callPos());
    }


    /**
     * 从PHP函数堆栈中取出外部调用写日志方法所在的位置
     *
     * @return  string
     */
    private static function callPos()
    {
        $trace_data = debug_backtrace();
        for ($i = 0; $i < 1; $i ++) {
            if (! isset($trace_data[$i])) {
                return "";
            }
            unset($trace_data[$i]);
        }
        $code = "";
        $template = "{file}::{line}";
        foreach ($trace_data as $row) {
            if (isset($row['file']) && isset($row['line']) && isset($row['function']) && isset($row['class'])) {
                $code = strtr($template, [
                    "{file}" => $row['file'],
                    "{line}" => $row['line'],
                    "{class}" => $row['class'],
                    "{function}" => $row['function'],
                ]);
                break;
            }
        }
        return $code;
    }

}