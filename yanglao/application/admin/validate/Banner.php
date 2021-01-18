<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/24
 * Time: 2:48 PM
 */

namespace app\admin\validate;


use think\Validate;

class Banner extends Validate
{
    protected $rule = [
        'title'     => 'require',
        'position'  => 'require',
        'pic'       => 'require',
        'link_type' => 'require',
        'link_url'  => 'require',
        'id'        => 'require',
        'deadline'  => 'require'
    ];

    protected $message = [
        'title.require'     => 'require_param_error',
        'position.require'  => 'require_param_error',
        'pic.require'       => 'require_param_error',
        'link_type.require' => 'require_param_error',
        'link_url.require'  => 'require_param_error',
        'id.require'        => 'require_param_error',
    ];

    protected $scene = [
        'create' => ['title','position','pic','link_type','link_url','deadline'],
        'update' => ['id','title','position','pic','link_type','link_url','deadline']
    ];
}