<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/29
 * Time: 3:04 PM
 */

namespace app\admin\validate;
use think\Validate;

class Food extends Validate
{
    protected $rule = [
        'name'  => 'require',
        'city_id'    => 'require',
        'district_id'    => 'require',
        'street_id'    => 'require',
        'community_id'    => 'require',
        'address'    => 'require',
        'area'    => 'require',
        'opening_hours'    => 'require',
        'provide_food'    => 'require',
        'contacts'    => 'require'
    ];

    protected $message = [
        'name.require' => 'require_param_error',
        'city_id.require' => 'require_param_error',
        'district_id.require' => 'require_param_error',
        'street_id.require' => 'require_param_error',
        'community_id.require' => 'require_param_error',
        'address.require' => 'require_param_error',
        'area.require' => 'require_param_error',
        'opening_hours.require' => 'require_param_error',
        'provide_food.require' => 'require_param_error',
        'contacts.require' => 'require_param_error'
    ];

    protected $scene = [
        'create' => ['name','city_id','district_id','street_id','community_id','address','area','opening_hours','provide_food','contacts'],
        'update' => ['id','name','city_id','district_id','street_id','community_id','address','area','opening_hours','provide_food','contacts']
    ];
}