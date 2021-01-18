<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/10/16
 * Time: 4:08 PM
 */

namespace app\admin\controller;


use think\Request;
use app\model\City;
use app\model\Food as FoodModel;

class Food extends AdminBase
{
    public $cityModel;
    public $foodModel;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->cityModel = new City();
        $this->foodModel = new FoodModel();
    }

    public function index()
    {
        $name = input('name');
        $cityName = input('city_name');
        $districtName = input('district_name');
        $this->assign([
            'city_name' => $cityName,
            'name' => $name,
            'district_name' => $districtName
        ]);

        return $this->fetch();
    }

    public function foodList()
    {
        $this->isAjaxRequest();

        $name = $this->request->get('name');
        $cityName = $this->request->get('city_name');
        $districtName = $this->request->get('district_name');
        list($page,$limit) = $this->getPaginateInfo();

        $data = $this->foodModel->foodPageList($name,$cityName,$districtName,$page,$limit);
        return $this->jsonData('success',0,$data);
    }

    public function add()
    {
        // 获取所有城市
        $cityDistricts = $this->cityModel->getAllCityDistrict(true,true);
        // 加载基本配置
        $config = get_page_config('org_page');
        $data = [
            'city_district' => $cityDistricts,
            'config' => $config
        ];
        $this->assign($data);

        return $this->fetch();
    }

    public function edit()
    {
        $id = $this->request->get('id');
        // 查询相信信息
        $food = $this->foodModel->find($id);
        // 获取所有城市
        $cityDistricts = $this->cityModel->getAllCityDistrict(true,true);
        // 加载基本配置
        $config = get_page_config('org_page');
        // 获取所有设施设备
        $data = [
            'city_district' => $cityDistricts,
            'config'        => $config,
            'food'          => $food
        ];

        $this->assign($data);
        return $this->fetch();
    }

    public function create()
    {
        $this->isAjaxRequest();

        $data = $this->request->param();
        $this->dataValidate($data,'food.create');

        $ret = $this->foodModel->createFood($data);
        if(empty($ret)) {
            return $this->jsonData('create_error');
        }
        return $this->jsonData('create_success',0);
    }

    public function update()
    {
        // 验证是否为ajax请求
        $this->isAjaxRequest();
        // 数据验证
        $data = $this->request->param();
        $this->dataValidate($data,'food.update');

        $id = $data['id'];
        unset($data['id']);
        $ret = $this->foodModel->update($data,['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    public function delete()
    {
        // 验证是否为ajax请求
        $this->isAjaxRequest();
        // 数据验证
        $id = $this->request->param('id');
        $ret = $this->foodModel->update(['is_delete' => 1],['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    /**
     * 修改状态
     * @return \think\response\Json
     */
    public function status()
    {
        // 验证是否为ajax请求
        $this->isAjaxRequest();
        // 数据验证
        $id = $this->request->param('id');
        $status = $this->request->param('status');
        $ret = $this->foodModel->update(['status' => $status],['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    public function sort()
    {
        $id = input('id');
        $sort = !empty(input('sort')) ? input('sort') : 0;
        $ret = $this->foodModel->update([
            'sort' => $sort
        ],['id' => $id]);

        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }
}