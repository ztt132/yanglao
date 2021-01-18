<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/24
 * Time: 2:47 PM
 */

namespace app\model;


use think\Model;

class Banner extends Model
{
    protected $autoWriteTimestamp = true;

    protected $dateFormat = 'Y-m-d';

    protected $type = [
        'deadline' => 'timestamp'
    ];

    /**
     * 获取banner
     * @param string $title
     * @param int $page
     * @param int $limit
     */
    public function getBannerList($cityId = 0,$title = '',$position = 1,$page = 1,$limit = 10) {
        // banner需要追加全国数据 city_id = 0
        $cityIds = [$cityId,0];
        $where = [
            'city_id'  => ['in',$cityIds],
            'deadline' => ['>=',time() - 3600 * 24],
            'position' => $position,
            'status'   => 1
        ];
        if (!empty($title)) {
            $where['title'] = ['like',"%$title%"];
        }

        return $this->field('id,title,position,link_type,link_url,deadline,pic,sort')
            ->where($where)->order('sort desc')->order('id desc')
            ->paginate($limit,false,['page' => $page])->toArray();
    }

    public function bannerPageList($title = '',$page = 1,$limit = 10) {
        $where = [];
        if (!empty($title)) {
            $where['title'] = ['like',"%$title%"];
        }
        $where['status'] = 1;

        return $this->field('id,title,position,link_type,deadline,sort')
            ->where($where)->order('id desc')
            ->paginate($limit,false,['page' => $page])->toArray();
    }
}