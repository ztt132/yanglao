<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/7/2
 * Time: 3:37 PM
 */

namespace app\model;


use think\Model;

class Feedback extends Model
{
    protected $autoWriteTimestamp = true;

    /**
     * 分页
     * 查询意见反馈
     */
    public function feedbackPageList($phone = '',$page = 1,$limit = 10) {
        $where = [];
        if (!empty($phone)) {
            $where['phone'] = ['like',"%$phone%"];
        }

        return $this->where($where)
            ->order('id desc')
            ->paginate($limit,false,['page' => $page]);
    }
}