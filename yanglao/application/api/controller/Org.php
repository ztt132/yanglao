<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/7/1
 * Time: 2:36 PM
 */

namespace app\api\controller;
use app\model\Activity;
use app\model\Hx;
use app\model\Introduce;
use app\model\Photo;
use app\model\Userinfo;
use app\model\Collection;
use think\Config;
use think\Request;
use app\model\Org as OrgModel;

class Org extends Base
{
    public $orgModel;
    public $hxModel;
    public $photoModel;
    public $introduceModel;
    public $activityModel;
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->orgModel = new OrgModel();
        $this->hxModel = new Hx();
        $this->photoModel = new Photo();
        $this->introduceModel = new Introduce();
        $this->activityModel = new Activity();
    }

    /**
     * 机构列表
     */
    public function index() {
        // 筛选条件
        $condition = $this->getCondition();
        $orgFilterCondition = $this->getOrgFilterCondition();
        $priceContidion = $this->getPriceCondition();
        $bedContidion = $this->getBedCondition();
        list($page,$limit) = $this->getPaginateInfo();
        $data = $this->orgModel->getPageOrgList($condition,$orgFilterCondition,[$priceContidion,$bedContidion],$page,$limit);
        $data['list'] = $data['data'];
        unset($data['data']);
        return ['code' => 1, 'data' => $data, 'msg'  => 'success'];
    }

    /**
     * 机构详清接口
     * 机构信息
     * 户型列表，优先有vr
     * 相册列表
     */
    public function detail() {
        $id = input('id');
        if (empty($id)) {
            return ['code' => 0, 'data' => [], 'msg'  => '缺少参数'];
        }
        $org = $this->orgModel->getOrgDetail($id);
        if (empty($org)) {
            return ['code' => 0, 'data' => [], 'msg'  => '对象不存在'];
        }
        // 验证状态
        $this->cityStatusValidate($org['city_id']);
        $this->districtStatusValidate($org['district_id']);
        $org['comment'] = content_img_add_class($org['comment']);
        // 获取hx，优先展示有vr的
        $hxs = $this->hxModel->getHxListByOrgId($id);
        // vr
        $vr = [];
        if (!empty($hxs)) {
            foreach ($hxs as $item) {
                if (!empty($item['vr'])) {
                    $item['pic'] = $item['cover_pic'];
                    $vr[] = $item;
                }
            }
        }
        // 获取相册
        $photos = $this->photoModel->getPhotoListByOrgId($id);
        // 获取院长介绍
//        $introduce = $this->introduceModel->getByOrgId($id);
        $introduce = [
            'dean_name' => $org['dean_name'],
            'dean_desc' => $org['dean_desc'],
            'pic' => $org['dean_pic'],
            'content' => $org['dean_content'],
        ];
        if (!empty($introduce)) {
            $introduce['content'] = content_img_add_class($introduce['content']);
        }
        // 活动体验
        $activitys = $this->activityModel->getActivitysByOrgId($id);
        if (!empty($activitys)) {
            // 追加展示图
            $showPic = $this->photoModel->getShowPicByOrgId($id);
            $activitys = array_map(function(&$v) use ($showPic) {
                $v['pic'] = $showPic;
                return $v;
            },$activitys);
        }
        // 返回数据
        $detail = [
            'hx' => $hxs,
            'vr' => $vr,
            'photo' => $photos,
            'introduce' => $introduce,
            'activitys' => $activitys,
            'org' => $org
        ];

        return ['code' => 1, 'data' => $detail, 'msg'  => 'success'];
    }

    /**
     * 检测机构是否被收藏
     */
    public function orgIsCollectioned() {
        $orgId = input('id');
        $openId= input('openid/s');

        if (empty($orgId) || empty($openId)) {
            return ['code' => 0, 'data' => [], 'msg'  => '缺少参数'];
        }

        $collectionModel = new Collection();
        $collection = $collectionModel->where([
            'type'  => 1,
            'object_id' => $orgId,
            'open_id' => $openId
        ])->find();

        return [
            'code' => 1,
            'data' => ['is_collection' => !empty($collection) ? 1 : 0],
            'msg' => 'success'
        ];
    }

    private function getBedCondition() {
        $condition = [];
        // 床位数
        if (array_key_exists('bed_number',input()) && input('bed_number') != '') {
            // 解析value
            $filterConfig = Config::get('Org.bed_number');
            $bedNumber = explode(',',input('bed_number'));
            foreach ($bedNumber as $b) {
                $item = [];
                $bedNumberValue = $filterConfig[$b];
                // 处理数量
                $pattern = '/^\d+\-\d+$/';
                if (strpos($bedNumberValue,'以下') !== false) {
                    $item['o.bed_number'] = ['<',str_replace("以下","",$bedNumberValue)];
                } elseif (strpos($bedNumberValue,'以上') !== false) {
                    $item['o.bed_number'] = ['>',str_replace("以上","",$bedNumberValue)];
                } elseif (preg_match($pattern,$bedNumberValue)) {
                    $bedNumberValueArr = explode('-',$bedNumberValue);
                    $item['o.bed_number'] = [['<=',$bedNumberValueArr[1]],['>=',$bedNumberValueArr[0]]];
                } else {
                    toJson(['code' => 0, 'data' => [], 'msg'  => '床位筛选条件错误']);
                }
                $condition[] = $item;
            }
        }

        return $condition;
    }

    private function getPriceCondition() {
        $condition = [];
        // 价格  TODO 前端要求用XXXX_index格式
        if (!empty(input('price_index'))) {
            $prices = explode(',',input('price_index'));
            foreach ($prices as $price) {
                $item = [];
                $pattern = '/^\d+\-\d+$/';
                // 处理价格
                if (strpos($price,'以下') !== false) {
                    $item['o.min_price'] = ['<',str_replace("以下","",$price)];
                } elseif (strpos($price,'以上') !== false) {
                    $item['o.max_price'] = ['>',str_replace("以上","",$price)];
                } elseif (preg_match($pattern,$price)) {
                    $priceArr = explode('-',$price);
                    $item['o.min_price'] = ['<=',$priceArr[1]];
                    $item['o.max_price'] = ['>=',$priceArr[0]];
                } else {
                    toJson(['code' => 0, 'data' => [], 'msg'  => '价格筛选条件错误']);
                }
                $condition[] = $item;
            }
        }

        return $condition;
    }


    /**
     * 获取关联表中的查询条件
     */
    private function getOrgFilterCondition() {
        $condition = [];
        /**关联org_filter表  service_scope,target_person**/
        $orgFilterKey = ['service_scope','target_person'];
        foreach ($orgFilterKey as $ok) {
            if (array_key_exists($ok,input()) && input($ok) !== '') {
                $condition[] = [
                    'of.type'  => $ok,
                    'of.value' => ['in',explode(',',input($ok))]
                ];
            }
        }
        return $condition;
    }

    /**
     * 获取分页查询条件
     * 筛选条件：
     * 城市，区域，
     * 价格  min-max  XX以下  XX以上
     * 民政评级 床位 医保 机构性质 收住对象 服务范围
     * @return array
     */
    private function getCondition() {
        $condition = [];
        if (array_key_exists('vr',input())) {
            if (input('vr') !== "") {
                $vr = input('vr');
                switch ($vr) {
                    case "0":
                        $condition['h.vr'] = ['EXP','IS NULL'];
                        break;
                    case "1":
                        $condition['h.vr'] = ['<>',""];
                        break;
                }
            }
        }
        // 关键词
        if (!empty(input('keyword'))) {
            $title = input('keyword');
            $condition['o.name'] = ['like',"%$title%"];
        }
        // 城市默认追加
        $condition['o.city_id'] = $this->cityId;
        /**直接追加类的条件  区域,民政评级,医保,性质**/
        $directConditionKey = ['grade','nature'];
        foreach ($directConditionKey as $dk) {
            if (array_key_exists($dk,input()) && input($dk) !== '') {
                $condition['o.'.$dk] = ['in',explode(',',input($dk))];
            }
        }
        // district_id需要特殊处理 如果不传，必须传status为开启的区域，传的话过滤已关闭的
        $districts = $this->districtModel->getDistrictsById($this->cityId);
        $districtIds = array_column($districts,'district_id');
        // TODO 前端要求用XXX_index格式
        if (!empty(input('district_index'))) {
            $paramDistrictIds = explode(',',input('district_index'));
            // 取交集
            $condition['o.district_id'] = ['in',array_intersect($paramDistrictIds,$districtIds)];
        } else {
            $condition['o.district_id'] = ['in',$districtIds];
        }

        // health_insurance 特殊处理
        if (array_key_exists('health_insurance',input())) {
            if (input('health_insurance') !== "") {
                $condition['o.health_insurance'] = ['in',explode(',',input('health_insurance'))];
            }
        }

        return $condition;
    }
}