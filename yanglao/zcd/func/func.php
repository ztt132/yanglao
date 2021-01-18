<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/12/15
 * Time: 10:07 AM
 */

function return_result($code = 0,$data = [])
{
    echo json_encode([
        'code' => $code,
        'msg'  => get_msg($code),
        'data' => $data
    ]);
    exit;
}

/**
 * 过滤20%的请求
 */
function filter_request()
{
    $number = rand(1,100);
    if ($number > 80) {
        return true;
    }
    return false;
}

function get_msg($code = 0)
{
    $msgConf = [
        0     => 'success',
        -1    => '系统错误',
        10001 => '秘钥错误',
        10002 => '缺少token',
        10003 => 'token错误',
        10004 => 'token过期',
        10005 => '缺少必填参数',
    ];

    return $msgConf[$code];
}