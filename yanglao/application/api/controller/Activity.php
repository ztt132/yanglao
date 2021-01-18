<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/7/2
 * Time: 10:59 AM
 */

namespace app\api\controller;


use think\Request;
use app\model\Activity as ActivityModel;

class Activity extends Base
{
    public $activityModel;
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->activityModel = new ActivityModel();
    }

    /**
     * 体验券专区
     * 全部输出
     */
    public function index() {
        $activitys = $this->activityModel->getActivitysByCity($this->cityId);
        return ['code' => 1,'data' => $activitys,'msg'  => 'success'];
    }

    /**
     * 体验券详情接口
     * @return array
     */
    public function detail() {
        $id = input('id');
        if (empty($id)) {
            return ['code' => 0,'data' => [],'msg' => '缺少参数'];
        }
        $activity = $this->activityModel->getActivityDetail($id);
        if (empty($activity)) {
            return ['code' => 0,'data' => [],'msg'  => '对象不存在'];
        }
        $this->cityStatusValidate($activity['city_id']);
        $activity['content'] = content_img_add_class($activity['content']);

        return ['code' => 1,'data' => $activity,'msg' => 'success'];
    }
}