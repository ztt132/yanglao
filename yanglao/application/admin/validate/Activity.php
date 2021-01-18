<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/28
 * Time: 1:01 PM
 */

namespace app\admin\validate;


use think\Validate;

class Activity extends Validate
{
    protected $rule = [
        'title'   => 'require',
        'org_id' => 'require',
        'type'    => 'require',
        'content' => 'require',
        'price'      => 'require',
        'id'      => 'require'
    ];

    protected $message = [
        'title.require' => 'require_param_error',
        'org_id.require' => 'require_param_error',
        'type.require' => 'require_param_error',
        'content.require' => 'require_param_error',
        'id.require' => 'require_param_error',
        'price.require' => 'require_param_error'
    ];

    protected $scene = [
        'create' => ['title','org_id','type','content','price'],
        'update' => ['id','title','org_id','type','content','price']
    ];
}