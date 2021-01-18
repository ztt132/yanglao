<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/28
 * Time: 1:46 PM
 */

namespace app\admin\validate;


use think\Validate;

class Account extends Validate
{
    protected $rule = [
        'id'   => 'require',
        'user_name' => 'require',
        'account_name'    => 'require',
        'password' => 'require',
        'old_password' => 'require'
    ];

    protected $message = [
        'id.require' => 'require_param_error',
        'user_name.require' => 'require_param_error',
        'account_name.require' => 'require_param_error',
        'password.require' => 'require_param_error',
        'old_password.require' => 'require_param_error'
    ];

    protected $scene = [
        'create' => ['user_name','account_name'],
        'update' => ['id']
    ];
}