<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/28
 * Time: 2:20 PM
 */

namespace app\admin\controller;


use think\Config;
use app\admin\model\Role as RoleModel;
use think\Request;

class Role extends AdminBase
{
    public $roleModel;
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->roleModel = new RoleModel();
    }

    public function index() {
        return $this->fetch();
    }

    public function add() {
        // 加载所有菜单
        $menu = Config::get('Menu');
        $this->assign('menu',$menu);
        return $this->fetch();
    }

    public function create() {
        $this->isAjaxRequest();

        $data = $this->request->param();
        $this->dataValidate($data,'role.create');

        $ret = $this->roleModel->save($data);
        if(empty($ret)) {
            return $this->jsonData('create_error');
        }
        return $this->jsonData('create_success',0);
    }

    public function edit() {
        $id = $this->request->get('id');
        // 加载所有菜单
        $menu = Config::get('Menu');
        $role = $this->roleModel->field('id,name,menus')->find($id);
        $data = [
            'menu' => $menu,
            'role' => $role
        ];
        $this->assign($data);
        return $this->fetch();
    }

    public function update() {
        $this->isAjaxRequest();

        $data = $this->request->param();
        $this->dataValidate($data,'role.update');

        $id = $data['id'];
        unset($data['id']);
        $ret = $this->roleModel->update($data,['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    public function roleList() {
        $this->isAjaxRequest();
        list($page,$limit) = $this->getPaginateInfo();

        $data = $this->roleModel->getRolePageList($page,$limit);
        return $this->jsonData('success',0,$data);
    }
}