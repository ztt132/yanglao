<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/7/1
 * Time: 1:46 PM
 */

namespace app\api\controller;
use app\model\Estate;
use think\Request;

use app\model\Org;
use app\model\News;
use app\model\Food;

class Search extends Base
{
    public $orgModel;
    public $newsModel;
    public $foodModel;
    public $estateModel;
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->orgModel = new Org();
        $this->newsModel = new News();
        $this->foodModel = new Food();
        $this->estateModel = new Estate();
    }

    /**
     * 首页搜索
     * 养老院，咨询，政策数量
     * @return \think\response\Json
     */
    public function index() {
        $keyword = $this->request->get('keyword');
        // 机构数量
        $orgCount = $this->orgModel->getCount($this->cityId,$keyword);
        // 咨询数量
        $newsCount = $this->newsModel->getCount($this->cityId,$keyword,1);
        // 政策数量
        $policyCount = $this->newsModel->getCount($this->cityId,$keyword,2);
        // 助餐点数量
        $foodCount = $this->foodModel->getCount($this->cityId,$keyword);
        // TODO 2021-01-12 增加搜索养老地产
        $estateCount = $this->estateModel->getCount($this->cityId,$keyword);
        $data = [
            'org'    => $orgCount,
            'news'   => $newsCount,
            'policy' => $policyCount,
            'food'   => $foodCount,
            'estate' => $estateCount
        ];

        return ['code' => 1, 'data' => $data, 'msg'  => 'success'];
    }
}