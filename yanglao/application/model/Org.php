<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/22
 * Time: 2:25 PM
 */

namespace app\model;


use think\Config;
use think\Db;
use think\Model;

class Org extends Model
{
    protected $dateFormat = 'Y-m-d';

    protected $autoWriteTimestamp = true;

    protected $type = [
//        'set_time' => 'timestamp',
        'tag'           => 'json',
        'service'       => 'json'
    ];

    public static $filterTypes = [
        'org_type','target_person','service_scope','equipment'
    ];

    public $orgFilterModel;
    public $equipmentModel;
    public function __construct($data = []) {
        parent::__construct($data);
        $this->orgFilterModel = new OrgFilter();
        $this->equipmentModel = new Equipment();
    }


    protected static function init()
    {
        Org::event('before_insert', function ($org) {
            if (empty($org->tag)) {
                $org->tag = ["","",""];
            }
            if (empty($org->service)) {
                $org->service = [];
            }
        });

        Org::event('before_update', function ($org) {

        });
    }

    /**
     * 查询机构数量
     * @param string $title
     */
    public function getCount($cityId = 0,$name = '') {
        $where['city_id'] = $cityId;
        if (!empty($name)) {
            $where['name'] = ['like',"%$name%"];
        }
        return $this->where($where)->count();
    }



    /**
     * 机构列表
     * 带分页
     * @param array $where
     * @param array $orgFilterCondition 关联表条件
     * @param array $scopeCondition 范围型筛选，目前床位和价格
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\exception\DbException
     */
    public function getPageOrgList($where = [],$orgFilterCondition,$scopeCondition = [],$page = 1,$limit = 10) {
        $where['o.status'] = 1;
        $field = 'o.id as id,o.name,o.tag,o.is_hot,c.city_name,d.name as district_name,o.min_price,o.max_price,o.sort,h.vr,o.qrcode_image';
        // 优先展示带vr的
        $vrDB = Db::name("hx")->field("id,org_id,(case vr when NULL then 0 else 1 end) vr")->where(["vr" => ["<>",""]])->buildSql();
        $query = $this->where($where)->field($field)
            ->alias('o')
            ->join('city c','c.id = o.city_id','LEFT')
            ->join('district d','d.id = o.district_id','LEFT')
            ->join([$vrDB => 'h'],'h.org_id = o.id','LEFT');


        if (!empty($scopeCondition)) {
            foreach ($scopeCondition as $scope) {
                if (!empty($scope)) {
                    $query = $this->handleCondition($query,$scope);
                }
            }
        }
//        if (!empty($priceContion)) {
//            $query = $this->handleCondition($query,$priceContion);
//        }
//        if (!empty($bedCondition)) {
//            $query = $this->handleCondition($query,$bedCondition);
//        }

        // 存在部分关联关系数据
        if (!empty($orgFilterCondition)) {
            $query->join('org_filter of','of.org_id = o.id','LEFT');
            $query = $this->handleCondition($query,$orgFilterCondition);
            $query->having('count(o.id) >= '. count($orgFilterCondition));
        }
        $query->group('o.id');
        $data = $query->order('h.vr is null,h.vr')->order('o.sort desc')
            ->paginate($limit,false,['page' => $page])
            ->toArray();
        // 如果有数据，处理数据，追加封面图以及判断是否有vr
        if (!empty($data['data'])) {
            $data['data'] = $this->formatListData($data['data']);
        }

        return $data;
    }

    /**
     * 处理 需要and同时or的查询条件
     * @param $query
     * @param array $condition
     * @return mixed
     */
    private function handleCondition($query,$condition = []) {
        $query->where(function($query) use ($condition) {
            foreach ($condition as $key => $item) {
                if ($key == 0) {
                    $query->where(function ($query) use ($item) {
                        $query->where($item);
                    });
                } else {
                    $query->whereOr(function ($query) use ($item) {
                        $query->where($item);
                    });
                }
            }
        });

        return $query;
    }

    /**
     * 根据条件获取所有的机构
     * @param array $condition
     */
    public function getOrgsByCondition($condition = []) {
        $orgs = $this->field('o.id as id,o.name,o.tag,o.is_hot,c.city_name,d.name as district_name')
            ->alias('o')
            ->join('city c','c.id = o.city_id','LEFT')
            ->join('district d','d.id = o.district_id','LEFT')
            ->where($condition)->select()->toArray();
        // 格式数据
        if (!empty($orgs)) {
            $orgs = $this->formatListData($orgs);
        }

        return $orgs;
    }

