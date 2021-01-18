<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/11/12
 * Time: 3:09 PM
 */

namespace app\api\controller;


use think\Request;
use app\model\City;
use app\model\District;
use app\model\Community;
use app\model\Street;
use app\model\Food;

class Foodfilter extends Base
{
    public $cityModel;
    public $districtModel;
    public $communityModel;
    public $streetModel;
    public $foodModel;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->cityModel = new City();
        $this->districtModel = new District();
        $this->communityModel = new Community();
        $this->streetModel = new Street();
        $this->foodModel = new Food();
    }

    /**
     * 获取城市下筛选条件以及层级
     */
    public function index()
    {
        $data = [];
        // 获取所有区域 //需要过滤没有数据的区
        $districtData = [];
        $districts = $this->districtModel->getDistrictsByCity($this->cityId);
        if (!empty($districts)) {
            $districtFoodCount = $this->foodModel->getDistrictFoodCount(array_column($districts,'id'));
            if (!empty($districtFoodCount)) {
                $districtFoodCountMap = array_column($districtFoodCount,'count','district_id');
                foreach ($districts as $district) {
                    if (!empty($districtFoodCountMap[$district['id']])) {
                        $districtData[] = $district;
                    }
                }
            }
        }

        // 获取所有街道
        $streets = $this->streetModel->getStreetsByCityId($this->cityId);
        // 获取所有社区
        $communitys = $this->communityModel->getCommunityByCityId($this->cityId);
        // 树结构
        $tree = $this->tree($districts,$streets,$communitys);

        $data = [
            'districts'  => $districtData,
            'tree'       => $tree
        ];

        return ['code' => 1, 'data' => $data, 'msg'  => 'success'];
    }

    private function tree($districts = [],$streets = [],$communitys = [])
    {
        $ret = [];
        if (empty($districts) || empty($streets)) {
            return $ret;
        }
        // 处理区域下街道
        foreach ($districts as $district) {
            foreach ($streets as $street) {
                if ($street['district_id'] == $district['id']) {
                    $ret['d_s_'.$district['id']][] = [
                        'id' => $street['id'],
                        'name' => $street['name']
                    ];
                }
            }
        }
        if (empty($communitys)) {
            return $ret;
        }

        // 街道下所有社区
        foreach ($streets as $street) {
            foreach ($communitys as $community) {
                if ($community['street_id'] == $street['id']) {
                    $ret['s_c_'.$street['id']][] = [
                        'id'   => $community['id'],
                        'name' => $community['name']
                    ];
                }
            }
        }

        return $ret;
    }
}