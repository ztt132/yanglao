<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/7/1
 * Time: 10:26 AM
 */

namespace app\api\controller;


use app\api\service\CityService;
use think\Controller;
use think\Request;

class BaseController extends Controller
{
    private $city;

    public $cityService;

    /**
     * @return mixed
     */
    public function getCity() {
        return $this->city;
    }

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        // 基础验证
        $this->cityValidate();
    }

    /**
     * api接口的部分基础验证
     * 1. 城市必传
     */
    private function cityValidate() {
        $pinyin = $this->request->param('pinyin');
        if (empty($pinyin)) {
            toJson([
                'code' => 10002,
                'msg ' => '城市信息错误'
            ]);
        }
        $this->cityService = new CityService();
        if (!$this->city = $this->cityService->getCityByPinyin($this->request->param('pinyin'))) {
            if (empty($city)) {
                toJson([
                    'code' => 10002,
                    'msg ' => '城市信息错误'
                ]);
            }
        }
    }

    public function getPaginateInfo() {
        $page = !empty($this->request->get('page')) ? $this->request->get('page') : 1;
        $limit = !empty($this->request->get('limit')) ? $this->request->get('limit') : 10;

        return [$page,$limit];
    }
}