    /**
     * 格式化列表数据
     * @param array $data
     */
    public function formatListData($data = []) {
        $orgIds = array_column($data,'id');
        // 查询机构下是否有带vr的户型图
        $hxModel = new Hx();
        $hxs = $hxModel->getVrHxListByOrgIds($orgIds);
        // org_id=>vr地址
        $hxVrMapping = !empty($hxs) ? array_column($hxs,'vr','org_id') : [];
        // 查询相册
        $photoModel = new Photo();
        $photos = $photoModel->getPhotoListByOrgIds($orgIds);
        // org_id=>pic
        $photoMapping = [];
        if (!empty($photos)){
            foreach ($photos as $photo) {
                // 如果此时mapping中无此org，设置图片
                if (!array_key_exists($photo['org_id'],$photoMapping)) {
                    $photoMapping[$photo['org_id']] = $photo['pic'];
                } else {
                    // 判断是否需要替换
                    if ($photo['is_cover']) {
                        $photoMapping[$photo['org_id']] = $photo['pic'];
                    }
                }
            }
        }
        foreach ($data as &$org) {
            $org['is_vr'] = array_key_exists($org['id'],$hxVrMapping) ? 1 : 0;
            $org['pic'] = array_key_exists($org['id'],$photoMapping) ? $photoMapping[$org['id']] : '';
        }

        return $data;
    }

