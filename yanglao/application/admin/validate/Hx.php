<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/24
 * Time: 11:20 AM
 */

namespace app\admin\validate;


use think\Validate;

class Hx extends Validate
{
    protected $rule = [
        'org_id'  => 'require',
        'name' => 'require',
        'desc' => 'require',
        'cover_pic' => 'require',
        'id'    => 'require'
    ];

    protected $message = [
        'org_id.require' => 'require_param_error',
        'cover_pic.require' => 'require_param_error',
        'name.require' => 'require_param_error',
        'desc.require' => 'require_param_error',
        'id.require' => 'require_param_error'
    ];

    protected $scene = [
        'create' => ['org_id','cover_pic','name','desc'],
        'update' => ['id','org_id','cover_pic','name','desc']
    ];
}