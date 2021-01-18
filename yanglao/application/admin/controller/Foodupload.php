<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/8/5
 * Time: 10:02 AM
 */

namespace app\admin\controller;
use app\model\Food;
use app\model\City;
use app\model\District;
use think\Request;
use app\model\Street;
use app\model\Community;

class Foodupload extends AdminBase
{
    CONST BASE_UPLOAD_DIR = '/static/upload/food_excel';

    CONST BASE_IMAGE_UPLOAD_DIR = '/static/upload/image';

    public $foodModel;
    public $cityModel;
    public $districtModel;
    private $shortPhone;
    public $streetModel;
    public $communityModel;

    private $cityId;
    private $city;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->foodModel = new Food();
        $this->cityModel = new City();
        $this->districtModel = new District();
        require __DIR__ . '/../../../extend/shortphone/ShortPhone.php';
        $this->shortPhone = new \ShortPhone();
        $this->streetModel = new Street();
        $this->communityModel = new Community();
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
                    $uploadRet = $this->addOrg($v);
                    if ($uploadRet === true) {
                        $retWord = 'success';
                    } else {
                        $retWord = $uploadRet;
                    }
                    try {
                        $retData[($k+2).'-'.$v[3].'-'.$v[5]] = $retWord;
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
     * 新建助餐点
     * @param $v
     */
    private function addOrg($v) {
        // 拼接数据
        //step-1查询区
        $district = $this->districtModel->findByNameAndCity($v[0],$this->cityId);
        if (empty($district)) {
            return '区域错误';
        }
        $name = $v[3];//名称

        if (empty($name)) {
            return '名称为空';
        }

        $districtId = $district[0]['id'];
        $streetName = $v[1] ? $v[1] : '';// 街道
        $communityName = $v[2] ? $v[2] : '';//社区
        // 处理社区街道
        if (empty($streetName) || empty($communityName)) {
            return '社区或者街道名称为空';
        }
        $street = $this->streetModel->where('name',$streetName)->find();
        $community = $this->communityModel->where('name',$communityName)->find();
        if (empty($street)) {
            return '街道不存在';
        }
        if (empty($community)) {
            return '社区不存在';
        }
        $streetId = $street->id;
        $communityId = $community->id;

        $natural = $v[4] ? $v[4] : '';//资质编号
        $address = $v[5] ? $v[5] : '';//地址
        $lat = $v[6] ? $v[6] : '';//纬度
        $lng= $v[7] ? $v[7] : '';//经度
        $area = $v[8] ? $v[8] : '';//面积
        $openingHours = $v[9] ? $v[9] : '';//营业时间
        $provideFood = $v[10] ? $v[10] : '';//提供餐次

        $breafastPrice = $v[11] ? $v[11] : '';//早餐价格
        $lunchPrice = $v[12] ? $v[12] : '';//午餐价格
        $dinnerPrice = $v[13] ? $v[13] : '';//晚餐价格

        $breafastSub = $v[14] ? $v[14] : '';//早餐价格
        $lunchSub = $v[15] ? $v[15] : '';//午餐价格
        $dinnerSub = $v[16] ? $v[16] : '';//晚餐价格

        $breafastTime = $v[17] ? $v[17] : '';//早餐价格
        $lunchTime = $v[18] ? $v[18] : '';//午餐价格
        $dinnerTime = $v[19] ? $v[19] : '';//晚餐价格

        $contacts = $v[20] ? $v[20] : '';//联系人

        $phone2 = $v[21] ? $v[21] : '';//电话
        $prefix =  !empty($v[22]) ? $v[22] : 0;//区号
        $shortTel = '';
        if (!empty($phone2)) {
            // 生成短号
            $ret = $this->shortPhone->getShort($this->city['city_name'],$name);
            if (!$ret) {
                return '获取短号失败';
            }
            $bindKey = $ret['bind_key'];
            $shortTel = $ret['short_tel'];
            // 绑定
            $bindRet = $this->shortPhone->bindShort($bindKey,$phone2,$prefix);
            if (!$bindRet) {
                return '绑定短号失败';
            }
        }
        $pic = $this->getPic($name);

        $data = [
            'name' => $name,
            'city_id' => $this->cityId,
            'district_id' => $districtId,
            'street_id' => $streetId,
            'community_id' => $communityId,
            'address' => $address,
            'area' => $area,
            'opening_hours' => $openingHours,
            'provide_food' => $provideFood,
            'contacts' => $contacts,
            'prefix' => $prefix,
            'phone2' => $phone2,
            'short_tel' => $shortTel,
            'pic' => $pic,
            'lng' => $lng,
            'lat' => $lat,
            'breakfast_price' => $breafastPrice,
            'breakfast_sub' => $breafastSub,
            'breakfast_time' => $breafastTime,
            'lunch_price' => $lunchPrice,
            'lunch_sub' => $lunchSub,
            'lunch_time' => $lunchTime,
            'dinner_price' => $dinnerPrice,
            'dinner_sub' => $dinnerSub,
            'dinner_time' => $dinnerTime,
            'natural' => $natural,
            'status' => 1,
            'is_delete' => 0
        ];
        $this->foodModel->createFood($data);
        return true;
    }

    private function getPic($name) {
        $extension = ['png','jpg'];
        $pic = '';
        foreach ($extension as $v) {
            $fileTemp = './static/food_images/'.$name.'.'.$v;
            if (file_exists($fileTemp)) {
                // 目录
                $dir = make_file_dir(SELF::BASE_IMAGE_UPLOAD_DIR);
                // 生成文件名
                $fileName = make_file_name($v);
                $newFile = $dir . '/' . $fileName;
                copy($fileTemp,$newFile);
                $pic = get_protocol().'://'.get_server_name().SELF::BASE_IMAGE_UPLOAD_DIR . '/' . date('Ymd',time()) . '/' . $fileName;
            }
        }

        return $pic;
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
}