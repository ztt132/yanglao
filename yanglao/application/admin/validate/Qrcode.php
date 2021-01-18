<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/8/26
 * Time: 3:26 PM
 */

namespace app\admin\validate;
use think\Validate;
use app\model\News;
use app\model\Org;

class Qrcode extends Validate
{
    protected $rule = [
        'name'  => 'require',
        'obj_id' => 'require|objExist',
        'id'    => 'require'
    ];

    protected $message = [
        'name.require' => 'require_param_error',
        'obj_id.require' => 'require_param_error',
        'id.require' => 'require_param_error'
    ];

    protected $scene = [
        'create' => ['name','obj_id'],
        'update' => ['id','name','obj_id']
    ];

    protected function objExist($value)
    {
        $qrcodeType = input('qrcode_type');
        // 验证objid
        $model = null;
        switch ($qrcodeType) {
            case 0:
                $model = new Org();
                break;
            case 1:
                $model = new News();
                break;
        }
        $obj = $model->where('id',$value)->find();
        return empty($obj) ? 'obj_not_exist' : true;
    }

}