    /**
     * 获取机构详情，同时转换数据
     * @param int $orgId
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOrgDetail($orgId = 0) {
        $detail = [];
        $org = $this->where('id',$orgId)->where('status',1)
            ->find();
        if (empty($org)) {
            return $detail;
        }
        $org = $org->toArray();
        $orgConfig = Config::get('Org');
        // 查询关联数据
        $orgFilter = $this->orgFilterModel->getOrgFilterByOrgId($orgId);
        $typeValueArr = [];
        foreach (self::$filterTypes as $type) {
            $typeValueArr[$type] = [];
            foreach ($orgFilter as $orgFilterValue) {
                if ($orgFilterValue['type'] == $type) {
                    $typeValueArr[$type][] = $orgFilterValue['value'];
                }
            }
        }
        // 1. 医保和服务范围
//        $healthOption = [
//            'option'  => '支持医保',
//            'checked' => $org['health_insurance']
//        ];
        $healthService = [];
//        $healthService[] = $healthOption;
        $serviceScope = [];
        // TODO 2020-07-28前端要求给为选中的才输出
        foreach ($orgConfig['service_scope'] as $value => $option) {
            if (!in_array($value,$typeValueArr['service_scope'])) {
                continue;
            }
            $healthService[] = [
                'option'  => $option,
                'checked' => in_array($value,$typeValueArr['service_scope']) ? 1 : 0
            ];
            if (in_array($value,$typeValueArr['service_scope'])) {
                $serviceScope[] = $option;
            }
        }
        // 先获取所有设备，设施设备
        $equipments = $this->equipmentModel->getAllEquipment();
        $equipment = [];
        foreach ($equipments as $e) {
            // TODO 修改为0的不输出
            if (!in_array($e['id'],$typeValueArr['equipment'])) {
                continue;
            }
            $equipment[] = [
                'option'  => $e['name'],
                'checked' => in_array($e['id'],$typeValueArr['equipment']) ? 1 : 0,
                'pic' => $e['pic']
            ];
        }
        // 收住对象
        $targetPersion = [];
        foreach ($orgConfig['target_person'] as $value => $option) {
            if (in_array($value,$typeValueArr['target_person'])) {
                $targetPersion[] = $option;
            }
        }

        $detail = [
            'name'  => $org['name'],
            'address'  => $org['address'],
            'grade' => $org['grade'],//评级 TODO +1展示实际的星数
            'price' => $org['min_price'] . '-' . $org['max_price'],
            'health_service' => $healthService,//医保以及服务范围组成tag
            'service' => $org['service'],//提供服务
            'equipment' => $equipment,//设备
            'nature' => $orgConfig['nature'][$org['nature']],
            'company' => $org['company'],
            'health_insurance' => $org['health_insurance'],
            'set_time' => $org['set_time'],
            'cover_area' => !empty($org['cover_area']) ? $org['cover_area'].'平米' : '',
            'structure_area' => !empty($org['structure_area']) ? $org['structure_area'].'平米' : '',
            'bed_number' => $org['bed_number'],
            'employee_number' => $org['employee_number'],
            'comment' => $org['comment'],
            'phone1' => '',
            'phone2' => !empty($org['short_tel']) ? '4008109969,'.$org['short_tel'] :$org['phone2'],
            'target_person' => $targetPersion,//收住对象
            'service_scope' => $serviceScope,///服务范围
            'city_id' => $org['city_id'],
            'district_id' => $org['district_id'],
            'dean_name' => $org['dean_name'],
            'dean_desc' => $org['dean_desc'],
            'dean_content' => $org['dean_content'],
            'dean_pic' => $org['dean_pic'],
        ];

        return $detail;
    }

    public function createOrg($data = []) {
        $orgData = $data;
        unset($orgData['org_type'],$orgData['target_person'],$orgData['service_scope'],$orgData['equipment']);
        // 先新建org,得到orgId
        $ret = $this->create($orgData);
        if (!empty($ret)) {
            // 将复选数据存入org_filter表  org_type,target_person,service_scope,equipment
            $orgId = $ret['id'];
            foreach (self::$filterTypes as $type) {
                $filterData[$type] = !empty($data[$type]) ? $data[$type] : [];
            }
            $this->orgFilterModel->updateFilterValue($filterData,$orgId);
            $this->update(['sort' => $orgId],['id' => $orgId]);
        }
        return $ret;
    }

    public function updateOrg($orgId,$data = []) {
        $orgData = $data;
        unset($orgData['org_type'],$orgData['target_person'],$orgData['service_scope'],$orgData['equipment']);
        // 先新建org,得到orgId
        $ret = $this->update($orgData,['id' => $orgId]);
        if (!empty($ret)) {
            // 将复选数据存入org_filter表  org_type,target_person,service_scope,equipment
            foreach (self::$filterTypes as $type) {
                $filterData[$type] = !empty($data[$type]) ? $data[$type] : [];
            }
            $this->orgFilterModel->updateFilterValue($filterData,$orgId);
        }
        return $ret;
    }

    /**
     * 查询机构时需要同时将所有筛选值查出来
     * @param $id
     * @param string $fields
     * @return array|void
     */
    public function findOrg($id) {
        $org = $this->where('id',$id)->find()->toArray();
        // 查询所有filter
        $filters = $this->orgFilterModel->findAllByOrgId($id);
        foreach (self::$filterTypes as $type) {
            $org[$type] = [];
            foreach ($filters as $v) {
                if ($type == $v['type']) {
                    $org[$type][] = $v['value'];
                }
            }
        }
        return $org;
    }

    /**
     * 根据名称搜索机构
     * @param string $name
     */
    public function findOrgByName($name = '')
    {
        $org = $this->where('name',$name)->find();
        if (!empty($org)) {
            $org = $org->toArray();
        }

        return $org;
    }

    /**
     * 分页
     * 查询机构
     */
    public function orgPageList($name = '',$cityName = '',$page = 1,$limit = 10) {
        $where = [];
        if (!empty($name)) {
            $where['o.name'] = ['like',"%$name%"];
        }

        if (!empty($cityName)) {
            $where['c.city_name'] = ['like',"%$cityName%"];
        }
        // $where['o.status'] = 1;

        return $this->where($where)
            ->field('o.id,o.name,o.city_id,o.district_id,o.company,o.min_price,o.max_price,d.name as district_name,c.city_name,o.is_hot,o.sort,o.phone2,o.qrcode_image,o.haibao_image,o.status')
            ->alias('o')
            ->join('city c','c.id = o.city_id','LEFT')
            ->join('district d','d.id = o.district_id','LEFT')
            ->order('o.id desc')
            ->paginate($limit,false,['page' => $page])
            ->toArray();
    }

    /**
     * 查询所有机构
     */
    public function allOrgs() {
        return $this->field('id,name')->select();
    }
}