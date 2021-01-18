<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;
use app\admin\model\Account;
use app\admin\model\Role;

/**
 * @description 登陆相关
 * Class Login
 * @package app\admin\controller
 */
class Login extends Controller
{
    private $accountModel;
    private $roleModel;
    public function _initialize()
    {
        $this->accountModel = new Account();
        $this->roleModel = new Role();
    }

    /**
     * 登陆界面
     * @return mixed
     */
    public function index() {
        return $this->fetch();
    }

    /**
     * 执行登陆http://fqxcxxh.house365.com/admin/login
     * @param array $params
     */
    public function doLogin() {
        if (!$this->request->isAjax()) {
            return json(returnDataFormat('request_error'));
        }
        $accountName = $this->request->param('account_name');
        $password = $this->request->param('password');

        $account = $this->accountModel->findUser($accountName,$password);
        if (empty($account)) {
            return json(returnDataFormat('login_account_error'));
        }
        // 查询用户权限
        if ($accountName != 'admin') {
            $role = $this->roleModel->find($account['role_id']);
            $account['menus'] = $role['menus'];
        }
        // 登录成功设置Session
        Session::set("account_info",$account);
        return json(returnDataFormat('login_success',0));
    }

    /**
     * 退出登陆
     */
    public function loginOut() {
        Session::delete("account_info");
        $this->redirect('/admin/login');
    }
}