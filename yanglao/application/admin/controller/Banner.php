<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/24
 * Time: 2:46 PM
 */

namespace app\admin\controller;

use think\Config;
use think\Request;
use app\model\Banner as BannerModel;
use app\model\City;

class Banner extends AdminBase
{
    private $bannerModel;
    private $cityModel;

    /**
     * banner constructor
     */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->bannerModel = new BannerModel();
        $this->cityModel = new City();
    }

    public function index()
    {
        $title = $this->request->get('title');
        $this->assign('title',$title);

        return $this->fetch();
    }

    public function add() {
        $config = Config::get('Enum.banner');
        // 获取所有城市同时追加配置中的城市
        $citys = array_merge($config['more_city'],$this->cityModel->allCitys());
        $data = [
            'config' => $config,
            'citys'  => $citys
        ];

        $this->assign($data);
        return $this->fetch();
    }

    public function create() {
        $this->isAjaxRequest();

        $data = $this->request->param();
        $this->dataValidate($data,'banner.create');

        $ret = $this->bannerModel->save($data);
        if(empty($ret)) {
            return $this->jsonData('create_error');
        }
        return $this->jsonData('create_success',0);
    }

    public function edit() {
        $id = $this->request->get('id');
        // 查询photo
        $banner = $this->bannerModel->find($id);
        $config = Config::get('Enum.banner');
        // 获取所有城市同时追加配置中的城市
        $citys = array_merge($config['more_city'],$this->cityModel->allCitys());
        $data = [
            'banner' => $banner,
            'config' => $config,
            'citys'  => $citys
        ];
        $this->assign($data);
        return $this->fetch();
    }


    public function update() {
        // 验证是否为ajax请求
        $this->isAjaxRequest();
        // 数据验证
        $data = $this->request->param();
        $this->dataValidate($data,'banner.update');

        $id = $data['id'];
        unset($data['id']);
        $ret = $this->bannerModel->update($data,['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    public function bannerList() {
        $this->isAjaxRequest();

        $orgName = input('title');
        list($page,$limit) = $this->getPaginateInfo();
        $data = $this->bannerModel->bannerPageList($orgName,$page,$limit);
        // 数据处理
        if (!empty($data['data'])) {
            $config = Config::get('Enum.banner');
            foreach ($data['data'] as &$value) {
                $value['position'] = $config['position'][$value['position']];
                $value['link_type'] = $config['link_type'][$value['link_type']];
            }
        }
        return $this->jsonData('',0,$data);
    }

    public function delete() {
        // 验证是否为ajax请求
        $this->isAjaxRequest();
        // 数据验证
        $id = $this->request->param('id');
        $ret = $this->bannerModel->update(['status' => 0],['id' => $id]);
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
        $ret = $this->bannerModel->update([
            'sort' => $sort
        ],['id' => $id]);

        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }
}