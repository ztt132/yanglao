<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/10/20
 * Time: 9:57 AM
 */

namespace app\api\controller;

use think\Request;
use app\model\Food as FoodModel;
use app\model\District;
class Food extends Base
{
    public $foodModel;
    public $districtModel;
    public function __construct(Request $request = null) {
        parent::__construct();
        $this->foodModel = new FoodModel();
        $this->districtModel = new District();
    }

    /**
     * 助餐点列表
     */
    public function index()
    {
        $name = $this->request->get('name');
        $lat = input('lat');
        $lng = input('lng');
        $position = [
            'district_id' => input('district_id'),
            'community_id' => input('community_id'),
            'street_id' => input('street_id'),
        ];
        // 获取banner
        list($page,$limit) = $this->getPaginateInfo();
        $data = $this->foodModel->getFoodList($name,$position,$lng,$lat,$this->cityId,$page,$limit);
        $data['list'] = $data['data'];
        unset($data['data']);
        // 处理距离
        if (!empty($data['list'])) {
            foreach ($data['list'] as &$v) {
                if (!empty($v['lat']) && !empty($v['lng']) && !empty($lat) && !empty($lng)) {
                    $v['distance'] = $v['distance'] <= 1000 ? $v['distance'].'米' : round($v['distance']/1000,2) . ' 千米';
                } else {
                    $v['distance'] = '';
                }
            }
        }
        return ['code' => 1, 'msg'  => 'success', 'data' => $data];
    }

    public function detail()
    {
        $id = input('id');
        if (empty($id)) {
            return ['code' => 0, 'data' => [], 'msg'  => '缺少参数'];
        }

        $food = $this->foodModel->getFoodDetail($id);
        return ['code' => 1, 'data' => $food, 'msg'  => 'success'];
    }

    /**
     * 获取地图中区域数据,计算范围中有多少区每个区有多少助餐点
     * x1 y1 左下角
     * x2 y2 右上角
     */
    public function mapDistrictData()
    {
        $x1 = input('x1');
        $y1 = input('y1');
        $x2 = input('x2');
        $y2 = input('y2');

        if (empty($x1) || empty($y1) || empty($x2) || empty($y2)) {
            return ['code' => 0, 'data' => [], 'msg'  => '缺少参数'];
        }

        //查处所有的区
        $districts = $this->districtModel->getDistrictsByPosition($x1,$y1,$x2,$y2);
        if (empty($districts)) {
            return ['code' => 1, 'data' => [], 'msg'  => 'success'];
        }

        $districtIds = array_column($districts,"id");
        // 查询出所有的助餐以及数量
        $data = $this->foodModel->getCountGroupByDistrict($districtIds);
        return ['code' => 1, 'data' => $data, 'msg'  => 'success'];
    }

    /**
     * 获取范围内的助餐点
     */
    public function mapFoodData()
    {
        $x1 = input('x1');
        $y1 = input('y1');
        $x2 = input('x2');
        $y2 = input('y2');
        $lat = input('lat');
        $lng = input('lng');

        if (empty($x1) || empty($y1) || empty($x2) || empty($y2)) {
            return ['code' => 0, 'data' => [], 'msg'  => '缺少参数'];
        }

        $data = $this->foodModel->getFoodListByPostion($x1,$y1,$x2,$y2,$lng,$lat);
        if (!empty($data)) {
            foreach ($data as &$v) {
                if (!empty($v['lat']) && !empty($v['lng']) && !empty($lat) && !empty($lng)) {
                    $v['distance'] = $v['distance'] <= 1000 ? $v['distance'].'米' : round($v['distance']/1000,2) . ' 千米';
                } else {
                    $v['distance'] = '';
                }
            }
        }
        return ['code' => 1, 'data' => $data, 'msg'  => 'success'];
    }
}