<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/19
 * Time: 2:07 PM
 */

namespace app\admin\validate;
use app\admin\service\DistrictService;
use think\Validate;
use app\model\District as DistrictModel;

class District extends Validate
{
    protected $rule = [
        'city_id'  => 'require',
        'name' => 'require|districtExist',
        'id'   => 'require'
    ];

    protected $message = [
        'name.require' => 'require_param_error',
        'city_id.require' => 'require_param_error',
        'id.require' => 'require_param_error'
    ];

    protected $scene = [
        'create' => ['name','city_id'],
        'update' => ['name','city_id','id'],
        'delete' => ['id']
    ];

    /**
     * 判断区域是否存在
     * @param $value
     */
    protected function districtExist($value) {
        $model = new DistrictModel();
        $cityId = input('city_id');
        $districts = $model->findByNameAndCity($value,$cityId);
        if (!empty($districts)) {
            if ($this->currentScene == 'create') {
                return 'district_exist';
            } else {
                $district = $districts[0];
                $updateId = input('id');
                if ($updateId != $district['id']) {
                    return 'district_exist';
                }
            }
        }
        return true;
    }
}