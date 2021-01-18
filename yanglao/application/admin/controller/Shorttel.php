<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/8/20
 * Time: 8:47 AM
 */

namespace app\admin\controller;
use app\model\City;
use app\model\Org;


class Shorttel extends AdminBase
{
    private $shortPhone;
    private $cityModel;
    private $orgModel;

    public function __construct()
    {
        require __DIR__ . '/../../../extend/shortphone/ShortPhone.php';
        $this->shortPhone = new \ShortPhone();
        $this->cityModel = new City();
        $this->orgModel = new Org();
    }

    /**
     * 获取短号
     */
    public function bind()
    {
        $cityId = input('city_id');
        $name = input('name');
        $prefix = input('prefix');
        $phone = input('phone');
        // 根据cityid获取city
        $cityInfo = $this->cityModel->find($cityId)->toArray();
        $city = $cityInfo['pinyin'];
        $ret = $this->shortPhone->getShort($city,$name);
        if (!$ret) {
            return $this->jsonData('get_short_error');
        }
        $bindKey = $ret['bind_key'];
        $shortTel = $ret['short_tel'];

        // 绑定
        $bindRet = $this->shortPhone->bindShort($bindKey,$phone,$prefix);
        if (!$bindRet) {
            return $this->jsonData('bind_short_error');
        }

        return $this->jsonData('success',0,[
            'short_tel' => $shortTel
        ]);
    }

    public function delete()
    {
        $shortTel = input('short_tel');
        $orgId = input('org_id');
        $ret = $this->shortPhone->deleteShort($shortTel);
        if (!$ret) {
            return $this->jsonData('delete_short_error');
        }
        // 更新org表此机构 此短号清除
        if (!empty($orgId)) {
            $this->orgModel->update(['short_tel'=>''],['id' => $orgId]);
        }

        return $this->jsonData('success',0);
    }
}