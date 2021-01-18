<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use think\config;


/**
 * 后台ajax接口处理返回数据
 */
if (!function_exists('returnDataFormat')) {
    function returnDataFormat($msg = '',$code = 1,$data = []) {
        $ret = [
            'code' => $code,
            'data' => $data
        ];
        if (!empty($msg)) {
            $ret['msg'] = config::get('ReturnMsg.'.$msg) ? config::get('ReturnMsg.'.$msg) : $ret['msg'];
        }
        return $ret;
    }
}

if (!function_exists('array_get')) {

    function array_get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (! is_array($array) || ! array_key_exists($segment, $array)) {
                return $default;
            }

            $array = $array[$segment];
        }
        return $array;
    }
}

/**
 * 调试用
 */
if (!function_exists('toJson')) {
    function toJson($data = []) {
        echo json_encode($data);exit;
    }
}

/**
 * 获取文件后缀名
 */
if (!function_exists('get_file_extension')) {
    function get_file_extension($fileName = '') {
        return pathinfo($fileName, PATHINFO_EXTENSION);
    }
}
/**
 * 获取所有城市
 */
if (!function_exists('getAllCity')) {
    function getAllCity() {
        return Config::get('City');
    }
}
/**
 * 根据pinyin获取城市名称
 */
if (!function_exists('get_city_name_by_pinyin')) {
    function get_city_name_by_pinyin($pinyin = '') {
        $cityMappings = array_column(Config::get('City'),'city_name','city_key');
        return $cityMappings[$pinyin];
    }
}
if (!function_exists('curl_get_contents')) {
    function curl_get_contents($url, $outtime = 30,$header = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);                //设置访问的url地址
        //curl_setopt($ch,CURLOPT_HEADER,1);                //是否显示头部信息
        curl_setopt($ch, CURLOPT_TIMEOUT, $outtime);               //设置超时
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        //返回结果
        if(!empty($header)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        $sContent = curl_exec($ch);
        $aStatus  = curl_getinfo($ch);
        curl_close($ch);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }
}

if (!function_exists('get_protocol')) {
    function get_protocol() {
        if ( !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return 'https';
        } elseif ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
            return 'https';
        } elseif ( !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return 'https';
        }else{
            return 'http';
        }
    }
}

if (!function_exists('get_server_name')) {
    function get_server_name() {
        return $_SERVER['SERVER_NAME'];
    }
}

if (!function_exists('apiReturn')) {
    function apiReturn($code,$data = []) {
        $config = Config::get('ApiCode');
        $ret = [
            'code' => $code,
            'msg'  => $config[$code],
            'data' => $data
        ];
        return $ret;
    }
}

/**
 * 时间转中文显示
 */
if (!function_exists('show_date_format')) {
    function show_date_format($date = '') {
        $time = strtotime($date);
        return date('Y',$time).'.'.
            date('n',$time).'.'.
            date('j',$time);
    }

}

if (!function_exists('get_page_config')) {
    function get_page_config($page = '') {
        $config = [];
        $keys = Config::get('Org.'.$page);
        if (!empty($keys)) {
            foreach ($keys as $key) {
                $config[$key] = Config::get('Org.'.$key);
            }
        }

        return $config;
    }
}

if (!function_exists('get_filter_config')) {
    function get_filter_config() {
        $config = Config::get('Org.filter_page');
        // 追加子选项
        foreach ($config as &$cItem) {
            $subConfig = Config::get('Org.'.$cItem['value']);
            // 处理格式
            $sub = [];
            foreach ($subConfig as $sk => $sc) {
                $sub[] = [
                    'value' => $sk,
                    'option' => $sc
                ];
            }
            $cItem['sub'] = $sub;
        }
        return $config;
    }
}

if (!function_exists('content_img_add_class')) {
    function content_img_add_class($content) {
        $content = str_replace('<img src=','<img class="content_img" src=',$content);
        return $content;
    }
}

if (!function_exists('make_file_dir')) {
    function make_file_dir($baseDir = '') {
        $date = date('Ymd',time());
        $dir = '.' . $baseDir .'/' . $date;
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        return $dir;
    }
}

if (!function_exists('make_file_name')) {
    function make_file_name($extension = '',$accountId = 0) {
        $fileName = '';
        if (empty($accountId)) {
            $fileName = md5(substr(uniqid(),7)).'.'.$extension;
        } else {
            $fileName = md5(substr(uniqid(),7)) . '_' . $accountId . '.'.$extension;
        }
        return $fileName;
    }
}

if (!function_exists('make_qrcode')) {
    function make_qrcode($coderesult) {
        $dir = make_file_dir('/static/upload/qrcode');
        $extension = 'jpg';
        $fileName = make_file_name($extension);
        file_put_contents($dir.'/'.$fileName,$coderesult);
        $qrcodeImage = get_protocol().'://'.get_server_name().'/static/upload/qrcode/'.date('Ymd',time()).'/'.$fileName;

        return $qrcodeImage;
    }
}

if (!function_exists('get_distance_by_lat_lng')) {
    function get_distance_by_lat_lng($lng1,$lat1,$lng2,$lat2) {
        $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
        return $s;
    }
}