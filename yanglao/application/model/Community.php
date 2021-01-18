<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/11/11
 * Time: 8:30 AM
 */

namespace app\model;


use think\Model;

class Community extends Model
{
    protected $autoWriteTimestamp = true;

    public function communityPageList($names = [],$page = 1,$limit = 10)
    {
        $where = [];
        if (!empty($names['name'])) {
            $where['co.name'] = ['like',"%".$names['name']."%"];
        }
        if (!empty($names['city_name'])) {
            $where['c.city_name'] = ['like',"%".$names['city_name']."%"];
        }
        if (!empty($names['district_name'])) {
            $where['d.name'] = ['like',"%".$names['district_name']."%"];
        }
        if (!empty($names['street_name'])) {
            $where['s.name'] = ['like',"%".$names['street_name']."%"];
        }
        return $this->field('co.id,co.name,c.city_name,d.name as district_name,s.name as street_name')
            ->alias('co')
            ->join('city c','c.id = co.city_id','LEFT')
            ->join('district d','d.id = co.district_id','LEFT')
            ->join('street s','co.street_id = s.id','LEFT')
            ->where($where)->order('co.id desc')
            ->paginate($limit,false,['page' => $page]);
    }

    public function detail($id)
    {
        return $this->field('co.id,co.name,c.city_name,d.name as district_name,s.name as street_name')
            ->alias('co')
            ->join('city c','c.id = co.city_id','LEFT')
            ->join('district d','d.id = co.district_id','LEFT')
            ->join('street s','co.street_id = s.id','LEFT')
            ->where('co.id',$id)->find();
    }

    public function getAllCommunitys()
    {
        return $this->field('co.id,co.city_id,co.district_id,co.street_id,co.name')
            ->alias('co')
            ->join('city c','c.id = co.city_id')
            ->join('district d','d.id = co.district_id')
            ->where('c.status',1)
            ->where('d.status',1)
            ->select()->toArray();
    }

    public function getCommunityByCityId($cityId = 0)
    {
        return $this->field('co.id,co.district_id,co.street_id,co.name')
            ->alias('co')
            ->join('city c','c.id = co.city_id')
            ->join('district d','d.id = co.district_id')
            ->where('c.status',1)
            ->where('d.status',1)
            ->where('c.id',$cityId)
            ->select()->toArray();
    }
}