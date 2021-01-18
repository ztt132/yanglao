<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/23
 * Time: 1:34 PM
 */
namespace app\admin\validate;
use think\Validate;

class Photo extends Validate
{
    protected $rule = [
        'org_id'  => 'require',
        'photo_type' => 'require',
        'pic' => 'require',
        'id'    => 'require'
    ];

    protected $message = [
        'org_id.require' => 'require_param_error',
        'photo_type.require' => 'require_param_error',
        'pic.require' => 'require_param_error',
        'id.require' => 'require_param_error'
    ];

    protected $scene = [
        'create' => ['org_id','photo_type','pic'],
        'update' => ['id','org_id','photo_type','pic'],
        'cover' => ['id']
    ];
}