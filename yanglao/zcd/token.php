<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/12/15
 * Time: 9:59 AM
 */
include_once "./init.php";

class Token
{
    private $appId;
    private $appSecret;
    private $db;

    CONST EXPIRE_TIME = 7200;//过期时间2小时

    public function __construct($appId,$appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->db = new DB();
    }

    public function getToken()
    {
        // 先查询token
        $sql = "SELECT * FROM yanglao_token WHERE app_id = '".$this->appId."' AND app_secret = '".$this->appSecret."'";
        $tokenDetail = $this->db->get_one($sql);
        if (!$tokenDetail) {
            return_result(10001);
        }
        // 没有token则生成新的token，过期时间为两小时
        $nowTime = time();
        // 判断有没有过期,如果没过期则直接返回
        $expire = $tokenDetail['expire'];
        if ($expire > $nowTime) {
            $token = $tokenDetail['token'];
        } else {
            // 重新生成
            $str = $this->appId.$this->appSecret.$nowTime;
            $token = md5($str);
            $updateData = [
                'token' => $token,
                'update_time' => $nowTime
            ];
            $expire = $updateData['expire'] = $nowTime + SELF::EXPIRE_TIME;
            $this->db->update('yanglao_token',$updateData," id = ".$tokenDetail['id']);
        }
        return_result(0,[
            'token' => $token,
            'expire' => intval($expire)
        ]);
    }
}

$appId = $_GET['app_id'];
$appSecret = $_GET['app_secret'];

$tokenClass = new Token($appId,$appSecret);
$tokenClass->getToken();

