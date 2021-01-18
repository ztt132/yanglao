<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/28
 * Time: 1:00 PM
 */

namespace app\admin\controller;


use think\Config;
use think\Request;
use app\model\Org;
use app\model\Activity as ActivityModel;

class Activity extends AdminBase
{
    public $activityModel;
    public $orgModel;
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->activityModel = new ActivityModel();
        $this->orgModel = new Org();
    }

    public function index() {
        $title = $this->request->get('title');
        $this->assign('title',$title);

        return $this->fetch();
    }

    public function add() {
        // 查询所有机构
        $orgs = $this->orgModel->allOrgs();
        $config = Config::get('Enum.activity');
        $data = [
            'orgs' => $orgs,
            'config' => $config
        ];

        $this->assign($data);
        return $this->fetch();
    }

    public function create() {
        $this->isAjaxRequest();

        $data = $this->request->param();
        $this->dataValidate($data,'activity.create');

        $ret = $this->activityModel->save($data);
        if(empty($ret)) {
            return $this->jsonData('create_error');
        }
        return $this->jsonData('create_success',0);
    }

    public function edit() {
        $id = $this->request->get('id');
        $activity = $this->activityModel->find($id);
        // 查询所有机构
        $orgs = $this->orgModel->allOrgs();
        $config = Config::get('Enum.activity');
        $data = [
            'orgs' => $orgs,
            'config' => $config,
            'activity' => $activity
        ];

        $this->assign($data);
        return $this->fetch();
    }

    public function update() {
        // 验证是否为ajax请求
        $this->isAjaxRequest();
        // 数据验证
        $data = $this->request->param();
        $this->dataValidate($data,'activity.update');

        $id = $data['id'];
        unset($data['id']);
        $ret = $this->activityModel->update($data,['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    public function activityList() {
        $this->isAjaxRequest();

        $title = input('title');
        list($page,$limit) = $this->getPaginateInfo();
        $data = $this->activityModel->activityPageList($title,$page,$limit);
        // 数据处理
        if (!empty($data['data'])) {
            $config = Config::get('Enum.activity');
            $typeConfig = $config['type'];
            foreach ($data['data'] as &$value) {
                $value['type'] = $typeConfig[$value['type']];
            }
        }
        return $this->jsonData('',0,$data);
    }

    /**
     * 修改排序
     */
    public function sort() {
        $id = input('id');
        $sort = !empty(input('sort')) ? input('sort') : 0;
        $ret = $this->activityModel->update([
            'sort' => $sort
        ],['id' => $id]);

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
        $ret = $this->activityModel->update(['status' => $status],['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }
}