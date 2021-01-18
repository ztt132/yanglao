<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/7/1
 * Time: 10:26 AM
 */

namespace app\api\controller;


use app\api\service\CityService;
use app\model\City;
use app\model\District;
use tests\thinkphp\library\think\config\driver\iniTest;
use think\Controller;
use think\Request;

class Base extends Controller
{
    public $cityModel;
    public $cityId;
    public $version;
    public $districtModel;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->cityModel = new City();
        $this->districtModel = new District();
        // 基础验证
        $this->cityValidate();
        // 接口版本号，目前第一版暂时没业务
        $this->version = input('version');
    }

    /**
     * api接口的部分基础验证
     * 1. 部分接口需要传城市,对这些接口验证城市是否正确
     */
    private function cityValidate() {
        if (!array_key_exists('pinyin',input())) {
            return true;
        }
        $pinyin = input('pinyin');
        $city = $this->cityModel->getCityByPinyin($pinyin);
        if (empty($city)) {
            toJson(['code' => 0, 'msg ' => '城市信息错误']);
        }
        // 状态
        if (!$city['status']) {
            toJson(['code' => 0, 'msg ' => '城市关闭']);
        }

        $this->cityId = $city['id'];
    }

    public function getPaginateInfo() {
        $page = input('page',1);
        $limit = input('pagesize',10);

        return [$page,$limit];
    }

    /**
     * 验证城市状态是否打开
     */
    public function cityStatusValidate($cityId = 0) {
        $city = $this->cityModel->where('id',$cityId)->find();
        if (!empty($city)) {
            if (!$city['status']) {
                toJson(['code' => 0, 'msg ' => '城市关闭']);
            }
        }
    }

    /**
     * 验证区域状态是否打开
     */
    public function districtStatusValidate($districtId = 0) {
        $district = $this->districtModel->where('id',$districtId)->find();
        if (!empty($district)) {
            if (!$district['status']) {
                toJson(['code' => 0, 'msg ' => '区域关闭']);
            }
        }
    }
}