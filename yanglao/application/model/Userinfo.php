<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/7/2
 * Time: 2:26 PM
 */

namespace app\model;


use think\Model;

class Userinfo extends Model
{
    /**
     * 获取用户信息
     * @param string $phone
     * @param int $page
     * @param int $limit
     */
    public function getPageUserList($phone = '',$page = 1,$limit = 1) {
        $where = [];
        if (!empty($phone)) {
            $where['phone'] = ['like',"%$phone%"];
        }

        return $this->where($where)
            ->order('id desc')
            ->paginate($limit,false,['page' => $page]);
    }
}