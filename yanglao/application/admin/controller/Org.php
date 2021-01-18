<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/19
 * Time: 1:36 PM
 */

namespace app\admin\controller;
use app\model\Org as OrgModel;
use think\Request;
use app\model\City;
use app\model\OrgFilter;
use app\model\Equipment;
use app\model\Weouth;
use think\Config;
use app\model\Qrcode;
use app\model\Hx;
use app\model\Photo;

class Org extends AdminBase
{
    public $orgModel;
    public $cityModel;
    public $orgFilterModel;
    public $equipmentModel;
    public $qrcodeModel;
    public $hxModel;
    public $photoModel;

    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->orgModel = new OrgModel();
        $this->cityModel = new City();
        $this->orgFilterModel = new OrgFilter();
        $this->equipmentModel = new Equipment();
        $this->qrcodeModel = new Qrcode();
        $this->hxModel = new Hx();
        $this->photoModel = new Photo();
    }

    public function index() {
        $name = input('name');
        $cityName = input('city_name');
        $this->assign([
            'city_name' => $cityName,
            'name' => $name
        ]);

        return $this->fetch();
    }

    /**
     * 新建操作
     */
    public function create() {
        $this->isAjaxRequest();
        // 基础验证
        $data = $this->request->param();
        $this->dataValidate($data,'org.create');

        $ret = $this->orgModel->createOrg($data);
        if(empty($ret)) {
            return $this->jsonData('create_error');
        }
        return $this->jsonData('create_success',0);
    }

    public function orgList() {
        $this->isAjaxRequest();

        $name = $this->request->get('name');
        $cityName = $this->request->get('city_name');
        list($page,$limit) = $this->getPaginateInfo();

        $data = $this->orgModel->orgPageList($name,$cityName,$page,$limit);
        if (!empty($data['data'])) {
            // 查询所有类型
            $orgIds = array_column($data['data'],'id');
            // 查询所有筛选
            $filters = $this->orgFilterModel->findAllByOrgIds($orgIds);
            if (!empty($filters)) {
                foreach ($data['data'] as &$v) {
                    foreach ($filters as $f) {
                        if ($f['type'] == 'org_type' && $v['id'] == $f['org_id']) {
                            $v['org_type'][] = $f['value'];
                        }
                    }
                }
            }
            // 机构类型 , 城市区域  转换
            $config = get_page_config('org_page');
            $typeConfig = $config['org_type'];
            foreach ($data['data'] as &$value) {
                if (!empty($value['org_type'])) {
                    $type = array_map(function($v) use ($typeConfig) {
                        return $typeConfig[$v];
                    },$value['org_type']);
                    $value['org_type'] = implode(',',$type);
                }
            }
        }
        return $this->jsonData('success',0,$data);
    }

    public function add() {
        // 获取所有城市
        $cityDistricts = $this->cityModel->getAllCityDistrict();
        // 加载基本配置
        $config = get_page_config('org_page');
        // 获取所有设施设备
        $equipments = $this->equipmentModel->getAllEquipment();
        $data = [
            'city_district' => $cityDistricts,
            'config' => $config,
            'equipments' => $equipments
        ];
        $this->assign($data);
        return $this->fetch();
    }

    /**
     * 编辑界面
     */
    public function edit() {
        $id = $this->request->get('id');
        // 查询相信信息
        $org = $this->orgModel->findOrg($id);
        // 获取所有城市
        $cityDistricts = $this->cityModel->getAllCityDistrict();
        // 加载基本配置
        $config = get_page_config('org_page');
        // 获取所有设施设备
        $equipments = $this->equipmentModel->getAllEquipment();
        $data = [
            'city_district' => $cityDistricts,
            'config'        => $config,
            'org'           => $org,
            'equipments'    => $equipments
        ];

        $this->assign($data);
        return $this->fetch();
    }

    /**
     * 更新操作
     */
    public function update() {
        $this->isAjaxRequest();
        // 基础验证
        $data = $this->request->param();
        $this->dataValidate($data,'org.update');

        $id = $data['id'];
        unset($data['id']);
        $ret = $this->orgModel->updateOrg($id,$data);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    /**
     * 修改热门状态
     */
    public function hot() {
        $id = input('id');
        $isHot = !empty(input('is_hot')) ? 1 : 0;
        $ret = $this->orgModel->update([
            'is_hot' => $isHot
        ],['id' => $id]);

        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }


    /**
     * 修改排序
     */
    public function sort() {
        $id = input('id');
        $sort = !empty(input('sort')) ? input('sort') : 0;
        $ret = $this->orgModel->update([
            'sort' => $sort
        ],['id' => $id]);

        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    /**
     * 制作海报
     * @return \think\response\Json
     */
    public function makeHaibao()
    {
        $id = input('id');
        $org = $this->orgModel->find($id);
        if (empty($org)) {
            return $this->jsonData('param_error');
        }
        $org = $org->toArray();
        // 先判断有没有小程序二维码,没有先生成小程序二维码
        $qrcodeImage = $org['qrcode_image'];
        if (empty($qrcodeImage)) {
            // 创建qrcode基本数据
            $qrcode = $this->qrcodeModel->findQrcodeByObjId($id);
            if (empty($qrcode)) {
                $config = Config::get('Qrcode.qrcode_type');
                // 创建qrcode数据
                $data = [
                    'name' => $org['name'],
                    'qrcode_type' => 0,
                    'path' => $config[0]['path'],
                    'param' => ['id' => $id],
                    'obj_id' => $id,
                    'status' => 1
                ];
                $qrcodeRet = $this->qrcodeModel->create($data);
                // 生成二维码
                $page = 'pages/index/route';
                $weouth = new Weouth();
                $coderesult = $weouth->getwxacodeunlimit($qrcodeRet['id'],$page,430);
                if ($coderesult) {
                    $qrcodeImage = make_qrcode($coderesult);
                    // 更新二维码
                    $this->qrcodeModel->update(['qrcode_image'=>$qrcodeImage],['id'=>$qrcodeRet['id']]);
                    $this->orgModel->update(['qrcode_image'=>$qrcodeImage],['id'=>$id]);
                } else {
                    return $this->jsonData('request_error');
                }
            }
        }
        // 制作海报 判断有没有vr
        $hxs = $this->hxModel->getVrHxListByOrgIds([$id]);
        if (empty($hxs)) {
            require __DIR__ . '/../../../extend/haibao/NoVrHaibao.php';
            $haibao = new \NoVrHaibao();
        } else {
            require __DIR__ . '/../../../extend/haibao/VrHaibao.php';
            $haibao = new \VrHaibao();
        }
        // 查找封面图
        $photo = $this->photoModel->getCoverPhoto($id);
        $haibaoImage = $haibao->make($org['name'],$org['bed_number'],$org['tag'],$qrcodeImage,$photo);
        $this->orgModel->update(['haibao_image'=>$haibaoImage],['id'=>$id]);

        return $this->jsonData('create_success',0,['image' => $haibaoImage]);
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
        $ret = $this->orgModel->update(['status' => $status],['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }
}