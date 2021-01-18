<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/28
 * Time: 1:44 PM
 */

namespace app\admin\controller;


use app\admin\model\Role;
use app\admin\model\Account as AccountModel;
use think\Request;

class Account extends AdminBase
{
    private $accountModel;
    private $roleModel;
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->accountModel = new AccountModel();
        $this->roleModel = new Role();
    }

    public function index() {
        return $this->fetch();
    }

    public function add() {
        // 查询所有权限
        $roles = $this->roleModel->allRoles();
        $this->assign('roles',$roles);
        return $this->fetch();
    }

    public function create() {
        $this->isAjaxRequest();

        $data = $this->request->param();
        $this->dataValidate($data,'account.create');

        $ret = $this->accountModel->create($data);
        if(empty($ret)) {
            return $this->jsonData('create_error');
        }
        return $this->jsonData('create_success',0);
    }

    public function edit() {
        $id = $this->request->get('id');
        $account = $this->accountModel->find($id);
        // 查询所有权限
        $roles = $this->roleModel->allRoles();
        $data = [
            'roles' => $roles,
            'account' => $account
        ];
        $this->assign($data);
        return $this->fetch();
    }

    public function editPwd() {
        $id = $this->request->get('id');
        $account = $this->accountModel->find($id);
        // 查询所有权限
        $roles = $this->roleModel->allRoles();
        $data = [
            'roles' => $roles,
            'account' => $account
        ];
        $this->assign($data);
        return $this->fetch();
    }

    public function update() {
        $this->isAjaxRequest();

        $data = $this->request->param();
        $this->dataValidate($data,'account.update');

        $id = $data['id'];
        unset($data['id']);
        $ret = $this->accountModel->update($data,['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    public function updatePwd() {
        $this->isAjaxRequest();

        $data = $this->request->param();
        $this->dataValidate($data,'account.update');
        // 验证旧密码
        $id = input('id');
        $oldPassword = input('old_password');
        $account = $this->accountModel->find($id);
        if (!$account) {
            return $this->jsonData('data_error');
        }
        if (md5($oldPassword) != $account->password) {
            return $this->jsonData('password_error');
        }
        $password = input('password');
        if (md5($password) == $account->password) {
            return $this->jsonData('password_repeat');
        }

        $ret = $this->accountModel->update(['password' => $password],['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    public function accountList() {
        $this->isAjaxRequest();

        list($page,$limit) = $this->getPaginateInfo();

        $data = $this->accountModel->accountPageList($page,$limit);
        return $this->jsonData('success',0,$data);
    }
}