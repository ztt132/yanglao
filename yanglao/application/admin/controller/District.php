<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/18
 * Time: 2:28 PM
 */

namespace app\admin\controller;
use think\Request;
use app\model\City;
use app\model\District as DistrictModel;

class District extends AdminBase
{
    public $districtModel;
    public $cityModel;

    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->districtModel = new DistrictModel();
        $this->cityModel = new City();
    }

    public function index() {
        $cityName = input('city_name');
        $districtName = input('district_name');
        $this->assign([
            'city_name' => $cityName,
            'district_name' => $districtName
        ]);

        return $this->fetch();
    }

    /**
     * 新增界面
     */
    public function add() {
        // 获取所有城市
        $citys = $this->cityModel->allCitys([0,1]);
        $this->assign('citys',$citys);
        return $this->fetch();
    }

    /**
     * 编辑界面
     */
    public function edit() {
        $id = $this->request->get('id');
        // 查询区域
        $district = $this->districtModel->field('id,city_id,name,lat,lng')->find($id);
        // 获取所有城市
        $citys = $this->cityModel->allCitys([0,1]);
        $this->assign([
            'district' => $district,
            'citys' => $citys
        ]);
        return $this->fetch();
    }

    /**
     * 新建操作
     */
    public function create() {
        $this->isAjaxRequest();
        // 基础验证
        $data = $this->request->param();
        $this->dataValidate($data,'district.create');

        $ret = $this->districtModel->save($data);
        if(empty($ret)) {
            return $this->jsonData('create_error');
        }
        return $this->jsonData('create_success',0);
    }

    /**
     * 更新操作
     */
    public function update() {
        $this->isAjaxRequest();
        // 基础验证
        $data = $this->request->param();
        $this->dataValidate($data,'district.update');

        $id = $data['id'];
        unset($data['id']);
        $ret = $this->districtModel->update($data,['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    /**
     * 分页
     * 获取列表
     * @return array
     */
    public function districtList() {
        $this->isAjaxRequest();

        $cityName = $this->request->get('city_name');
        $districtName = $this->request->get('district_name');
        list($page,$limit) = $this->getPaginateInfo();

        $data = $this->districtModel->districtPageList($cityName,$districtName,$page,$limit);
        return $this->jsonData('success',0,$data);
    }

    /**
     * 修改城市状态
     */
    public function status() {
        $id = $this->request->post('id');
        $status = !empty($this->request->post('status')) ? 1 : 0;
        $ret = $this->districtModel->update([
            'status' => $status
        ],['id' => $id]);

        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }
}