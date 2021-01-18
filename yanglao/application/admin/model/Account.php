<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/17
 * Time: 5:02 PM
 */

namespace app\admin\model;

use think\Model;

class Account extends Model
{
    protected $autoWriteTimestamp = true;

    protected static function init()
    {
        // 密码md5
        Account::event('before_insert', function ($account) {
            if (!empty($account->password)) {
                $account->password = md5($account->password);
            }
        });

        Account::event('before_update', function ($account) {
            if (!empty($account->password)) {
                $account->password = md5($account->password);
            }
        });
    }

    public function findUser($accountName = '',$password = '') {
        $account = $this->where('account_name',$accountName)
            ->where('password',md5($password))
            ->field('id,user_name,account_name,role_id')
            ->find();

        return !empty($account) ? $account->toArray() : [];
    }

    public function accountPageList($page = 1,$limit = 10) {
        return $this->field('a.id,a.account_name,a.user_name,r.name as role_name')
            ->alias('a')
            ->join('role r','r.id = a.role_id','LEFT')
            ->order('id asc')
            ->paginate($limit,false,['page' => $page]);
    }

}