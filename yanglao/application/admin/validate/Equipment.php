<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/29
 * Time: 3:04 PM
 */

namespace app\admin\validate;


use think\Validate;

class Equipment extends Validate
{
    protected $rule = [
        'sort'  => 'require',
        'id'    => 'require',
        'name'    => 'require'
    ];

    protected $message = [
        'name.require' => 'require_param_error',
        'id.require' => 'require_param_error',
        'sort.require' => 'require_param_error',
    ];

    protected $scene = [
        'create' => ['sort','name'],
        'update' => ['id','sort','name']
    ];
}