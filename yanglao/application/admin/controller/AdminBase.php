<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/17
 * Time: 3:54 PM
 */
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Session;

class AdminBase extends Controller
{
    /**
     * @var用户信息
     */
    private $account;

//    const ADMIN_ACCOUNT_NAME = 'admin';

    public function __construct(Request $request = null)
    {
        $this->checkLogin();
        parent::__construct($request);
        // 非admin账户需要验证权限 7.17改为根据roleid判断
        if (array_get($this->account,'role_id') != 0) {
            $this->checkAuthorization();
        }
    }

    public function getAccount()
    {
        return $this->account;
    }

    public function index() {
        $name = $this->request->get('name');
        $this->assign('name',$name);

        return $this->fetch();
    }

    /**
     * 检查登录状态，如果存在session则赋值
     * 不存在跳登录页
     */
    private function checkLogin() {
        if (!Session::has("account_info")) {
            $this->redirect('/admin/login');
        }
        $this->account = Session::get('account_info');
    }

    /**
     * 检测用户是否有模块权限
     */
    private function checkAuthorization() {
        $controller = strtolower($this->request->controller());
        $menus = array_get($this->getAccount(),'menus');
        // 追加index
        $menus[] = 'index';
        $menus[] = 'file';
        if (!in_array($controller,$menus)) {
            $this->redirect('/index');
        }
    }

    /**
     * 分页请求获取页码以及数量
     * @return array
     */
    public function getPaginateInfo() {
        $page = input('page',1);
        $limit = input('limit',10);

        return [$page,$limit];
    }

    /**
     * 部分接口为ajax请求，如果非ajax请求则过滤
     */
    public function isAjaxRequest(){
        if (!$this->request->isAjax()) {
            toJson(returnDataFormat('request_error'));
        }
    }

    /**
     * 数据验证
     * 验证不通过，直接输出
     */
    public function dataValidate($data = [],$validate = '') {
        $validateResult = $this->validate($data,$validate);
        if ($validateResult !== true) {
            toJson(returnDataFormat($validateResult));
        }
    }

    /**
     * @param string $msg
     * @param $code
     * @param array $data
     * @return \think\response\Json
     */
    public function jsonData($msg = '',$code = 1,$data = []) {
        return json(returnDataFormat( $msg,$code,$data));
    }
}