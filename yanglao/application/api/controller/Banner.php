<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/28
 * Time: 7:35 PM
 */

namespace app\api\controller;

use think\Request;
use app\model\Banner as BannerModel;

class Banner extends Base
{
    public $bannerModel;
    public function __construct(Request $request = null) {
        parent::__construct();
        $this->bannerModel = new BannerModel();
    }

    /**
     * banner列表
     */
    public function index() {
        $position = input('position');
        if (empty($position)) {
            return ['code' => 0, 'data' => [], 'msg'  => '缺少参数'];
        }

        $title = $this->request->get('title');
        // 获取banner
        $data = $this->bannerModel->getBannerList($this->cityId,$title,$position,1,3);
        return ['code' => 1, 'msg'  => 'success', 'data' => $data['data']];
    }
}