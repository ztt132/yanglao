<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2021/1/11
 * Time: 6:21 PM
 */

namespace app\admin\controller;

use app\model\Estatehx as EstatehxModel;
use app\model\Estate;
use think\Request;
use think\Config;

class Estatehx extends AdminBase
{
    private $estatehxModel;
    private $estateModel;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->estatehxModel = new EstatehxModel();
        $this->estateModel = new Estate();
    }

    public function add()
    {
        // 获取所有楼盘
        $estates = $this->estateModel->getAllEstatesAndCity();
        // 加载配置
        $hxConfig = Config::get('Enum.estate_hx');
        $this->assign([
            'estates' => $estates,
            'hx_config' => $hxConfig
        ]);
        return $this->fetch();
    }

    public function edit() {
        $id = $this->request->get('id');
        // 获取所有楼盘
        $estates = $this->estateModel->getAllEstatesAndCity();
        // 查询photo
        $hx = $this->estatehxModel->find($id);
        // 加载配置
        $hxConfig = Config::get('Enum.estate_hx');
        $data = [
            'estates' => $estates,
            'hx'   => $hx,
            'hx_config' => $hxConfig
        ];
        $this->assign($data);
        return $this->fetch();
    }

    public function create()
    {
        $this->isAjaxRequest();
        $data = $this->request->param();
        // 数据验证
        $ret = $this->estatehxModel->save($data);
        if(empty($ret)) {
            return $this->jsonData('create_error');
        }
        return $this->jsonData('create_success',0);
    }

    public function hxList() {
        $this->isAjaxRequest();
        $orgName = input('name');
        list($page,$limit) = $this->getPaginateInfo();
        $data = $this->estatehxModel->hxPageList($orgName,$page,$limit);
        return $this->jsonData('',0,$data);
    }

    public function update() {
        // 验证是否为ajax请求
        $this->isAjaxRequest();
        // 数据验证
        $data = $this->request->param();

        $id = $data['id'];
        unset($data['id']);
        $ret = $this->estatehxModel->update($data,['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }
}