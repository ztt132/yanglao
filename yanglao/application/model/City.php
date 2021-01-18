<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/18
 * Time: 4:23 PM
 */

namespace app\model;
use think\Model;
use app\model\Street;
use app\model\Community;

class City extends Model
{
    protected $autoWriteTimestamp = true;

    protected static function init()
    {
        // 追加城市名称
        City::event('before_insert', function ($city) {
            if (empty($city->city_name) && !empty($city->pinyin)) {
                $city->city_name = get_city_name_by_pinyin($city->pinyin);
            }
        });

        City::event('before_update', function ($city) {
            if (empty($city->city_name) && !empty($city->pinyin)) {
                $city->city_name = get_city_name_by_pinyin($city->pinyin);
            }
        });
    }

    public function getCityByPinyin($pinyin) {
        $city = $this->where('pinyin',$pinyin)->find();
        return !empty($city) ? $city->toArray() : [];
    }

    /**
     * 分页
     * 查询城市列表
     */
    public function cityPageList($name = '',$page = 1,$limit = 10) {
        $where = [];
        if (!empty($name)) {
            $where['city_name'] = ['like',"%$name%"];
        }

        return $this->where($where)
            ->order('id desc')
            ->paginate($limit,false,['page' => $page]);
    }


    /**
     * 查询所有城市
     */
    public function allCitys($status = [1]) {
        return $this->field('id,city_name')->whereIn('status',$status)->select()->toArray();
    }

    /**
     * 查询所有的城市，区域，街道，社区信息
     * @param bool $needStreet 是否需要街道信息
     * @param bool $needCommunity 是否需要街道信息
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllCityDistrict($needStreet = false,$needCommunity = false) {
        $cityDistrict = [];
        $data = $this->field('c.id as city_id,c.city_name,d.id as district_id,d.name as district_name')
            ->alias('c')
            ->join('district d','c.id = d.city_id','LEFT')
            ->where('d.status',['=',1],['EXP','IS NULL'],'or')
            ->where('c.status',1)
            ->order('c.id desc')->select()->toArray();
        if (!empty($data)) {
            foreach ($data as $v) {
                if (!array_key_exists($v['city_id'],$cityDistrict)) {
                    $cityDistrict[$v['city_id']] = [
                        'city_name' => $v['city_name'],
                        'districts' => []
                    ];
                }
                if (!empty($v['district_id']) && !empty($v['district_name'])) {
                    $cityDistrict[$v['city_id']]['districts'][$v['district_id']] = [
                        'district_id' => $v['district_id'],
                        'district_name' => $v['district_name'],
                        'streets' => []
                    ];
                }
            }
        }
        krsort($cityDistrict);
        // 区域中追加街道信息
        if ($needStreet) {
            // 查处所有街道
            $streetModel = new Street();
            $streets = $streetModel->getAllStreets();
            if (!empty($streets)) {
                // 处理$cityDistrict结构，方便处理数据
                foreach ($streets as $street) {
                    $cityId = $street['city_id'];
                    $districtId = $street['district_id'];
                    $cityDistrict[$cityId]['districts'][$districtId]['streets'][$street['id']] = $street;
                }
            }
        }
        // 街道中最佳社区信息
        if ($needCommunity) {
            $communityModel = new Community();
            $communitys = $communityModel->getAllCommunitys();
            if (!empty($communitys)) {
                foreach ($communitys as $community) {
                    $cityId = $community['city_id'];
                    $districtId = $community['district_id'];
                    $streetId = $community['street_id'];
                    $cityDistrict[$cityId]['districts'][$districtId]['streets'][$streetId]['communitys'][$community['id']] = $community;
                }
            }
        }
        return $cityDistrict;
    }


    public function findByPinyin($pinyin) {
        return $this->where('pinyin',$pinyin)->select()->toArray();
    }


    /**
     * 上传excel时用
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllCityAndDistrict() {
        $cityDistrict = [];
        $data = $this->field('c.id as city_id,c.city_name,d.id as district_id,d.name as district_name')
            ->alias('c')
            ->join('district d','c.id = d.city_id','LEFT')
            ->order('c.id desc')->select()->toArray();
        if (!empty($data)) {
            foreach ($data as $v) {
                if (!array_key_exists($v['city_name'],$cityDistrict)) {
                    $cityDistrict[$v['city_name']] = [
                        'city_id' => $v['city_id'],
                        'districts' => []
                    ];
                }
                if (!empty($v['district_id']) && !empty($v['district_name'])) {
                    $cityDistrict[$v['city_name']]['districts'][$v['district_name']] = $v['district_id'];
                }
            }
        }
        return $cityDistrict;
    }
}
