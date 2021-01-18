<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/28
 * Time: 5:43 PM
 */
namespace app\api\controller;

use app\model\City;
use app\model\District;
use app\model\Filter;
use think\Controller;
use think\Config;
use think\Request;

class Util extends Controller
{
    public $filterModel;
    public $cityModel;
    public $districtModel;
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->filterModel = new Filter();
        $this->cityModel = new City();
        $this->districtModel = new District();
    }

    /**
     * 获取筛选配置以及快捷筛选配置
     */
    public function getFilterConfig() {
        $data =[];
        $filter = $this->filterModel->getFilter();
        if (empty($filter)) {
            return ['code' => 0, 'data' => [], 'msg'  => '暂无配置'];
        }

        // 获取筛选配置 按sort排序
        $listFilter = [];
        if (!empty($filter['list_filter'])) {
            $listFilter = $this->formatListFilter($filter['list_filter']);
        }
        $data['list_filter'] = $listFilter;
        // 获取快捷筛选配置
        $quickFilter = [];
        if (!empty($filter['quick_filter'])) {
            $quickFilter = $this->formatQuickFilter($filter['quick_filter']);
        }
        $data['quick_filter'] = $quickFilter;
        // 获取价格配置
        $data['price'] = !empty($filter['price']) ? explode(',',$filter['price']) : [];
        // 获取城市区域
        $pinyin = $this->request->get('pinyin');
        $data['district'] = $this->getDistrictFilter($pinyin);

        return ['code' => 1, 'data' => $data, 'msg'  => 'success'];
    }

    /**
     * 获取养老地产筛选配置
     */
    public function getEstateFilterConfig()
    {
        $data =[];
        // 获取城市区域
        $pinyin = $this->request->get('pinyin');
        $data['district'] = $this->getDistrictFilter($pinyin);

        $config = Config::get('Estate');
        // 获取筛选项下配置
        $listFilter = [];
        foreach ($config['filter_page'] as $lc) {
            $subsArr = $config['enum'][$lc['key']];
            $subs = [];
            foreach ($subsArr as $k => $s) {
                $subs[] = [
                    'value'  => $k,
                    'option' => $s
                ];
            }
            $listFilter[$lc['key']] = [
                'key'    => $lc['key'],
                'option' => $lc['option'],
                'sub'    => $subs
            ];
        }
        $data['list_filter'] = $listFilter;
        // 价格
        $data['price'] = $config['enum']['price'][$pinyin];
        // 类型
        $data['type'] = $config['enum']['type'];

        return ['code' => 1, 'data' => $data, 'msg'  => 'success'];
    }


    private function getDistrictFilter($pinyin = '')
    {
        $districts = [];
        if (!empty($pinyin)) {
            $city = $this->cityModel->getCityByPinyin($pinyin);
            if (!empty($city)) {
                $districts = $this->districtModel->getDistrictsById($city['id']);
            }
        }

        return $districts;
    }

    /**
     * 获取所有城市
     *
     */
    public function getAllCity() {
        $citys = Config::get('City');
        // 获取所有状态为开的城市
        $openStatusCitys = $this->cityModel->where('status',1)->select()->toArray();
        if (empty($openStatusCitys)) {
            return [
                'code' => 1,
                'data' => []
            ];
        }
        $openStatusCitysPinyins = array_column($openStatusCitys,'pinyin');
        $data = [];
        // 处理数据
        foreach ($citys as $city) {
            if (!in_array($city['city_key'],$openStatusCitysPinyins)) {
                continue;
            }
            $firstWord = substr($city['city_py'],0,1);
            $data[$firstWord][] = $city;
        }
        ksort($data);
        return [
            'code' => 1,
            'data' => $data
        ];
    }

    /**
     * 获取所有城市
     *
     */
    public function getAllCity2() {
        $citys = Config::get('City');
        // 获取所有状态为开的城市
        $openStatusCitys = $this->cityModel->select()->toArray();
        if (empty($openStatusCitys)) {
            return [
                'code' => 1,
                'data' => []
            ];
        }
        $openStatusCitysPinyins = array_column($openStatusCitys,'pinyin');
        $data = [];
        // 处理数据
        foreach ($citys as $city) {
            if (!in_array($city['city_key'],$openStatusCitysPinyins)) {
                continue;
            }
            $firstWord = substr($city['city_py'],0,1);
            $data[$firstWord][] = $city;
        }
        ksort($data);
        return [
            'code' => 1,
            'data' => $data
        ];
    }

    /**
     *  根据拼音获取城市信息
     */
    public function getCityByPinyin() {
        $pinyin = input('pinyin');
        if (empty($pinyin)) {
            return ['code' => 0,'msg' => '参数异常'];
        }
        $ret = [];
        $city = $this->cityModel->where([
            'status' => 1,
            'pinyin' => $pinyin
        ])->find();
        if (!empty($city)) {
            $ret = [
                'city_key' => $city->pinyin,
                'city_name' => $city->city_name
            ];
        }

        return ['code' => 1,'data' => $ret,'msg' =>'success'];
    }

    /**
     * 根据经纬度获取城市信息
     */
    public function getCityByLocation() {
        $lat = $this->request->get('latitude');//经度
        $lng = $this->request->get('longitude');//维度
        if (empty($lat) || empty($lng)) {
            return json(['code' => -1,'msg' => '参数异常']);
        }
        $url ="http://mtapi.house365.com/?method=user.coordConverttoCity&lat=".$lat."&lng=".$lng."&city=&app=tfxcx";
        $ret = curl_get_contents($url);
        if (!$ret) {
            return ['code' => -1,'msg' => '网络异常,稍后再试'];
        }
        $retArr = json_decode($ret,1);
        // 查看是否在开启城市列表中
        $cityKey = '';
        $cityName = '';
        $openStatusCitys = $this->cityModel->field('pinyin')->where('status',1)->select()->toArray();
        if (in_array($retArr['city_key'],array_column($openStatusCitys,'pinyin'))) {
            $cityKey = $retArr['city_key'];
            $cityName = $retArr['data'];
        }
        $data = [
            'city_key'  => $cityKey,
            'city_name' => $cityName
        ];

        return ['code' => 1,'data' => $data];
    }

    /**
     * 格式化筛选项
     * @param $data
     * @return array
     */
    private function formatListFilter($data) {
        $filterConfig = get_filter_config();
        // 排序
        $listSort = array_column($data,'sort');
        array_multisort($listSort,SORT_ASC,$data);
        // 追加名称 以及选项
        $listFilter = [];
        foreach ($data as $v) {
            foreach ($filterConfig as $config) {
                if ($v['key'] == $config['value']) {
                    if ($v['checked']) {
                        $listFilter[$v['key']] = [
                            'key'    => $v['key'],
                            'option' => $config['option'],
                            'sub'    => $config['sub']
                        ];
                        continue;
                    }
                }
            }
        }

        return $listFilter;
    }

    private function formatQuickFilter($data) {
        $quickFilter = [];
        $filterConfig = get_filter_config();
        // 先排序
        $quickSort = array_column($data,'sort');
        array_multisort($quickSort,SORT_ASC,$data);
        foreach ($data as $k => $v) {
            foreach ($filterConfig as $config) {
                if ($config['value'] == $v['key']) {
                    $item = [
                        'key'   => $v['key'],
                        'value' => $v['value']
                    ];
                    if (!empty($v['alias'])) {
                        $item['option'] = $v['alias'];
                    } else {
                        // 获取option以及value
                        foreach ($config['sub'] as $sub) {
                            if ($v['value'] == $sub['value']) {
                                $item['option'] = !empty($sub['alias']) ? $sub['alias'] :$config['option'].'-'.$sub['option'];
                                continue;
                            }
                        }
                    }

                    $quickFilter[($k+1) . $v['key']] = $item;
                    continue;
                }
            }
        }

        return $quickFilter;
    }

    public function getServicePhone() {
        return ['code' => 1,'data' => ['phone' => 18652059298,'business'=>1801588136],'msg' => 'success'];
    }
}