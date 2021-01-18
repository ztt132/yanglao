<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/8/5
 * Time: 10:02 AM
 */

namespace app\admin\controller;
use app\model\Org;
use app\model\City;
use think\Config;
use think\Request;

class Upload extends AdminBase
{
    CONST BASE_UPLOAD_DIR = '/static/upload/org_excel';

    public $orgModel;
    public $cityModel;

    private $cityDistricts;
    private $config;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->orgModel = new Org();
        $this->cityModel = new City();
    }

    public function index()
    {
        return $this->fetch();
    }

    /**
     * 上传机构excel
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
//        $data = $this->getExcelData('./test11.xlsx');
        if (!(empty($data) || count($data) < 2)) {
            // 第一行为标题，忽略。第二行开始上传机构数据
            array_shift($data);
            // 获取所有城市和区域
            $this->setConfig();
            $retData = [];
            foreach ($data as $v) {
                $uploadRet = $this->addOrg($v);
                if ($uploadRet === true) {
                    $retData[$v[0]] = '上传成功';
                } else {
                    $retData[$v[0]] = $uploadRet;
                }
            }
        }

        return json(returnDataFormat('upload_success',0,$retData));
    }

    private function setConfig() {
        $this->cityDistricts = $this->cityModel->getAllCityAndDistrict();
        $config = [];
        $config['nature'] = array_flip(Config::get('org.nature'));
        $config['org_type'] = array_flip(Config::get('org.org_type'));
        $config['service_scope'] = array_flip(Config::get('org.service_scope'));
        $config['target_person'] = array_flip(Config::get('org.target_person'));
        $this->config = $config;
    }

    /**
     * 新建机构
     * @param $v
     */
    private function addOrg($v) {
        $orgName = $v[0];
        if (empty($orgName)) {
            return '机构名称为空';
        }
        // 增加验证 如果已经存在则忽略
        $org = $this->orgModel->findOrgByName($orgName);
        if (!empty($org)) {
            return '已存在此名称';
        }

        $cityId = array_get($this->cityDistricts,$v[1] . '.city_id');
        $districtId = array_get($this->cityDistricts,$v[1] . '.districts.'.$v[2]);
        if (empty($cityId) || empty($districtId)) {
            return '城市区域错误';
        }
        $nature = array_get($this->config,'nature.'.$v[3]);
        if (empty($nature) && $nature !== 0) {
            return '机构性质错误';
        }
        $grade = empty($v[5]) ? 0 : $v[5];
        $bedNumber = $v[6];
        $priceArr = explode('|',$v[7]);
        $employeeNumber = $v[8];
        $healthInsurance = $v[9] == '是' ? 1 : 0;
        $tag = explode('|',$v[10]);
        if (count($tag) < 3) {
            $l = 3 - count($tag);
            for ($i = 0;$i<$l;$i++) {
                $tag[] = "";
            }
        }

        $setTime = $v[11];
        $coverArea = $v[12];
        $structureArea = $v[13];
        $company = $v[14];
        $deanContent = $v['15'] ? "<p>" . $v[15] . "</p>" : '';
        $address = $v[16] ? $v[16]  : '';
        $phoneArr = explode('-',$v[17]);
        // 处理filter表中数据
        $orgType = [];
        $targetPerson = [];
        $serviceScope = [];

        // 机构类型
        if (!empty($v[4])) {
            $orgTypeArr = explode('|',$v[4]);
            foreach ($orgTypeArr as $orgTypeV) {
                $orgV = array_get($this->config,'org_type.'.$orgTypeV);
                if (null !== $orgV) {
                    $orgType[] = $orgV;
                }
            }
        } else {
            $orgType = array_values(array_get($this->config,'org_type'));
        }

        // 对象
        if (!empty($v[18])) {
            $targetPersonArr = explode('|',$v[18]);
            foreach ($targetPersonArr as $tpv) {
                $targetPersonV = array_get($this->config,'target_person.'.$tpv);
                if (null !== $targetPersonV) {
                    $targetPerson[] = $targetPersonV;
                }
            }
        } else {
            $targetPerson = array_values(array_get($this->config,'target_person'));
        }

        // 服务范围
        if (!empty($v[19])) {
            $serviceScopeArr = explode('|',$v[19]);
            foreach ($serviceScopeArr as $ssv) {
                $serviceScopeV = array_get($this->config,'service_scope.'.$ssv);
                if (null !== $serviceScopeV) {
                    $serviceScope[] = $serviceScopeV;
                }
            }
        } else {
            $serviceScope = array_values(array_get($this->config,'service_scope'));
        }

        // 追加服务项目和评论
        $comment = Config::get('org.comment');
        $serviceTemp = Config::get('org.service');
        $service = array_map(function($v) {
            $item = [
                'service_name' => $v['name'],
                'service_desc' => $v['desc']
            ];

            return $item;
        },$serviceTemp);

        $data = [
            'name' => $orgName,//机构名称
            'city_id' => $cityId,//城市id
            'district_id' => $districtId,//区域id
            'nature' => $nature,//机构性质
            'grade' => $grade,// 评级
            'bed_number' => $bedNumber ? $bedNumber : '',// 床位
            'min_price' => isset($priceArr[0]) ? $priceArr[0] : 0,//最低价格
            'max_price' => isset($priceArr[1]) ? $priceArr[1] : 0,//最高价格findOrg
            'employee_number' => $employeeNumber ? $employeeNumber : '',//医护人数
            'health_insurance' => $healthInsurance,//医保
            'tag' => $tag,//标签
            'set_time' => $setTime ? $setTime : '',//成立时间
            'cover_area' => $coverArea ? $coverArea : '',//占地面积
            'structure_area' => $structureArea ? $structureArea : '',//建筑时间
            'company' => $company ? $company : '',//公司
            'dean_content' => $deanContent,//机构介绍(院长内容)
            'address' => $address,
            'org_type' => $orgType,
            'target_person' => $targetPerson,
            'service_scope' => $serviceScope,
            'comment' => $comment,
            'service' => $service
        ];
        // 电话
        if (count($phoneArr) == 1) {
            $data['phone2'] = $phoneArr[0];
            $data['prefix'] = '0';
        } elseif (count($phoneArr) == 2) {
            $data['phone2'] = $phoneArr[1];
            $data['prefix'] = (string)$phoneArr[0];
        }

        $this->orgModel->createOrg($data);
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
                $item[] = $value;
            }
            if (!empty($item[0])) {
                $data[] = $item;
            }
        }

        return $data;
    }
}