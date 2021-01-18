<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2021/1/11
 * Time: 3:58 PM
 */

namespace app\admin\controller;

use app\model\Estate;
use app\model\Estatenews as EstatenewsModel;
use think\Request;

class Estatenews extends AdminBase
{
    public $cityModel;
    public $estateModel;
    public $estateEstatenewsModel;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->estateModel = new Estate();
        $this->estateEstatenewsModel = new EstatenewsModel();
    }

    public function newsList()
    {
        $this->isAjaxRequest();

        $name = input('name');
        list($page,$limit) = $this->getPaginateInfo();
        $data = $this->estateEstatenewsModel->newsPageList($name,$page,$limit);

        return $this->jsonData('',0,$data);
    }

    public function add()
    {
        // 获取所有楼盘
        $estates = $this->estateModel->getAllEstatesAndCity();
        $this->assign([
            'estates' => $estates
        ]);
        return $this->fetch();
    }

    public function create()
    {
        $this->isAjaxRequest();
        $data = $this->request->param();

        $ret = $this->estateEstatenewsModel->save($data);
        if(empty($ret)) {
            return $this->jsonData('create_error');
        }
        return $this->jsonData('create_success',0);
    }

    public function edit()
    {
        $id = input('id');
        $news = $this->estateEstatenewsModel->find($id);
        $estates = $this->estateModel->getAllEstatesAndCity();
        // 获取所有城市
        $data = [
            'news'    => $news,
            'estates' => $estates
        ];

        $this->assign($data);
        return $this->fetch();
    }

    public function update()
    {
        // 验证是否为ajax请求
        $this->isAjaxRequest();
        // 数据验证
        $data = $this->request->param();

        $id = $data['id'];
        unset($data['id']);
        $ret = $this->estateEstatenewsModel->update($data,['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    public function delete() {
        // 验证是否为ajax请求
        $this->isAjaxRequest();
        // 数据验证
        $id = $this->request->param('id');
        $ret = $this->estateEstatenewsModel->update(['status' => 0],['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }
}