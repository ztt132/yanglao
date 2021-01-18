<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/7/2
 * Time: 2:29 PM
 */

namespace app\admin\controller;


use think\Request;
use app\model\Userinfo as UserinfoModel;

class Userinfo extends AdminBase
{
    public $userinfoModel;
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->userinfoModel = new UserinfoModel();

    }

    public function index() {
        $phone = input('phone');
        $this->assign('phone',$phone);
        return $this->fetch();
    }

    public function userinfoList() {
        $this->isAjaxRequest();
        $phone = input('phone');
        list($page,$limit) = $this->getPaginateInfo();
        $data = $this->userinfoModel->getPageUserList($phone,$page,$limit);

        return $this->jsonData('success',0,$data);
    }
}