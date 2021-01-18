<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/11/11
 * Time: 8:30 AM
 */

namespace app\model;


use think\Model;

class Street extends Model
{
    protected $autoWriteTimestamp = true;

    public function streetPageList($names = [],$page = 1,$limit = 10)
    {
        $where = [];
        if (!empty($names['name'])) {
            $where['s.name'] = ['like',"%".$names['name']."%"];
        }
        if (!empty($names['city_name'])) {
            $where['c.city_name'] = ['like',"%".$names['city_name']."%"];
        }
        if (!empty($names['district_name'])) {
            $where['d.name'] = ['like',"%".$names['district_name']."%"];
        }
        return $this->field('s.id,s.name,c.city_name,d.name as district_name')
            ->alias('s')
            ->join('city c','s.city_id = c.id','LEFT')
            ->join('district d','d.id = s.district_id','LEFT')
            ->where($where)->order('s.id desc')
            ->paginate($limit,false,['page' => $page]);
    }

    /**
     * 获取街道详情
     * @param int $id
     */
    public function detail($id = 0)
    {
        return $this->field('s.id,s.name,c.city_name,d.name as district_name')
            ->alias('s')
            ->join('city c','s.city_id = c.id','LEFT')
            ->join('district d','d.id = s.district_id','LEFT')
            ->where('s.id',$id)->find();
    }

    public function getAllStreets()
    {
        return $this->field('s.id,s.city_id,s.district_id,s.name')
            ->alias('s')
            ->join('city c','c.id = s.city_id')
            ->join('district d','d.id = s.district_id')
            ->where('c.status',1)
            ->where('d.status',1)
            ->select()->toArray();
    }

    public function getStreetsByCityId($cityId = 0)
    {
        return $this->field('s.id,s.district_id,s.name')
            ->alias('s')
            ->join('city c','c.id = s.city_id')
            ->join('district d','d.id = s.district_id')
            ->where('c.status',1)
            ->where('d.status',1)
            ->where('c.id',$cityId)
            ->select()->toArray();
    }
}