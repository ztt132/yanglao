<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/8/26
 * Time: 10:01 AM
 */

namespace app\admin\controller;

use app\model\Qrcode as QrcodeModel;
use think\Request;
use think\Config;
use app\model\Weouth;

class Qrcode extends AdminBase
{
    private $qrcodeModel;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->qrcodeModel = new QrcodeModel();
    }

    public function qrcodeList()
    {
        $this->isAjaxRequest();

        $name = $this->request->request('name');
        list($page,$limit) = $this->getPaginateInfo();
        $data = $this->qrcodeModel->qrcodePageList($name,$page,$limit);
        // 数据转换
        if (!empty($data['data'])) {
            $config = Config::get('Qrcode.qrcode_type');
            foreach ($data['data'] as &$v) {
                $v['qrcode_type'] = $config[$v['qrcode_type']]['name'];
            }
        }

        return $this->jsonData('',0,$data);
    }

    public function delete()
    {
        $this->isAjaxRequest();
        $id = input('id');
        $ret = $this->qrcodeModel->update([
            'is_delete' => 1
        ],['id' => $id]);

        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    public function add()
    {
        $config = Config::get('Qrcode.qrcode_type');
        $this->assign('config',$config);
        return $this->fetch();
    }

    public function create()
    {
        $param = $this->request->param();
        $this->dataValidate($param,'qrcode.create');
        // 检查是否已经添加过
        $qrcode = $this->qrcodeModel->findQrcodeByObjId($param['obj_id'],$param['qrcode_type']);
        if (!empty($qrcode)) {
            return $this->jsonData('qrcode_exist',1);
        }

        $name = $param['name'];
        $config = Config::get('Qrcode.qrcode_type');
        $data = [
            'name' => $name,
            'qrcode_type' => $param['qrcode_type'],
            'path' => $config[$param['qrcode_type']]['path'],
            'param' => ['id' => $param['obj_id']],//目前只需要id参数
            'obj_id' => $param['obj_id']
        ];
        $this->qrcodeModel->create($data);
        return $this->jsonData('create_success',0);
    }

    public function edit()
    {
        $id = $this->request->get('id');
        $qrcode = $this->qrcodeModel->find($id);
        if (!empty($qrcode)) {
            $qrcode = $qrcode->toArray();
            // 参数转换
            $qrcode['obj_id'] = $qrcode['param']['id'];
        }
        $config = Config::get('Qrcode.qrcode_type');
        $data = [
            'config' => $config,
            'qrcode' => $qrcode
        ];
        $this->assign($data);
        return $this->fetch();
    }

    public function update()
    {
        $param = $this->request->param();
        $this->dataValidate($param,'qrcode.update');

        $name = input('name');
        $config = Config::get('Qrcode.qrcode_type');
        $data = [
            'name' => $name,
            'qrcode_type' => $param['qrcode_type'],
            'path' => $config[$param['qrcode_type']]['path'],
            'param' => ['id' => $param['obj_id']],//目前只需要id参数
        ];
        $this->qrcodeModel->update($data,['id' => $param['id']]);
        return $this->jsonData('create_success',0);
    }

    /**
     * 修改状态
     * @return \think\response\Json
     */
    public function status()
    {
        $id = input('id');
        $status = !empty(input('status')) ? 1 : 0;
        $ret = $this->qrcodeModel->update([
            'status' => $status
        ],['id' => $id]);

        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    /**
     * 生成二维码
     * @return \think\response\Json
     */
    public function makeQrcode()
    {
        $scene = input('id');
        $qrcode = $this->qrcodeModel->find($scene);
        if (!empty($qrcode) && $qrcode->qrcode_image) {
            return $this->jsonData('qrcode_exist',1);
        }

        $page = 'pages/index/route';
        $weouth = new Weouth();
        $coderesult = $weouth->getwxacodeunlimit($scene,$page,430);
        if ($coderesult) {
            $qrcodeImage = make_qrcode($coderesult);
            // 更新数据
            $this->qrcodeModel->update(['qrcode_image'=>$qrcodeImage],['id'=>$scene]);
            return $this->jsonData('create_success',0,[
                'qrcode_image' => $qrcodeImage
            ]);
        } else {
            return $this->jsonData($weouth->errMsg,1);
        }

    }
}