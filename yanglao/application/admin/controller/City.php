<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/18
 * Time: 2:21 PM
 */
namespace app\admin\controller;

use app\model\City as CityModel;
use think\Request;

class City extends AdminBase
{
    public $cityModel;
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->cityModel = new CityModel();
    }

    public function index() {
        $name = $this->request->get('city_name');
        $this->assign('city_name',$name);

        return $this->fetch();
    }

    /**
     * 新增城市界面
     */
    public function add() {
        // 加载所有城市配置
        $this->assign('citys',getAllCity());
        return $this->fetch();
    }

    /**
     * 新建保存城市操作
     */
    public function create() {
        $this->isAjaxRequest();

        $data = $this->request->param();
        $this->dataValidate($data,'city.create');

        $ret = $this->cityModel->save($data);
        if(empty($ret)) {
            return $this->jsonData('create_error');
        }
        return $this->jsonData('create_success',0);
    }

    /**
     * 编辑界面
     * @return mixed
     */
    public function edit() {
        $id = $this->request->get('id');
        $city = $this->cityModel->find($id);
        $data = [
            'citys' => getAllCity(),
            'city'  => $city
        ];
        $this->assign($data);
        return $this->fetch();
    }

    /**
     * 保存操作
     */
    public function update() {
        $this->isAjaxRequest();

        $data = $this->request->param();
        $this->dataValidate($data,'city.update');

        $id = $data['id'];
        unset($data['id']);
        $ret = $this->cityModel->update($data,['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    /**
     * 获取城市列表
     * @return array
     */
    public function cityList() {
        $this->isAjaxRequest();

        $name = input('city_name');
        list($page,$limit) = $this->getPaginateInfo();

        $data = $this->cityModel->cityPageList($name,$page,$limit);
        return $this->jsonData('success',0,$data);
    }

    /**
     * 修改城市状态
     */
    public function status() {
        $id = $this->request->post('id');
        $status = !empty($this->request->post('status')) ? 1 : 0;
        $ret = $this->cityModel->update([
            'status' => $status
        ],['id' => $id]);

        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }
}