<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/7/1
 * Time: 2:44 PM
 */

namespace app\api\controller;
use think\Request;
use app\model\Org;
use app\model\News;
use app\model\Activity;

class Homepage extends Base
{
    public $orgModel;
    public $newsModel;
    public $activityModel;
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->orgModel = new Org();
        $this->newsModel = new News();
        $this->activityModel = new Activity();
    }

    /**
     * 首页数据展示
     * 5条专题
     * 2条咨询
     * 5入住体验 (活动管理)
     * 3条热门机构
     */
    public function index() {
        // 专题
        $subject = $this->newsModel->getNews($this->cityId,3,5);
        // 咨询
        $news = $this->newsModel->getNews($this->cityId,1,2);
        // 机构
        $orgs = $this->orgModel->getPageOrgList(['is_hot'=>1,'o.city_id' => $this->cityId],[],[],1,10);
        // 入住体验
        $activity = $this->activityModel->getActivitys($this->cityId,1,5);
        $data = [
            'subject' => $subject,
            'org'     => $orgs['data'],
            'news'    => $news,
            'activity' => $activity
        ];
        return ['code' => 1, 'data' => $data, 'msg'  => 'success'];
    }


}