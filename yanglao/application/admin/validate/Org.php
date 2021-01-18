<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/21
 * Time: 4:39 PM
 */

namespace app\admin\validate;
use think\Validate;

class Org extends Validate
{
    protected $rule = [
        'name'        => 'require',
        'address'     => 'require',
        'city_id'     => 'require',
        'district_id' => 'require',
        'grade'       => 'require',
        'nature'      => 'require',
        'company'     => 'require',
        'set_time'    => 'require',
        'cover_area'  => 'require',
        'structure_area'  => 'require',
        'bed_number'      => 'require',
        'employee_number' => 'require',
        'id'              => 'require',
        'tag'             => 'checkTag'
    ];

    protected $message = [
        'name.require' => 'require_param_error',
        'address.require' => 'require_param_error',
        'city_id.require' => 'require_param_error',
        'district_id.require' => 'require_param_error',
        'grade.require' => 'require_param_error',
        'nature.require' => 'require_param_error',
        'company.require' => 'require_param_error',
        'set_time.require' => 'require_param_error',
        'cover_area.require' => 'require_param_error',
        'structure_area.require' => 'require_param_error',
        'bed_number.require' => 'require_param_error',
        'employee_number.require' => 'require_param_error',
        'id.require' => 'require_param_error'
    ];

    protected $scene = [
        'create' => ['city_id','district_id','nature','address','tag'],
        'update' => ['id','city_id','district_id','nature','address','tag'],
    ];

    protected function checkTag($value) {
        $ret = 'org_tag_error';
        if (!empty($value)) {
            foreach ($value as $v) {
                if ($v !== '') {
                    $ret = true;
                    break;
                }
            }
        }
        return $ret;
    }
}