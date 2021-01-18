<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/28
 * Time: 10:11 AM
 */

namespace app\model;


use think\Model;

class News extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $autoWriteTimestamp = true;

    protected $type = [
        'create_time' => 'timestamp'
    ];

    /**
     * 获取咨询数量
     * @param string $title
     * @param int $type
     */
    public function getCount($cityId = 0,$title = '',$type = 0) {
        $cityIdArr = !empty($cityId) ? [$cityId,0] : [$cityId];
        $where = [
            'city_id' => ['in',$cityIdArr],
            'type'    => $type
        ];
        if (!empty($title)) {
            $where['title'] = ['like',"%$title%"];
        }
        return $this->where($where)->count();
    }

    /**
     * 根据条件获取指定数量的咨询（专题，政策）
     */
    public function getNews($cityId = 0,$type = 1,$limit = 5) {
        $cityIdArr = !empty($cityId) ? [$cityId,0] : [$cityId];
        $where = [
            'city_id' => ['in',$cityIdArr],
            'type'    => $type,
            'status'  => 1
        ];

        return $this->field('id,title')
            ->where($where)->order('id','desc')->limit($limit)->select()->toArray();
    }

    /**
     * 获取咨询列表
     * @param string $titile
     * @param int $page
     * @param int $limit
     */
    public function getNewsList($title = '',$type = 0,$cityId = 0,$page = 1,$limit = 10) {
        $where = [
            'type' => $type,
            'city_id' => ['in',[$cityId,0]],
            'n.status' => 1
        ];
        if (!empty($title)) {
            $where['title'] = ['like',"%$title%"];
        }

        return $this->field('n.id,n.title,n.type,n.create_time,n.publisher,c.city_name as city,n.pic')
            ->alias('n')
            ->join('city c','c.id = n.city_id','LEFT')
            ->where($where)->order('n.id desc')
            ->paginate($limit,false,['page' => $page])->toArray();
    }

    public function newsPageList($title = '',$page = 1,$limit = 10) {
        $where = ['n.status' => 1];
        if (!empty($title)) {
            $where['title'] = ['like',"%$title%"];
        }

        return $this->field('n.id,n.title,n.type,n.create_time,n.publisher,c.city_name as city,c.id as city_id')
            ->alias('n')
            ->join('city c','c.id = n.city_id','LEFT')
            ->where($where)->order('n.id desc')
            ->paginate($limit,false,['page' => $page])->toArray();
    }
}