<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/29
 * Time: 2:07 PM
 */

namespace app\model;


use think\Model;

class Introduce extends Model
{
    protected $autoWriteTimestamp = true;

    public function getByOrgId($orgId) {
        $introduce = $this->field('id,dean_name,dean_desc,pic,content')
            ->where('org_id',$orgId)->find();
        return !empty($introduce) ? $introduce->toArray() : [];
    }

    public function introducePageList($orgName = '',$page = 1,$limit = 10) {
        $where = [];
        if (!empty($orgName)) {
            $where['o.name'] = ['like',"%$orgName%"];
        }

        return $this->field('i.id,i.pic,i.dean_name,o.name as org_name')
            ->alias('i')
            ->join('org o','o.id = i.org_id','LEFT')
            ->where($where)->order('i.id desc')
            ->paginate($limit,false,['page' => $page])->toArray();
    }

    public function findByOrgId($orgId = 0) {
        $introduce = $this->where('org_id',$orgId)->find();
        return !empty($introduce) ? $introduce->toArray() : [];
    }
}