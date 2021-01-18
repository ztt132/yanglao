<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/29
 * Time: 3:03 PM
 */

namespace app\admin\controller;


use app\model\Equipment as EquipmentModel;
use think\Request;

class Equipment extends AdminBase
{
    public $equipmentModel;

    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->equipmentModel = new EquipmentModel();
    }

    public function add() {
        return $this->fetch();
    }

    public function edit() {
        $id = input('id');
        // 查询photo
        $equipment = $this->equipmentModel->find($id);
        $this->assign('equipment',$equipment);
        return $this->fetch();
    }

    public function create() {
        $this->isAjaxRequest();
        // 基础验证
        $data = $this->request->param();
        $this->dataValidate($data,'equipment.create');

        $ret = $this->equipmentModel->save($data);
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
        $this->dataValidate($data,'equipment.update');

        $id = $data['id'];
        unset($data['id']);
        $ret = $this->equipmentModel->update($data,['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    public function equipmentList() {
        $this->isAjaxRequest();
        $name = input('name');
        list($page,$limit) = $this->getPaginateInfo();
        $data = $this->equipmentModel->equipmentPageList($name,$page,$limit);
        return $this->jsonData('',0,$data);
    }

}