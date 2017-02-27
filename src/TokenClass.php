<?php
/**
 * Created by PhpStorm.
 * User: Hon
 * Date: 2016/12/30
 * Time: 15:33
 */

namespace Hon;


class TokenClass
{
    public static function getToken(array $params)
    {
        $hash_v = array_keys($params);
        asort($hash_v);
        $hash_v = implode('::',$hash_v);
        return hash_hmac('sha256', $hash_v, get_server_ip(), false);
    }

    public static function checkToken(array $params)
    {
        if(!isset($params['token']))
            return false;
        $token = $params['token'];
        unset($params['token']);
        $hash_v = array_keys($params);
        asort($hash_v);
        $hash_v = implode('::',$hash_v);
        $true_token = hash_hmac('sha256', $hash_v, get_client_ip(), false);
        return $true_token === $token;
    }
}