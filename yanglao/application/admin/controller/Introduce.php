<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/29
 * Time: 2:07 PM
 */

namespace app\admin\controller;

use think\Request;
use app\model\Org;
use app\model\Introduce as IntroduceModel;

class Introduce extends AdminBase
{
    public $introduceModel;
    public $orgModel;

    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->introduceModel = new IntroduceModel();
        $this->orgModel = new Org();
    }

    public function add() {
        // 查询所有机构
        $orgs = $this->orgModel->allOrgs();
        $this->assign('orgs',$orgs);
        return $this->fetch();
    }

    public function edit() {
        $id = $this->request->get('id');
        $introduce = $this->introduceModel->find($id);
        // 查询所有机构
        $orgs = $this->orgModel->allOrgs();
        $data = [
            'orgs' => $orgs,
            'introduce' => $introduce
        ];
        $this->assign($data);
        return $this->fetch();
    }

    public function create() {
        $this->isAjaxRequest();

        $data = $this->request->param();
        $this->dataValidate($data,'introduce.create');

        $ret = $this->introduceModel->save($data);
        if(empty($ret)) {
            return $this->jsonData('create_error');
        }
        return $this->jsonData('create_success',0);
    }

    public function update() {
        $this->isAjaxRequest();
        // 基础验证
        $data = $this->request->param();
        $this->dataValidate($data,'introduce.update');

        $id = $data['id'];
        unset($data['id']);
        $ret = $this->introduceModel->update($data,['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    public function introduceList() {
        $this->isAjaxRequest();
        $orgName = input('name');
        list($page,$limit) = $this->getPaginateInfo();
        $data = $this->introduceModel->introducePageList($orgName,$page,$limit);
        return $this->jsonData('',0,$data);
    }
}