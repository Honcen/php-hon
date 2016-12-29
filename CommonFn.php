<?php
/**
 * Created by PhpStorm.
 * User: Hon
 * Date: 2016/12/29
 * Time: 15:31
 */

if (! function_exists('json_encode_cn')) {
    function json_encode_cn($res)
    {
        return json_encode($res, JSON_UNESCAPED_UNICODE);
    }
}


if (! function_exists('str_replace_once')) {
    function str_replace_once($needle, $replace, $haystack)
    {
        $pos = strpos($haystack, $needle);
        if ($pos === false) {
            // Nothing found
            return $haystack;
        }
        return substr_replace($haystack, $replace, $pos, strlen($needle));
    }
}

if (! function_exists('isJson')) {

    /**
    * 验证输入的字符串是否符合json标准
    * @param $param
    * @return bool
    * @author Hon <chenhong@fangstar.net>
    */
    function isJson($param)
    {
        json_decode($param);
        if (json_last_error()) {
            return false;
        }

        if(!is_array(json_decode($param,true))){
            return false;
        }
        return true;
    }
}


if (! function_exists('matchKeyInArray')) {
    /**
     * 匹配两个数组中的key
     * @param   array   $needle     必传数组
     * @param   array   $hayStack   被比较的数组
     * @author  Qvil<yangqingwu@fangstar.net>
     * @return  String:如果hayStack中所有key，在needle中也存在，那么返回空字符串，否则返回在needle中第一个没有匹配到的key字符串
     */
    function matchKeyInArray($needle,$hayStack)
    {
        foreach($hayStack as $key){
            if(null === array_get($needle,$key))return strval($key);
        }
        return "";
    }
}

if (! function_exists('floor_float')) {
    /**
     * 向下获取小数
     * @param $val
     * @param $precision
     * @return float
     * @author Hon <chenhong@fangstar.net>
     */
    function floor_float($val,$precision=0)
    {
        $location = strpos($val,".");
        if($location){
            $temp_val =  substr($val,0,$location);
            $t_val = substr(strstr($val,'.'),0,$precision+1);
            $val = $temp_val.$t_val;
        }
        return $val;
    }
}


if (! function_exists('checkDecimal')) {
    /**
     * 检测給定数值是否符合给定边界限制
     * @param $number
     * @param $max
     * @param $min
     * @return bool
     */
    function checkDecimal($number,$max,$min)
    {
        $number = floatval($number);
        if($number > $max || $number < $min)
            return false;
        return true;
    }
}
if (! function_exists('arrayStripTags')) {
    /**
     * 去除标签
     * @param $array
     * @return array
     * @author Hon <chenhong@fangstar.net>
     * @datetime 2016-04-12 15:20
     */
    function arrayStripTags($array)
    {
        $result = array();

        foreach ($array as $key => $value) {
            $key = strip_tags($key);
            if (is_array($value)) {
                $result[$key] = arrayStripTags($value);
            } if( is_object($value)){
                $result[$key] = arrayStripTags((array)$value);
            }else {
                $result[$key] = trim(strip_tags($value));
            }
        }

        return $result;
    }
}


if (! function_exists('shiftHtmlTags')) {
    /**
     * 字符转换为HTML实体
     * @param $array [一般是数组，但不限制]
     * @return array
     * @author Hon <chenhong@fangstar.net>
     * @datetime 2016-04-12 15:22
     */
    function shiftHtmlTags($array)
    {
        if(is_array($array)){
            $result = array();
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $result[$key] = shiftHtmlTags($value);
                } else if( is_object($value)){ // 此步是扩展一般是在输出数据库查询结果时才会进入
                    $result[$key] = shiftHtmlTags((array)$value);
                } else if(isJson($value)){ // 此步本应不存在，但是为了兼容前端post提交 数据用 json方式提交
                    $json_re = json_decode($value,true);
                    $json_re = json_encode(shiftHtmlTags($json_re));
                    $result[$key] = $json_re;
                } else{
                    $result[$key] = htmlspecialchars(htmlspecialchars_decode($value));

                }
            }
            return $result;
        }else{
            return htmlspecialchars(htmlspecialchars_decode($array));
        }
    }
}