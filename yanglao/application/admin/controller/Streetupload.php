<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/8/5
 * Time: 10:02 AM
 */

namespace app\admin\controller;
use app\model\City;
use app\model\District;
use app\model\Street;
use app\model\Community;
use app\model\Food;
use think\Request;

class Streetupload extends AdminBase
{
    CONST BASE_UPLOAD_DIR = '/static/upload/street_excel';

    CONST BASE_IMAGE_UPLOAD_DIR = '/static/upload/image';

    public $cityModel;
    public $districtModel;
    public $streetModel;
    public $communityModel;
    public $foodModel;

    private $cityId;
    private $city;

    private $districtArray = [];

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->cityModel = new City();
        $this->districtModel = new District();
        $this->streetModel = new Street();
        $this->communityModel = new Community();
        $this->foodModel = new Food();
    }

    public function index()
    {
        $citys = $this->cityModel->allCitys([0,1]);
        $this->assign('citys',$citys);
        return $this->fetch();
    }

    /**
     * 上传助餐点excel
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function upload()
    {
        // 获取file
        $file = $_FILES['file'];
        // 后缀
        $extension = get_file_extension($file['name']);
        // 目录
        $dir = make_file_dir(SELF::BASE_UPLOAD_DIR);
        // 生成文件名
        $fileName = make_file_name($extension,array_get($this->getAccount(),'id'));
        $newFile = $dir . '/' . $fileName;
        move_uploaded_file($_FILES["file"]["tmp_name"],$newFile);
        $data = $this->getExcelData($newFile);
        $retData = [];
        if (!(empty($data) || count($data) < 2)) {
            // 第一行为标题，忽略。第二行开始上传助餐点数据
            array_shift($data);
            // 获取所有城市和区域
            $this->cityId = input('city_id');
            $this->city = $this->cityModel->find($this->cityId)->toArray();
            foreach ($data as $k => $v) {
                try {
                    $uploadRet = $this->addStreetAndCommunity($v);
                    if ($uploadRet === true) {
                        $retWord = 'success';
                    } else {
                        $retWord = $uploadRet;
                    }
                    try {
                        $retData[$v[1]] = $retWord;
                    } catch (\Exception $ee) {
                        $retData['第'.($k+1).'条数据'] = '此数据有异常,'.$uploadRet;
                    }
                } catch ( \Exception $e) {
                    $retData['第'.($k+1).'条数据'] = '此数据有异常';
                }
            }
        }

        return json(returnDataFormat('upload_success',0,$retData));
    }
    /**
     * 新建街道以及社区
     * @param $v
     */
    private function addStreetAndCommunity($v) {
        // 拼接数据
        //step-1查询区 先看数组里有没有
        $districtId = 0;
        if (empty($this->districtArray[$v[0]])) {
            $district = $this->districtModel->findByNameAndCity(trim($v[0]),$this->cityId);
            if (empty($district)) {
                return '区域错误';
            }
            $districtId = $district[0]['id'];
            $this->districtArray[$v[0]] = $districtId;
        } else {
            $districtId = $this->districtArray[$v[0]];
        }
        // 先建街道
        $streetData = [
            'name' => $v[1],
            'city_id' => $this->cityId,
            'district_id' => $districtId
        ];
        $streetRet = $this->streetModel->create($streetData);
        $streetId = $streetRet['id'];
        // 创建社区
        $communityNames = explode(' ',trim($v[2]));
        if (!empty($communityNames)) {
            foreach ($communityNames as $n) {
                if (empty($n)) {
                    continue;
                }
                $communityData = [
                    'city_id' => $this->cityId,
                    'district_id' => $districtId,
                    'street_id' => $streetId,
                    'name' => $n
                ];
                $this->communityModel->create($communityData);
            }
        }
        return true;
    }

    private function getExcelData($file) {
        require __DIR__ . '/../../../extend/classes/PHPExcel.php';
        $data = [];
        //得到excel操作对象
        $excel = \PHPExcel_IOFactory::load($file);
        // 所有sheep
        $SheetNames = $excel->getSheetNames();
        //获取当前工作表名
        $SheetName = $SheetNames[0];
        //根据表名切换当前工作表
        $excel->setActiveSheetIndexByName($SheetName);
        //得到当前工作表对象
        $curSheet = $excel->getActiveSheet();
        //获取当前工作表最大行数
        $rows = $curSheet->getHighestRow();
        //获取当前工作表最大列数,返回的是最大的列名，如：B
        $cols = $curSheet->getHighestColumn();

        for ($i = 1;$i <= $rows;$i++) {
            $item = [];
            for ($j = 'A';$j <= $cols; $j++) {
                $key = $j.$i;
                $value = $curSheet->getCell($key)->getValue();
                // 做兼容处理
                if (gettype($value) == 'object') {
                    if (get_class($value) == 'PHPExcel_RichText') {
                        $value = $value->__toString();
                    }
                }
                $item[] = $value;
            }
            if (!empty($item[0])) {
                $data[] = $item;
            }
        }

        return $data;
    }

    public function updateFood()
    {
        $foods = $this->foodModel->field('id,name,street,community')->select()->toArray();
        if (!empty($foods)) {
            foreach ($foods as $f) {
                $street = $this->streetModel->where('name',$f['street'])->find();
                $community = $this->communityModel->where('name',$f['community'])->find();
                if (empty($street) || empty($community)) {
                    continue;
                }
                // 更新
                $updateData = [
                    'street_id' => $street->id,
                    'community_id' => $community->id
                ];
                $this->foodModel->update($updateData,['id' => $f['id']]);
            }
        }
        die('over');
    }
}