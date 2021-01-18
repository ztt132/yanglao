<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/29
 * Time: 2:07 PM
 */

namespace app\admin\validate;

use app\model\Introduce as IntroduceModel;
use think\Validate;

class Introduce extends Validate
{
    protected $rule = [
        'org_id'     => 'require|introduceExist',
        'dean_name'  => 'require',
        'dean_desc'  => 'require',
        'pic'        => 'require',
        'content'    => 'require',
        'id'         => 'require'
    ];

    protected $message = [
        'org_id.require' => 'require_param_error',
        'dean_name.require' => 'require_param_error',
        'dean_desc.require' => 'require_param_error',
        'pic.require' => 'require_param_error',
        'content.require' => 'require_param_error',
        'id.require' => 'require_param_error'
    ];

    protected $scene = [
        'create' => ['org_id','dean_name','dean_desc','pic','content'],
        'update' => ['id','org_id','dean_name','dean_desc','pic','content']
    ];

    protected function introduceExist($value) {
        $introduceModel = new IntroduceModel();
        // 需要区分更新和创建
        $data = $introduceModel->where('org_id',$value)->find();
        if ($this->currentScene == 'create') {
            if (!empty($data)) {
                return 'introduce_exist';
            }
        } else {
            // 更新时,如果是当前id则不考虑
            $updateId = input('id');
            if ($updateId != $data['id']) {
                return 'introduce_exist';
            }
        }

        return true;
    }
}