<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/7/9
 * Time: 9:23 AM
 */

namespace app\model;


use think\Model;

class Qrcode extends Model
{
    protected $autoWriteTimestamp = true;

    protected $dateFormat = 'Y-m-d';

    protected $type = [
        'param' => 'json'
    ];

    public function qrcodePageList($name = '',$page = 1,$limit = 10)
    {
        $where = [
            'is_delete' => 0
        ];
        if (!empty($name)) {
            $where['name'] = ['like', "%$name%"];
        }

        return $this->where($where)->order('id desc')
            ->paginate($limit,false,['page' => $page])->toArray();
    }

    /**
     * 查询二维码
     * @param $objId
     * @param int $qrcodeType 0机构 1资讯
     */
    public function findQrcodeByObjId($objId,$qrcodeType = 0) {
        return $this->where([
            'obj_id' => $objId,
            'qrcode_type' => $qrcodeType
        ])->find();
    }
}