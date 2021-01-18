<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/28
 * Time: 1:00 PM
 */

namespace app\model;


use think\Model;

class Activity extends Model
{
    protected $dateFormat = 'Y-m-d';

    protected $autoWriteTimestamp = true;

    protected $type = [
        'create_time' => 'timestamp'
    ];

    public function getActivitys($cityId = 0,$type = 1 , $limit = 5) {
        $activitys = $this->field('a.title,a.id,o.name,o.id as org_id')
            ->alias('a')
            ->join('org o','o.id = a.org_id')
            ->where('o.city_id',$cityId)
            ->where('a.status',1)
            ->order('a.sort desc')
            ->select()->toArray();
        if (!empty($activitys)) {
            $orgIds = array_column($activitys,'org_id');
            // 查询活动图片
            $photoModel = new Photo();
            $photos = $photoModel->getPhotoListByOrgIds($orgIds);
            $photoMapping = [];
            if (!empty($photos)){
                foreach ($photos as $photo) {
                    // 如果此时mapping中无此org，设置图片
                    if (!array_key_exists($photo['org_id'],$photoMapping)) {
                        $photoMapping[$photo['org_id']] = $photo['pic'];
                    } else {
                        // 判断是否需要替换
                        if ($photo['is_cover']) {
                            $photoMapping[$photo['org_id']] = $photo['pic'];
                        }
                    }
                }
            }
            foreach ($activitys as &$a) {
                $a['pic'] = array_key_exists($a['org_id'],$photoMapping) ? $photoMapping[$a['org_id']] : '';
            }
        }

        return $activitys;
    }

    public function getActivitysByOrgId($orgId,$type = 1){
        $where = [
            'org_id' => $orgId,
            'type' => $type
        ];
        return $this->where($where)->field('id,title')->select()->toArray();
    }

    public function getActivitysByCity($cityId,$type = 1) {
        $where = [
            'o.city_id' => $cityId,
            'a.type'    => $type,
            'a.status'  => 1
        ];
        return $this->field('a.id,a.title,a.price,o.name')
            ->alias('a')
            ->join('org o','o.id = a.org_id','LEFT')
            ->where($where)
            ->order('a.id desc')
            ->select()->toArray();
    }

    public function getActivityDetail($id) {
        $activity = $this->field('a.id,a.title,a.price,o.name,a.content,o.city_id')
            ->alias('a')
            ->join('org o','o.id = a.org_id','LEFT')
            ->where('a.id',$id)->find();
        return !empty($activity) ? $activity->toArray() : [];
    }

    public function activityPageList($title = '',$page = 1,$limit = 10) {
        $where = [];
        if (!empty($title)) {
            $where['title'] = ['like',"%$title%"];
        }

        return $this->field('a.title,a.create_time,a.id,o.name as org_name,a.type,a.sort,a.status')
            ->alias('a')
            ->join('org o','o.id = a.org_id','LEFT')
            ->where($where)->order('a.id desc')
            ->paginate($limit,false,['page' => $page])->toArray();
    }
}