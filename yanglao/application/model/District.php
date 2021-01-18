<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/19
 * Time: 2:08 PM
 */

namespace app\model;


use think\Model;

class District extends Model
{
    protected $autoWriteTimestamp = true;

    public function getDistrictsById($cityId = 0) {
        return $this->field('id as district_id,name')->where([
            'city_id' => $cityId,
            'status'  => 1
        ])->select()->toArray();
    }

    /**
     * 分页
     * 查询区域列表
     */
    public function districtPageList($cityName = '',$districtName = '',$page = 1,$limit = 10) {
        $where = [];
        if (!empty($cityName)) {
            $where['c.city_name'] = ['like',"%$cityName%"];
        }
        if (!empty($districtName)) {
            $where['d.name'] = ['like',"%$districtName%"];
        }

        return $this->field('d.id,d.name,c.city_name,d.status')
            ->alias('d')
            ->join('city c','d.city_id = c.id','LEFT')
            ->where($where)->order('d.id desc')
            ->paginate($limit,false,['page' => $page]);
    }

    public function findByNameAndCity($name,$cityId) {
        return $this->where([
            'name' => $name,
            'city_id' => $cityId
        ])->select()->toArray();
    }

    /**
     * 根据经纬度获取区域
     * @param string $x1
     * @param string $y1
     * @param string $x2
     * @param string $y2
     */
    public function getDistrictsByPosition($x1 = '',$y1 = '',$x2 = '',$y2 = '')
    {
        $where = [
            'lng' => [['>=',$x1],['<=',$x2]],
            'lat' => [['>=',$y1],['<=',$y2]]
        ];
        return $this->where($where)->select()->toArray();
    }

    /**
     * 获取城市下所有区域，街道，社区配置
     * @param int $cityId
     */
    public function getDistrictStreetCommunityConf($cityId = 0)
    {
        $ret = [];
        // 先找出所有的区域
        $data = $this->field('d.id as district_id,d.name as district_name,s.id as street_id,s.name as street_name,c.id as community_id,c.name as community_name')
            ->alias('d')
            ->join('street s','d.id = s.district_id','LEFT')
            ->join('community c','c.district_id = d.id','LEFT')
            ->where('d.city_id',$cityId)
            ->where('d.status',1)
            ->order('d.id desc')->select()->toArray();
        if (empty($data)) {
            return $ret;
        }
//        toJson($data);

        foreach ($data as $v) {
            if (!array_key_exists($v['district_id'],$ret)) {
                $ret[$v['district_id']] = [
                    'district_id' => $v['district_id'],
                    'name' => $v['district_name'],
                    'streets' => []
                ];
            }
            if (empty($v['street_id'])) {
                continue;
            }
            if (!array_key_exists($v['street_id'],$ret[$v['district_id']]['streets'])) {
                $ret[$v['district_id']]['streets'][$v['street_id']] = [
                    'street_id' => $v['street_id'],
                    'name' => $v['street_name'],
                    'communitys' => []
                ];
            }
            if (empty($v['community_id'])) {
                continue;
            }
            if (!array_key_exists($v['community_id'],$ret[$v['district_id']]['streets'][$v['street_id']]['communitys'])) {
                $ret[$v['district_id']]['streets'][$v['street_id']]['communitys'][$v['community_id']] = [
                    'community_id' => $v['community_id'],
                    'name' => $v['community_name']
                ];
            }
        }

        return $ret;
    }

    public function getDistrictsByCity($cityId = 0) {
        return $this->field('id,name')->where([
            'city_id' => $cityId,
            'status'  => 1
        ])->select()->toArray();
    }
}