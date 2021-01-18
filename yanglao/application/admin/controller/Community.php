<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/11/10
 * Time: 4:48 PM
 */

namespace app\admin\controller;

use app\model\City;
use app\model\Community as CommunityModel;
use think\Request;

class Community extends AdminBase
{
    public $cityModel;
    public $communityModel;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->cityModel = new City();
        $this->communityModel = new CommunityModel();
    }

    public function index()
    {
        $cityName = input('city_name');
        $districtName = input('district_name');
        $streetName = input('street_name');
        $name = input('name');
        $this->assign([
            'city_name' => $cityName,
            'district_name' => $districtName,
            'street_name' => $streetName,
            'name' => $name,
        ]);
        return $this->fetch();
    }

    public function communityList()
    {
        $this->isAjaxRequest();
        list($page,$limit) = $this->getPaginateInfo();
        $names = [
            'city_name' => $this->request->get('city_name'),
            'district_name' => $this->request->get('district_name'),
            'name' => $this->request->get('name'),
            'street_name' => $this->request->get('street_name')
        ];

        $data = $this->communityModel->communityPageList($names,$page,$limit);
        return $this->jsonData('success',0,$data);
    }

    public function add()
    {
        // 获取所有城市以及区域
        $cityDistricts = $this->cityModel->getAllCityDistrict(true);
        $this->assign('city_district',$cityDistricts);
        return $this->fetch();
    }

    public function edit()
    {
        $id = $this->request->get('id');
        // 查询区域
        $community = $this->communityModel->detail($id);
        $this->assign('community',$community);
        return $this->fetch();
    }

    public function create()
    {
        $data = $this->request->param();
        $ret = $this->communityModel->save($data);
        if(empty($ret)) {
            return $this->jsonData('create_error');
        }
        return $this->jsonData('create_success',0);
    }

    public function update()
    {
        $id = input('id');
        $name = input('name');
        $ret = $this->communityModel->update([
            'name' => $name
        ],['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }
}