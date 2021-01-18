<?php
namespace app\admin\controller;
use think\Config;
use think\Request;

class Index extends AdminBase
{
    public function __construct(Request $request = null) {
        parent::__construct($request);
    }


    public function _initialize() {}

    public function index() {
        $this->assign('account',$this->getAccount());
        // 获取菜单
        $menus = Config::get('Menu');
        $this->assign('menus',$menus);
        return $this->fetch();
    }
}