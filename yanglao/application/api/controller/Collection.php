<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/7/2
 * Time: 3:21 PM
 */

namespace app\api\controller;
use think\Request;
use app\model\Collection as CollectionMode;
use app\model\Org;
use app\model\Estate as EstateModel;
use think\Db;

class Collection extends Baseuser
{
    public $collectionModel;
    public $orgMode;
    public $estateMode;
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->collectionModel = new CollectionMode();
        $this->orgMode = new Org();
        $this->estateMode = new EstateModel();
    }

    /**
     * 收藏操作
     */
    public function operation() {
        // 放置后期增加其他类型收藏，暂定1is_collection为机构
        $type     = input('post.type',1);
        $objectId = input('post.object_id');
        if (empty($objectId)) {
            return ['code' => 0, 'data' => [], 'msg'  => '缺少参数'];
        }
        // 验证org养老小程序增加取消收藏接口
        if ($type == 1) {
            $obj = $this->orgMode->where('id',$objectId)->find();
        } else {
            $obj = $this->estateMode->where('id',$objectId)->find();
        }
        if (empty($obj)) {
            return ['code' => 0, 'data' => [], 'msg'  => '对象不存在'];
        }
        $collection = $this->collectionModel->where([
            'type'      => $type,
            'open_id'   => $this->userinfo->openid,
            'object_id' => $objectId
        ])->find();
        if (empty($collection)) {
            // 新建
            $this->collectionModel->save([
                'type'    => $type,
                'open_id' => $this->userinfo->openid,
                'object_id'   => $objectId
            ]);
        } else {
            // 删除
            $this->collectionModel->where([
                'type'    => $type,
                'open_id' => $this->userinfo->openid,
                'object_id'   => $objectId
            ])->delete();
        }

        return ['code' => 1, 'data' => [], 'msg'  => 'success'];
    }

    /**
     * 收藏列表
     */
    public function index() {
        $openId = $this->userinfo->openid;
        // 获取所有收藏的机构id
        $collections = $this->collectionModel->getCollectionsByUserId($openId);
        if (empty($collections)) {
            return ['code' => 1, 'data' => [], 'msg'  => 'success'];
        }
        // 处理数据
        $orgIds = [];
        $estateIds = [];
        foreach ($collections as $collection) {
            if ($collection['type'] == 1) {
                // 机构
                $orgIds[] = $collection['object_id'];
            } elseif ($collection['type'] == 2) {
                $estateIds[] = $collection['object_id'];
            }
        }
        // 获取机构
        if (!empty($orgIds)) {
            $condition['o.id'] = ['in',$orgIds];
            $condition['o.status'] = 1;
            $orgs = $this->orgMode->getOrgsByCondition($condition);
            // 转换格式 方便处理
            if (!empty($orgs)) {
                $orgs = array_column($orgs,null,'id');
            }
        }
        // 获取地产
        if (!empty($estateIds)) {
            $eCon = [
                'e.id' => ['in',$estateIds],
                'e.status' => 1
            ];
            $estates = $this->estateMode->getEstatesByCondition($eCon);
            if (!empty($estates)) {
                $estates = array_column($estates,null,'id');
            }
        }

        $ret = [];
        $org_ids = array_column($orgs,'id');
        $estate_ids = array_column($estates,'id');

        foreach ($collections as $tc) {
            if ($tc['type'] == 1) {
                if (in_array($tc['object_id'], $org_ids)) {
                    $obj = $orgs[$tc['object_id']];
                    $obj['type'] = 1;
                    $ret[] = $obj;
                }
            } elseif ($tc['type'] == 2) {
                if (in_array($tc['object_id'], $estate_ids)) {
                    $obj = $estates[$tc['object_id']];
                    $obj['type'] = 2;
                    $ret[] = $obj;
                }
            }
        }


//
//
//        $data = $this->orgMode->getOrgsByCondition($condition);
//        // 按照orgid顺序进行排序
//        $data = array_column($data,NULL,'id');
//        $ret = [];
//        foreach ($orgIds as $orgId) {
//            if (isset($data[$orgId])) {
//                $ret[] = $data[$orgId];
//            }
//
//        }

        return ['code' => 1, 'data' => $ret, 'msg'  => 'success'];
    }

//    public function deleteUser() {
//        Db::name("userinfo")->where('openid','oUlCn5GrPCG5ODeYrD1PhGKfdzCU')->delete();
//        return ['code' => 1, 'data' => [], 'msg'  => 'success'];
//    }
}