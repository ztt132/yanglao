<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2021/1/7
 * Time: 3:05 PM
 */

namespace app\admin\controller;


use think\Request;
use app\model\Estate as EstateModel;
use app\model\City;
use think\Config;
use app\model\Equipment;

class Estate extends AdminBase
{
    public $estateModel;
    public $cityModel;
    public $equipmentModel;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->estateModel = new EstateModel();
        $this->cityModel = new City();
        $this->equipmentModel = new Equipment();
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

    public function estateList()
    {
        $this->isAjaxRequest();

        $name = $this->request->get('name');
        $cityName = $this->request->get('city_name');
        $districtName = $this->request->get('district_name');
        list($page,$limit) = $this->getPaginateInfo();

        $data = $this->estateModel->estatePageList($name,$cityName,$districtName,$page,$limit);
        return $this->jsonData('success',0,$data);
    }

    public function add()
    {
        $cityDistricts = $this->cityModel->getAllCityDistrict();
        // 获取所有设施设备
        $equipments = $this->equipmentModel->getAllEquipment();
        $data = [
            'city_district' => $cityDistricts,
            'form' => Config::get('Estate.form'),
            'enum' => Config::get('Estate.enum'),
            'equipments' => $equipments
        ];
        $this->assign($data);

        return $this->fetch();
    }
    
    public function create()
    {
        $this->isAjaxRequest();
        // 基础验证
        $data = $this->request->param();
        $ret = $this->estateModel->createEstate($data);
        if(empty($ret)) {
            return $this->jsonData('create_error');
        }
        return $this->jsonData('create_success',0);
    }

    public function edit()
    {
        $id = $this->request->get('id');
        $cityDistricts = $this->cityModel->getAllCityDistrict();
        // 获取所有设施设备
        $equipments = $this->equipmentModel->getAllEquipment();
        // 获取养老地产基本信息
        $estate = $this->estateModel->find($id);
        $data = [
            'city_district' => $cityDistricts,
            'form' => Config::get('Estate.form'),
            'enum' => Config::get('Estate.enum'),
            'equipments' => $equipments,
            'estate' => $estate
        ];
        $this->assign($data);

        return $this->fetch();
    }

    public function update()
    {
        $this->isAjaxRequest();
        // 基础验证
        $data = $this->request->param();

        $id = $data['id'];
        unset($data['id']);
        $ret = $this->estateModel->update($data,['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    /**
     * 修改排序
     */
    public function sort() {
        $id = input('id');
        $sort = !empty(input('sort')) ? input('sort') : 0;
        $ret = $this->estateModel->update([
            'sort' => $sort
        ],['id' => $id]);

        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    public function delete()
    {
        $id = input('id');
        $ret = $this->estateModel->update([
            'status' => 0
        ],['id' => $id]);

        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }
}