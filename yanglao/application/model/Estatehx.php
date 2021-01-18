<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2021/1/11
 * Time: 6:23 PM
 */

namespace app\model;


use think\Model;

class Estatehx extends Model
{
    protected $autoWriteTimestamp = true;

    protected $type = [
        'pics' => 'json'
    ];

    public function hxPageList($name = '',$page = 1,$limit = 10) {
        $where = [];
        if (!empty($name)) {
            $where['e.name'] = ['like',"%$name%"];
        }

        return $this->field('h.id,h.pics,h.name,e.name as estate_name')
            ->alias('h')
            ->join('estate e','e.id = h.estate_id','LEFT')
            ->where($where)->order('h.id desc')
            ->paginate($limit,false,['page' => $page])->toArray();
    }

    public function getVrHxListByEstateIds($estateIds = []) {
        $where = [
            'estate_id' => ['in',$estateIds],
            'vr' => ["<>",""]
        ];
        return $this->where($where)->select()->toArray();
    }

    /**
     * 获取地产下所有户型
     * @param int $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getHxListByEstateId($id = 0)
    {
        $hxs = $this->where('estate_id',$id)->field('id,name,vr,desc,pics,shi,ting,wei,area')
            ->order('id','desc')->select()->toArray();
        if (empty($hxs)) {
            return [];
        }

        $data = [];
        if (!empty($hxs)) {
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
}