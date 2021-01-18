<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/24
 * Time: 11:19 AM
 */

namespace app\model;


use think\Model;

class Hx extends Model
{
    protected $autoWriteTimestamp = true;

    public function getVrHxListByOrgIds($orgIds = []) {
        $where = [
            'org_id' => ['in',$orgIds],
            'vr' => ["<>",""]
        ];
        return $this->where($where)->select()->toArray();
    }

    /**
     * 返回机构下所有户型图，优先存在vr的
     * @param int $orgId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getHxListByOrgId($orgId = 0) {
        $hxs = $this->where('org_id',$orgId)->field('id,name,cover_pic,vr,desc')
            ->order('id','desc')->select()->toArray();
        if (empty($hxs)) {
            return [];
        }

        $data = [];
        if (!empty($hxs)) {
//            array_multisort(array_column($hxs,'vr'),SORT_DESC,$hxs);
            $vrHx = $noVrHX = [];
            foreach ($hxs as $hx) {
                if (!empty($hx['vr'])) {
                    $vrHx[] = $hx;
                } else {
                    $noVrHX[] = $hx;
                }
            }
            $data = array_merge($vrHx,$noVrHX);
        }
        return $data;
    }

    public function hxPageList($name = '',$page = 1,$limit = 10) {
        $where = [];
        if (!empty($name)) {
            $where['o.name'] = ['like',"%$name%"];
        }

        return $this->field('h.id,h.cover_pic,h.name,o.name as org_name')
            ->alias('h')
            ->join('org o','o.id = h.org_id','LEFT')
            ->where($where)->order('h.id desc')
            ->paginate($limit,false,['page' => $page])->toArray();
    }
}