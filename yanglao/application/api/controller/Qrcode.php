<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/7/9
 * Time: 9:14 AM
 */

namespace app\api\controller;


use think\Controller;
use think\Request;
use app\model\Qrcode as QrcodeModel;

class Qrcode extends Controller
{
    public $qrcodeModel;
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->qrcodeModel = new QrcodeModel();
    }

    public function index() {
        $id = input('id/d');
        if (empty($id)) {
            return ['code' => 0, 'data' => [], 'msg'  => '缺少参数'];
        }

        $qrcode = $this->qrcodeModel->where('id',$id)->find();
        if (empty($qrcode)) {
            return ['code' => 0, 'data' => [], 'msg'  => '对象不存在'];
        }
        if (!$qrcode->is_delete && $qrcode->status) {
            // PV+1
            $this->qrcodeModel->update(['pv' => $qrcode->pv+1],['id'=>$id]);
        }
        // 转换id 资讯的参数为newsid
        $param = $qrcode->param;
        if ($qrcode->qrcode_type == 1) {
            $param = [
                'newsid' => $param['id']
            ];
        }


        $data = [
            'img' => $qrcode->qrcode_image,
            'path' => $qrcode->path . '?' . http_build_query($param),
            'status' => $qrcode->status,
            'isdel'  => $qrcode->is_delete
        ];

        return ['code' => 1, 'data' => $data, 'msg'  => 'success'];
    }
}