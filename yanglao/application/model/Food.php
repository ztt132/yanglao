<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/10/20
 * Time: 8:28 AM
 */

namespace app\model;

use think\Model;
class Food extends Model
{
    public function foodPageList($name,$cityName,$districtName,$page,$limit)
    {
        $where = [];
        if (!empty($name)) {
            $where['f.name'] = ['like',"%$name%"];
        }
        if (!empty($cityName)) {
            $where['c.city_name'] = ['like',"%$cityName%"];
        }
        if (!empty($districtName)) {
            $where['d.name'] = ['like',"%$districtName%"];
        }
//        $where['f.status'] = 1;
        $where['f.is_delete'] = 0;

        return $this->field('f.*,c.city_name,d.name as district_name')
            ->alias('f')
            ->join('city c','c.id = f.city_id','LEFT')
            ->join('district d','d.id = f.district_id','LEFT')
            ->where($where)->order('f.id desc')
            ->paginate($limit,false,['page' => $page])->toArray();
    }

    public function createFood($data = [])
    {
        $ret = $this->create($data);
        if (!empty($ret)) {
            // 更新sort
            $foodId = $ret['id'];
            $this->update(['sort' => $foodId],['id' => $foodId]);
        }
        return $ret;
    }

    /**
     * 助餐点列表，小程序接口
     * @param string $name
     * @param array $position  district_id,street_id,district_id
     * @param string $lng
     * @param string $lat
     * @param int $cityId
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\exception\DbException
     */
    public function getFoodList($name = '',$position = [],$lng = '',$lat = '',$cityId = 0,$page = 1,$limit = 10)
    {
        $where = [
            'f.city_id'  => $cityId,
            'f.is_delete'   => 0,
            'f.status'   => 1
        ];
        $condition = [];
        if (!empty($name)) {
            $condition[] = ['f.name','like',"%$name%"];
            $condition[] = ['s.name','like',"%$name%"];
            $condition[] = ['c.name','like',"%$name%"];
        }
        foreach ($position as $k => $v) {
            if (!empty($v)) {
                $where['f.'.$k] = $v;
            }
        }
        // 需要计算距离
        $fields = "f.id,f.pic,f.name,f.street_id,f.community_id,f.address,f.opening_hours,f.lat,f.lng,f.sort,f.provide_food,s.name as street,c.name as community";
        if (!empty($lng) && !empty($lat)) {
            $distanceFiled = ",ROUND(6378.138 * 2 * ASIN(SQRT(POW(SIN(($lat * PI() / 180 - f.lat * PI() / 180) / 2),2) + COS(23.163292 * PI() / 180)
         * COS(f.lat * PI() / 180) * POW(SIN(($lng * PI() / 180 - f.lng * PI() / 180) / 2),2))) * 1000) AS distance";
            $fields = $fields.$distanceFiled;
        }
        $query = $this->field($fields)
            ->alias('f')
            ->join('street s','s.id = f.street_id','LEFT')
            ->join('community c','c.id = f.community_id','LEFT')
            ->where($where);
        if (!empty($condition)) {
            $query->where(function($query) use ($condition) {
                foreach ($condition as $key => $item) {
                    if ($key == 0) {
                        $query->where($item[0],$item[1],$item[2]);
                    } else {
                        $query->whereOr($item[0],$item[1],$item[2]);
                    }
                }
            });
        }
        if (!empty($lng) && !empty($lat)) {
            $query->order('distance asc');
        }
        return $query->order('sort desc')
            ->paginate($limit,false,['page' => $page])->toArray();
    }

    public function getFoodDetail($id)
    {
        $where = [
            'f.status' => 1,
            'f.is_delete' => 0,
            'f.id' => $id,
        ];

        $field = "d.name as district_name,f.*,s.name as street,c.name as community";
        $food = $this->alias('f')
            ->join('district d','d.id = f.district_id','LEFT')
            ->join('street s','s.id = f.street_id','LEFT')
            ->join('community c','c.id = f.community_id','LEFT')
            ->where($where)->field($field)->find();
        if (empty($food)) {
            return [];
        }
        $food = $food->toArray();
        // 处理电话
        $food['phone'] = !empty($food['short_tel']) ? '4008109969,'.$food['short_tel'] : $food['prefix'].$food['phone2'];
        // 处理早中晚餐
        $food['breakfast'] = [
            'time' => $food['breakfast_time'],
            'price' => $food['breakfast_price'],
            'sub'  => $food['breakfast_sub']
        ];
        $food['lunch'] = [
            'time' => $food['lunch_time'],
            'price' => $food['lunch_price'],
            'sub' => $food['lunch_sub'],
        ];
        $food['dinner'] = [
            'time' => $food['dinner_time'],
            'price' => $food['dinner_price'],
            'sub' => $food['dinner_sub'],
        ];
        return $food;
    }

    public function getCount($cityId = 0,$name = '')
    {
        $where = [
            'city_id' => $cityId
        ];
        $condition = [];
        if (!empty($name)) {
            $condition[] = ['name','like',"%$name%"];
            $condition[] = ['street','like',"%$name%"];
            $condition[] = ['community','like',"%$name%"];
        }
        $query = $this->where($where);
        if (!empty($condition)) {
            $query->where(function($query) use ($condition) {
                foreach ($condition as $key => $item) {
                    if ($key == 0) {
                        $query->where($item[0],$item[1],$item[2]);
                    } else {
                        $query->whereOr($item[0],$item[1],$item[2]);
                    }
                }
            });
        }
        return $query->count();
    }

    public function getCountGroupByDistrict($districtIds = [])
    {
        return $this->field('d.lng,d.lat,d.name,count(*) as count,d.id')
            ->alias('f')
            ->join('district d','d.id = f.district_id','LEFT')
            ->whereIn('district_id',$districtIds)
            ->where('f.is_delete',0)
            ->where('f.status',1)
            ->where('f.lat','<>','')
            ->where('f.lng','<>','')
            ->group('district_id')->select();
    }

    public function getFoodListByPostion($x1,$y1,$x2,$y2,$lng = '',$lat = '')
    {
        $where = [
            'f.lng' => [['>=',$x1],['<=',$x2]],
            'f.lat' => [['>=',$y1],['<=',$y2]]
        ];
        $fields = 'f.*,s.name as street,c.name as community';
        if (!empty($lng) && !empty($lat)) {
            $distanceFiled = ",ROUND(6378.138 * 2 * ASIN(SQRT(POW(SIN(($lat * PI() / 180 - f.lat * PI() / 180) / 2),2) + COS(23.163292 * PI() / 180)
         * COS(f.lat * PI() / 180) * POW(SIN(($lng * PI() / 180 - f.lng * PI() / 180) / 2),2))) * 1000) AS distance";
            $fields = $fields.$distanceFiled;
        }

        return $this->field($fields)
            ->alias('f')
            ->join('district d','d.id = f.district_id','LEFT')
            ->join('street s','s.id = f.street_id','LEFT')
            ->join('community c','c.id = f.community_id','LEFT')
            ->where($where)
            ->where('f.is_delete',0)
            ->where('f.status',1)
            ->where('d.status',1)
            ->select()
            ->toArray();
    }

    /**
     * 获取区域下 助餐点数量
     * @param array $districtIds
     */
    public function getDistrictFoodCount($districtIds = [])
    {
        return $this->field('district_id,count(*) as count')->where(['district_id' => ['in',$districtIds]])
            ->group(' district_id')->select()->toArray();
    }

}