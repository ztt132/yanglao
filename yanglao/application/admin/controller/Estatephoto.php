<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2021/1/11
 * Time: 5:54 PM
 */

namespace app\admin\controller;

use think\Config;
use think\Request;
use app\model\Estate;
use app\model\Estatephoto as EstatephotoModel;

class Estatephoto extends AdminBase
{
    private $estateModel;
    private $estatephotoModel;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->estateModel = new Estate();
        $this->estatephotoModel = new EstatephotoModel();
    }

    public function photoList() {
        $this->isAjaxRequest();

        $estateName = $this->request->request('name');
        list($page,$limit) = $this->getPaginateInfo();
        $data = $this->estatephotoModel->photoPageList($estateName,$page,$limit);
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


    public function add() {
        // 获取所有楼盘
        $estates = $this->estateModel->getAllEstatesAndCity();
        // 加载配置
        $config = Config::get('Enum.photo');
        $this->assign([
            'estates' => $estates,
            'config' => $config
        ]);
        return $this->fetch();
    }

    public function create() {
        // 验证是否为ajax请求
        $this->isAjaxRequest();
        $data = $this->request->param();
        // 数据验证
        // TODO 2020-7-27改为多图，生成多个相册
        if (!empty($data['pic'])) {
            foreach ($data['pic'] as $pic) {
                $this->estatephotoModel->insert([
                    'estate_id' => $data['estate_id'],
                    'photo_type' => $data['photo_type'],
                    'pic' => $pic,
                    'create_time' => time(),
                    'update_time' => time()
                ]);
            }
        }

        return $this->jsonData('create_success',0);
    }

    /**
     * 修改封面图
     * @return \think\response\Json
     */
    public function changeCover() {
        // 验证是否为ajax请求
        $this->isAjaxRequest();
        $id = $this->request->post('id');

        $photo = $this->estatephotoModel->find($id);
        if (empty($photo)) {
            return $this->jsonData('data_error');
        }
        // 此相册当前是否为封面图
        $newCover = $photo['is_cover'] ? 0 : 1;
        // 如果要将此条改为封面，则需要先将已设为封面图的给关掉，再更新本条
        if ($newCover) {
            $this->estatephotoModel->changeOtherCover($photo['estate_id'],$newCover);
        }
        $ret = $this->estatephotoModel->update([
            'is_cover' => $newCover
        ],['id' => $id]);

        if(empty($ret)) {
            return $this->jsonData('change_is_cover_error');
        }
        return $this->jsonData('change_is_cover_success',0);
    }

    public function edit() {
        $id = $this->request->get('id');
        // 获取所有楼盘
        $estates = $this->estateModel->getAllEstatesAndCity();
        // 加载配置
        $config = Config::get('Enum.photo');
        // 查询photo
        $photo = $this->estatephotoModel->find($id);
        $data = [
            'estates' => $estates,
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

        $id = $data['id'];
        unset($data['id']);
        $ret = $this->estatephotoModel->update($data,['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }
}