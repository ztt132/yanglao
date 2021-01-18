<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/29
 * Time: 3:04 PM
 */

namespace app\model;


use think\Model;

class Equipment extends Model
{
    protected $autoWriteTimestamp = true;

    public function getAllEquipment() {
        return $this->field('id,name,pic')->order('sort','desc')->select()->toArray();
    }

    public function equipmentPageList($name = '',$page = 1,$limit = 10) {
        $where = [];
        if (!empty($name)) {
            $where['name'] = ['like',"%$name%"];
        }

        return $this->field('id,name,sort,pic')
            ->where($where)->order('id desc')
            ->paginate($limit,false,['page' => $page])->toArray();
    }
}