<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/28
 * Time: 2:20 PM
 */

namespace app\admin\model;


use think\Model;

class Role extends Model
{
    protected $autoWriteTimestamp = true;

    protected $type = [
        'menus' => 'json'
    ];

    protected static function init()
    {
        Role::event('before_insert', function ($role) {
            if (empty($role->menus)) {
                $role->menus = [];
            }
        });

        Role::event('before_update', function ($role) {
            if (empty($role->menus)) {
                $role->menus = [];
            }
        });
    }

    public function getRolePageList($page = 1,$limit = 10) {
        return $this->field('id,name')
            ->order('id asc')
            ->paginate($limit,false,['page' => $page]);
    }

    public function allRoles() {
        return $this->field('id,name')->select()->toArray();
    }
}