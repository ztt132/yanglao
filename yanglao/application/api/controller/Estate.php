<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2021/1/12
 * Time: 3:22 PM
 */

namespace app\api\controller;


use think\Config;
use think\Request;
use app\model\Estate as EstateModel;
use app\model\Estatehx;
use app\model\Estatephoto;
use app\model\Collection;

class Estate extends Base
{
    private $estateModel;
    private $estateHxModel;
    private $estatePhotoModel;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->estateModel = new EstateModel();
        $this->estateHxModel = new Estatehx();
        $this->estatePhotoModel = new Estatephoto();
    }

    /**
     * 列表接口
     */
    public function index()
    {
        list($page,$limit) = $this->getPaginateInfo();
        $condition = $this->getCondition();
        $hxCondition = $this->getHxCondition();
        $data = $this->estateModel->getList($this->cityId,$condition,$hxCondition,$page,$limit);
        $data['list'] = $data['data'];
        unset($data['data']);
        return ['code' => 1, 'data' => $data, 'msg'  => 'success'];
    }

    /**
     * 处理地产表中条件
     * naturl项目性质
     * price价格
     * nature类型
     */
    private function getCondition()
    {
        $condition = [];
        // 关键词
        if (array_key_exists('keyword',input())) {
            if (input('keyword') !== '') {
                $condition['e.name'] = ['like',"%".trim(input('keyword'))."%"];
            }
        }

        // 城市
//        $condition['e.city_id'] = $this->cityId;
        // 性质
        if (array_key_exists('nature',input())) {
            if (input('nature') !== '') {
                $condition['e.nature'] = intval(input('nature'));
            }
        }
        // 类型
        if (array_key_exists('type_index',input())) {
            if (input('type_index') !== '') {
                $conf = Config::get('estate.enum');
                $typeConf = $conf['type'];
                $typeValue = array_search(trim(input('type_index')),$typeConf);
                if ($typeValue === false) {
                    toJson(['code' => 0, 'data' => [], 'msg'  => '类型筛选条件错误']);
                }
                $condition['e.type'] = intval($typeValue);
            }
        }
        // 价格
        if (array_key_exists('price_index',input())) {
            if (input('price_index') !== '') {
                $priceValue = trim(input('price_index'));
                $pattern = '/^\d+\-\d+$/';

                if (strpos($priceValue,'以下') !== false) {
                    $condition['e.price'] = ['<',intval(str_replace("以下","",$priceValue))];
                } elseif (strpos($priceValue,'以上') !== false) {
                    $condition['e.price'] = ['>',intval(str_replace("以上","",$priceValue))];
                } elseif (preg_match($pattern,$priceValue)) {
                    $priceValueArr = explode('-',$priceValue);
                    $condition['e.price'] = [['<=',intval($priceValueArr[1])],['>=',intval($priceValueArr[0])]];
                } else {
                    toJson(['code' => 0, 'data' => [], 'msg'  => '价格筛选条件错误']);
                }
            }
        }
        // 区域
        if (array_key_exists('district_index',input())) {
            if (input('district_index') !== '') {
                $condition['e.district_id'] = intval(input('district_index'));
            }
        }

        return $condition;
    }

    /**
     * 处理户型条件
     * vr
     * 面积
     * 户型
     */
    private function getHxCondition()
    {
        $condition = [];
        // 户型
        if (array_key_exists('hx',input())) {
            if (input('hx') !== '') {
                $shi = intval(input('hx')) + 1;
                if ($shi > 5) {
                    $condition['eh.shi'] = ['>=',$shi];
                } else {
                    $condition['eh.shi'] = $shi;
                }
            }
        }
        // 有无vr
        if (array_key_exists('vr',input())) {
            if (input('vr') !== '') {
                $vr = input('vr');
                switch ($vr) {
                    case 0:
                        $condition['h.vr'] = ['EXP','IS NULL'];
                        break;
                    case 1:
                        $condition['h.vr'] = ['<>',""];
                        break;
                }
            }
        }
        // 面积
        if (array_key_exists('area',input())) {
            if (input('area') !== '') {
                $area = intval(input('area'));
                $conf = Config::get('estate.enum');
                if (empty($conf['area'][$area])) {
                    toJson(['code' => 0, 'data' => [], 'msg'  => '面积筛选条件错误']);
                }
                $areaValue = $conf['area'][$area];
                // 处理面积大小
                $pattern = '/^\d+\-\d+$/';
                if (strpos($areaValue,'以下') !== false) {
                    $condition['eh.area'] = ['<',intval(str_replace("以下","",$areaValue))];
                } elseif (strpos($areaValue,'以上') !== false) {
                    $condition['eh.area'] = ['>',intval(str_replace("以上","",$areaValue))];
                } elseif (preg_match($pattern,$areaValue)) {
                    $areaValueArr = explode('-',$areaValue);
                    $condition['eh.area'] = [['<=',intval($areaValueArr[1])],['>=',intval($areaValueArr[0])]];
                } else {
                    toJson(['code' => 0, 'data' => [], 'msg'  => '面积筛选条件错误']);
                }
            }
        }

        return $condition;
    }

    /**
     * 详情接口
     */
    public function detail()
    {
        $id = input('id');
        if (empty($id)) {
            return ['code' => 0, 'data' => [], 'msg'  => '缺少参数'];
        }
        $estate = $this->estateModel->getEstateDetail($id);
        $estate['desc'] = content_img_add_class($estate['desc']);
        if (empty($estate)) {
            return ['code' => 0, 'data' => [], 'msg'  => '对象不存在'];
        }
        // 相册列表
        $photos = $this->estatePhotoModel->getPhotoListByEstateId($id);

        // 户型列表
        $hxs = $this->estateHxModel->getHxListByEstateId($id);

        // 根据户型得到轮播中的vr列表
        $vrs = [];
        $count = count($photos);
        if (!empty($hxs)) {
            foreach ($hxs as $h) {
                if (!empty($h['pics'])) {
                    foreach ($h['pics'] as $pic) {
                        if (!empty($h['vr'])) {
                            $vrs[] = [
                                "pic" => $pic,
                                "vr" => $h['vr']
                            ];
                        } else {
                            $photos[] = [
                                'id' => $count++,
                                'photo_type' => 1,
                                'is_cover' => 1,
                                'pic' => $pic
                            ];
                        }
                    }
                }
            }
        }
        // photo
        $data = [
            'estate' => $estate,
            'hx' => $hxs,
            'photo' => $photos,
            'vr' => $vrs
        ];

        return ['code' => 1, 'data' => $data, 'msg'  => 'success'];
    }

    /**
     * 检测地产是否被收藏
     */
    public function estateIsCollectioned() {
        $estateId = input('id');
        $openId= input('openid/s');
        if (empty($estateId) || empty($estateId)) {
            return ['code' => 0, 'data' => [], 'msg'  => '缺少参数'];
        }

        $collectionModel = new Collection();
        $collection = $collectionModel->where([
            'type'  => 2,
            'object_id' => $estateId,
            'open_id' => $openId
        ])->find();

        return [
            'code' => 1,
            'data' => ['is_collection' => !empty($collection) ? 1 : 0],
            'msg' => 'success'
        ];
    }
}