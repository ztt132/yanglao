<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/17
 * Time: 10:11 AM
 * @desciption
 */
namespace app\admin\validate;

use think\Validate;
use app\model\City as CityModel;

class City extends Validate
{
    protected $rule = [
        'pinyin'  => 'require|cityExist',
        'id'    => 'require'
    ];

    protected $message = [
        'name.require' => 'require_param_error',
        'id.require' => 'param_error'
    ];

    protected $scene = [
        'create' => ['pinyin'],
        'update' => ['id','pinyin']
    ];

    protected function cityExist($value) {
        $cityModel = new CityModel();
        if (!empty($cityModel->findByPinyin($value))) {
            return 'city_exist';
        }
        return true;
    }

}