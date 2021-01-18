<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2021/1/7
 * Time: 3:07 PM
 */

namespace app\model;
use think\Config;
use think\Model;
use think\Db;
use app\model\Estatehx;
use app\model\Estatephoto;
use app\model\Equipment;
use app\model\Estatenews;

class Estate extends Model
{
    protected $dateFormat = 'Y-m-d';

    protected $autoWriteTimestamp = true;

    public $equipmentModel;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->equipmentModel = new Equipment();
    }

    protected $type = [
        'equipment' => 'json',
        'service' => 'json',
        'medical' => 'json',
    ];

    protected static function init()
    {
        Estate::event('before_insert', function ($estate) {
            if (empty($estate->equipment)) {
                $estate->equipment = [];
            }
            if (empty($estate->service)) {
                $estate->service = [];
            }
            if (empty($estate->medical)) {
                $estate->medical = [];
            }
        });

        Org::event('before_update', function ($org) {

        });
    }

    /**
     * 新增地产时保存操作
     * @param array $data
     */
    public function createEstate($data = [])
    {
        $ret = $this->create($data);
        if (!empty($ret)) {
            // 将复选数据存入org_filter表  org_type,target_person,service_scope,equipment
            $estateId = $ret['id'];
            $this->update(['sort' => $estateId],['id' => $estateId]);
        }
        return $ret;
    }

    /**
     * 后台获取养老地产列表
     * @param $name
     * @param $cityName
     * @param $districtName
     * @param $page
     * @param $limit
     */
    public function estatePageList($name = '',$cityName = '',$districtName = '',$page = 1,$limit = 10)
    {
        $where = [];
        if (!empty($name)) {
            $where['e.name'] = ['like',"%$name%"];
        }

        if (!empty($cityName)) {
            $where['c.city_name'] = ['like',"%$cityName%"];
        }

        if (!empty($districtName)) {
            $where['d.name'] = ['like',"%$districtName%"];
        }

        $where['e.status'] = 1;
        return $this->where($where)
            ->field('e.id,e.name,e.sort,c.city_name,d.name as district_name')
            ->alias('e')
            ->join('city c','c.id = e.city_id','LEFT')
            ->join('district d','d.id = e.district_id','LEFT')
            ->order('e.id desc')
            ->paginate($limit,false,['page' => $page])
            ->toArray();
    }

    /**
     * 获取所有地产，包含城市名称
     */
    public function getAllEstatesAndCity()
    {
        return $this->where(['e.status' => 1])
            ->field("e.id,CONCAT_WS('-',e.name,c.city_name) as name")
            ->alias('e')
            ->join('city c','c.id = e.city_id','LEFT')
            ->order('e.id desc')
            ->select()
            ->toArray();
    }

    /**
     * 首页获取养老地产数量
     * @param int $cityId
     * @param string $keyWord
     */
    public function getCount($cityId = 0,$keyWord = '')
    {
        $where = [];
        if (!empty($cityId)) {
            $where['city_id'] = $cityId;
        }

        if (!empty($keyWord)) {
            $where['name'] = ['like',"%$keyWord%"];
        }

        return $this->where($where)->count();
    }

    /**
     * 获取养老地产列表接口
     * @param array $condition
     * @param array $hxCondition
     * @param int $page
     * @param int $limit
     */
    public function getList($cityId = 0,$condition = [],$hxCondition = [],$page = 1,$limit = 10)
    {
        $condition['e.status'] = 1;

        // 优先展示带vr的
        $vrDB = Db::name("estatehx")->field("id,estate_id,(case vr when NULL then 0 else 1 end) vr")->where(["vr" => ["<>",""]])->buildSql();
//        toJson($condition);
        $fields = 'e.id,e.name,d.name as district_name,c.city_name,e.status,e.price,e.type,e.nature,h.vr,e.sort,e.city_id,e.district_id';
        $query = $this->alias('e')
            ->join('city c','c.id = e.city_id','LEFT')
            ->join('district d','d.id = e.district_id','LEFT')
            ->join([$vrDB => 'h'],'h.estate_id = e.id','LEFT');
        if (!empty($hxCondition)) {
            $condition = array_merge($condition,$hxCondition);
            $query->join('estatehx eh','eh.estate_id = e.id','LEFT');
            $fields .= ',eh.shi';
        }
        // 追加城市,区域状态
        $condition['c.status'] = 1;
        $condition['d.status'] = 1;

        $query->where($condition)->field($fields);

        $data = $query->group('e.id')->order('e.city_id <> '.$cityId)
            ->order('h.vr is null,h.vr')
            ->order('e.sort desc')
            ->order('c.pinyin asc')
            ->paginate($limit,false,['page' => $page])
            ->toArray();
        if (!empty($data['data'])) {
            // 格式化数据
            $data['data'] = $this->format($data['data']);
        }

        return $data;
    }

    /**
     * 格式化地产列表数据
     * @param array $data
     */
    private function format($data = [])
    {
        $estateIds = array_column($data,'id');
        // 查询地产下是否有带vr的户型图
        $hxModel = new Estatehx();
        $hxs = $hxModel->getVrHxListByEstateIds($estateIds);
        // estate_id=>vr地址
        $hxVrMapping = !empty($hxs) ? array_column($hxs,'vr','estate_id') : [];
        // 查询相册
        $photoModel = new Estatephoto();
        $photos = $photoModel->getPhotoListByEstateIds($estateIds);
        // estate_id=>pic
        $photoMapping = [];
        if (!empty($photos)) {
            foreach ($photos as $photo) {
                // 如果此时mapping中无此estate，设置图片
                if (!array_key_exists($photo['estate_id'],$photoMapping)) {
                    $photoMapping[$photo['estate_id']] = $photo['pic'];
                } else {
                    // 判断是否需要替换
                    if ($photo['is_cover']) {
                        $photoMapping[$photo['estate_id']] = $photo['pic'];
                    }
                }
            }
        }

        foreach ($data as &$estate) {
            $estate['vr'] = array_key_exists($estate['id'],$hxVrMapping) ? 1 : 0;
            $estate['pic'] = array_key_exists($estate['id'],$photoMapping) ? $photoMapping[$estate['id']] : '';
            $estate['price'] = !empty($estate['price']) ? $estate['price'] . '元/㎡' : '待定';
//            $estate['vr'] = empty($estate['vr']) ? 0 : 1;
        }
        return $data;
    }

    /**
     *
     * @param int $id
     */
    public function getEstateDetail($id = 0)
    {
        $fields = 'e.*,c.city_name,d.name as district_name';
        $estate = $this->alias('e')->field($fields)
            ->join('city c','c.id = e.city_id','left')
            ->join('district d','d.id = e.district_id','left')
            ->where('e.id',$id)->where('e.status',1)->find();
        if (empty($estate)) {
            return [];
        }
        $estate = $estate->toArray();
        // 先获取所有设备，设施设备
        $equipments = $this->equipmentModel->getAllEquipment();
        $equipment = [];
        foreach ($equipments as $e) {
            // TODO 修改为0的不输出
            if (!in_array($e['id'],$estate['equipment'])) {
                continue;
            }
            $equipment[] = [
                'option'  => $e['name'],
                'checked' => in_array($e['id'],$estate['equipment']) ? 1 : 0,
                'pic' => $e['pic']
            ];
        }
        $conf = Config::get('estate.enum');
        // 部分数据转化
        $estate['equipment'] = $equipment;
        $estate['tag'] =  !empty($estate['tag']) ? explode(',',$estate['tag']) : [];
        $estate['type'] = $conf['type'][$estate['type']];
        $estate['nature'] = $conf['nature'][$estate['nature']];
        // 动态
        $estateNewsModel = new Estatenews();
        $newsList = $estateNewsModel->getNewsByEstateId($id);
        // 电话
        $estate['phone'] = !empty($estate['short_tel']) ? '4008109969,'.$estate['short_tel'] :$estate['phone'];
        $estate['news'] = $newsList;
        // 周边风景合并
        $estate['zhoubian'] = [
            ['key' => '高速','value' => $estate['gaosu']],
            ['key' => '医院','value' => $estate['hospital']],
            ['key' => '公交','value' => $estate['gongjiao']],
            ['key' => '地铁','value' => $estate['subway']],
            ['key' => '商业','value' => $estate['business']],
            ['key' => '风景','value' => $estate['scenery']]
        ];
        $estate['price'] = !empty($estate['price']) ? $estate['price'] . '元/㎡' : '待定';

        return $estate;
    }

    /**
     * 根据条件获取所有的地产
     * @param array $condition
     */
    public function getEstatesByCondition($condition = []) {
        $estates = $this->field('e.id as id,e.name,c.city_name,d.name as district_name,e.price,e.address')
            ->alias('e')
            ->join('city c','c.id = e.city_id','LEFT')
            ->join('district d','d.id = e.district_id','LEFT')
            ->where($condition)->select()->toArray();
        // 格式数据
        if (!empty($estates)) {
            $estates = $this->format($estates);
        }

        return $estates;
    }

}