<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/8/20
 * Time: 8:46 AM
 */

class ShortPhone
{
    private $appId;
    private $appSecret;
    private $randStr;
    private $deviceId;
    private $urls;
    private $timestamp;

    private $token;

    // 默认来源网页
    CONST FROM = 1;

    public function __construct()
    {
        $shortPhoneConfig = \think\Config::get('ShortPhone');
        $this->appId = $shortPhoneConfig['config']['app_id'];
        $this->appSecret = $shortPhoneConfig['config']['app_secret'];
        $this->randStr = $shortPhoneConfig['config']['rand_str'];
        $this->deviceId = $shortPhoneConfig['config']['device_id'];
        $this->timestamp = time();
        $this->urls = $shortPhoneConfig['url'];
        $this->token = $this->getAccessToken();
    }

    /**
     * 获取短号
     */
    public function getShort($city = '',$name = '')
    {
        if (empty($this->token)) {
            return false;
        }

        $url = array_get($this->urls,'get_short');
        $header = [
            'version:v3.0',
            'access-token:'.$this->token,
            'Content-Type:application/x-www-form-urlencoded'
        ];
        $param = [
            'city' => $city,
            'name' => $name,
            'from' => self::FROM
        ];
        $url .= '?' . http_build_query($param);
        $ret = curl_get_contents($url,30,$header);
        $retArr = json_decode($ret,1);
        if (!empty($retArr['code']) && $retArr['code'] == 1) {
            return $retArr['data'];
        } else {
            return false;
        }
    }

    /**
     * 生成签名
     */
    private function getSignature()
    {
        $data = [
            'timestamp' => $this->timestamp,
            'app_id'    => $this->appId,
            'rand_str'  => $this->randStr,
            'device_id' => $this->deviceId
        ];
        $preArr = array_merge($data, ['app_secret' => $this->appSecret]);
        ksort($preArr);
        $preStr = http_build_query($preArr);
        $signature = md5($preStr);
        return $signature;
    }

    /**
     * 绑定短号
     */
    public function bindShort($bindKey,$phone,$prefix)
    {
        $url = array_get($this->urls,'bind_short');
        $header = [
            'version:v3.0',
            'access-token:'.$this->token,
            'Content-Type:application/x-www-form-urlencoded'
        ];
        $param = [
            'bind_key' => $bindKey,
            'telephone' => $phone,
            'prefix' => $prefix
        ];
        $url .= '?' . http_build_query($param);
        $ret = curl_get_contents($url,30,$header);
        $retArr = json_decode($ret,1);
        if (!empty($retArr['code']) && $retArr['code'] == 1) {
            return $retArr['data'];
        } else {
            return false;
        }
    }

    public function deleteShort($shortTel)
    {
        $url = array_get($this->urls,'delete_short');
        $header = [
            'version:v3.0',
            'access-token:'.$this->token
        ];
        $param = ['short_tel' => $shortTel];
        $url .= '?' . http_build_query($param);
        $ret = curl_get_contents($url,30,$header);
        $retArr = json_decode($ret,1);
        if (!empty($retArr['code']) && $retArr['code'] == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取token
     */
    private function getAccessToken()
    {
        $url = array_get($this->urls,'get_token');
        $param = [
            'timestamp' => $this->timestamp,
            'app_id'    => $this->appId,
            'rand_str'  => $this->randStr,
            'signature' => $this->getSignature(),
            'device_id' => $this->deviceId
        ];
        $header = ['version:v3.0'];

        $url .= '?' . http_build_query($param);
        $res = curl_get_contents($url,30,$header);
        $resArr = json_decode($res,1);
        if (isset($resArr['code']) && $resArr['code'] == 1) {
            $token = $resArr['data']['access_token'];
            return $token;
        } else {
            return '';
        }
    }
}