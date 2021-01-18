<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/7/2
 * Time: 3:44 PM
 */

namespace app\model;
use think\Model;

class Collection extends Model
{
    protected $autoWriteTimestamp = true;

    public function getCollectionsByUserId($openId = '') {
        $collections = $this->where('open_id',$openId)
            ->order('id desc')
            ->select()
            ->toArray();
        return $collections;
    }
}