<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/28
 * Time: 10:12 AM
 */

namespace app\admin\validate;


use think\Validate;

class News extends Validate
{
    protected $rule = [
        'title'   => 'require',
        'city_id' => 'require',
        'type'    => 'require',
        'publisher'    => 'require',
        'content' => 'require',
        'id'      => 'require'
    ];

    protected $message = [
        'title.require' => 'require_param_error',
        'city_id.require' => 'require_param_error',
        'type.require' => 'require_param_error',
        'publisher.require' => 'require_param_error',
        'content.require' => 'require_param_error',
        'id.require' => 'require_param_error'
    ];

    protected $scene = [
        'create' => ['title','city_id','type','content','publisher'],
        'update' => ['id','title','city_id','type','content','publisher']
    ];
}