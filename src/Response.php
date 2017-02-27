<?php
/**
 * Created by PhpStorm.
 * User: Hon
 * Date: 2017/1/4
 * Time: 13:40
 */

namespace Hon;


use Illuminate\Http\Request;

class Response
{
    public static function outputWithLog($result=1,$data=[],$msg='查询成功！',$errorcode=0,$statusCode=200)
    {
        $reData = [
            'result' => $result,
            'errorcode' => $errorcode,
            'msg' => $msg,
            'data' => $data
        ];
        $request = new Request();
        Log::infoLog($msg,$request->all(),$reData);
        return response($reData, $statusCode)->header('Content-Type', 'application/json');
    }

    public static function output($result=1,$data=[],$msg='查询成功！',$errorcode=0,$statusCode=200)
    {
        $reData = [
            'result' => $result,
            'errorcode' => $errorcode,
            'msg' => $msg,
            'data' => $data
        ];
        return response($reData, $statusCode)->header('Content-Type', 'application/json');
    }
}