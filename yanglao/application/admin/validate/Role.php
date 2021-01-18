<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/28
 * Time: 2:23 PM
 */

namespace app\admin\validate;


use think\Validate;

class Role extends Validate
{
    protected $rule = [
        'id'   => 'require',
        'name' => 'require'
    ];

    protected $message = [
        'id.require' => 'require_param_error',
        'name.require' => 'require_param_error'
    ];

    protected $scene = [
        'create' => ['name'],
        'update' => ['id','name']
    ];
}