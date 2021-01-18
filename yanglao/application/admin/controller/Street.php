<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/11/10
 * Time: 4:28 PM
 */

namespace app\admin\controller;

use app\model\City;
use app\model\Street as StreetModel;
use think\Request;

class Street extends AdminBase
{
    public $cityModel;
    public $streetModel;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->cityModel = new City();
        $this->streetModel = new StreetModel();
    }

    public function index()
    {
        $cityName = input('city_name');
        $districtName = input('district_name');
        $name = input('name');
        $this->assign([
            'city_name' => $cityName,
            'district_name' => $districtName,
            'name' => $name,
        ]);
        return $this->fetch();
    }

    public function streetList()
    {
        $this->isAjaxRequest();
        list($page,$limit) = $this->getPaginateInfo();
        $names = [
            'city_name' => $this->request->get('city_name'),
            'district_name' => $this->request->get('district_name'),
            'name' => $this->request->get('name'),
        ];

        $data = $this->streetModel->streetPageList($names,$page,$limit);
        return $this->jsonData('success',0,$data);
    }

    public function add()
    {
        // 获取所有城市以及区域
        $cityDistricts = $this->cityModel->getAllCityDistrict();
        $this->assign('city_district',$cityDistricts);
        return $this->fetch();
    }

    public function edit()
    {
        $id = $this->request->get('id');
        // 查询区域
        $street = $this->streetModel->detail($id);
        $this->assign('street',$street);
        return $this->fetch();
    }

    public function create()
    {
        $data = $this->request->param();
        $ret = $this->streetModel->save($data);
        if(empty($ret)) {
            return $this->jsonData('create_error');
        }
        return $this->jsonData('create_success',0);
    }

    public function update()
    {
        $id = input('id');
        $name = input('name');
        $ret = $this->streetModel->update([
            'name' => $name
        ],['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }
}