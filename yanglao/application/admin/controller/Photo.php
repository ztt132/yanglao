<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/19
 * Time: 1:23 PM
 */

namespace app\admin\controller;

use think\Config;
use app\model\Photo as PhotoModel;
use app\model\Org;
use think\Request;


class Photo extends AdminBase
{
    private $orgModel;
    private $photoModel;

    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->orgModel = new Org();
        $this->photoModel = new PhotoModel();
    }

    public function add() {
        // 查询所有机构
        $orgs = $this->orgModel->allOrgs();
        // 加载配置
        $config = Config::get('Enum.photo');
        $data = [
            'orgs' => $orgs,
            'config' => $config
        ];

        $this->assign($data);
        return $this->fetch();
    }

    public function create() {
        // 验证是否为ajax请求
        $this->isAjaxRequest();
        $data = $this->request->param();
        // 数据验证
        $this->dataValidate($data,'photo.create');
        // TODO 2020-7-27改为多图，生成多个相册
        if (!empty($data['pic'])) {
            foreach ($data['pic'] as $pic) {
                $this->photoModel->insert([
                    'org_id' => $data['org_id'],
                    'photo_type' => $data['photo_type'],
                    'pic' => $pic
                ]);
            }
        }

        return $this->jsonData('create_success',0);
    }

    public function photoList() {
        $this->isAjaxRequest();

        $orgName = $this->request->request('name');
        list($page,$limit) = $this->getPaginateInfo();
        $data = $this->photoModel->photoPageList($orgName,$page,$limit);
        // 数据处理
        if (!empty($data['data'])) {
            $config = Config::get('Enum.photo');
            $photoTypeConfig = $config['photo_type'];
            foreach ($data['data'] as &$value) {
                $value['photo_type'] = $photoTypeConfig[$value['photo_type']];
            }
        }
        return $this->jsonData('',0,$data);
    }

    public function edit() {
        $id = $this->request->get('id');
        // 查询所有机构
        $orgs = $this->orgModel->allOrgs();
        // 加载配置
        $config = Config::get('Enum.photo');
        // 查询photo
        $photo = $this->photoModel->find($id);
        $data = [
            'orgs' => $orgs,
            'config' => $config,
            'photo' => $photo
        ];

        $this->assign($data);
        return $this->fetch();
    }

    public function update() {
        // 验证是否为ajax请求
        $this->isAjaxRequest();
        // 数据验证
        $data = $this->request->param();
        $this->dataValidate($data,'photo.update');

        $id = $data['id'];
        unset($data['id']);
        $ret = $this->photoModel->update($data,['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    /**
     * 修改封面图
     * @return \think\response\Json
     */
    public function changeCover() {
        // 验证是否为ajax请求
        $this->isAjaxRequest();
        $id = $this->request->post('id');
        $this->dataValidate(['id' => $id],'photo.cover');

        $photo = $this->photoModel->find($id);
        if (empty($photo)) {
            return $this->jsonData('data_error');
        }
        // 此相册当前是否为封面图
        $newCover = $photo['is_cover'] ? 0 : 1;
        // 如果要将此条改为封面，则需要先将已设为封面图的给关掉，再更新本条
        if ($newCover) {
            $this->photoModel->changeOtherCover($photo['org_id'],$newCover);
        }
        $ret = $this->photoModel->update([
            'is_cover' => $newCover
        ],['id' => $id]);

        if(empty($ret)) {
            return $this->jsonData('change_is_cover_error');
        }
        return $this->jsonData('change_is_cover_success',0);
    }
}