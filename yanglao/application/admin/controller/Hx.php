<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/24
 * Time: 11:19 AM
 */

namespace app\admin\controller;
use think\Request;
use app\model\Hx as HxModel;
use app\model\Org;

class Hx extends AdminBase
{
    private $orgModel;
    private $hxModel;
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->orgModel = new Org();
        $this->hxModel = new HxModel();
    }

    public function add() {
        // 查询所有机构
        $orgs = $this->orgModel->allOrgs();
        $this->assign('orgs',$orgs);
        return $this->fetch();
    }

    public function edit() {
        $id = $this->request->get('id');
        // 查询所有机构
        $orgs = $this->orgModel->allOrgs();
        // 查询photo
        $hx = $this->hxModel->find($id);
        $data = [
            'orgs' => $orgs,
            'hx'   => $hx
        ];
        $this->assign($data);
        return $this->fetch();
    }

    public function hxList() {
        $this->isAjaxRequest();
        $orgName = input('name');
        list($page,$limit) = $this->getPaginateInfo();
        $data = $this->hxModel->hxPageList($orgName,$page,$limit);
        return $this->jsonData('',0,$data);
    }

    public function create() {
        $this->isAjaxRequest();
        $data = $this->request->param();
        // 数据验证
        $this->dataValidate($data,'hx.create');
        $ret = $this->hxModel->save($data);
        if(empty($ret)) {
            return $this->jsonData('create_error');
        }
        return $this->jsonData('create_success',0);
    }

    public function update() {
        // 验证是否为ajax请求
        $this->isAjaxRequest();
        // 数据验证
        $data = $this->request->param();
        $this->dataValidate($data,'hx.update');

        $id = $data['id'];
        unset($data['id']);
        $ret = $this->hxModel->update($data,['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }
}